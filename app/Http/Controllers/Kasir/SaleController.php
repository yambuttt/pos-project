<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\DiningTable;
class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::query()
            ->with('items.product')
            ->where('user_id', auth()->id())
            ->whereDate('created_at', now()->toDateString())
            // untuk testing di kemudian hari ini kode nya  ->whereDate('created_at', now()->addDay()->toDateString())
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

        // Tambahkan atribut runtime untuk UI kasir
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




    public function store(Request $request)
    {
        $data = $request->validate([
            'order_type' => ['required', 'in:dine_in,takeaway'],
            'dining_table_id' => ['nullable', 'exists:dining_tables,id'],

            'paid_amount' => ['required', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        if (($data['order_type'] ?? null) === 'dine_in' && empty($data['dining_table_id'])) {
            return back()->withErrors(['sale' => 'Kalau Dine In, wajib pilih meja.'])->withInput();
        }

        $userId = \Illuminate\Support\Facades\Auth::id();

        // Helper: random huruf A-Z saja (bukan angka)
        $randomLetters = function (int $len): string {
            $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $out = '';
            for ($i = 0; $i < $len; $i++) {
                $out .= $alphabet[random_int(0, 25)];
            }
            return $out;
        };

        try {
            $sale = \Illuminate\Support\Facades\DB::transaction(function () use ($data, $userId, $randomLetters) {

                // Ambil produk + resep
                $productIds = collect($data['items'])->pluck('product_id')->unique()->values();
                $products = \App\Models\Product::with('recipes.rawMaterial')
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // 1) Hitung total & kebutuhan bahan (aggregate)
                $total = 0;
                $needs = []; // raw_material_id => qty_needed

                foreach ($data['items'] as $it) {
                    $p = $products[$it['product_id']] ?? null;
                    if (!$p || !$p->is_active) {
                        throw new \RuntimeException('Produk tidak ditemukan / tidak aktif.');
                    }
                    if ($p->recipes->count() === 0) {
                        throw new \RuntimeException("Produk '{$p->name}' belum punya resep, tidak bisa dijual (STRICT).");
                    }

                    $qty = (int) $it['qty'];
                    $subtotal = (int) $p->price * $qty;
                    $total += $subtotal;

                    foreach ($p->recipes as $r) {
                        $rid = (int) $r->raw_material_id;
                        $need = (float) $r->qty * $qty;
                        $needs[$rid] = ($needs[$rid] ?? 0) + $need;
                    }
                }

                $paid = (int) $data['paid_amount'];
                $tax = (int) round($total * 0.11);
                $grandTotal = $total + $tax;

                // Kalau QRIS: boleh “bablas” (anggap paid cukup)
                if ($data['payment_method'] === 'qris') {
                    $paid = $grandTotal;
                }

                if ($paid < $grandTotal) {
                    throw new \RuntimeException(
                        "Uang bayar kurang. Total (incl. pajak) Rp " . number_format($grandTotal, 0, ',', '.') . "."
                    );
                }

                // 2) Lock bahan baku yang dibutuhkan & validasi stok
                $rawIds = collect(array_keys($needs))->values();
                $materials = \App\Models\RawMaterial::whereIn('id', $rawIds)->lockForUpdate()->get()->keyBy('id');

                $insufficient = [];
                foreach ($needs as $rid => $qtyNeed) {
                    $m = $materials[$rid] ?? null;
                    $stock = (float) ($m?->stock_on_hand ?? 0);
                    if ($stock + 1e-9 < (float) $qtyNeed) {
                        $insufficient[] = [
                            'name' => $m?->name ?? "Material#$rid",
                            'unit' => $m?->unit ?? '',
                            'need' => $qtyNeed,
                            'stock' => $stock,
                        ];
                    }
                }
                if (count($insufficient)) {
                    $msg = "Stok bahan kurang:\n";
                    foreach ($insufficient as $x) {
                        $msg .= "- {$x['name']} ({$x['unit']}): butuh {$x['need']}, stok {$x['stock']}\n";
                    }
                    throw new \RuntimeException($msg);
                }

                // 3) Create Sale (langsung masuk antrian kitchen)
                $sale = \App\Models\Sale::create([
                    'invoice_no' => 'TEMP',
                    'user_id' => $userId,
                    'total_amount' => $grandTotal,
                    'paid_amount' => $paid,
                    'payment_method' => $data['payment_method'],
                    'order_type' => $data['order_type'],
                    'dining_table_id' => $data['order_type'] === 'dine_in' ? ($data['dining_table_id'] ?? null) : null,
                    'change_amount' => $paid - $grandTotal,
                    'status' => 'completed',
                    'kitchen_status' => 'new',
                ]);

                // ===== INVOICE RANDOM HURUF, ANTI DUPLICATE, PANJANG DINAMIS =====
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
                        \App\Models\Sale::where('invoice_no', $invoice)->exists()
                        && $attempt < $maxAttempts
                    );

                    if (\App\Models\Sale::where('invoice_no', $invoice)->exists()) {
                        $length++;
                        continue;
                    }

                    $sale->update(['invoice_no' => $invoice]);
                    break;
                }
                // ================================================================

                // 4) Sale items
                foreach ($data['items'] as $it) {
                    $p = $products[$it['product_id']];
                    $qty = (int) $it['qty'];
                    $subtotal = (int) $p->price * $qty;

                    \App\Models\SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $p->id,
                        'qty' => $qty,
                        'price' => (int) $p->price,
                        'subtotal' => $subtotal,
                        'note' => isset($it['note']) && trim((string) $it['note']) !== '' ? trim((string) $it['note']) : null,
                    ]);
                }

                // 5) Deduct stock + inventory movement (sale)
                foreach ($needs as $rid => $qtyNeed) {
                    $m = $materials[$rid];

                    $m->update([
                        'stock_on_hand' => (float) $m->stock_on_hand - (float) $qtyNeed,
                    ]);

                    \App\Models\InventoryMovement::create([
                        'raw_material_id' => $m->id,
                        'type' => 'sale',
                        'qty_in' => 0,
                        'qty_out' => $qtyNeed,
                        'reference_type' => \App\Models\Sale::class,
                        'reference_id' => $sale->id,
                        'created_by' => $userId,
                        'note' => 'Sale: ' . $sale->invoice_no,
                    ]);
                }

                return $sale;
            });

        } catch (\Throwable $e) {
            return back()->withErrors(['sale' => $e->getMessage()])->withInput();
        }

        return redirect()->route('kasir.sales.index')->with('success', 'Transaksi berhasil: ' . $sale->invoice_no);
    }
}
