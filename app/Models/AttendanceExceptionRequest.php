<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceExceptionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_date',
        'mode',
        'device_hash',
        'device_owner_device_id',
        'device_owner_user_id',
        'device_name',
        'user_agent',
        'lat',
        'lng',
        'attendance_qr_token_id',
        'photo_path',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deviceOwnerUser()
    {
        return $this->belongsTo(User::class, 'device_owner_user_id');
    }

    public function deviceOwnerDevice()
    {
        return $this->belongsTo(EmployeeDevice::class, 'device_owner_device_id');
    }

    public function qrToken()
    {
        return $this->belongsTo(AttendanceQrToken::class, 'attendance_qr_token_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}