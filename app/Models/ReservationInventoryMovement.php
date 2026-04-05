<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationInventoryMovement extends Model
{
    protected $fillable = [
        'reservation_id','raw_material_id','type','qty','note','created_by'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
