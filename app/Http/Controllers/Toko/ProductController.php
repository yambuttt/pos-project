<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Models\TokoProduct;
use App\Models\TokoCategory;
use App\Models\TokoProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = TokoProduct::with('category')->latest()->paginate(10);
        return view('toko.admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = TokoCategory::all();
        return view('toko.admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:toko_products,sku',
            'toko_category_id' => 'nullable|exists:toko_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'has_variants' => 'boolean',
            'variants' => 'array',
        ]);

        $validated['has_variants'] = $request->has('has_variants');

        $product = TokoProduct::create($validated);

        if ($validated['has_variants'] && !empty($validated['variants'])) {
            foreach ($validated['variants'] as $variantData) {
                if (!empty($variantData['name'])) {
                    $product->variants()->create([
                        'name' => $variantData['name'],
                        'sku' => $variantData['sku'] ?? null,
                        'price' => $variantData['price'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('toko.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(TokoProduct $product)
    {
        $product->load('variants');
        $categories = TokoCategory::all();
        return view('toko.admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, TokoProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:toko_products,sku,'.$product->id,
            'toko_category_id' => 'nullable|exists:toko_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'has_variants' => 'boolean',
            'variants' => 'array',
        ]);

        $validated['has_variants'] = $request->has('has_variants');

        $product->update($validated);

        if ($validated['has_variants']) {
            // Very simple variant sync
            $existingVariantIds = [];
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $variantData) {
                    if (!empty($variantData['name'])) {
                        if (isset($variantData['id'])) {
                            $variant = TokoProductVariant::find($variantData['id']);
                            if ($variant) {
                                $variant->update($variantData);
                                $existingVariantIds[] = $variant->id;
                            }
                        } else {
                            $newVariant = $product->variants()->create($variantData);
                            $existingVariantIds[] = $newVariant->id;
                        }
                    }
                }
            }
            $product->variants()->whereNotIn('id', $existingVariantIds)->delete();
        } else {
            $product->variants()->delete();
        }

        return redirect()->route('toko.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(TokoProduct $product)
    {
        $product->delete();
        return redirect()->route('toko.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
