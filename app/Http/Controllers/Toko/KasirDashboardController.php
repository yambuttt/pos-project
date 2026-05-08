<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TokoProduct;
use App\Models\TokoCategory;

class KasirDashboardController extends Controller
{
    public function index()
    {
        return view('toko.kasir.dashboard');
    }
    // app/Http/Controllers/Toko/KasirDashboardController.php
public function pos()
    {
        // Mengambil produk beserta kategori dan variannya agar sinkron dengan modelmu
        $products = TokoProduct::with(['category', 'variants'])->get();
        $categories = TokoCategory::all();

        return view('toko.kasir.pos', compact('products', 'categories'));
    }
}
