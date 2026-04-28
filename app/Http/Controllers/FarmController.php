<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FarmController extends Controller
{
    /**
     * Afficher la ferme (accessible à tous)
     */
    public function index()
    {
        $farm = Farm::first();

        if (!$farm) {
            return response()->json([
                'message' => 'Aucune ferme trouvée'
            ], 404);
        }

        return response()->json([
            'farm' => $farm,
            'stats' => [
                'total_reservations' => $farm->reservations()->count(),
                'total_revenue' => $farm->reservations()->sum('total_price'),
            ]
        ]);
    }

    /**
     * Modifier les infos de la ferme (admin seulement)
     */
    public function update(Request $request)
    {
        // Récupérer la première ferme (ou créer si inexistante)
        $farm = Farm::first();

        if (!$farm) {
            // Créer une ferme par défaut
            $farm = Farm::create([
                'name' => 'Ferme Khadija',
                'description' => 'Description par défaut',
                'price_per_day' => 800,
                'location' => 'El Haj Kedour, Meknès, Maroc',
                'has_house' => true,
                'has_pool' => true,
                'has_garden' => true,
            ]);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price_per_day' => 'sometimes|numeric|min:0',
            'location' => 'sometimes|string',
            'has_house' => 'sometimes|boolean',
            'has_pool' => 'sometimes|boolean',
            'has_garden' => 'sometimes|boolean',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'check_in_time' => 'nullable|string',
            'check_out_time' => 'nullable|string',
            'max_persons' => 'nullable|integer',
            'min_nights' => 'nullable|integer',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'nearby_attractions' => 'nullable|string',
            'house_rules' => 'nullable|string',
            'cancellation_policy' => 'nullable|string',
        ]);

        $farm->update($request->all());

        return response()->json([
            'message' => 'Ferme mise à jour avec succès',
            'farm' => $farm
        ]);
    }

    /**
     * Mettre à jour les statistiques
     */
    public function updateStats(Request $request, Farm $farm)
    {
        $request->validate([
            'total_reviews' => 'sometimes|integer',
            'average_rating' => 'sometimes|numeric|min:0|max:5',
        ]);

        $farm->update($request->only(['total_reviews', 'average_rating']));

        return response()->json([
            'message' => 'Statistiques mises à jour',
            'farm' => $farm
        ]);
    }
}
