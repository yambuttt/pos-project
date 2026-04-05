<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceQrToken extends Model
{
    protected $fillable = [
        'token','mode','expires_at','created_by','used_at','used_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at && now()->lt($this->expires_at);
    }
}