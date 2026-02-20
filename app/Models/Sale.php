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
        'change_amount',
        'status',
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
}
