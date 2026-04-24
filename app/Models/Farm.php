<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price_per_day',
        'has_house',
        'has_pool',
        'has_garden',
        'location',
        'image',
    ];

    protected $casts = [
        'has_house'  => 'boolean',
        'has_pool'   => 'boolean',
        'has_garden' => 'boolean',
        'price_per_day' => 'decimal:2',
    ];

    // Relation : une ferme peut avoir plusieurs réservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
