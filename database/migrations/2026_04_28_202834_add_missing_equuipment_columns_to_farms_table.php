<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            // Équipements supplémentaires
            if (!Schema::hasColumn('farms', 'has_wifi')) {
                $table->boolean('has_wifi')->default(true)->after('has_garden');
            }
            
            if (!Schema::hasColumn('farms', 'has_parking')) {
                $table->boolean('has_parking')->default(true)->after('has_wifi');
            }
            
            if (!Schema::hasColumn('farms', 'has_kitchen')) {
                $table->boolean('has_kitchen')->default(true)->after('has_parking');
            }
            
            if (!Schema::hasColumn('farms', 'has_air_conditioning')) {
                $table->boolean('has_air_conditioning')->default(true)->after('has_kitchen');
            }
            
            if (!Schema::hasColumn('farms', 'has_tv')) {
                $table->boolean('has_tv')->default(true)->after('has_air_conditioning');
            }
            
            // Capacités
            if (!Schema::hasColumn('farms', 'bedrooms')) {
                $table->integer('bedrooms')->default(3)->after('max_persons');
            }
            
            if (!Schema::hasColumn('farms', 'bathrooms')) {
                $table->integer('bathrooms')->default(2)->after('bedrooms');
            }
            
            // SEO - Description courte
            if (!Schema::hasColumn('farms', 'short_description')) {
                $table->string('short_description')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $columns = [
                'has_wifi', 'has_parking', 'has_kitchen', 
                'has_air_conditioning', 'has_tv',
                'bedrooms', 'bathrooms',
                'short_description'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('farms', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};