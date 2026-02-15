<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'raw_material_id',
        'type',
        'qty_in',
        'qty_out',
        'reference_type',
        'reference_id',
        'created_by',
        'note'
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
