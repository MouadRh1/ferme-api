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

    // URL publique de l'image - Version corrigée pour Railway
    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return '';
        }
        
        // Extraire juste le nom du fichier
        $filename = basename($this->image_path);
        
        // Utiliser la route personnalisée au lieu de Storage::url
        return url('/gallery-images/' . $filename);
    }
}