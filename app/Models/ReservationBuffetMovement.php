<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationBuffetMovement extends Model
{
    protected $fillable = [
        'reservation_id','raw_material_id','type','qty_in','qty_out','unit_cost','note','created_by'
    ];

    protected $casts = [
        'qty_in' => 'float',
        'qty_out' => 'float',
        'unit_cost' => 'float',
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