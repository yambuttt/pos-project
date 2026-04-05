<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in_at',
        'check_out_at',
        'device',
        'ip',
        'device_hash',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'check_in_photo_path',
        'check_out_photo_path',
        'check_in_qr_id',
        'check_out_qr_id',
        // match_distance boleh tetap ada untuk backward-compat, tapi nanti nggak dipakai lagi
        'match_distance',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'match_distance' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}