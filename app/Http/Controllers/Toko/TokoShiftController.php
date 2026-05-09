<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use App\Models\TokoShift;
use App\Models\TokoSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokoShiftController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        // Cek jika sudah ada shift yang buka
        $activeShift = TokoShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            return back()->with('error', 'Anda masih memiliki shift yang aktif.');
        }

        TokoShift::create([
            'user_id' => auth()->id(),
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);

        return back()->with('success', 'Shift berhasil dimulai.');
    }

    public function end(Request $request)
    {
        $activeShift = TokoShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->firstOrFail();

        $sales = TokoSale::where('toko_shift_id', $activeShift->id)->get();
        
        $totalCash = $sales->where('payment_method', 'cash')->sum('total_amount');
        $totalNonCash = $sales->where('payment_method', '!=', 'cash')->sum('total_amount');
        
        $activeShift->update([
            'end_time' => now(),
            'ending_cash' => $activeShift->starting_cash + $totalCash, // Uang yang seharusnya ada di laci
            'total_sales_cash' => $totalCash,
            'total_sales_non_cash' => $totalNonCash,
            'total_transactions' => $sales->count(),
            'status' => 'closed',
        ]);

        return back()->with('success', 'Shift berhasil ditutup.');
    }

    public function history()
    {
        $shifts = TokoShift::with('user')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('toko.kasir.shift_history', compact('shifts'));
    }

    public function show($id)
    {
        $shift = TokoShift::with(['user', 'sales.items.product'])->findOrFail($id);
        
        // Ambil rincian item yang terjual
        $itemsSold = DB::table('toko_sale_items')
            ->join('toko_sales', 'toko_sale_items.toko_sale_id', '=', 'toko_sales.id')
            ->where('toko_sales.toko_shift_id', $shift->id)
            ->select(
                'toko_sale_items.product_name', 
                'toko_sale_items.variant_name', 
                DB::raw('SUM(toko_sale_items.qty) as total_qty'), 
                DB::raw('SUM(toko_sale_items.subtotal) as total_amount')
            )
            ->groupBy('toko_sale_items.product_name', 'toko_sale_items.variant_name')
            ->get();

        return response()->json([
            'shift' => $shift,
            'items_sold' => $itemsSold
        ]);
    }
}
