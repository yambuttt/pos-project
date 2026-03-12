<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DiningTable;
use App\Services\MidtransService;
use App\Services\SaleInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::query()
            ->with('items.product')
            ->where('user_id', auth()->id())
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->paginate(10);

        return view('dashboard.kasir.sales.index', compact('sales'));
    }

    public function create(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->with(['recipes.rawMaterial'])
            ->orderBy('name')
            ->get();

        $products->each(function (Product $p) {
            $p->max_portions = (int) $p->maxServingsFromStock();
            $p->is_sellable = $p->max_portions > 0;
        });

        $productsJson = $products->map(function (Product $p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category' => $p->category,
                'price' => (int) $p->price,
                'max_portions' => (int) ($p->max_portions ?? 0),
                'is_sellable' => (bool) ($p->is_sellable ?? false),
            ];
        })->values();

        $tables = DiningTable::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('dashboard.kasir.sales.create', compact('products', 'productsJson', 'tables'));
    }

    public function store(
        Request $request,
        MidtransService $midtrans,
        SaleInventoryService $inventory
    ) {
        $data = $request->validate([
            'order_type' => ['required', 'in:dine_in,takeaway'],
            'dining_table_id' => ['nullable', 'exists:dining_tables,id'],
            'paid_amount' => ['required', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris,bca_va,bni_va,bri_va,permata_va'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        if (($data['order_type'] ?? null) === 'dine_in' && empty($data['dining_table_id'])) {
            return $this->errorResponse($request, 'Kalau Dine In, wajib pilih meja.');
        }

        $userId = auth()->id();
        $isCash = $data['payment_method'] === 'cash';

        $randomLetters = function (int $len): string {
            $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $out = '';
            for ($i = 0; $i < $len; $i++) {
                $out .= $alphabet[random_int(0, 25)];
            }
            return $out;
        };

        try {
            $sale = DB::transaction(function () use ($data, $userId, $isCash, $randomLetters, $inventory) {
                [$products, $needs, $subtotal] = $inventory->prepareFromItems($data['items']);
                $tax = (int) round($subtotal * 0.11);
                $grandTotal = $subtotal + $tax;

                $paid = $isCash ? (int) $data['paid_amount'] : $grandTotal;

                if ($isCash && $paid < $grandTotal) {
                    throw new \RuntimeException(
                        "Uang bayar kurang. Total (incl. pajak) Rp " . number_format($grandTotal, 0, ',', '.') . "."
                    );
                }

                $materials = $inventory->lockAndValidateMaterials($needs);

                $sale = Sale::create([
                    'invoice_no' => 'TEMP',
                    'user_id' => $userId,
                    'total_amount' => $grandTotal,
                    'paid_amount' => $isCash ? $paid : 0,
                    'payment_method' => $data['payment_method'],
                    'payment_status' => $isCash ? 'paid' : 'pending',
                    'order_type' => $data['order_type'],
                    'dining_table_id' => $data['order_type'] === 'dine_in'
                        ? ($data['dining_table_id'] ?? null)
                        : null,
                    'change_amount' => $isCash ? ($paid - $grandTotal) : 0,
                    'status' => $isCash ? 'completed' : 'pending',
                    'kitchen_status' => 'new',
                    'paid_at' => $isCash ? now() : null,
                ]);

                $datePart = now()->format('Ymd');
                $length = 3;
                $maxAttempts = 50;

                while (true) {
                    $attempt = 0;

                    do {
                        $suffix = $randomLetters($length);
                        $invoice = "INV-{$datePart}-{$suffix}";
                        $attempt++;
                    } while (
                        Sale::where('invoice_no', $invoice)->exists()
                        && $attempt < $maxAttempts
                    );

                    if (Sale::where('invoice_no', $invoice)->exists()) {
                        $length++;
                        continue;
                    }

                    $sale->update([
                        'invoice_no' => $invoice,
                        'midtrans_order_id' => $invoice,
                    ]);
                    break;
                }

                foreach ($data['items'] as $it) {
                    $p = $products[$it['product_id']];
                    $qty = (int) $it['qty'];

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $p->id,
                        'qty' => $qty,
                        'price' => (int) $p->price,
                        'subtotal' => (int) $p->price * $qty,
                        'note' => filled($it['note'] ?? null) ? trim((string) $it['note']) : null,
                    ]);
                }

                $inventory->reserve($needs, $materials, $sale, $userId);

                return $sale->fresh(['items.product']);
            });

            if ($isCash) {
                return $request->expectsJson()
                    ? response()->json([
                        'ok' => true,
                        'redirect' => route('kasir.sales.index'),
                        'message' => 'Transaksi berhasil: ' . $sale->invoice_no,
                    ])
                    : redirect()->route('kasir.sales.index')
                        ->with('success', 'Transaksi berhasil: ' . $sale->invoice_no);
            }

            try {
                $charge = $midtrans->charge($sale);

                $sale->update([
                    'midtrans_transaction_id' => $charge['transaction_id'] ?? null,
                    'midtrans_transaction_status' => $charge['transaction_status'] ?? null,
                    'midtrans_payment_type' => $charge['payment_type'] ?? null,
                    'midtrans_response' => $charge,
                    'payment_expires_at' => $charge['expiry_time'] ?? null,
                ]);

                return response()->json([
                    'ok' => true,
                    'sale_id' => $sale->id,
                    'invoice_no' => $sale->invoice_no,
                    'payment_status' => $sale->payment_status,
                    'payment' => $midtrans->extractInstruction($charge),
                ]);
            } catch (\Throwable $e) {
                DB::transaction(function () use ($sale, $inventory, $userId) {
                    $sale->refresh();

                    $inventory->release($sale, $userId);

                    $sale->update([
                        'payment_status' => 'failed',
                        'status' => 'cancelled',
                    ]);
                });

                throw $e;
            }
        } catch (\Throwable $e) {
            return $this->errorResponse($request, $e->getMessage());
        }
    }

    public function paymentStatus(Sale $sale)
    {
        abort_unless($sale->user_id === auth()->id(), 403);

        return response()->json([
            'sale_id' => $sale->id,
            'invoice_no' => $sale->invoice_no,
            'status' => $sale->status,
            'payment_status' => $sale->payment_status,
            'paid_at' => optional($sale->paid_at)->toDateTimeString(),
        ]);
    }

    protected function errorResponse(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['ok' => false, 'message' => $message], 422)
            : back()->withErrors(['sale' => $message])->withInput();
    }
}