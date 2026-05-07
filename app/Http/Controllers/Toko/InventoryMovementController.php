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
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:toko_products,id',
            'items.*.variant_id' => 'nullable|integer|exists:toko_product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        foreach ($request->items as $index => $itemData) {
            $product = TokoProduct::findOrFail($itemData['product_id']);
            $itemModel = null;
            
            if ($product->has_variants) {
                if (empty($itemData['variant_id'])) {
                    return back()->withErrors(['items.'.$index.'.variant_id' => 'Produk "'.$product->name.'" memiliki varian. Harap pilih varian spesifik.']);
                }
                $itemModel = TokoProductVariant::where('toko_product_id', $product->id)->findOrFail($itemData['variant_id']);
            } else {
                $itemModel = $product;
            }

            // Use the service
            \App\Services\TokoInventoryService::recordMovement(
                $itemModel, 
                $request->action_type, 
                $itemData['qty'], 
                null, 
                $request->notes
            );
        }

        $actionNames = [
            'in' => 'Barang Masuk',
            'out' => 'Barang Keluar',
            'waste' => 'Barang Waste'
        ];

        return back()->with('success', $actionNames[$request->action_type] . ' (' . count($request->items) . ' item) berhasil dicatat.');
    }
}
