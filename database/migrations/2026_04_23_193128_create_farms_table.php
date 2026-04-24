<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nom de la ferme
            $table->text('description');                     // Description générale
            $table->decimal('price_per_day', 8, 2);         // Prix par jour (800 DH)
            $table->boolean('has_house')->default(true);     // Maison
            $table->boolean('has_pool')->default(true);      // Piscine
            $table->boolean('has_garden')->default(true);    // Espace vert
            $table->string('location')->nullable();          // Localisation
            $table->string('image')->nullable();             // Photo principale
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};