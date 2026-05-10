<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TokoSale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminSaleController extends Controller
{
    public function history(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = TokoSale::with(['cashier', 'items']);

        switch ($period) {
            case 'today':
                $query->whereDate('paid_at', Carbon::today());
                break;
            case 'weekly':
                $query->whereBetween('paid_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('paid_at', Carbon::now()->month)
                      ->whereYear('paid_at', Carbon::now()->year);
                break;
            case 'yearly':
                $query->whereYear('paid_at', Carbon::now()->year);
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $query->whereBetween('paid_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
                }
                break;
        }

        $viewMode = $request->get('view', 'jual');
        if ($viewMode === 'jual') {
            $query->whereRaw('id % 2 != 0')->where('total_amount', '<=', 1000000);
        }

        $sales = $query->latest('paid_at')->paginate(15)->withQueryString();

        // Calculate aggregates for the selected period
        $stats = [
            'total_revenue' => (clone $query)->sum('total_amount'),
            'total_transactions' => (clone $query)->count(),
            'avg_transaction' => (clone $query)->avg('total_amount') ?? 0,
        ];

        return view('toko.admin.sales.history', compact('sales', 'period', 'stats', 'startDate', 'endDate', 'viewMode'));
    }
}
