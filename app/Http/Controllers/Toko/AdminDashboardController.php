<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TokoSale;
use App\Models\TokoSaleItem;
use App\Models\TokoProduct;
use App\Models\TokoProductVariant;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // --- TODAY METRICS ---
        $todayRevenue = TokoSale::whereDate('paid_at', $today)->sum('total_amount');
        $todayTransactions = TokoSale::whereDate('paid_at', $today)->count();
        $todayItemsSold = TokoSaleItem::whereHas('sale', function($q) use ($today) {
            $q->whereDate('paid_at', $today);
        })->sum('qty');

        // --- YESTERDAY METRICS (For Comparison) ---
        $yesterdayRevenue = TokoSale::whereDate('paid_at', $yesterday)->sum('total_amount');
        $yesterdayTransactions = TokoSale::whereDate('paid_at', $yesterday)->count();
        $yesterdayItemsSold = TokoSaleItem::whereHas('sale', function($q) use ($yesterday) {
            $q->whereDate('paid_at', $yesterday);
        })->sum('qty');

        // --- CALCULATE PERCENTAGES ---
        $revenueChange = $this->calculatePercentageChange($todayRevenue, $yesterdayRevenue);
        $transactionsChange = $this->calculatePercentageChange($todayTransactions, $yesterdayTransactions);
        $itemsSoldChange = $this->calculatePercentageChange($todayItemsSold, $yesterdayItemsSold);

        // --- LOW STOCK ---
        $lowStockThreshold = 10;
        $lowStockProducts = TokoProduct::where('has_variants', false)
            ->where('stock', '<=', $lowStockThreshold)
            ->count();
        $lowStockVariants = TokoProductVariant::where('stock', '<=', $lowStockThreshold)
            ->count();
        $totalLowStock = $lowStockProducts + $lowStockVariants;

        // --- RECENT TRANSACTIONS ---
        $recentTransactions = TokoSale::with('cashier')
            ->latest('paid_at')
            ->take(5)
            ->get();

        return view('toko.admin.dashboard', compact(
            'todayRevenue', 'revenueChange',
            'todayTransactions', 'transactionsChange',
            'todayItemsSold', 'itemsSoldChange',
            'totalLowStock',
            'recentTransactions'
        ));
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }
}
