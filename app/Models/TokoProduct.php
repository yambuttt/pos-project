<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoProduct extends Model
{
    protected $fillable = [
        'toko_category_id', 'name', 'sku', 'description', 'image_url',
        'price', 'stock', 'has_variants', 'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(TokoCategory::class, 'toko_category_id');
    }

    public function variants()
    {
        return $this->hasMany(TokoProductVariant::class);
    }

    public function inventoryMovements()
    {
        return $this->morphMany(TokoInventoryMovement::class, 'item');
    }
}
