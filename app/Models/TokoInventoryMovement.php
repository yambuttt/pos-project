<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoInventoryMovement extends Model
{
    protected $fillable = [
        'item_type', 'item_id', 'type', 'qty', 'stock_before', 'stock_after',
        'reference_type', 'reference_id', 'notes', 'created_by'
    ];

    public function item()
    {
        return $this->morphTo();
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
