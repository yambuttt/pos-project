<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use App\Models\SaleItem;
class KitchenController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $period = $request->get('period', 'today'); // today, week, month

        $query = Sale::query()
            ->where('kitchen_user_id', $userId)
            ->whereIn('kitchen_status', ['done', 'delivered'])
            ->whereNotNull('kitchen_started_at')
            ->whereNotNull('kitchen_done_at');

        if ($period === 'week') {
            $query->where('kitchen_done_at', '>=', now()->startOfWeek());
        } elseif ($period === 'month') {
            $query->where('kitchen_done_at', '>=', now()->startOfMonth());
        } else {
            $query->whereDate('kitchen_done_at', now()->toDateString());
        }

        $stats = $query->selectRaw('
                COUNT(*) as total_done,
                AVG(TIMESTAMPDIFF(SECOND, kitchen_started_at, kitchen_done_at)) as avg_seconds,
                SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, kitchen_started_at, kitchen_done_at) <= 15 THEN 1 ELSE 0 END) as on_time_count
            ')
            ->first();

        return view('dashboard.kitchen.index', compact('stats', 'period'));
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
            ->whereIn('kitchen_status', ['done', 'delivered'])
            ->when($from, fn($q) => $q->whereDate('kitchen_done_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('kitchen_done_at', '<=', $to));

        $totalOrdersDone = (clone $salesDone)->count();

        $byProduct = SaleItem::query()
            ->selectRaw('product_id, SUM(qty) as total_qty')
            ->whereHas('sale', function ($q) use ($from, $to) {
                $q->whereIn('kitchen_status', ['done', 'delivered'])
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

    public function performance(Request $request)
    {
        $userId = auth()->id();
        $period = $request->get('period', 'month'); // default month for detail page

        $query = Sale::query()
            ->where('kitchen_user_id', $userId)
            ->whereIn('kitchen_status', ['done', 'delivered'])
            ->whereNotNull('kitchen_started_at')
            ->whereNotNull('kitchen_done_at');

        if ($period === 'week') {
            $query->where('kitchen_done_at', '>=', now()->startOfWeek());
        } elseif ($period === 'month') {
            $query->where('kitchen_done_at', '>=', now()->startOfMonth());
        } elseif ($period === 'today') {
            $query->whereDate('kitchen_done_at', now()->toDateString());
        }

        // 1. General Stats
        $stats = (clone $query)->selectRaw('
            COUNT(*) as total_done,
            AVG(TIMESTAMPDIFF(SECOND, kitchen_started_at, kitchen_done_at)) as avg_seconds,
            SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, kitchen_started_at, kitchen_done_at) <= 10 THEN 1 ELSE 0 END) as fast_count,
            SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, kitchen_started_at, kitchen_done_at) > 20 THEN 1 ELSE 0 END) as slow_count
        ')->first();

        // 2. Performance per Product
        $productStats = SaleItem::query()
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.kitchen_user_id', $userId)
            ->whereIn('sales.kitchen_status', ['done', 'delivered'])
            ->whereNotNull('sale_items.kitchen_started_at')
            ->whereNotNull('sale_items.kitchen_done_at')
            ->when($period === 'week', fn($q) => $q->where('sales.kitchen_done_at', '>=', now()->startOfWeek()))
            ->when($period === 'month', fn($q) => $q->where('sales.kitchen_done_at', '>=', now()->startOfMonth()))
            ->when($period === 'today', fn($q) => $q->whereDate('sales.kitchen_done_at', now()->toDateString()))
            ->selectRaw('
                products.name as product_name,
                COUNT(*) as total_cooked,
                AVG(TIMESTAMPDIFF(SECOND, sale_items.kitchen_started_at, sale_items.kitchen_done_at)) as avg_cook_seconds,
                MIN(TIMESTAMPDIFF(SECOND, sale_items.kitchen_started_at, sale_items.kitchen_done_at)) as min_cook_seconds,
                MAX(TIMESTAMPDIFF(SECOND, sale_items.kitchen_started_at, sale_items.kitchen_done_at)) as max_cook_seconds
            ')
            ->groupBy('products.id', 'products.name')
            ->orderBy('avg_cook_seconds', 'asc')
            ->get();

        return view('dashboard.kitchen.performance', compact('stats', 'productStats', 'period'));
    }
}