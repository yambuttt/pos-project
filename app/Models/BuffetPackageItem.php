<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuffetPackageItem extends Model
{
    protected $fillable = [
        'buffet_package_id','product_id','qty','note'
    ];

    protected $casts = [
        'qty' => 'int',
    ];

    public function package()
    {
        return $this->belongsTo(BuffetPackage::class, 'buffet_package_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}