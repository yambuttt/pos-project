<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    protected $table = 'dining_tables';

    protected $fillable = [
        'name',
        'is_active',
    ];
}