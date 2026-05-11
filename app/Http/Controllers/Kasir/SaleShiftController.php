<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\SaleShift;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleShiftController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        $activeShift = SaleShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            return back()->with('error', 'Anda masih memiliki shift yang aktif.');
        }

        SaleShift::create([
            'user_id' => auth()->id(),
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);

        return back()->with('success', 'Shift berhasil dimulai.');
    }

    public function end(Request $request)
    {
        $activeShift = SaleShift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->firstOrFail();

        $sales = Sale::where('sale_shift_id', $activeShift->id)
            ->where('payment_status', 'paid')
            ->get();
        
        $totalCash = $sales->where('payment_method', 'cash')->sum('total_amount');
        $totalNonCash = $sales->where('payment_method', '!=', 'cash')->sum('total_amount');
        
        $activeShift->update([
            'end_time' => now(),
            'ending_cash' => $activeShift->starting_cash + $totalCash,
            'total_sales_cash' => $totalCash,
            'total_sales_non_cash' => $totalNonCash,
            'total_transactions' => $sales->count(),
            'status' => 'closed',
        ]);

        return back()->with('success', 'Shift berhasil ditutup.');
    }

    public function history()
    {
        $shifts = SaleShift::with('user')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('dashboard.kasir.shifts.history', compact('shifts'));
    }

    public function show($id)
    {
        $shift = SaleShift::with(['user', 'sales.items.product'])->findOrFail($id);
        
        // Sum items sold during this shift
        $itemsSold = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.sale_shift_id', $shift->id)
            ->where('sales.payment_status', 'paid')
            ->select(
                'products.name as product_name', 
                DB::raw('SUM(sale_items.qty) as total_qty'), 
                DB::raw('SUM(sale_items.subtotal) as total_amount')
            )
            ->groupBy('products.name')
            ->get();

        return response()->json([
            'shift' => $shift,
            'items_sold' => $itemsSold
        ]);
    }
}
