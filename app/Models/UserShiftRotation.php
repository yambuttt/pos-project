<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserShiftRotation extends Model
{
    protected $fillable = [
        'user_id',
        'rotation_type',
        'start_date',
        'first_shift_id',
        'week_starts_on',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function firstShift()
    {
        return $this->belongsTo(Shift::class, 'first_shift_id');
    }
}