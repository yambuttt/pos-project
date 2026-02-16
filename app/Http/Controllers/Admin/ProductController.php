<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('dashboard.admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('dashboard.admin.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'sku' => ['nullable','string','max:100','unique:products,sku'],
            'category' => ['nullable','string','max:100'],
            'price' => ['required','integer','min:0'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = auth()->id();

        $p = Product::create($data);

        return redirect()->route('admin.products.recipes', $p->id)
            ->with('success', 'Produk dibuat. Sekarang isi resepnya ya.');
    }

    public function edit(Product $product)
    {
        return view('dashboard.admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'sku' => ['nullable','string','max:100','unique:products,sku,'.$product->id],
            'category' => ['nullable','string','max:100'],
            'price' => ['required','integer','min:0'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['updated_by'] = auth()->id();

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success','Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success','Produk dihapus.');
    }

    // Halaman resep
    public function recipes(Product $product)
    {
        $product->load('recipes.rawMaterial');
        $materials = RawMaterial::orderBy('name')->get();

        return view('dashboard.admin.products.recipes', compact('product','materials'));
    }

    public function recipesStore(Request $request, Product $product)
    {
        $data = $request->validate([
            'raw_material_id' => ['required','exists:raw_materials,id'],
            'qty' => ['required','numeric','min:0.001'],
            'note' => ['nullable','string','max:150'],
        ]);

        // unique per product
        $product->recipes()->updateOrCreate(
            ['raw_material_id' => $data['raw_material_id']],
            ['qty' => $data['qty'], 'note' => $data['note'] ?? null]
        );

        return back()->with('success','Resep berhasil ditambahkan/diupdate.');
    }

    public function recipesDestroy(Product $product, $recipeId)
    {
        $product->recipes()->where('id', $recipeId)->delete();
        return back()->with('success','Item resep dihapus.');
    }
}
