<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TokoSale;
use App\Models\TokoSaleItem;
use App\Models\TokoProduct;
use App\Models\TokoProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TokoSaleController extends Controller
{
    /**
     * Simpan transaksi kasir toko baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'nullable|string|max:255',
            'paid_amount'    => 'required|numeric|min:0',
            'items'          => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:toko_products,id',
            'items.*.variant_id'         => 'nullable|exists:toko_product_variants,id',
            'items.*.qty'                => 'required|integer|min:1',
            'items.*.price'              => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Cek shift aktif
            $activeShift = \App\Models\TokoShift::where('user_id', Auth::id())
                ->where('status', 'open')
                ->first();

            if (!$activeShift) {
                throw new \Exception("Anda harus memulai shift terlebih dahulu sebelum melakukan transaksi.");
            }

            $taxRate = 11; // 11%
            $subtotal = 0;

            // Hitung subtotal
            foreach ($request->items as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }

            $taxAmount    = round($subtotal * $taxRate / 100, 2);
            $totalAmount  = $subtotal + $taxAmount;
            $paidAmount   = $request->paid_amount;
            $changeAmount = max(0, $paidAmount - $totalAmount);

            // Buat invoice number: TKO-YYYYMMDD-XXXX
            $invoiceNo = 'TKO-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $sale = TokoSale::create([
                'toko_shift_id'  => $activeShift->id,
                'invoice_no'     => $invoiceNo,
                'user_id'        => Auth::id(),
                'customer_name'  => $request->customer_name,
                'subtotal'       => $subtotal,
                'tax_rate'       => $taxRate,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $totalAmount,
                'payment_method' => 'cash',
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'payment_status' => 'paid',
                'paid_at'        => now(),
            ]);

            // Simpan items & kurangi stok
            foreach ($request->items as $item) {
                $product = TokoProduct::findOrFail($item['product_id']);
                $variantId = $item['variant_id'] ?? null;
                $variantName = null;

                if ($variantId) {
                    $variant = TokoProductVariant::findOrFail($variantId);
                    // Kurangi stok varian & catat movement
                    if ($variant->stock < $item['qty']) {
                        throw new \Exception("Stok varian '{$variant->name}' tidak mencukupi.");
                    }
                    
                    \App\Services\TokoInventoryService::recordMovement(
                        $variant,
                        'sale',
                        $item['qty'],
                        $sale,
                        "Penjualan Kasir (POS): " . $sale->invoice_no
                    );
                    
                    $variantName = $variant->name;
                } else {
                    // Kurangi stok produk & catat movement
                    if ($product->stock < $item['qty']) {
                        throw new \Exception("Stok produk '{$product->name}' tidak mencukupi.");
                    }

                    \App\Services\TokoInventoryService::recordMovement(
                        $product,
                        'sale',
                        $item['qty'],
                        $sale,
                        "Penjualan Kasir (POS): " . $sale->invoice_no
                    );
                }

                TokoSaleItem::create([
                    'toko_sale_id'             => $sale->id,
                    'toko_product_id'          => $item['product_id'],
                    'toko_product_variant_id'  => $variantId,
                    'product_name'             => $product->name,
                    'variant_name'             => $variantName,
                    'qty'                      => $item['qty'],
                    'price'                    => $item['price'],
                    'subtotal'                 => $item['price'] * $item['qty'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale->load('items', 'cashier'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
    public function history(Request $request)
    {
        $days = $request->get('days', 7);
        $query = TokoSale::with(['items', 'cashier'])
            ->where('created_at', '>=', now()->subDays($days));

        // Hitung stats dari seluruh query sebelum dipaginasi
        $totalRevenue = $query->sum('total_amount');
        $avgTransaction = $query->avg('total_amount') ?? 0;
        $totalTransactions = $query->count();

        $sales = $query->latest()->paginate(15)->withQueryString();

        return view('toko.kasir.history', compact('sales', 'days', 'totalRevenue', 'avgTransaction', 'totalTransactions'));
    }
}
