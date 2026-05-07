<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function products()
    {
        return $this->hasMany(TokoProduct::class);
    }
}
