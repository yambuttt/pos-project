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

        /**
         * Running Balance:
         * - Kalau filter 1 bahan: kita hitung saldo awal sebelum date_from lalu running.
         * - Kalau tidak filter bahan: kita hitung running per bahan (group) tapi hanya untuk page ini.
         *   (Ini cukup untuk tampilan page, dan tetap konsisten.)
         */

        $openingBalances = [];

        if ($materialId) {
            $opening = InventoryMovement::query()
                ->where('raw_material_id', $materialId)
                ->when($dateFrom, fn($qq) => $qq->whereDate('created_at', '<', $dateFrom))
                ->selectRaw('COALESCE(SUM(qty_in - qty_out),0) as bal')
                ->value('bal');

            $openingBalances[$materialId] = (float)$opening;
        } else {
            // saldo awal per bahan (opsional, kalau date_from diisi)
            if ($dateFrom) {
                $rows = InventoryMovement::query()
                    ->whereDate('created_at', '<', $dateFrom)
                    ->selectRaw('raw_material_id, COALESCE(SUM(qty_in - qty_out),0) as bal')
                    ->groupBy('raw_material_id')
                    ->get();

                foreach ($rows as $r) {
                    $openingBalances[$r->raw_material_id] = (float)$r->bal;
                }
            }
        }

        // hitung running balance untuk data yang ditampilkan
        $running = $openingBalances; // per raw_material_id
        $rowsForView = $movements->getCollection()->map(function ($m) use (&$running) {
            $rid = $m->raw_material_id;
            $running[$rid] = ($running[$rid] ?? 0) + ((float)$m->qty_in - (float)$m->qty_out);

            $m->running_balance = $running[$rid];
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
