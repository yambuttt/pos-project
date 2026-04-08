<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'code',
        'name',
        'start_time',
        'end_time',
        'checkin_early_minutes',
        'checkin_late_minutes',
        'checkout_early_minutes',
        'checkout_late_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}