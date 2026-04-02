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
use Illuminate\Validation\Rule;
use App\Services\MidtransService;
use App\Services\SaleInventoryService;


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
        return redirect('/menu?table=' . $table->qr_token);
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

    // public function checkout(Request $request)
    // {
    //     $data = $request->validate([
    //         'customer_name' => ['required', 'string', 'max:80'],

    //         // ✅ wajib ada, supaya backend tahu mode apa
    //         'order_type' => ['required', Rule::in(['dine_in', 'delivery'])],

    //         // ✅ Meja hanya wajib kalau dine_in
    //         'dining_table_id' => [
    //             Rule::requiredIf(fn() => $request->input('order_type') === 'dine_in'),
    //             'nullable',
    //             'exists:dining_tables,id',
    //         ],

    //         // ✅ Field delivery hanya wajib kalau delivery
    //         'delivery_phone' => [
    //             Rule::requiredIf(fn() => $request->input('order_type') === 'delivery'),
    //             'nullable',
    //             'string',
    //             'max:30',
    //         ],
    //         'delivery_address' => [
    //             Rule::requiredIf(fn() => $request->input('order_type') === 'delivery'),
    //             'nullable',
    //             'string',
    //             'max:500',
    //         ],
    //         'delivery_lat' => [
    //             Rule::requiredIf(fn() => $request->input('order_type') === 'delivery'),
    //             'nullable',
    //             'numeric',
    //         ],
    //         'delivery_lng' => [
    //             Rule::requiredIf(fn() => $request->input('order_type') === 'delivery'),
    //             'nullable',
    //             'numeric',
    //         ],
    //         'delivery_distance_km' => ['nullable', 'numeric', 'min:0'],
    //         'delivery_fee' => ['nullable', 'integer', 'min:0'],

    //         'items' => ['required', 'array', 'min:1'],
    //         'items.*.product_id' => ['required', 'exists:products,id'],
    //         'items.*.qty' => ['required', 'integer', 'min:1'],
    //         'items.*.note' => ['nullable', 'string', 'max:255'],
    //     ]);

    //     $customerDisplay = trim($data['customer_name']) . '(Tamu)';

    //     // Helper: random huruf A-Z (persis kasir)
    //     $randomLetters = function (int $len): string {
    //         $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //         $out = '';
    //         for ($i = 0; $i < $len; $i++)
    //             $out .= $alphabet[random_int(0, 25)];
    //         return $out;
    //     };

    //     try {
    //         $sale = DB::transaction(function () use ($data, $customerDisplay, $randomLetters) {

    //             // Buat user guest per transaksi (agar user_id valid untuk relasi cashier)
    //             $guest = User::create([
    //                 'name' => $customerDisplay,
    //                 'email' => 'guest_' . now()->format('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '@local.test',
    //                 'password' => Hash::make(bin2hex(random_bytes(16))),
    //                 'role' => 'guest',
    //             ]);

    //             $userId = $guest->id;

    //             // === COPY LOGIC KASIR: ambil produk + recipes, hitung total & needs ===
    //             $productIds = collect($data['items'])->pluck('product_id')->unique()->values();
    //             $products = Product::with('recipes.rawMaterial')
    //                 ->whereIn('id', $productIds)
    //                 ->lockForUpdate()
    //                 ->get()
    //                 ->keyBy('id');

    //             $total = 0;
    //             $needs = []; // raw_material_id => qty_needed

    //             foreach ($data['items'] as $it) {
    //                 $p = $products[$it['product_id']] ?? null;
    //                 if (!$p || !$p->is_active) {
    //                     throw new \RuntimeException('Produk tidak ditemukan / tidak aktif.');
    //                 }
    //                 if ($p->recipes->count() === 0) {
    //                     throw new \RuntimeException("Produk '{$p->name}' belum punya resep, tidak bisa dijual (STRICT).");
    //                 }

    //                 $qty = (int) $it['qty'];
    //                 $subtotal = (int) $p->price * $qty;
    //                 $total += $subtotal;

    //                 foreach ($p->recipes as $r) {
    //                     $rid = (int) $r->raw_material_id;

    //                     // KUNCI: WAJIB pakai $r->qty (bukan qty_needed)
    //                     $need = (float) $r->qty * $qty;

    //                     $needs[$rid] = ($needs[$rid] ?? 0) + $need;
    //                 }
    //             }

    //             $tax = (int) round($total * 0.11);
    //             $grandTotal = $total + $tax;

    //             // Untuk flow “tamu”: belum bayar di sistem -> set 0
    //             $paid = 0;

    //             // Lock bahan baku & validasi stok (copy kasir)
    //             $rawIds = collect(array_keys($needs))->values();
    //             $materials = RawMaterial::whereIn('id', $rawIds)->lockForUpdate()->get()->keyBy('id');

    //             $insufficient = [];
    //             foreach ($needs as $rid => $qtyNeed) {
    //                 $m = $materials[$rid] ?? null;
    //                 $stock = (float) ($m?->stock_on_hand ?? 0);

    //                 if ($stock + 1e-9 < (float) $qtyNeed) {
    //                     $insufficient[] = [
    //                         'name' => $m?->name ?? "Material#$rid",
    //                         'unit' => $m?->unit ?? '',
    //                         'need' => $qtyNeed,
    //                         'stock' => $stock,
    //                     ];
    //                 }
    //             }
    //             if (count($insufficient)) {
    //                 $msg = "Stok bahan kurang:\n";
    //                 foreach ($insufficient as $x) {
    //                     $msg .= "- {$x['name']} ({$x['unit']}): butuh {$x['need']}, stok {$x['stock']}\n";
    //                 }
    //                 throw new \RuntimeException($msg);
    //             }

    //             // Create sale (masuk kitchen)
    //             $sale = Sale::create([
    //                 'invoice_no' => 'TEMP',
    //                 'user_id' => $userId,
    //                 'total_amount' => $grandTotal,
    //                 'paid_amount' => $paid,
    //                 'payment_method' => 'cash',

    //                 'order_type' => $data['order_type'],
    //                 'dining_table_id' => ($data['order_type'] === 'dine_in') ? $data['dining_table_id'] : null,

    //                 // ✅ TAMBAHKAN INI (biar tidak null)
    //                 'delivery_phone' => $data['order_type'] === 'delivery' ? ($data['delivery_phone'] ?? null) : null,
    //                 'delivery_address' => $data['order_type'] === 'delivery' ? ($data['delivery_address'] ?? null) : null,
    //                 'delivery_lat' => $data['order_type'] === 'delivery' ? ($data['delivery_lat'] ?? null) : null,
    //                 'delivery_lng' => $data['order_type'] === 'delivery' ? ($data['delivery_lng'] ?? null) : null,
    //                 'delivery_distance_km' => $data['order_type'] === 'delivery' ? ($data['delivery_distance_km'] ?? null) : null,
    //                 'delivery_fee' => $data['order_type'] === 'delivery' ? (int) ($data['delivery_fee'] ?? 0) : 0,

    //                 'change_amount' => 0,
    //                 'status' => 'completed',
    //                 'kitchen_status' => 'new',
    //             ]);

    //             // Generate invoice unik (copy kasir)
    //             $datePart = now()->format('Ymd');
    //             $length = 3;
    //             $maxAttempts = 50;

    //             while (true) {
    //                 $attempt = 0;

    //                 do {
    //                     $suffix = $randomLetters($length);
    //                     $invoice = "INV-{$datePart}-{$suffix}";
    //                     $attempt++;
    //                 } while (Sale::where('invoice_no', $invoice)->exists() && $attempt < $maxAttempts);

    //                 if (Sale::where('invoice_no', $invoice)->exists()) {
    //                     $length++;
    //                     continue;
    //                 }

    //                 $sale->update(['invoice_no' => $invoice]);
    //                 break;
    //             }

    //             // Sale items (note per item)
    //             foreach ($data['items'] as $it) {
    //                 $p = $products[$it['product_id']];
    //                 $qty = (int) $it['qty'];
    //                 $subtotal = (int) $p->price * $qty;

    //                 SaleItem::create([
    //                     'sale_id' => $sale->id,
    //                     'product_id' => $p->id,
    //                     'qty' => $qty,
    //                     'price' => (int) $p->price,
    //                     'subtotal' => $subtotal,
    //                     'note' => isset($it['note']) && trim((string) $it['note']) !== '' ? trim((string) $it['note']) : null,
    //                 ]);
    //             }

    //             // Deduct stock + inventory movement (sale) (copy kasir)
    //             foreach ($needs as $rid => $qtyNeed) {
    //                 $m = $materials[$rid];

    //                 $m->update([
    //                     'stock_on_hand' => (float) $m->stock_on_hand - (float) $qtyNeed,
    //                 ]);

    //                 InventoryMovement::create([
    //                     'raw_material_id' => $m->id,
    //                     'type' => 'sale',
    //                     'qty_in' => 0,
    //                     'qty_out' => $qtyNeed,
    //                     'reference_type' => Sale::class,
    //                     'reference_id' => $sale->id,
    //                     'created_by' => $userId,
    //                     'note' => 'Sale: ' . $sale->invoice_no,
    //                 ]);
    //             }

    //             return $sale;
    //         });

    //         return response()->json([
    //             'ok' => true,
    //             'invoice_no' => $sale->invoice_no,
    //             'sale_id' => $sale->id,
    //         ]);
    //     } catch (\Throwable $e) {
    //         return response()->json([
    //             'ok' => false,
    //             'message' => $e->getMessage(),
    //         ], 422);
    //     }
    // }


    public function landingTrial()
    {
        $featuredProducts = Product::query()
            ->where('is_active', 1)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        $galleryItems = [
            [
                'title' => 'Interior',
                'image' => asset('images/landing/gallery-1.jpg'),
                'size' => 'large',
            ],
            [
                'title' => 'Beverage',
                'image' => asset('images/landing/gallery-2.jpg'),
                'size' => 'large',
            ],
            [
                'title' => 'Ambiance',
                'image' => asset('images/landing/gallery-3.jpg'),
                'size' => 'large',
            ],
            [
                'title' => 'Dessert',
                'image' => asset('images/landing/gallery-4.jpg'),
                'size' => 'medium',
            ],
            [
                'title' => 'Dining Area',
                'image' => asset('images/landing/gallery-5.jpg'),
                'size' => 'medium',
            ],
            [
                'title' => 'Premium Room',
                'image' => asset('images/landing/gallery-6.jpg'),
                'size' => 'medium',
            ],
        ];

        $testimonials = [
            [
                'name' => 'Siti Nurhaliza',
                'role' => 'Food Blogger',
                'initial' => 'SN',
                'text' => 'Ayo Renne memberikan pengalaman kuliner yang luar biasa. Setiap hidangan disajikan dengan penuh perhatian dan cita rasa yang autentik.',
            ],
            [
                'name' => 'Budi Santoso',
                'role' => 'Local Resident',
                'initial' => 'BS',
                'text' => 'Sudah beberapa kali makan di sini dan selalu puas. Menu favoritku adalah hidangan premium-nya. Pelayanannya ramah dan tempatnya bersih.',
            ],
            [
                'name' => 'Amanda Wijaya',
                'role' => 'Travel Enthusiast',
                'initial' => 'AW',
                'text' => 'Perfect place untuk family gathering. Suasananya cozy, menunya beragam, dan yang paling penting rasanya enak semua.',
            ],
            [
                'name' => 'Rizki Pratama',
                'role' => 'Business Owner',
                'initial' => 'RP',
                'text' => 'Tempat favorit untuk meeting informal dengan klien. Ambience-nya profesional tapi tetap hangat. Sangat recommended.',
            ],
        ];

        return view('landing-trial', compact('featuredProducts', 'galleryItems', 'testimonials'));
    }


    public function checkout(
        Request $request,
        SaleInventoryService $inventory,
        MidtransService $midtrans
    ) {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:80'],
            'order_type' => ['required', Rule::in(['dine_in', 'delivery'])],
            'payment_method' => ['required', Rule::in(['qris', 'cash'])],

            'dining_table_id' => [
                Rule::requiredIf(fn() => $request->input('order_type') === 'dine_in'),
                'nullable',
                'exists:dining_tables,id',
            ],

            'delivery_phone' => [
                Rule::requiredIf(fn() => $request->input('order_type') === 'delivery'),
                'nullable',
                'string',
                'max:30',
            ],
            'delivery_address' => [
                Rule::requiredIf(fn() => $request->input('order_type') === 'delivery'),
                'nullable',
                'string',
                'max:500',
            ],
            'delivery_lat' => [
                Rule::requiredIf(fn() => $request->input('order_type') === 'delivery'),
                'nullable',
                'numeric',
            ],
            'delivery_lng' => [
                Rule::requiredIf(fn() => $request->input('order_type') === 'delivery'),
                'nullable',
                'numeric',
            ],
            'delivery_distance_km' => ['nullable', 'numeric', 'min:0'],
            'delivery_fee' => ['nullable', 'integer', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $customerDisplay = trim($data['customer_name']) . ' (Tamu)';

        $randomLetters = function (int $len): string {
            $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $out = '';
            for ($i = 0; $i < $len; $i++) {
                $out .= $alphabet[random_int(0, 25)];
            }
            return $out;
        };

        try {
            $sale = DB::transaction(function () use ($data, $customerDisplay, $randomLetters, $inventory) {
                $guest = User::create([
                    'name' => $customerDisplay,
                    'email' => 'guest_' . now()->format('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '@local.test',
                    'password' => Hash::make(bin2hex(random_bytes(16))),
                    'role' => 'guest',
                ]);

                [$products, $needs, $subtotal] = $inventory->prepareFromItems($data['items']);
                $materials = $inventory->lockAndValidateMaterials($needs);

                $tax = (int) round($subtotal * 0.11);
                $deliveryFee = $data['order_type'] === 'delivery' ? (int) ($data['delivery_fee'] ?? 0) : 0;
                $grandTotal = $subtotal + $tax + $deliveryFee;

                $sale = Sale::create([
                    'invoice_no' => 'TEMP',
                    'user_id' => $guest->id,
                    'total_amount' => $grandTotal,
                    'paid_amount' => 0,
                    'payment_method' => $data['payment_method'],
                    'payment_status' => 'pending',
                    'order_type' => $data['order_type'],
                    'dining_table_id' => $data['order_type'] === 'dine_in' ? $data['dining_table_id'] : null,
                    'delivery_phone' => $data['order_type'] === 'delivery' ? ($data['delivery_phone'] ?? null) : null,
                    'delivery_address' => $data['order_type'] === 'delivery' ? ($data['delivery_address'] ?? null) : null,
                    'delivery_lat' => $data['order_type'] === 'delivery' ? ($data['delivery_lat'] ?? null) : null,
                    'delivery_lng' => $data['order_type'] === 'delivery' ? ($data['delivery_lng'] ?? null) : null,
                    'delivery_distance_km' => $data['order_type'] === 'delivery' ? ($data['delivery_distance_km'] ?? null) : null,
                    'delivery_fee' => $deliveryFee,
                    'change_amount' => 0,
                    'status' => 'pending',
                    'kitchen_status' => 'pending',
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
                    } while (Sale::where('invoice_no', $invoice)->exists() && $attempt < $maxAttempts);

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
                    $subtotalItem = (int) $p->price * $qty;

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $p->id,
                        'qty' => $qty,
                        'price' => (int) $p->price,
                        'subtotal' => $subtotalItem,
                        'note' => isset($it['note']) && trim((string) $it['note']) !== '' ? trim((string) $it['note']) : null,
                    ]);
                }

                $inventory->reserve($needs, $materials, $sale, $guest->id);

                return $sale;
            });

            if ($sale->payment_method === 'cash') {
                return response()->json([
                    'ok' => true,
                    'invoice_no' => $sale->invoice_no,
                    'sale_id' => $sale->id,
                    'redirect_url' => route('public.order.invoice', ['invoice' => $sale->invoice_no]),
                    'payment' => [
                        'type' => 'cash',
                    ],
                ]);
            }

            try {
                $charge = $midtrans->charge($sale->fresh());
                $instruction = $midtrans->storeChargeResponse($sale->fresh(), $charge);
            } catch (\Throwable $e) {
                \Log::error('Midtrans charge failed', [
                    'invoice_no' => $sale->invoice_no,
                    'message' => $e->getMessage(),
                ]);

                throw $e;
            }

            return response()->json([
                'ok' => true,
                'invoice_no' => $sale->invoice_no,
                'sale_id' => $sale->id,
                'redirect_url' => route('public.order.invoice', ['invoice' => $sale->invoice_no]),
                'payment' => $instruction,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function invoice(string $invoice, MidtransService $midtrans)
    {
        $sale = Sale::with(['items.product', 'diningTable'])
            ->where('invoice_no', $invoice)
            ->firstOrFail();

        $payment = $sale->midtrans_response
            ? $midtrans->extractInstruction($sale->midtrans_response)
            : null;

        return view('order.invoice', compact('sale', 'payment'));
    }

    public function invoiceStatus(string $invoice, MidtransService $midtrans)
    {
        $sale = Sale::where('invoice_no', $invoice)->firstOrFail();

        $payment = $sale->midtrans_response
            ? $midtrans->extractInstruction($sale->midtrans_response)
            : null;

        if (
            $sale->payment_status === 'pending' &&
            $sale->payment_expires_at &&
            $sale->payment_expires_at->isPast()
        ) {
            app(\App\Services\SaleInventoryService::class)->release($sale, null);

            $sale->update([
                'payment_status' => 'expired',
                'status' => 'expired',
            ]);

            $sale->refresh();
        }

        return response()->json([
            'ok' => true,
            'invoice_no' => $sale->invoice_no,
            'status' => $sale->status,
            'payment_status' => $sale->payment_status,
            'kitchen_status' => $sale->kitchen_status,
            'paid_at' => optional($sale->paid_at)?->toDateTimeString(),
            'expires_at' => optional($sale->payment_expires_at)?->toDateTimeString(),
            'qr_url' => $payment['qr_url'] ?? null,
        ]);
    }
}