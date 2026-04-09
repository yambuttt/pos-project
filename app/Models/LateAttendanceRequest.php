<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LateAttendanceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'allowed_until_time',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
        'requested_until_time',
        'evidence_path',
    ];

    protected $casts = [
        'date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}