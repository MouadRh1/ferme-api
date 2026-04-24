<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Lié à users
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');  // Lié à farms
            $table->date('start_date');                                         // Date début
            $table->date('end_date');                                           // Date fin
            $table->decimal('total_price', 10, 2);                             // Prix total calculé
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();                                  // Notes optionnelles
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
