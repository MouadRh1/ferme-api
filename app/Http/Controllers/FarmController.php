<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    /**
     * Afficher la ferme (accessible à tous)
     */
    public function index()
    {
        $farm = Farm::first(); // On a une seule ferme

        if (!$farm) {
            return response()->json([
                'message' => 'Aucune ferme trouvée'
            ], 404);
        }

        return response()->json($farm);
    }

    /**
     * Modifier les infos de la ferme (admin seulement)
     */
    public function update(Request $request, Farm $farm)
    {
        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'description'   => 'sometimes|string',
            'price_per_day' => 'sometimes|numeric|min:0',
            'has_house'     => 'sometimes|boolean',
            'has_pool'      => 'sometimes|boolean',
            'has_garden'    => 'sometimes|boolean',
            'location'      => 'sometimes|string|nullable',
        ]);

        $farm->update($request->all());

        return response()->json([
            'message' => 'Ferme mise à jour avec succès',
            'farm'    => $farm,
        ]);
    }
}