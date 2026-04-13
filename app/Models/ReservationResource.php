<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationResource extends Model
{
    protected $fillable = [
        'type',
        'name',
        'capacity',
        'hourly_rate',
        'flat_rate',
        'min_duration_minutes',
        'buffer_minutes',
        'is_active'
    ];

    protected $casts = [
        'capacity' => 'int',
        'hourly_rate' => 'int',
        'flat_rate' => 'int',
        'min_duration_minutes' => 'int',
        'buffer_minutes' => 'int',
        'is_active' => 'bool',
    ];
}