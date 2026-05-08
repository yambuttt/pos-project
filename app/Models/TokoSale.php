<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoSale extends Model
{
    protected $fillable = [
        'invoice_no',
        'user_id',
        'customer_name',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'payment_method',
        'paid_amount',
        'change_amount',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
        'paid_amount' => 'float',
        'change_amount' => 'float',
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(TokoSaleItem::class);
    }
}
