<?php

namespace Database\Seeders;

use App\Models\Farm;
use Illuminate\Database\Seeder;

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        // Vérifier si une ferme existe déjà
        if (Farm::count() > 0) {
            $this->command->info('Une ferme existe déjà. Mise à jour...');
            $farm = Farm::first();
        } else {
            $this->command->info('Création d\'une nouvelle ferme...');
            $farm = new Farm();
        }

        // Données complètes de la ferme
        $farm->fill([
            // ==================== INFORMATIONS GÉNÉRALES ====================
            'name' => 'Ferme Khadija',
            'price_per_day' => 799.00,
            'location' => 'El Haj Kedour, Province de Meknès, Maroc',
            'image' => null, // À remplacer par l'URL de l'image après upload

            // Descriptions
            'short_description' => 'Ferme de charme avec piscine et jardin luxuriant à Meknès. Location de vacances authentique pour famille ou groupe. Détente garantie.',
            'description' => "Bienvenue à la Ferme Khadija, un havre de paix authentique niché au cœur de la campagne marocaine. Notre ferme traditionnelle rénovée offre un cadre idyllique pour des vacances inoubliables en famille ou entre amis.\n\nEntourée de verdure et de montagnes majestueuses, notre propriété vous invite à la détente et à la découverte des richesses naturelles de la région de Meknès. Ici, le temps s'arrête et laissez-vous bercer par la douceur de vivre marocaine.\n\nNotre maison allie le charme de l'architecture traditionnelle au confort moderne, avec des espaces soigneusement aménagés pour votre bien-être. La piscine à débordement offre une vue imprenable sur les montagnes du Moyen Atlas, tandis que le jardin luxuriant vous promet des moments de sérénité absolue.\n\nQue vous souhaitiez vous ressourcer au bord de la piscine, explorer les jardins luxuriants, ou partir à la découverte des trésors historiques des environs, la Ferme Khadija est le point de départ idéal pour vos aventures marocaines.\n\nNotre équipe sera ravie de vous accueillir et de vous faire découvrir l'authenticité de l'hospitalité marocaine. Réservez dès maintenant pour vivre une expérience unique !",

            // Galerie
            // 'gallery_images' => json_encode([
            //     '/images/gallery/ferme-vue-aerienne.jpg',
            //     '/images/gallery/piscine-panoramique.jpg',
            //     '/images/gallery/maison-traditionnelle.jpg',
            //     '/images/gallery/jardin-luxuriant.jpg',
            //     '/images/gallery/salon-marocain.jpg',
            //     '/images/gallery/terrasse-coucher-soleil.jpg',
            //     '/images/gallery/chambre-principale.jpg',
            //     '/images/gallery/cuisine-equipee.jpg',
            // ]),

            // ==================== ÉQUIPEMENTS DE BASE ====================
            'has_house' => true,
            'has_pool' => true,
            'has_garden' => true,

            // ==================== ÉQUIPEMENTS SUPPLÉMENTAIRES ====================
            'has_wifi' => true,
            'has_parking' => true,
            'has_kitchen' => true,
            'has_air_conditioning' => true,
            'has_tv' => true,

            // ==================== CAPACITÉS ====================
            'max_persons' => 8,
            'min_nights' => 2,
            'bedrooms' => 3,
            'bathrooms' => 2,

            // ==================== CONTACT ====================
            'email' => 'contact@fermekhadija.ma',
            'phone' => '+212 6 27 24 85 80',
            'whatsapp' => '+212 6 27 24 85 80',

            // ==================== RÉSEAUX SOCIAUX ====================
            // 'facebook_url' => 'https://www.facebook.com/fermekhadija',
            'instagram_url' => 'https://www.instagram.com/__fermekhadija__/',
            // 'youtube_url' => 'https://www.youtube.com/@fermekhadija',

            // ==================== HORAIRES ====================
            'check_in_time' => '15:00',
            'check_out_time' => '11:00',

            // ==================== ÉQUIPEMENTS SUPPLÉMENTAIRES (JSON) ====================
            'amenities' => json_encode([
                // 'Barbecue', 'Lave-linge', 'Lave-vaisselle', 'Four',
                'Micro-ondes',
                'Réfrigérateur',
                'Cafetière',
                'Bouilloire',
                'Fer à repasser',
                'Sèche-cheveux',
                'Chauffage',
                'Parking privé',
                'Terrasse',
                'Mobilier de jardin',
                'Transats',
                'Parasol',
                'Jeux de société',
                'Livres',
                'Espace repas extérieur',
                // 'Rasoirs (sur demande)', 'Kit de couture (sur demande)',
            ]),

            // ==================== ATTRACTIONS À PROXIMITÉ ====================
            'nearby_attractions' => "🏛️ **Volubilis (30 min)** - Découvrez les ruines romaines classées à l'UNESCO, l'un des sites archéologiques les mieux préservés du Maroc.\n\n🏰 **Moulay Idriss (25 min)** - Ville sainte au charme authentique, nichée entre deux collines. Ne manquez pas la vue panoramique depuis le sommet.\n\n🏙️ **Meknès (20 min)** - La ville impériale et sa célèbre porte Bab Mansour. Visitez le mausolée Moulay Ismaïl et les greniers historiques.\n\n⛲ **Place El Hedim** - Cœur vibrant de Meknès, animée jour et nuit. Idéal pour déguster un thé à la menthe.\n\n🏺 **Souk traditionnel** - Artisanat local et produits du terroir : tapis, poterie, huile d'argan, épices, cuir...\n\n🌄 **Montagnes du Moyen Atlas (45 min)** - Randonnées et paysages époustouflants. Découvrez les cèdres centenaires et les singes de la forêt d'Azrou.\n\n🍷 **Domaine viticole (15 min)** - Dégustation de vins marocains dans les vignobles environnants.\n\n🕌 **Mosquée Bou Inania (25 min)** - Chef-d'œuvre de l'architecture mérinide.",

            // ==================== RÈGLES DE LA MAISON ====================
            'house_rules' => "📜 **Nos règles de la maison :**\n\n• 🚭 **Non fumeur** - Merci de ne pas fumer à l'intérieur de la maison. Des espaces extérieurs sont à disposition.\n\n• 🐾 **Animaux** - Les animaux ne sont pas acceptés pour le confort de tous.\n\n• 🔇 **Calme** - Respect du voisinage entre 22h et 8h. La ferme est un lieu de détente.\n\n• 👨‍👩‍👧‍👦 **Capacité** - Maximum 8 personnes. Les invités extérieurs ne sont pas autorisés sans accord préalable.\n\n• 🎉 **Événements** - Les fêtes et soirées ne sont pas autorisées.\n\n• 🅿️ **Stationnement** - Parking privé gratuit disponible sur place.\n\n• ♻️ **Tri sélectif** - Merci de respecter l'environnement et d'utiliser les poubelles de tri.\n\n• 🚿 **Économie d'eau** - L'eau est une ressource précieuse dans la région, merci de ne pas la gaspiller.\n\n• 🔌 **Électricité** - Éteignez les lumières et appareils lorsque vous quittez les pièces.\n\n• 🗑️ **Poubelles** - Les poubelles sont collectées tous les mercredis. Merci de les sortir la veille au soir.\n\n• 📦 **Consignes livraison** - Si vous attendez un colis, merci de nous prévenir à l'avance.\n\n• 🔑 **Clés** - Une caution de 1000 DH vous sera demandée à l'arrivée pour les clés (restituable au départ).",

            // ==================== POLITIQUE D'ANNULATION ====================
            'cancellation_policy' => "📅 **Politique d'annulation - Ferme Khadija**\n\n• **Annulation gratuite** jusqu'à 14 jours avant l'arrivée - remboursement intégral.\n\n• **50% de remboursement** entre 7 et 14 jours avant l'arrivée.\n\n• **Non remboursable** moins de 7 jours avant l'arrivée - la totalité du montant sera facturée.\n\n• **No-show** (non-présentation) - 100% du montant facturé.\n\n• **Départ anticipé** - aucun remboursement pour les nuits non utilisées.\n\n• **Cas particuliers** - En cas de **force majeure** (Covid, intempéries, restrictions gouvernementales...), nous vous offrons un avoir valable 1 an pour reprogrammer votre séjour.\n\n• **Modification de dates** - Possible sans frais jusqu'à 14 jours avant l'arrivée, sous réserve de disponibilité.",

            // ==================== SEO ====================
            'meta_title' => 'Ferme Khadija - Location de vacances avec piscine à Meknès | Séjour authentique au Maroc',
            'meta_description' => 'Découvrez la Ferme Khadija, une location de vacances authentique à Meknès. Maison traditionnelle marocaine avec piscine privée à débordement, jardin luxuriant de 5000m², 3 chambres, cuisine équipée. Idéal pour des séjours en famille ou entre amis. Réservation en ligne - Meilleur prix garanti.',

            // ==================== STATISTIQUES ====================
            'total_reviews' => 20,
            'average_rating' => 5.0,
        ]);

        $farm->save();

        $this->command->info('✅ Ferme "Ferme Khadija" a été ' . (Farm::count() === 1 ? 'créée' : 'mise à jour') . ' avec succès !');

        // Afficher un résumé
        $this->command->table(
            ['ID', 'Nom', 'Prix', 'Localisation', 'Email', 'Téléphone'],
            [
                [$farm->id, $farm->name, $farm->price_per_day . ' DH', $farm->location, $farm->email, $farm->phone],
            ]
        );
    }
}
