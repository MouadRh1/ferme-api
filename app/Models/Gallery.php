<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    // URL publique de l'image - Version corrigée
    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return '';
        }
        
        // Utiliser Storage::url au lieu de asset
        return Storage::disk('public')->url($this->image_path);
    }
}