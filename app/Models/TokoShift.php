<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoShift extends Model
{
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'starting_cash',
        'ending_cash',
        'total_sales_cash',
        'total_sales_non_cash',
        'total_transactions',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'starting_cash' => 'float',
        'ending_cash' => 'float',
        'total_sales_cash' => 'float',
        'total_sales_non_cash' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(TokoSale::class);
    }
}
