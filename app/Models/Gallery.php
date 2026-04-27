<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';

    protected $fillable = [
        'title',
        'image_path',
        'category',
        'likes',
        'order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'likes'      => 'integer',
        'order'      => 'integer',
    ];

    // URL publique de l'image
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}