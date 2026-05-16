<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'sort_order',
        'is_active',
    ];

    public function imageUrl()
    {
        return asset('storage/' . $this->image_path);
    }
}
