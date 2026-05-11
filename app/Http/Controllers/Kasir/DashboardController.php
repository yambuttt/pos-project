<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleShift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $userId = Auth::id();

        // Cek shift aktif
        $activeShift = SaleShift::where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        // Summary (jika ada shift, ambil data shift, jika tidak ambil data hari ini sebagai preview)
        $base = Sale::query()
            ->where('user_id', $userId);

        if ($activeShift) {
            $base->where('sale_shift_id', $activeShift->id);
        } else {
            $base->whereDate('created_at', $today);
        }

        $summary = [
            'trx_count' => (clone $base)->count(),
            'omzet_total' => (int) (clone $base)->sum('total_amount'),

            // Total berdasarkan metode bayar
            'omzet_cash' => (int) (clone $base)->where('payment_method', 'cash')->sum('total_amount'),
            'omzet_qris' => (int) (clone $base)->where('payment_method', 'qris')->sum('total_amount'),

            // Cash yang benar-benar diterima (uang fisik masuk)
            'cash_in' => (int) (clone $base)->where('payment_method', 'cash')->sum('paid_amount'),
            'cash_out_change' => (int) (clone $base)->where('payment_method', 'cash')->sum('change_amount'),
        ];
        $summary['cash_net'] = max(0, $summary['cash_in'] - $summary['cash_out_change']);

        // Grafik per jam (00-23) - tetap hari ini agar grafik terlihat penuh
        $rows = Sale::query()
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
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

        return view('dashboard.kasir.dashboard', compact('summary', 'labels', 'totals', 'counts', 'activeShift'));
    }

    public function readyIndex()
    {
        $activeShift = SaleShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return redirect()->route('kasir.dashboard')->with('error', 'Anda harus memulai shift terlebih dahulu.');
        }

        return view('dashboard.kasir.ready');
    }

    public function readyOrders(Request $request)
    {
        $today = now()->toDateString();

        $sales = Sale::query()
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->whereIn('kitchen_status', ['done', 'delivered'])
            ->with(['items.product', 'diningTable', 'user'])
            ->orderByDesc('kitchen_done_at')
            ->get();

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