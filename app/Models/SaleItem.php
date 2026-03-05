<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'qty',
        'note',
        'price',
        'subtotal',
        'kitchen_cooked_qty',
        'kitchen_started_at',
        'kitchen_done_at',
    ];

    protected $casts = [
        'kitchen_started_at' => 'datetime',
        'kitchen_done_at' => 'datetime',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }



    public function sale()
    {
        return $this->belongsTo(\App\Models\Sale::class);
    }

}
