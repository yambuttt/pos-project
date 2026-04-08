<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserShiftOverride extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'shift_id',
        'status',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}