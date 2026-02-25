<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceProfile extends Model
{
    protected $fillable = ['user_id', 'descriptors', 'enrolled_at'];

    protected $casts = [
        'descriptors' => 'array',
        'enrolled_at' => 'datetime',
    ];
}