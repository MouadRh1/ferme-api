<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {

            // Supprimer l'ancienne colonne notes si elle existe
            if (Schema::hasColumn('reservations', 'notes')) {
                $table->dropColumn('notes');
            }

            // Colonnes de calcul
            $table->integer('total_days')->after('end_date');
            $table->decimal('total_price', 10, 2)->after('total_days')->change();
            $table->decimal('advance_amount', 10, 2)->after('total_price');

            // Paiement
            $table->enum('payment_method', ['card', 'bank_transfer'])
                ->after('advance_amount')
                ->default('card');

            $table->enum('payment_status', ['pending', 'paid', 'rejected'])
                ->after('payment_method')
                ->default('pending');

            $table->string('payment_proof')->nullable()->after('payment_status');

            // Stripe
            $table->string('stripe_payment_intent_id')->nullable()->after('payment_proof');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'total_days',
                'advance_amount',
                'payment_method',
                'payment_status',
                'payment_proof',
                'stripe_payment_intent_id',
            ]);
            $table->text('notes')->nullable();
        });
    }
};
