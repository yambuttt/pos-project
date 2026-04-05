<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationMaterialRequirement extends Model
{
    protected $fillable = [
        'reservation_id','raw_material_id','required_qty'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
