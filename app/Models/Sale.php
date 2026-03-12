<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_no',
        'user_id',
        'total_amount',
        'paid_amount',
        'payment_method',
        'payment_status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_transaction_status',
        'midtrans_payment_type',
        'midtrans_response',
        'payment_expires_at',
        'paid_at',
        'stock_released_at',
        'order_type',
        'dining_table_id',
        'change_amount',
        'status',
        'delivery_phone',
        'delivery_address',
        'delivery_lat',
        'delivery_lng',
        'delivery_distance_km',
        'delivery_fee',
        'delivered_at',
        'delivered_user_id',
        'kitchen_status',
        'kitchen_started_at',
        'kitchen_done_at',
        'kitchen_user_id',
    ];

    protected $casts = [
        'midtrans_response' => 'array',
        'payment_expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'stock_released_at' => 'datetime',
        'delivered_at' => 'datetime',
        'kitchen_started_at' => 'datetime',
        'kitchen_done_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function items()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function kitchenUser()
    {
        return $this->belongsTo(User::class, 'kitchen_user_id');
    }
    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class, 'dining_table_id');
    }

    public function deliveredUser()
    {
        return $this->belongsTo(User::class, 'delivered_user_id');
    }

}
