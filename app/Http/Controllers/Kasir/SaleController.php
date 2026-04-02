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
                        'redirect_url' => route('kasir.sales.show', $sale),
                        'message' => 'Transaksi berhasil: ' . $sale->invoice_no,
                    ])
                    : redirect()->route('kasir.sales.show', $sale)
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
                    'redirect_url' => route('kasir.sales.show', $sale),
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

    public function findPendingCashOrder(Request $request)
    {
        $data = $request->validate([
            'invoice_no' => ['required', 'string', 'max:50'],
        ]);

        $sale = Sale::with(['items.product', 'diningTable'])
            ->where('invoice_no', trim($data['invoice_no']))
            ->where('payment_method', 'cash')
            ->where('payment_status', 'pending')
            ->first();

        if (!$sale) {
            return response()->json([
                'ok' => false,
                'message' => 'Pesanan pending cash dengan kode bayar tersebut tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'sale' => [
                'id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
                'order_type' => $sale->order_type,
                'payment_method' => $sale->payment_method,
                'payment_status' => $sale->payment_status,
                'total_amount' => (int) $sale->total_amount,
                'dining_table' => $sale->diningTable?->name,
                'items' => $sale->items->map(function ($item) {
                    return [
                        'product_name' => $item->product->name ?? '-',
                        'qty' => (int) $item->qty,
                        'price' => (int) $item->price,
                        'subtotal' => (int) $item->subtotal,
                        'note' => $item->note,
                    ];
                })->values(),
            ],
        ]);
    }

    public function confirmPendingCashOrder(
        Request $request,
        SaleInventoryService $inventory
    ) {
        $data = $request->validate([
            'invoice_no' => ['required', 'string', 'max:50'],
            'paid_amount' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $result = DB::transaction(function () use ($data, $inventory) {
                $sale = Sale::with(['items.product', 'diningTable'])
                    ->lockForUpdate()
                    ->where('invoice_no', trim($data['invoice_no']))
                    ->where('payment_method', 'cash')
                    ->where('payment_status', 'pending')
                    ->first();

                if (!$sale) {
                    throw new \RuntimeException('Pesanan pending cash tidak ditemukan atau sudah dibayar.');
                }

                $paidAmount = (int) $data['paid_amount'];
                $totalAmount = (int) $sale->total_amount;

                if ($paidAmount < $totalAmount) {
                    throw new \RuntimeException(
                        'Uang bayar kurang. Total Rp ' . number_format($totalAmount, 0, ',', '.') . '.'
                    );
                }

                $inventory->commitPaid($sale, auth()->id());

                $sale->update([
                    'payment_status' => 'paid',
                    'status' => 'completed',
                    'paid_amount' => $paidAmount,
                    'change_amount' => $paidAmount - $totalAmount,
                    'paid_at' => now(),
                    'kitchen_status' => 'new',
                ]);

                return $sale->fresh(['items.product', 'diningTable']);
            });

            return response()->json([
                'ok' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi.',
                'redirect_url' => route('kasir.sales.show', $result),
                'sale' => [
                    'id' => $result->id,
                    'invoice_no' => $result->invoice_no,
                    'payment_status' => $result->payment_status,
                    'status' => $result->status,
                    'paid_amount' => (int) $result->paid_amount,
                    'change_amount' => (int) $result->change_amount,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    protected function errorResponse(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['ok' => false, 'message' => $message], 422)
            : back()->withErrors(['sale' => $message])->withInput();
    }

    public function show(Sale $sale, MidtransService $midtrans)
    {
        abort_unless($sale->user_id === auth()->id(), 403);

        $sale->load(['items.product', 'diningTable']);

        $payment = $sale->midtrans_response
            ? $midtrans->extractInstruction($sale->midtrans_response)
            : null;

        if (
            $sale->payment_status === 'pending' &&
            $sale->payment_expires_at &&
            $sale->payment_expires_at->isPast()
        ) {
            app(\App\Services\SaleInventoryService::class)->release($sale, auth()->id());

            $sale->update([
                'payment_status' => 'expired',
                'status' => 'expired',
            ]);

            $sale->refresh();
        }

        return view('dashboard.kasir.sales.show', compact('sale', 'payment'));
    }

    public function print(Sale $sale)
    {
        abort_unless($sale->user_id === auth()->id(), 403);
        abort_unless($sale->payment_status === 'paid', 403);

        $sale->load(['items.product', 'diningTable']);

        return view('dashboard.kasir.sales.print', compact('sale'));
    }
}