<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    protected $fillable = [
        'reservation_id','item_type','item_id',
        'snapshot_name','unit_price','qty','subtotal','meta'
    ];

    protected $casts = [
        'unit_price' => 'int',
        'qty' => 'int',
        'subtotal' => 'int',
        'meta' => 'array',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}