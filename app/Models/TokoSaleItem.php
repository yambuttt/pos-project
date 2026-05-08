<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoSaleItem extends Model
{
    protected $fillable = [
        'toko_sale_id',
        'toko_product_id',
        'toko_product_variant_id',
        'product_name',
        'variant_name',
        'qty',
        'price',
        'subtotal',
    ];

    public function sale()
    {
        return $this->belongsTo(TokoSale::class, 'toko_sale_id');
    }

    public function product()
    {
        return $this->belongsTo(TokoProduct::class, 'toko_product_id');
    }

    public function variant()
    {
        return $this->belongsTo(TokoProductVariant::class, 'toko_product_variant_id');
    }
}
