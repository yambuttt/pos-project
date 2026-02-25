<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Base: transaksi completed
        $baseToday = Sale::query()
            ->where('status', 'completed')
            ->whereDate('created_at', $today);

        $baseYesterday = Sale::query()
            ->where('status', 'completed')
            ->whereDate('created_at', $yesterday);

        // NOTE:
        // Ada flow "tamu/guest" yang paid_amount=0 (belum bayar di sistem) -> jangan dihitung cash/omzet masuk.
        // (lihat pola paid=0 pada flow tamu) :contentReference[oaicite:7]{index=7}
        $paidToday = (clone $baseToday)->where('paid_amount', '>', 0);
        $paidYesterday = (clone $baseYesterday)->where('paid_amount', '>', 0);

        // Summary utama
        $trxToday = (clone $baseToday)->count();
        $trxYesterday = (clone $baseYesterday)->count();

        $omzetToday = (int) (clone $paidToday)->sum('total_amount');
        $omzetYesterday = (int) (clone $paidYesterday)->sum('total_amount');

        // Cash / QRIS hari ini
        $omzetCash = (int) (clone $paidToday)->where('payment_method', 'cash')->sum('total_amount');
        $omzetQris = (int) (clone $paidToday)->where('payment_method', 'qris')->sum('total_amount');

        // Cash net = cash masuk - kembalian (mengikuti rumus kasir) :contentReference[oaicite:8]{index=8}
        $cashIn = (int) (clone $paidToday)->where('payment_method', 'cash')->sum('paid_amount');
        $cashChange = (int) (clone $paidToday)->where('payment_method', 'cash')->sum('change_amount');
        $cashNet = max(0, $cashIn - $cashChange);

        // % perubahan omzet vs kemarin
        $pctSales = 0;
        if ($omzetYesterday > 0) {
            $pctSales = (($omzetToday - $omzetYesterday) / $omzetYesterday) * 100;
        } elseif ($omzetToday > 0) {
            $pctSales = 100;
        }

        // Total produk
        $totalProducts = Product::query()->count();

        // Pajak (pajak = total_amount - sum(subtotal sale_items))
        $subtotalToday = (float) Sale::query()
            ->where('status', 'completed')
            ->where('paid_amount', '>', 0)
            ->whereDate('sales.created_at', $today)
            ->join('sale_items', 'sale_items.sale_id', '=', 'sales.id')
            ->sum('sale_items.subtotal');

        $taxToday = max(0, (float) $omzetToday - (float) $subtotalToday);

        // “Belum dibayar di sistem” (guest/order online) -> paid_amount=0
        $unpaidCount = (clone $baseToday)->where('paid_amount', 0)->count();
        $unpaidTotal = (int) (clone $baseToday)->where('paid_amount', 0)->sum('total_amount');

        // Latest sales
        $latestSales = Sale::query()
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->with(['cashier'])
            ->limit(8)
            ->get();

        // Top products hari ini (rank)
        $topProductsToday = SaleItem::query()
            ->selectRaw('sale_items.product_id, SUM(sale_items.qty) as total_qty, SUM(sale_items.subtotal) as total_subtotal')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.status', 'completed')
            ->where('sales.paid_amount', '>', 0)
            ->whereDate('sales.created_at', $today)
            ->groupBy('sale_items.product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->limit(10)
            ->get();

        $popularProduct = $topProductsToday->first();

        // ===== TOTAL KESELURUHAN (ALL TIME) - paid only =====
        $paidAll = Sale::query()
            ->where('status', 'completed')
            ->where('paid_amount', '>', 0);

        $totalCashAll = (int) (clone $paidAll)->where('payment_method', 'cash')->sum('total_amount');
        $totalQrisAll = (int) (clone $paidAll)->where('payment_method', 'qris')->sum('total_amount');

        // Cash net all time (uang fisik yang benar-benar pernah masuk) (opsional tapi recommended)
        $cashInAll = (int) (clone $paidAll)->where('payment_method', 'cash')->sum('paid_amount');
        $cashChangeAll = (int) (clone $paidAll)->where('payment_method', 'cash')->sum('change_amount');
        $cashNetAll = max(0, $cashInAll - $cashChangeAll);

        // Chart omzet 14 hari terakhir (paid only)
        $start = now()->subDays(13)->toDateString();

        $chartRows = Sale::query()
            ->where('status', 'completed')
            ->where('paid_amount', '>', 0)
            ->whereDate('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as trx_count, SUM(total_amount) as total_sum')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $labels = [];
        $totals = [];
        $counts = [];

        $cursor = now()->subDays(13)->startOfDay();
        $end = now()->startOfDay();

        while ($cursor->lte($end)) {
            $d = $cursor->toDateString();
            $labels[] = $cursor->format('d M');
            $totals[] = (int) (($chartRows[$d]->total_sum ?? 0));
            $counts[] = (int) (($chartRows[$d]->trx_count ?? 0));
            $cursor->addDay();
        }

        return view('dashboard.admin.index', compact(
            'trxToday',
            'trxYesterday',
            'omzetToday',
            'omzetYesterday',
            'pctSales',
            'omzetCash',
            'totalCashAll',
            'totalQrisAll',
            'cashNetAll',
            'omzetQris',
            'cashNet',
            'totalProducts',
            'taxToday',
            'unpaidCount',
            'unpaidTotal',
            'latestSales',
            'topProductsToday',
            'popularProduct',
            'labels',
            'totals',
            'counts',
        ));
    }

    public function kasir()
    {
        return view('dashboard.kasir.index');
    }
}