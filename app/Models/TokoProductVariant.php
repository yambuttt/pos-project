<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoProductVariant extends Model
{
    protected $fillable = [
        'toko_product_id', 'name', 'sku', 'price', 'stock'
    ];

    public function product()
    {
        return $this->belongsTo(TokoProduct::class, 'toko_product_id');
    }

    public function inventoryMovements()
    {
        return $this->morphMany(TokoInventoryMovement::class, 'item');
    }
}
