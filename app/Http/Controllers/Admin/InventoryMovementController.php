<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class InventoryMovementController extends Controller
{
    public function index(Request $request)
    {
        $materials = RawMaterial::orderBy('name')->get();

        $materialId = $request->integer('raw_material_id');
        $type       = $request->string('type')->toString(); // purchase|waste|opname|adjustment|...
        $dateFrom   = $request->string('date_from')->toString(); // YYYY-MM-DD
        $dateTo     = $request->string('date_to')->toString();   // YYYY-MM-DD

        $q = InventoryMovement::query()
            ->with(['rawMaterial', 'creator'])
            ->when($materialId, fn($qq) => $qq->where('raw_material_id', $materialId))
            ->when($type, fn($qq) => $qq->where('type', $type))
            ->when($dateFrom, fn($qq) => $qq->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($qq) => $qq->whereDate('created_at', '<=', $dateTo))
            ->orderBy('created_at','desc')
            ->orderBy('id','desc');

        // Pagination dulu biar ringan
        $movements = $q->paginate(20)->withQueryString();

        $currentStocks = RawMaterial::whereIn(
            'id',
            $movements->getCollection()->pluck('raw_material_id')->unique()->values()
        )->pluck('stock_on_hand', 'id');

        $rowsForView = $movements->getCollection()->map(function ($m) use ($currentStocks) {
            $newerDelta = InventoryMovement::query()
                ->where('raw_material_id', $m->raw_material_id)
                ->where(function ($q) use ($m) {
                    $q->where('created_at', '>', $m->created_at)
                        ->orWhere(function ($qq) use ($m) {
                            $qq->where('created_at', $m->created_at)
                                ->where('id', '>', $m->id);
                        });
                })
                ->selectRaw('COALESCE(SUM(qty_in - qty_out),0) as delta')
                ->value('delta');

            $m->running_balance = (float) ($currentStocks[$m->raw_material_id] ?? 0) - (float) $newerDelta;
            return $m;
        });

        $movements->setCollection($rowsForView);

        return view('dashboard.admin.inventory.movements.index', compact(
            'materials',
            'movements',
            'materialId',
            'type',
            'dateFrom',
            'dateTo'
        ));
    }
}
