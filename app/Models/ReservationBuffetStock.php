<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationBuffetStock extends Model
{
    protected $fillable = [
        'reservation_id','raw_material_id','qty_on_hand','avg_cost'
    ];

    protected $casts = [
        'qty_on_hand' => 'float',
        'avg_cost' => 'float',
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