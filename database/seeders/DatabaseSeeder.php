<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer l'admin
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@ferme.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        // Créer un utilisateur test
        User::create([
            'name'     => 'Utilisateur Test',
            'email'    => 'user@ferme.com',
            'password' => bcrypt('password'),
            'role'     => 'user',
        ]);

        // Créer la ferme
        Farm::create([
            'name'          => 'Ferme Al Baraka',
            'description'   => 'Une magnifique ferme avec maison, piscine et espace vert. Idéale pour vos événements et vacances en famille.',
            'price_per_day' => 800.00,
            'has_house'     => true,
            'has_pool'      => true,
            'has_garden'    => true,
            'location'      => 'Casablanca, Maroc',
        ]);
    }
}
