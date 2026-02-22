<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PublicMenuController extends Controller
{
    public function index()
    {
        // load recipes untuk hitung maxServingsFromStock()
        $products = Product::where('is_active', 1)
            ->with('recipes.rawMaterial')
            ->orderBy('name')
            ->get();

        // kategori unik
        $categories = $products
            ->pluck('category')
            ->filter()
            ->map(fn($c) => trim($c))
            ->unique()
            ->values();

        return view('welcome', compact('products', 'categories'));
    }

    public function overview()
    {
        // NEW: ambil meja aktif untuk dropdown
        $tables = DiningTable::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('order.overview', compact('tables'));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:80'],
            'dining_table_id' => ['required', 'exists:dining_tables,id'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $customerDisplay = trim($data['customer_name']) . '(Tamu)';

        // Helper random huruf A-Z (sama konsepnya dengan kasir)
        $randomLetters = function (int $len): string {
            $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $out = '';
            for ($i = 0; $i < $len; $i++) $out .= $alphabet[random_int(0, 25)];
            return $out;
        };

        try {
            $sale = DB::transaction(function () use ($data, $customerDisplay, $randomLetters) {

                // 1) buat user "guest" khusus untuk transaksi ini (supaya user_id tetap valid)
                $guest = User::create([
                    'name' => $customerDisplay,
                    'email' => 'guest_' . now()->format('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '@local.test',
                    'password' => Hash::make(bin2hex(random_bytes(16))),
                    'role' => 'guest',
                ]);

                $userId = $guest->id;

                // 2) ambil produk + recipes + rawMaterial untuk hitung kebutuhan stok
                $productIds = collect($data['items'])->pluck('product_id')->unique()->values();
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->with('recipes.rawMaterial')
                    ->get()
                    ->keyBy('id');

                // hitung subtotal
                $subtotalAll = 0;
                foreach ($data['items'] as $it) {
                    $p = $products[$it['product_id']];
                    $subtotalAll += ((int)$p->price) * ((int)$it['qty']);
                }

                $tax = (int) round($subtotalAll * 0.11);
                $grandTotal = $subtotalAll + $tax;

                // 3) hitung kebutuhan bahan (sama konsep kasir)
                $needs = []; // raw_material_id => qtyNeed
                foreach ($data['items'] as $it) {
                    $p = $products[$it['product_id']];
                    $qty = (int) $it['qty'];

                    foreach ($p->recipes as $r) {
                        $rid = $r->raw_material_id;
                        $needs[$rid] = ($needs[$rid] ?? 0) + ((float)$r->qty_needed * $qty);
                    }
                }

                $materials = RawMaterial::query()
                    ->whereIn('id', array_keys($needs))
                    ->get()
                    ->keyBy('id');

                // 4) strict: cek stok cukup
                $insufficient = [];
                foreach ($needs as $rid => $qtyNeed) {
                    $m = $materials[$rid] ?? null;
                    if (!$m) continue;

                    $stock = (float) $m->stock_on_hand;
                    if ($stock < (float)$qtyNeed) {
                        $insufficient[] = [
                            'name' => $m->name,
                            'unit' => $m->unit,
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

                // 5) create Sale (order_type dine_in wajib, masuk kitchen_status new)
                $sale = Sale::create([
                    'invoice_no' => 'TEMP',
                    'user_id' => $userId,
                    'total_amount' => $grandTotal,

                    // NOTE: sistemmu belum punya konsep "unpaid".
                    // Kita set default supaya data tetap masuk & tampil.
                    'paid_amount' => 0,
                    'payment_method' => 'cash',

                    'order_type' => 'dine_in',
                    'dining_table_id' => $data['dining_table_id'],

                    'change_amount' => 0,
                    'status' => 'completed',
                    'kitchen_status' => 'new',
                ]);

                // 6) generate invoice unik (mengikuti pola kasir :contentReference[oaicite:8]{index=8})
                $datePart = now()->format('Ymd');
                $length = 3;
                $maxAttempts = 50;

                while (true) {
                    $attempt = 0;

                    do {
                        $suffix = $randomLetters($length);
                        $invoice = "INV-{$datePart}-{$suffix}";
                        $attempt++;
                    } while (Sale::where('invoice_no', $invoice)->exists() && $attempt < $maxAttempts);

                    if (Sale::where('invoice_no', $invoice)->exists()) {
                        $length++;
                        continue;
                    }

                    $sale->update(['invoice_no' => $invoice]);
                    break;
                }

                // 7) sale items + note per item
                foreach ($data['items'] as $it) {
                    $p = $products[$it['product_id']];
                    $qty = (int) $it['qty'];
                    $subtotal = ((int)$p->price) * $qty;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $p->id,
                        'qty' => $qty,
                        'price' => (int) $p->price,
                        'subtotal' => $subtotal,
                        'note' => isset($it['note']) && trim((string)$it['note']) !== '' ? trim((string)$it['note']) : null,
                    ]);
                }

                // 8) deduct stock + inventory movement (sale) (pola kasir :contentReference[oaicite:9]{index=9})
                foreach ($needs as $rid => $qtyNeed) {
                    $m = $materials[$rid];

                    $m->update([
                        'stock_on_hand' => (float) $m->stock_on_hand - (float) $qtyNeed,
                    ]);

                    InventoryMovement::create([
                        'raw_material_id' => $m->id,
                        'type' => 'sale',
                        'qty_in' => 0,
                        'qty_out' => $qtyNeed,
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'created_by' => $userId,
                        'note' => 'Sale: ' . $sale->invoice_no,
                    ]);
                }

                return $sale;
            });

            return response()->json([
                'ok' => true,
                'invoice_no' => $sale->invoice_no,
                'sale_id' => $sale->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}