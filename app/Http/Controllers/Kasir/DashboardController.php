<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
  use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Summary hari ini untuk kasir yang login
        $base = Sale::query()
            ->where('user_id', Auth::id())
            ->whereDate('created_at', $today);

        $summary = [
            'trx_count' => (clone $base)->count(),
            'omzet_total' => (int) (clone $base)->sum('total_amount'),

            // Total berdasarkan metode bayar
            'omzet_cash' => (int) (clone $base)->where('payment_method', 'cash')->sum('total_amount'),
            'omzet_qris' => (int) (clone $base)->where('payment_method', 'qris')->sum('total_amount'),

            // Cash yang benar-benar diterima (uang fisik masuk)
            // Untuk cash: paid_amount masuk, change_amount keluar.
            'cash_in' => (int) (clone $base)->where('payment_method', 'cash')->sum('paid_amount'),
            'cash_out_change' => (int) (clone $base)->where('payment_method', 'cash')->sum('change_amount'),
        ];
        $summary['cash_net'] = max(0, $summary['cash_in'] - $summary['cash_out_change']);

        // Grafik per jam (00-23)
        $rows = (clone $base)
            ->selectRaw('HOUR(created_at) as h, COUNT(*) as trx_count, SUM(total_amount) as total_sum')
            ->groupBy('h')
            ->orderBy('h')
            ->get()
            ->keyBy('h');

        $labels = [];
        $totals = [];
        $counts = [];

        for ($h = 0; $h <= 23; $h++) {
            $labels[] = str_pad((string) $h, 2, '0', STR_PAD_LEFT) . ':00';
            $totals[] = (int) (($rows[$h]->total_sum ?? 0));
            $counts[] = (int) (($rows[$h]->trx_count ?? 0));
        }

        return view('dashboard.kasir.dashboard', compact('summary', 'labels', 'totals', 'counts'));
    }

  

public function readyIndex()
{
    return view('dashboard.kasir.ready');
}

public function readyOrders(Request $request)
{
    // default: hari ini saja (biar ringan)
    $today = now()->toDateString();

    $sales = Sale::query()
        ->whereDate('created_at', $today)
        ->where('status', 'completed')
        ->whereIn('kitchen_status', ['done', 'delivered'])
        ->with(['items.product', 'diningTable'])
        ->orderByDesc('kitchen_done_at')
        ->get();

    // kirim juga "readyOnly" supaya kasir bisa fokus yang belum delivered
    $readyOnly = $sales->where('kitchen_status', 'done')->values();

    return response()->json([
        'now' => now()->format('Y-m-d H:i:s'),
        'ready_count' => $readyOnly->count(),
        'ready_sales' => $readyOnly,
        'all_sales' => $sales->values(),
    ]);
}

public function deliver(Sale $sale)
{
    // hanya boleh deliver kalau statusnya done
    if ($sale->kitchen_status !== 'done') {
        return back()->withErrors(['sale' => 'Pesanan ini tidak dalam status READY.']);
    }

    $sale->update([
        'kitchen_status' => 'delivered',
        'delivered_at' => now(),
        'delivered_user_id' => Auth::id(),
    ]);

    return back()->with('success', 'Pesanan ditandai sudah diberikan ke customer.');
}
}