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
        'order_type',
        'dining_table_id',
        'change_amount',
        'status',
        'delivered_at',
        'delivered_user_id',
        'kitchen_status',
        'kitchen_started_at',
        'kitchen_done_at',
        'kitchen_user_id',
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
