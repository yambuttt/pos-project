<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
        // Halaman ini render view saja. Data cart ambil dari localStorage di browser.
        return view('order.overview');
    }
}