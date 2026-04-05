<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $status = (string) $request->get('status', '');

        $products = Product::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%' . $q . '%')
                        ->orWhere('sku', 'like', '%' . $q . '%')
                        ->orWhere('category', 'like', '%' . $q . '%');
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $inactiveProducts = Product::where('is_active', false)->count();
        $avgPrice = (int) Product::avg('price');

        return view('dashboard.admin.products.index', compact(
            'products',
            'q',
            'status',
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'avgPrice'
        ));
    }

    public function create()
    {
        return view('dashboard.admin.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            // simpan ke storage/app/public/products/...
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

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
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            // hapus lama kalau ada
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        // Kalau produk sudah pernah dipakai di transaksi, jangan delete (biar histori aman).
        $hasSales = \App\Models\SaleItem::where('product_id', $product->id)->exists();

        if ($hasSales) {
            // cukup arsipkan / nonaktifkan
            $product->update([
                'is_active' => false,
                'updated_by' => auth()->id(),
            ]);

            return back()->with('success', 'Produk tidak bisa dihapus karena sudah pernah terjual. Produk di-nonaktifkan (archived).');
        }

        // Kalau belum pernah dipakai transaksi, baru boleh delete beneran.
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return back()->with('success', 'Produk dihapus.');
    }

    // Halaman resep
    public function recipes(Product $product)
    {
        $product->load('recipes.rawMaterial');
        $materials = RawMaterial::orderBy('name')->get();

        return view('dashboard.admin.products.recipes', compact('product', 'materials'));
    }

    public function recipesStore(Request $request, Product $product)
    {
        $data = $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'qty' => ['required', 'numeric', 'min:0.001'],
            'note' => ['nullable', 'string', 'max:150'],
        ]);

        $product->recipes()->updateOrCreate(
            ['raw_material_id' => $data['raw_material_id']],
            ['qty' => $data['qty'], 'note' => $data['note'] ?? null]
        );

        return back()->with('success', 'Resep berhasil ditambahkan/diupdate.');
    }

    public function recipesDestroy(Product $product, $recipeId)
    {
        $product->recipes()->where('id', $recipeId)->delete();
        return back()->with('success', 'Item resep dihapus.');
    }
}