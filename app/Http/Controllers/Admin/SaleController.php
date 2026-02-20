<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from'); // YYYY-MM-DD
        $to = $request->query('to');   // YYYY-MM-DD
        $cashierId = $request->query('cashier_id');
        $q = $request->query('q');

        // kolom kasir yang aman (support cashier_id / user_id / created_by)
        $cashierColumn = collect(['cashier_id', 'user_id', 'created_by'])
            ->first(fn($col) => SchemaHasColumn('sales', $col))
            ?? 'user_id';

        // base query (biar konsisten dipakai untuk list/summary/chart)
        $base = Sale::query()
            ->when($from, fn($qq) => $qq->whereDate('sales.created_at', '>=', $from))
            ->when($to, fn($qq) => $qq->whereDate('sales.created_at', '<=', $to))
            ->when($cashierId, fn($qq) => $qq->where($cashierColumn, $cashierId))
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('invoice_no', 'like', "%{$q}%")
                        ->orWhere('id', $q);
                });
            });
        // ===== MODE TAMPILAN =====
// default: normal (tampilkan semua)
        $view = $request->query('view', 'all'); // all | alt

        // alt = selang-seling (tampilkan transaksi ganjil saja)
        if ($view === 'alt') {
            $base->whereRaw('MOD(sales.id, 2) = 1');
        }

        // LIST (tambah items_subtotal)
        $salesQuery = (clone $base)
            ->with(['cashier'])
            ->withSum('items as items_subtotal', 'subtotal')
            ->orderByDesc('created_at');

        $sales = $salesQuery->paginate(10)->withQueryString();

        // CHART (ikut filter juga biar konsisten)
        $chartFrom = $from ?: now()->subDays(13)->toDateString();
        $chartTo = $to ?: now()->toDateString();

        $daily = (clone $base)
            ->selectRaw('DATE(sales.created_at) as d, COUNT(*) as trx_count, SUM(total_amount) as total_sum')
            ->when(!$from, fn($qq) => $qq->whereDate('sales.created_at', '>=', $chartFrom))
            ->when(!$to, fn($qq) => $qq->whereDate('sales.created_at', '<=', $chartTo))
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $labels = [];
        $totals = [];
        $counts = [];

        $cursor = \Carbon\Carbon::parse($chartFrom);
        $end = \Carbon\Carbon::parse($chartTo);

        $map = $daily->keyBy('d');
        while ($cursor->lte($end)) {
            $d = $cursor->toDateString();
            $labels[] = $cursor->format('d M');
            $totals[] = (float) ($map[$d]->total_sum ?? 0);
            $counts[] = (int) ($map[$d]->trx_count ?? 0);
            $cursor->addDay();
        }

        $cashiers = User::query()
            ->where('role', 'kasir')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // SUMMARY
        $sumTotal = (float) (clone $base)->sum('total_amount');

        // total subtotal dari sale_items (mengikuti filter sales)
        $sumSubtotal = (float) (clone $base)
            ->join('sale_items', 'sale_items.sale_id', '=', 'sales.id')
            ->sum('sale_items.subtotal');

        $sumTax = max(0, $sumTotal - $sumSubtotal);

        $summary = [
            'total_trx' => (clone $base)->count(),
            'sum_total' => $sumTotal,
            'sum_subtotal' => $sumSubtotal, // DPP
            'sum_tax' => $sumTax,           // Pajak terkumpul
        ];

        return view('dashboard.admin.sales.index', [
            'sales' => $sales,
            'cashiers' => $cashiers,
            'labels' => $labels,
            'totals' => $totals,
            'counts' => $counts,
            'summary' => $summary,
        ]);
    }

    public function show(Sale $sale)
    {
        $sale->load([
            'cashier',
            'items.product',
        ]);

        return view('dashboard.admin.sales.show', compact('sale'));
    }
}

/**
 * Helper kecil: cek kolom ada di table.
 * Biar controller tetap jalan walau kolom kasir beda.
 */
function SchemaHasColumn(string $table, string $column): bool
{
    try {
        return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    } catch (\Throwable $e) {
        return false;
    }
}
