<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    protected $fillable = [
        'waste_date','reason','total_estimated_cost','created_by','note'
    ];

    public function items()
    {
        return $this->hasMany(WasteItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
