<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::query()
            ->with(['supplier', 'creator'])
            ->latest()
            ->paginate(10);

        return view('dashboard.admin.purchases.index', compact('purchases'));
    }
    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier',
            'creator',
            'items.rawMaterial',
        ]);

        return view('dashboard.admin.purchases.show', compact('purchase'));
    }
    public function create()
    {
        $materials = RawMaterial::query()->where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.admin.purchases.create', compact('materials', 'suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'source_type' => ['required', Rule::in(['supplier', 'external'])],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'source_name' => ['nullable', 'string', 'max:190'],
            'invoice_no' => ['nullable', 'string', 'max:190'],
            'purchase_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        // rule tambahan biar konsisten:
        if ($data['source_type'] === 'supplier' && empty($data['supplier_id'])) {
            return back()->withErrors(['supplier_id' => 'Pilih supplier jika source type = supplier.'])->withInput();
        }
        if ($data['source_type'] === 'external' && empty($data['source_name'])) {
            return back()->withErrors(['source_name' => 'Isi sumber pembelian (misal: Pasar / Tokopedia / Pak Budi).'])->withInput();
        }

        DB::transaction(function () use ($data, &$purchase) {
            $purchase = Purchase::create([
                'source_type' => $data['source_type'],
                'supplier_id' => $data['source_type'] === 'supplier' ? $data['supplier_id'] : null,
                'source_name' => $data['source_type'] === 'external' ? $data['source_name'] : null,
                'invoice_no' => $data['invoice_no'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'total_amount' => 0,
                'created_by' => Auth::id(),
                'note' => $data['note'] ?? null,
            ]);

            $grandTotal = 0;

            foreach ($data['items'] as $item) {
                $qty = (float) $item['qty'];
                $unitCost = isset($item['unit_cost']) ? (float) $item['unit_cost'] : 0;
                $subtotal = $qty * $unitCost;
                $grandTotal += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);

                // Tambah stok bahan
                RawMaterial::where('id', $item['raw_material_id'])
                    ->update([
                        'stock_on_hand' => DB::raw('stock_on_hand + ' . $qty),
                    ]);

                // Log movement
                InventoryMovement::create([
                    'raw_material_id' => $item['raw_material_id'],
                    'type' => 'purchase',
                    'qty_in' => $qty,
                    'qty_out' => 0,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'created_by' => Auth::id(),
                    'note' => 'Purchase',
                ]);
            }

            $purchase->update(['total_amount' => $grandTotal]);
        });

        return redirect()
            ->route('admin.purchases.index')
            ->with('success', 'Purchase berhasil disimpan dan stok bahan bertambah.');
    }

    public function exportPdf(Request $request)
{
    /**
     * Mode export:
     * - selected: berdasarkan checkbox purchase_ids[]
     * - range: berdasarkan from/to (tanggal)
     * - period: harian/mingguan/bulanan/tahunan berdasarkan anchor_date (opsional)
     */
    $data = $request->validate([
        'mode' => ['required', Rule::in(['selected', 'range', 'period'])],

        // mode = selected
        'purchase_ids'   => ['nullable', 'array'],
        'purchase_ids.*' => ['integer', 'exists:purchases,id'],

        // mode = range
        'from' => ['nullable', 'date'],
        'to'   => ['nullable', 'date'],

        // mode = period
        'period'      => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
        'anchor_date' => ['nullable', 'date'],
    ]);

    // Base query + relasi penting untuk PDF
    $q = \App\Models\Purchase::query()
        ->with([
            'supplier',
            'creator',
            'items.rawMaterial', // pastikan relasi ini ada di model PurchaseItem
        ])
        ->orderBy('purchase_date')
        ->orderBy('id');

    // Terapkan filter sesuai mode
    if ($data['mode'] === 'selected') {
        $ids = $data['purchase_ids'] ?? [];
        if (count($ids) === 0) {
            return back()->withErrors(['purchase_ids' => 'Pilih minimal 1 purchase untuk diexport.']);
        }
        $q->whereIn('id', $ids);
    }

    if ($data['mode'] === 'range') {
        if (empty($data['from']) || empty($data['to'])) {
            return back()->withErrors(['from' => 'Isi tanggal dari & sampai.']);
        }

        $from = Carbon::parse($data['from'])->startOfDay();
        $to   = Carbon::parse($data['to'])->endOfDay();

        if ($from->gt($to)) {
            return back()->withErrors(['from' => 'Tanggal "Dari" tidak boleh lebih besar dari "Sampai".']);
        }

        // NOTE: purchase_date di tabel purchases dipakai sebagai tanggal transaksi pembelian
        $q->whereBetween('purchase_date', [$from->toDateString(), $to->toDateString()]);
    }

    if ($data['mode'] === 'period') {
        $period = $data['period'] ?? null;
        if (!$period) {
            return back()->withErrors(['period' => 'Pilih period (harian/mingguan/bulanan/tahunan).']);
        }

        $anchor = !empty($data['anchor_date'])
            ? Carbon::parse($data['anchor_date'])
            : now();

        // Tentukan range berdasarkan period
        switch ($period) {
            case 'daily':
                $from = $anchor->copy()->startOfDay();
                $to   = $anchor->copy()->endOfDay();
                break;

            case 'weekly':
                // Senin - Minggu
                $from = $anchor->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $to   = $anchor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                break;

            case 'monthly':
                $from = $anchor->copy()->startOfMonth()->startOfDay();
                $to   = $anchor->copy()->endOfMonth()->endOfDay();
                break;

            case 'yearly':
                $from = $anchor->copy()->startOfYear()->startOfDay();
                $to   = $anchor->copy()->endOfYear()->endOfDay();
                break;
        }

        $q->whereBetween('purchase_date', [$from->toDateString(), $to->toDateString()]);
    }

    $purchases = $q->get();

    if ($purchases->isEmpty()) {
        return back()->withErrors(['mode' => 'Tidak ada data purchase untuk diexport (sesuai filter).']);
    }

    $printedAt      = now();
    $totalPurchases = $purchases->count();
    $grandTotal     = (float) $purchases->sum('total_amount');

    // Render PDF (format: rekap atas + setiap purchase punya tabel sendiri)
    $pdf = Pdf::loadView('dashboard.admin.purchases.export.bulk', [
        'purchases'      => $purchases,
        'printedAt'      => $printedAt,
        'totalPurchases' => $totalPurchases,
        'grandTotal'     => $grandTotal,
    ])->setPaper('a4', 'portrait');

    $filename = 'purchases-gabungan-' . $printedAt->format('Ymd-His') . '.pdf';
    return $pdf->download($filename);
}

    public function exportPdfBulk(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $sourceType = $request->query('source_type'); // optional
        $q = $request->query('q'); // optional

        $purchases = \App\Models\Purchase::query()
            ->with(['supplier', 'creator', 'items.rawMaterial'])
            ->when($from, fn($qq) => $qq->whereDate('purchase_date', '>=', $from))
            ->when($to, fn($qq) => $qq->whereDate('purchase_date', '<=', $to))
            ->when($sourceType, fn($qq) => $qq->where('source_type', $sourceType))
            ->when($q, function ($qq) use ($q) {
                $qq->where('invoice_no', 'like', "%{$q}%")
                    ->orWhere('source_name', 'like', "%{$q}%");
            })
            ->orderBy('purchase_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // grand total = total semua subtotal item di semua purchase
        $grandTotal = 0;
        foreach ($purchases as $p) {
            $grandTotal += (float) ($p->items?->sum('subtotal') ?? 0);
        }

        $meta = [
            'printed_at' => now(),
            'from' => $from,
            'to' => $to,
            'count_purchase' => $purchases->count(),
            'grand_total' => $grandTotal,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'dashboard.admin.purchases.export_pdf_bulk_sections',
            compact('purchases', 'meta')
        )->setPaper('A4', 'portrait');

        $datePart = now()->format('Ymd_His');
        return $pdf->download("PURCHASES_BULK_{$datePart}.pdf");
    }
}
