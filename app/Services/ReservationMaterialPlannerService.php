<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationMaterialRequirement;
use Illuminate\Support\Facades\DB;

class ReservationMaterialPlannerService
{
    /**
     * Rebuild requirements table from reservation_items using product recipes.
     * Does NOT touch operational stock.
     */
    public function rebuildRequirements(Reservation $reservation): void
    {
        $reservation->load(['items.product.recipes.rawMaterial']);

        $needs = []; // raw_material_id => qty

        foreach ($reservation->items as $item) {
            $qtyItem = (float) $item->qty;
            foreach ($item->product->recipes as $recipe) {
                $rmId = $recipe->raw_material_id;
                $need = (float) $recipe->qty * $qtyItem;
                $needs[$rmId] = ($needs[$rmId] ?? 0) + $need;
            }
        }

        DB::transaction(function () use ($reservation, $needs) {
            // delete removed
            ReservationMaterialRequirement::where('reservation_id', $reservation->id)
                ->whereNotIn('raw_material_id', array_keys($needs))
                ->delete();

            // upsert current
            foreach ($needs as $rmId => $qty) {
                ReservationMaterialRequirement::updateOrCreate(
                    ['reservation_id' => $reservation->id, 'raw_material_id' => $rmId],
                    ['required_qty' => $qty]
                );
            }
        });
    }
}
