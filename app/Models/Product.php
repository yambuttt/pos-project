<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name','sku','category','price','is_active','created_by','updated_by'
    ];

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class);
    }

    /**
     * Hitung estimasi maksimum porsi yang bisa dijual dari stok bahan saat ini.
     * (Nanti dipakai buat STRICT stock check.)
     */
    public function maxServingsFromStock(): int
    {
        $this->loadMissing('recipes.rawMaterial');

        if ($this->recipes->count() === 0) return 0;

        $mins = [];

        foreach ($this->recipes as $r) {
            $need = (float) $r->qty;
            $stock = (float) ($r->rawMaterial?->stock_on_hand ?? 0);

            if ($need <= 0) continue;

            $mins[] = (int) floor($stock / $need);
        }

        return count($mins) ? min($mins) : 0;
    }
}
