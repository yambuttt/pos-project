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


    public function byTableToken(string $token)
    {
        $table = DiningTable::query()
            ->where('qr_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        // PENTING: arahin ke halaman utama menu, BUKAN overview
        return redirect('/?table=' . $table->qr_token);
    }

    public function overview(Request $request)
    {
        $tables = DiningTable::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'qr_token']);

        $lockedTable = null;

        $token = (string) $request->query('table', '');
        if ($token !== '') {
            $lockedTable = DiningTable::query()
                ->where('qr_token', $token)
                ->where('is_active', true)
                ->first();
        }
        $isDelivery = !$lockedTable;

        return view('order.overview', compact('tables', 'lockedTable', 'isDelivery'));
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

        // Helper: random huruf A-Z (persis kasir)
        $randomLetters = function (int $len): string {
            $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $out = '';
            for ($i = 0; $i < $len; $i++)
                $out .= $alphabet[random_int(0, 25)];
            return $out;
        };

        try {
            $sale = DB::transaction(function () use ($data, $customerDisplay, $randomLetters) {

                // Buat user guest per transaksi (agar user_id valid untuk relasi cashier)
                $guest = User::create([
                    'name' => $customerDisplay,
                    'email' => 'guest_' . now()->format('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '@local.test',
                    'password' => Hash::make(bin2hex(random_bytes(16))),
                    'role' => 'guest',
                ]);

                $userId = $guest->id;

                // === COPY LOGIC KASIR: ambil produk + recipes, hitung total & needs ===
                $productIds = collect($data['items'])->pluck('product_id')->unique()->values();
                $products = Product::with('recipes.rawMaterial')
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

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

                        // KUNCI: WAJIB pakai $r->qty (bukan qty_needed)
                        $need = (float) $r->qty * $qty;

                        $needs[$rid] = ($needs[$rid] ?? 0) + $need;
                    }
                }

                $tax = (int) round($total * 0.11);
                $grandTotal = $total + $tax;

                // Untuk flow “tamu”: belum bayar di sistem -> set 0
                $paid = 0;

                // Lock bahan baku & validasi stok (copy kasir)
                $rawIds = collect(array_keys($needs))->values();
                $materials = RawMaterial::whereIn('id', $rawIds)->lockForUpdate()->get()->keyBy('id');

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

                // Create sale (masuk kitchen)
                $sale = Sale::create([
                    'invoice_no' => 'TEMP',
                    'user_id' => $userId,
                    'total_amount' => $grandTotal,
                    'paid_amount' => $paid,
                    'payment_method' => 'cash',
                    'order_type' => 'dine_in',
                    'dining_table_id' => $data['dining_table_id'],
                    'change_amount' => 0,
                    'status' => 'completed',
                    'kitchen_status' => 'new',
                ]);

                // Generate invoice unik (copy kasir)
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

                // Sale items (note per item)
                foreach ($data['items'] as $it) {
                    $p = $products[$it['product_id']];
                    $qty = (int) $it['qty'];
                    $subtotal = (int) $p->price * $qty;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $p->id,
                        'qty' => $qty,
                        'price' => (int) $p->price,
                        'subtotal' => $subtotal,
                        'note' => isset($it['note']) && trim((string) $it['note']) !== '' ? trim((string) $it['note']) : null,
                    ]);
                }

                // Deduct stock + inventory movement (sale) (copy kasir)
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