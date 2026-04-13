<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationMaterialLock extends Model
{
    protected $fillable = [
        'reservation_id','raw_material_id','qty_locked','qty_released','qty_consumed'
    ];

    protected $casts = [
        'qty_locked' => 'float',
        'qty_released' => 'float',
        'qty_consumed' => 'float',
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