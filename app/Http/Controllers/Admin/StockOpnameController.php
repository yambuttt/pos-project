<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\RawMaterial;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::query()
            ->with(['creator','poster'])
            ->latest()
            ->paginate(10);

        return view('dashboard.admin.opnames.index', compact('opnames'));
    }

    public function create()
    {
        $materials = RawMaterial::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // kirim data siap pakai untuk JS
        $materialsJson = $materials->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'unit' => $m->unit,
                'system_qty' => (float) $m->stock_on_hand,
            ];
        })->values();

        return view('dashboard.admin.opnames.create', compact('materials', 'materialsJson'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'opname_date' => ['required', 'date'],
            'note'        => ['nullable', 'string'],

            'items'                   => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'items.*.physical_qty'    => ['required', 'numeric', 'min:0'],
            'items.*.note'            => ['nullable', 'string'],
        ]);

        $opname = DB::transaction(function () use ($data) {
            $opname = StockOpname::create([
                'opname_date' => $data['opname_date'],
                'status'      => 'draft',
                'created_by'  => Auth::id(),
                'note'        => $data['note'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $material = RawMaterial::find($item['raw_material_id']);

                $system = (float) $material->stock_on_hand;
                $physical = (float) $item['physical_qty'];
                $diff = $physical - $system;

                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'raw_material_id' => $material->id,
                    'system_qty'      => $system,
                    'physical_qty'    => $physical,
                    'difference'      => $diff,
                    'note'            => $item['note'] ?? null,
                ]);
            }

            return $opname;
        });

        return redirect()
            ->route('admin.opnames.show', $opname->id)
            ->with('success', 'Opname tersimpan sebagai draft. Silakan review lalu POST.');
    }

    public function show($id)
    {
        $opname = StockOpname::with(['items.rawMaterial', 'creator', 'poster'])->findOrFail($id);
        return view('dashboard.admin.opnames.show', compact('opname'));
    }

    public function post($id)
    {
        $opname = StockOpname::with('items')->findOrFail($id);

        if ($opname->status === 'posted') {
            return back()->with('success', 'Opname sudah diposting.');
        }

        try {
            DB::transaction(function () use ($opname) {
                // lock semua bahan yang terlibat
                $materialIds = $opname->items->pluck('raw_material_id')->unique()->values()->all();
                $materials = RawMaterial::whereIn('id', $materialIds)->lockForUpdate()->get()->keyBy('id');

                foreach ($opname->items as $it) {
                    $material = $materials[$it->raw_material_id];

                    $systemNow = (float) $material->stock_on_hand;
                    $targetPhysical = (float) $it->physical_qty;

                    // set stok = physical
                    $diff = $targetPhysical - $systemNow;

                    if ($diff > 0) {
                        // stok naik
                        $material->update(['stock_on_hand' => $systemNow + $diff]);

                        InventoryMovement::create([
                            'raw_material_id' => $material->id,
                            'type'            => 'opname',
                            'qty_in'          => $diff,
                            'qty_out'         => 0,
                            'reference_type'  => StockOpname::class,
                            'reference_id'    => $opname->id,
                            'created_by'      => Auth::id(),
                            'note'            => 'Stock opname (increase)',
                        ]);
                    } elseif ($diff < 0) {
                        $out = abs($diff);
                        // STRICT: tidak boleh jadi minus -> targetPhysical sudah min 0, aman
                        $material->update(['stock_on_hand' => $systemNow - $out]);

                        InventoryMovement::create([
                            'raw_material_id' => $material->id,
                            'type'            => 'opname',
                            'qty_in'          => 0,
                            'qty_out'         => $out,
                            'reference_type'  => StockOpname::class,
                            'reference_id'    => $opname->id,
                            'created_by'      => Auth::id(),
                            'note'            => 'Stock opname (decrease)',
                        ]);
                    }

                    // update item juga biar “final” mengikuti stok saat posting
                    $it->update([
                        'system_qty'  => $systemNow,
                        'difference'  => $targetPhysical - $systemNow,
                    ]);
                }

                $opname->update([
                    'status' => 'posted',
                    'posted_by' => Auth::id(),
                    'posted_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['post' => 'Gagal posting opname: ' . $e->getMessage()]);
        }

        return redirect()
            ->route('admin.opnames.show', $opname->id)
            ->with('success', 'Opname berhasil diposting. Stok sudah disesuaikan.');
    }
}
