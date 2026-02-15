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

    public function create()
    {
        $materials = RawMaterial::query()->where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::query()->where('is_active', true)->orderBy('name')->get();

        return view('dashboard.admin.purchases.create', compact('materials', 'suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'source_type'   => ['required', Rule::in(['supplier', 'external'])],
            'supplier_id'   => ['nullable', 'integer', 'exists:suppliers,id'],
            'source_name'   => ['nullable', 'string', 'max:190'],
            'invoice_no'    => ['nullable', 'string', 'max:190'],
            'purchase_date' => ['required', 'date'],
            'note'          => ['nullable', 'string'],

            'items'                   => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'integer', 'exists:raw_materials,id'],
            'items.*.qty'             => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost'       => ['nullable', 'numeric', 'min:0'],
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
                'source_type'   => $data['source_type'],
                'supplier_id'   => $data['source_type'] === 'supplier' ? $data['supplier_id'] : null,
                'source_name'   => $data['source_type'] === 'external' ? $data['source_name'] : null,
                'invoice_no'    => $data['invoice_no'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'total_amount'  => 0,
                'created_by'    => Auth::id(),
                'note'          => $data['note'] ?? null,
            ]);

            $grandTotal = 0;

            foreach ($data['items'] as $item) {
                $qty = (float) $item['qty'];
                $unitCost = isset($item['unit_cost']) ? (float) $item['unit_cost'] : 0;
                $subtotal = $qty * $unitCost;
                $grandTotal += $subtotal;

                PurchaseItem::create([
                    'purchase_id'     => $purchase->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'qty'             => $qty,
                    'unit_cost'       => $unitCost,
                    'subtotal'        => $subtotal,
                ]);

                // Tambah stok bahan
                RawMaterial::where('id', $item['raw_material_id'])
                    ->update([
                        'stock_on_hand' => DB::raw('stock_on_hand + ' . $qty),
                    ]);

                // Log movement
                InventoryMovement::create([
                    'raw_material_id' => $item['raw_material_id'],
                    'type'            => 'purchase',
                    'qty_in'          => $qty,
                    'qty_out'         => 0,
                    'reference_type'  => Purchase::class,
                    'reference_id'    => $purchase->id,
                    'created_by'      => Auth::id(),
                    'note'            => 'Purchase',
                ]);
            }

            $purchase->update(['total_amount' => $grandTotal]);
        });

        return redirect()
            ->route('admin.purchases.index')
            ->with('success', 'Purchase berhasil disimpan dan stok bahan bertambah.');
    }
}
