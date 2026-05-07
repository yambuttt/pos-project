<?php

namespace App\Services;

use App\Models\TokoInventoryMovement;
use Illuminate\Database\Eloquent\Model;

class TokoInventoryService
{
    /**
     * Record an inventory movement and update the item's stock.
     */
    public static function recordMovement(
        Model $item, 
        string $type, 
        int $qty, 
        ?Model $reference = null, 
        ?string $notes = null
    ) {
        $stockBefore = $item->stock;
        
        if ($type === 'in' || $type === 'adjustment_up') {
            $item->stock += $qty;
        } elseif ($type === 'out' || $type === 'waste' || $type === 'sale' || $type === 'adjustment_down') {
            $item->stock -= $qty;
        } elseif ($type === 'adjustment') {
            // If it's a direct stock assignment from opname
            $item->stock = $qty; // qty here acts as the new stock
            $type = ($item->stock >= $stockBefore) ? 'adjustment_up' : 'adjustment_down';
            $qty = abs($item->stock - $stockBefore);
        }

        $item->save();

        return TokoInventoryMovement::create([
            'item_type' => get_class($item),
            'item_id' => $item->id,
            'type' => $type,
            'qty' => $qty,
            'stock_before' => $stockBefore,
            'stock_after' => $item->stock,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'notes' => $notes,
            'created_by' => auth()->id() ?? null,
        ]);
    }
}
