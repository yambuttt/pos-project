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
        // filter
        $from = $request->query('from'); // YYYY-MM-DD
        $to   = $request->query('to');   // YYYY-MM-DD
        $cashierId = $request->query('cashier_id');
        $q = $request->query('q');

        // NOTE PENTING:
        // Kolom kasir di sale kamu mungkin: created_by / cashier_id
        // Pakai salah satu yang sesuai.
        $cashierColumn = SchemaHasColumn('sales', 'cashier_id') ? 'cashier_id' : 'created_by';

        $salesQuery = Sale::query()
            ->with(['cashier']) // relasi kita buat di model
            ->when($from, fn($qq) => $qq->whereDate('created_at', '>=', $from))
            ->when($to, fn($qq) => $qq->whereDate('created_at', '<=', $to))
            ->when($cashierId, fn($qq) => $qq->where($cashierColumn, $cashierId))
            ->when($q, function ($qq) use ($q) {
                // invoice_no mungkin beda nama (misal invoice). Sesuaikan.
                $qq->where(function($w) use ($q) {
                    $w->where('invoice_no', 'like', "%{$q}%")
                      ->orWhere('id', $q);
                });
            })
            ->orderByDesc('created_at');

        $sales = $salesQuery->paginate(10)->withQueryString();

        // --- Grafik: total sales per hari (14 hari terakhir) ---
        $chartFrom = $from ?: now()->subDays(13)->toDateString();
        $chartTo   = $to   ?: now()->toDateString();

        // Total per hari
        $daily = Sale::query()
            ->selectRaw('DATE(created_at) as d, COUNT(*) as trx_count, SUM(total_amount) as total_sum')
            ->when($from, fn($qq) => $qq->whereDate('created_at', '>=', $chartFrom))
            ->when($to, fn($qq) => $qq->whereDate('created_at', '<=', $chartTo))
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        // Buat labels lengkap biar chart gak bolong
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

        // dropdown kasir
        $cashiers = User::query()
            ->where('role', 'kasir')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Ringkas angka
        $summary = [
            'total_trx' => (clone $salesQuery)->count(),
            'sum_total' => (float) (clone $salesQuery)->sum('total_amount'),
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
