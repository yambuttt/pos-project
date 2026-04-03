<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $materials = RawMaterial::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('unit', 'like', '%' . $q . '%');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $totalMaterials = RawMaterial::count();
        $lowStockCount = RawMaterial::whereColumn('stock_on_hand', '<=', 'min_stock')->count();
        $totalStockValue = RawMaterial::sum(\DB::raw('COALESCE(stock_on_hand,0) * COALESCE(default_cost,0)'));

        return view('dashboard.admin.raw_materials.index', compact(
            'materials',
            'q',
            'totalMaterials',
            'lowStockCount',
            'totalStockValue'
        ));
    }

    public function create()
    {
        return view('dashboard.admin.raw_materials.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $material = RawMaterial::create($data);

        if ((float) $material->stock_on_hand > 0) {
            InventoryMovement::create([
                'raw_material_id' => $material->id,
                'type' => 'adjustment',
                'qty_in' => $material->stock_on_hand,
                'created_by' => Auth::id(),
                'note' => 'Initial stock',
            ]);
        }

        return redirect()
            ->route('admin.raw_materials.index')
            ->with('success', 'Bahan baku berhasil dibuat.');
    }

    public function edit(RawMaterial $rawMaterial)
    {
        return view('dashboard.admin.raw_materials.edit', [
            'material' => $rawMaterial,
        ]);
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $oldStock = (float) $rawMaterial->stock_on_hand;
        $data = $this->validateData($request);

        $rawMaterial->update($data);

        $newStock = (float) $rawMaterial->stock_on_hand;
        $delta = $newStock - $oldStock;

        if (abs($delta) > 0.000001) {
            InventoryMovement::create([
                'raw_material_id' => $rawMaterial->id,
                'type' => 'adjustment',
                'qty_in' => $delta > 0 ? $delta : 0,
                'qty_out' => $delta < 0 ? abs($delta) : 0,
                'created_by' => Auth::id(),
                'note' => 'Manual stock update from raw material edit',
            ]);
        }

        return redirect()
            ->route('admin.raw_materials.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $name = $rawMaterial->name;
        $rawMaterial->delete();

        return redirect()
            ->route('admin.raw_materials.index')
            ->with('success', "Bahan baku {$name} berhasil dihapus.");
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:50',
            'stock_on_hand' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'default_cost' => 'nullable|numeric|min:0',
        ]);
    }
}