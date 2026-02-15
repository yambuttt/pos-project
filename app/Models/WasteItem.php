<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteItem extends Model
{
    protected $fillable = [
        'waste_id','raw_material_id','qty','estimated_cost','subtotal'
    ];

    public function waste()
    {
        return $this->belongsTo(Waste::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
