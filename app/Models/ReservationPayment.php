<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationPayment extends Model
{
    protected $fillable = [
        'reservation_id','type','amount','method','status','reference','paid_at','note','payment_proof'
    ];

    protected $casts = [
        'amount' => 'int',
        'paid_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}