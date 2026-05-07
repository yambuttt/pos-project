<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Models\TokoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = TokoCategory::withCount('products')->latest()->get();
        return view('toko.admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        TokoCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);
        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, TokoCategory $category)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);
        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(TokoCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }
}
