<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        // halaman view, data diambil via polling supaya realtime
        return view('dashboard.kitchen.index');
    }

    public function orders(Request $request)
    {
        // default: hari ini
        $today = now()->toDateString();

        $sales = Sale::query()
            ->with(['items.product', 'cashier'])
            ->whereDate('created_at', $today)
            ->where('status', 'completed')          // transaksi sudah dibayar/selesai di kasir
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'now' => now()->toDateTimeString(),
            'sales' => $sales,
        ]);
    }

    public function process(Sale $sale)
    {
        if ($sale->kitchen_status !== 'done') {
            $sale->update([
                'kitchen_status' => 'processing',
                'kitchen_started_at' => $sale->kitchen_started_at ?? now(),
                'kitchen_user_id' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Pesanan masuk PROSES.');
    }

    public function done(Sale $sale)
    {
        $sale->update([
            'kitchen_status' => 'done',
            'kitchen_done_at' => now(),
            'kitchen_user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Pesanan SELESAI.');
    }
}