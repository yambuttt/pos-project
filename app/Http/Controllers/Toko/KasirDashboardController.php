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
        $activeShift = \App\Models\TokoShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            $totalTransactions = \App\Models\TokoSale::where('toko_shift_id', $activeShift->id)->count();
            $totalRevenue = \App\Models\TokoSale::where('toko_shift_id', $activeShift->id)->sum('total_amount');
            $cashInDrawer = \App\Models\TokoSale::where('toko_shift_id', $activeShift->id)
                ->where('payment_method', 'cash')
                ->sum('total_amount');
        } else {
            $totalTransactions = 0;
            $totalRevenue = 0;
            $cashInDrawer = 0;
        }

        return view('toko.kasir.dashboard', compact(
            'totalTransactions',
            'totalRevenue',
            'cashInDrawer',
            'activeShift'
        ));
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
