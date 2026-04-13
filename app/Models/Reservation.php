<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'code',
        'reservation_resource_id',
        'customer_name',
        'customer_phone',
        'start_at',
        'end_at',
        'pax',
        'menu_type',
        'status',
        'stock_snapshot_at',
        'menu_total',
        'rental_total',
        'grand_total',
        'dp_amount',
        'paid_amount',
        'dp_paid_at',
        'checked_in_at',
        'checked_out_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'stock_snapshot_at' => 'datetime',
        'dp_paid_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'menu_total' => 'int',
        'rental_total' => 'int',
        'grand_total' => 'int',
        'dp_amount' => 'int',
        'paid_amount' => 'int',
        'pax' => 'int',
        'midtrans_response' => 'array',
        'payment_expires_at' => 'datetime',
    ];

    public function resource()
    {
        return $this->belongsTo(ReservationResource::class, 'reservation_resource_id');
    }

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function payments()
    {
        return $this->hasMany(ReservationPayment::class);
    }

    public function locks()
    {
        return $this->hasMany(ReservationMaterialLock::class);
    }
}