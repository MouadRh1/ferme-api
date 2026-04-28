<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Farm extends Model
{
    protected $table = 'farms';

    protected $fillable = [
        // Informations de base
        'name',
        'description',
        'short_description',
        'price_per_day',
        
        // Équipements
        'has_house',
        'has_pool',
        'has_garden',
        'has_wifi',
        'has_parking',
        'has_kitchen',
        'has_air_conditioning',
        'has_tv',
        
        // Coordonnées
        'location',
        'email',
        'phone',
        'whatsapp',
        
        // Réseaux sociaux
        'facebook_url',
        'instagram_url',
        'youtube_url',
        
        // Horaires
        'check_in_time',
        'check_out_time',
        
        // Capacités
        'max_persons',
        'min_nights',
        'bedrooms',
        'bathrooms',
        
        // Images
        'main_image',
        'gallery_images',
        'amenities_list',
        
        // Textes
        'nearby_attractions',
        'house_rules',
        'cancellation_policy',
        
        // SEO
        'meta_title',
        'meta_description',
        
        // Statistiques
        'total_reviews',
        'average_rating',
    ];

    protected $casts = [
        // Équipements
        'has_house' => 'boolean',
        'has_pool' => 'boolean',
        'has_garden' => 'boolean',
        'has_wifi' => 'boolean',
        'has_parking' => 'boolean',
        'has_kitchen' => 'boolean',
        'has_air_conditioning' => 'boolean',
        'has_tv' => 'boolean',
        
        // Prix
        'price_per_day' => 'decimal:2',
        
        // JSON
        'gallery_images' => 'array',
        'amenities_list' => 'array',
        
        // Statistiques
        'total_reviews' => 'integer',
        'average_rating' => 'decimal:2',
        'max_persons' => 'integer',
        'min_nights' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
    ];

    // Accesseurs pour les URLs complètes
    public function getMainImageUrlAttribute(): ?string
    {
        return $this->main_image ? asset('storage/' . $this->main_image) : null;
    }

    public function getFacebookUrlAttribute($value)
    {
        return $value ?: '#';
    }

    public function getInstagramUrlAttribute($value)
    {
        return $value ?: '#';
    }

    // Relation avec les réservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scope pour les fermes actives
    public function scopeActive($query)
    {
        return $query;
    }

    // Méthode pour calculer la note moyenne
    public function updateRating()
    {
        // Logique pour mettre à jour la note
    }
}