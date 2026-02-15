<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RawMaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::latest()->paginate(10);
        return view('dashboard.admin.raw_materials.index', compact('materials'));
    }

    public function create()
    {
        return view('dashboard.admin.raw_materials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:50',
            'stock_on_hand' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'default_cost' => 'nullable|numeric|min:0',
        ]);

        $material = RawMaterial::create($data);

        // log movement awal jika stok > 0
        if ($material->stock_on_hand > 0) {
            InventoryMovement::create([
                'raw_material_id' => $material->id,
                'type' => 'adjustment',
                'qty_in' => $material->stock_on_hand,
                'created_by' => Auth::id(),
                'note' => 'Initial stock'
            ]);
        }

        return redirect()->route('admin.raw_materials.index')
            ->with('success', 'Bahan baku berhasil dibuat.');
    }
}
