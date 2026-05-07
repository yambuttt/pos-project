<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Models\TokoInventoryMovement;
use App\Models\TokoProduct;
use App\Models\TokoProductVariant;
use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    public function index()
    {
        $movements = TokoInventoryMovement::with(['item', 'creator', 'reference'])->latest()->paginate(20);
        $products = TokoProduct::with('variants')->where('is_active', true)->get();
        
        return view('toko.admin.inventory.movements', compact('movements', 'products'));
    }

    public function storeAction(Request $request)
    {
        $request->validate([
            'action_type' => 'required|in:in,out,waste',
            'product_id' => 'required|integer|exists:toko_products,id',
            'variant_id' => 'nullable|integer|exists:toko_product_variants,id',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = null;
        $product = TokoProduct::findOrFail($request->product_id);
        
        if ($product->has_variants) {
            if (!$request->variant_id) {
                return back()->withErrors(['variant_id' => 'Produk ini memiliki varian. Harap pilih varian spesifik.']);
            }
            $item = TokoProductVariant::where('toko_product_id', $product->id)->findOrFail($request->variant_id);
        } else {
            $item = $product;
        }

        // Use the service
        \App\Services\TokoInventoryService::recordMovement(
            $item, 
            $request->action_type, 
            $request->qty, 
            null, 
            $request->notes
        );

        $actionNames = [
            'in' => 'Barang Masuk',
            'out' => 'Barang Keluar',
            'waste' => 'Barang Waste'
        ];

        return back()->with('success', $actionNames[$request->action_type] . ' berhasil dicatat.');
    }
}
