<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuffetPackage extends Model
{
    protected $fillable = [
        'name','pricing_type','price','min_pax','is_active','notes'
    ];

    protected $casts = [
        'price' => 'int',
        'min_pax' => 'int',
        'is_active' => 'bool',
    ];

    public function items()
    {
        return $this->hasMany(BuffetPackageItem::class);
    }
}