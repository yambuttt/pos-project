<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use App\Models\SaleItem;
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
            ->with(['items.product', 'cashier', 'diningTable'])
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
            $sale->items()->whereNull('kitchen_started_at')->update([
                'kitchen_started_at' => $sale->kitchen_started_at,
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
    public function history(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $salesDone = Sale::query()
            ->where('kitchen_status', 'done')
            ->when($from, fn($q) => $q->whereDate('kitchen_done_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('kitchen_done_at', '<=', $to));

        $totalOrdersDone = (clone $salesDone)->count();

        $byProduct = SaleItem::query()
            ->selectRaw('product_id, SUM(qty) as total_qty')
            ->whereHas('sale', function ($q) use ($from, $to) {
                $q->where('kitchen_status', 'done')
                    ->when($from, fn($qq) => $qq->whereDate('kitchen_done_at', '>=', $from))
                    ->when($to, fn($qq) => $qq->whereDate('kitchen_done_at', '<=', $to));
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->get();

        return view('dashboard.kitchen.history', compact('totalOrdersDone', 'byProduct', 'from', 'to'));
    }

    public function cookItem(SaleItem $saleItem)
    {
        $sale = $saleItem->sale;

        // kalau order belum processing, auto set processing + started_at
        if (($sale->kitchen_status ?? 'new') === 'new') {
            $sale->update([
                'kitchen_status' => 'processing',
                'kitchen_started_at' => $sale->kitchen_started_at ?? now(),
                'kitchen_user_id' => auth()->id(),
            ]);
        }

        // start timer item saat pertama kali dicoret
        // pastikan order sudah processing
        if (($sale->kitchen_status ?? 'new') === 'new') {
            $sale->update([
                'kitchen_status' => 'processing',
                'kitchen_started_at' => $sale->kitchen_started_at ?? now(),
                'kitchen_user_id' => auth()->id(),
            ]);

            // start semua item sekali
            $sale->items()->whereNull('kitchen_started_at')->update([
                'kitchen_started_at' => $sale->kitchen_started_at,
            ]);
        }

        // safety: kalau item ini belum punya started_at, pakai started_at order
        if (!$saleItem->kitchen_started_at) {
            $saleItem->kitchen_started_at = $sale->kitchen_started_at ?? now();
        }

        // increment cooked qty (maks qty)
        $saleItem->kitchen_cooked_qty = min($saleItem->qty, ($saleItem->kitchen_cooked_qty ?? 0) + 1);

        // kalau sudah matang semua qty, set done_at
        if ($saleItem->kitchen_cooked_qty >= $saleItem->qty) {
            $saleItem->kitchen_done_at = $saleItem->kitchen_done_at ?? now();
        }

        $saleItem->save();

        // kalau semua item di order sudah matang -> order done otomatis
        $this->syncSaleDoneIfAllItemsCooked($sale);

        return response()->json(['ok' => true]);
    }

    public function uncookItem(SaleItem $saleItem)
    {
        $sale = $saleItem->sale;

        $saleItem->kitchen_cooked_qty = max(0, ($saleItem->kitchen_cooked_qty ?? 0) - 1);

        // kalau turun dari qty penuh, done_at batal
        if ($saleItem->kitchen_cooked_qty < $saleItem->qty) {
            $saleItem->kitchen_done_at = null;
        }

        // kalau jadi 0 dan belum pernah mulai, boleh reset started_at (opsional)
        if ($saleItem->kitchen_cooked_qty === 0) {
            // biar lebih “buku coret” feel, biasanya timer tetap ada.
            // kalau kamu mau reset timer saat uncook sampai 0, uncomment:
            // $saleItem->kitchen_started_at = null;
        }

        $saleItem->save();

        // kalau order tadinya done tapi sekarang ada item belum matang, balik ke processing
        $this->syncSaleBackToProcessingIfNeeded($sale);

        return response()->json(['ok' => true]);
    }

    private function syncSaleDoneIfAllItemsCooked(Sale $sale): void
    {
        $sale->loadMissing('items');

        $allCooked = $sale->items->every(function ($it) {
            return ($it->kitchen_cooked_qty ?? 0) >= (int) $it->qty;
        });

        if ($allCooked) {
            $sale->update([
                'kitchen_status' => 'done',
                'kitchen_done_at' => $sale->kitchen_done_at ?? now(),
                'kitchen_user_id' => auth()->id(),
            ]);
        }
    }

    private function syncSaleBackToProcessingIfNeeded(Sale $sale): void
    {
        $sale->loadMissing('items');

        $allCooked = $sale->items->every(function ($it) {
            return ($it->kitchen_cooked_qty ?? 0) >= (int) $it->qty;
        });

        if (!$allCooked && $sale->kitchen_status === 'done') {
            $sale->update([
                'kitchen_status' => 'processing',
                'kitchen_done_at' => null,
                'kitchen_user_id' => auth()->id(),
            ]);
        }
    }
}