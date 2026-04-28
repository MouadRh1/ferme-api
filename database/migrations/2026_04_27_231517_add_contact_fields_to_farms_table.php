// database/migrations/xxxx_xx_xx_add_contact_fields_to_farms_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            // Coordonnées
            $table->string('email')->nullable()->after('location');
            $table->string('phone')->nullable()->after('email');
            $table->string('whatsapp')->nullable()->after('phone');
            
            // Réseaux sociaux
            $table->string('facebook_url')->nullable()->after('whatsapp');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('youtube_url')->nullable()->after('instagram_url');
            
            // Horaires et informations
            $table->string('check_in_time')->default('15:00')->after('youtube_url');
            $table->string('check_out_time')->default('11:00')->after('check_in_time');
            $table->string('min_nights')->default('2')->after('check_out_time');
            $table->string('max_persons')->default('8')->after('min_nights');
            
            // Galerie URLs (JSON pour plusieurs images)
            $table->json('gallery_images')->nullable()->after('image');
            
            // Informations supplémentaires
            $table->text('amenities')->nullable()->after('gallery_images'); // JSON ou texte
            $table->text('nearby_attractions')->nullable()->after('amenities');
            $table->text('house_rules')->nullable()->after('nearby_attractions');
            $table->text('cancellation_policy')->nullable()->after('house_rules');
            
            // SEO
            $table->string('meta_title')->nullable()->after('cancellation_policy');
            $table->text('meta_description')->nullable()->after('meta_title');
            
            // Statistiques
            $table->integer('total_reviews')->default(0)->after('meta_description');
            $table->decimal('average_rating', 2, 1)->default(5.0)->after('total_reviews');
        });
    }

    public function down(): void
    {
        Schema::table('farms', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'phone', 'whatsapp',
                'facebook_url', 'instagram_url', 'youtube_url',
                'check_in_time', 'check_out_time', 'min_nights', 'max_persons',
                'gallery_images', 'amenities', 'nearby_attractions',
                'house_rules', 'cancellation_policy',
                'meta_title', 'meta_description',
                'total_reviews', 'average_rating'
            ]);
        });
    }
};