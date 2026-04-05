<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDevice extends Model
{
    protected $fillable = [
        'user_id','device_hash','device_name','user_agent',
        'approved_at','approved_by','revoked_at','last_seen_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function isApproved(): bool
    {
        return $this->approved_at !== null && $this->revoked_at === null;
    }
}