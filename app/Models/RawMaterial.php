<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'stock_on_hand',
        'reserved_stock',
        'min_stock',
        'default_cost',
        'is_active',
    ];

    protected $casts = [
        'stock_on_hand' => 'float',
        'reserved_stock' => 'float',
        'min_stock' => 'float',
        'default_cost' => 'float',
        'is_active' => 'boolean',
    ];

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getAvailableStockAttribute(): float
    {
        return max(0, (float) $this->stock_on_hand - (float) $this->reserved_stock);
    }
}