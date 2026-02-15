<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'stock_on_hand',
        'min_stock',
        'default_cost',
        'is_active'
    ];

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
