<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\RawMaterial;
use App\Models\Waste;
use App\Models\WasteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WasteController extends Controller
{
    public function index()
    {
        $wastes = Waste::query()
            ->with(['creator'])
            ->latest()
            ->paginate(10);

        return view('dashboard.admin.wastes.index', compact('wastes'));
    }

    public function create()
    {
        $materials = RawMaterial::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $materialsJson = $materials->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'unit' => $m->unit,
                'stock' => $m->stock_on_hand,
            ];
        })->values();

        return view('dashboard.admin.wastes.create', [
            'materials' => $materials,
            'materialsJson' => $materialsJson,
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'waste_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.estimated_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($data, &$waste) {
                $waste = \App\Models\Waste::create([
                    'waste_date' => $data['waste_date'],
                    'reason' => $data['reason'] ?? null,
                    'note' => $data['note'] ?? null,
                    'created_by' => \Illuminate\Support\Facades\Auth::id(),
                    'total_estimated_cost' => 0,
                ]);

                $grand = 0;

                foreach ($data['items'] as $item) {
                    $materialId = (int) $item['raw_material_id'];
                    $qty = (float) $item['qty'];
                    $est = isset($item['estimated_cost']) ? (float) $item['estimated_cost'] : 0;
                    $sub = $qty * $est;
                    $grand += $sub;

                    // STRICT CHECK: stok harus cukup (lock supaya aman)
                    $material = \App\Models\RawMaterial::where('id', $materialId)
                        ->lockForUpdate()
                        ->first();

                    if (!$material) {
                        throw new \RuntimeException("Bahan baku tidak ditemukan.");
                    }

                    if ($material->stock_on_hand < $qty) {
                        $sisa = rtrim(rtrim(number_format($material->stock_on_hand, 2, '.', ''), '0'), '.');
                        $butuh = rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.');
                        throw new \RuntimeException("Stok tidak cukup untuk {$material->name}. Sisa: {$sisa} {$material->unit}, butuh: {$butuh} {$material->unit}.");
                    }

                    \App\Models\WasteItem::create([
                        'waste_id' => $waste->id,
                        'raw_material_id' => $materialId,
                        'qty' => $qty,
                        'estimated_cost' => $est,
                        'subtotal' => $sub,
                    ]);

                    // kurangi stok
                    $material->update([
                        'stock_on_hand' => $material->stock_on_hand - $qty,
                    ]);

                    // log movement
                    \App\Models\InventoryMovement::create([
                        'raw_material_id' => $materialId,
                        'type' => 'waste',
                        'qty_in' => 0,
                        'qty_out' => $qty,
                        'reference_type' => \App\Models\Waste::class,
                        'reference_id' => $waste->id,
                        'created_by' => \Illuminate\Support\Facades\Auth::id(),
                        'note' => 'Waste: ' . ($data['reason'] ?? 'unknown'),
                    ]);
                }

                $waste->update(['total_estimated_cost' => $grand]);
            });
        } catch (\RuntimeException $e) {
            return back()
                ->withErrors(['items' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('admin.wastes.index')
            ->with('success', 'Waste berhasil disimpan. Stok bahan berkurang.');
    }

}
