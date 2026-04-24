<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\PaymentProofService;
use Illuminate\Http\Request;

class PaymentProofController extends Controller
{
    public function __construct(
        protected PaymentProofService $proofService
    ) {}

    // ─────────────────────────────────────────────────
    // POST /api/reservations/{id}/upload-proof
    // Upload ou remplacer la preuve de paiement
    // ─────────────────────────────────────────────────
    public function upload(Request $request, Reservation $reservation)
    {
        // Vérifier que c'est bien sa réservation
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // Vérifier que c'est un virement bancaire
        if ($reservation->payment_method !== 'bank_transfer') {
            return response()->json([
                'message' => 'L\'upload de preuve est uniquement pour les virements.'
            ], 422);
        }

        // Vérifier que le paiement n'est pas déjà validé
        if ($reservation->payment_status === 'paid') {
            return response()->json([
                'message' => 'Cette réservation est déjà payée.'
            ], 422);
        }

        $request->validate([
            'payment_proof' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ]);

        try {
            // Uploader et remplacer l'ancienne preuve si existante
            $path = $this->proofService->replace(
                $request->file('payment_proof'),
                $reservation->payment_proof,
                $request->user()->id
            );

            // Mettre à jour la réservation
            $reservation->update([
                'payment_proof'  => $path,
                'payment_status' => 'pending', // Repasser en pending pour re-validation
            ]);

            return response()->json([
                'message'           => 'Preuve de paiement uploadée avec succès.',
                'payment_proof_url' => $this->proofService->getUrl($path),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────
    // GET /api/reservations/{id}/proof  (admin)
    // Voir la preuve de paiement
    // ─────────────────────────────────────────────────
    public function show(Request $request, Reservation $reservation)
    {
        if (!$request->user()->isAdmin() && $reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if (!$reservation->payment_proof) {
            return response()->json([
                'message' => 'Aucune preuve de paiement.'
            ], 404);
        }

        $exists = $this->proofService->exists($reservation->payment_proof);

        return response()->json([
            'exists'            => $exists,
            'payment_proof_url' => $this->proofService->getUrl($reservation->payment_proof),
            'payment_status'    => $reservation->payment_status,
        ]);
    }

    // ─────────────────────────────────────────────────
    // DELETE /api/reservations/{id}/proof  (admin)
    // Supprimer la preuve
    // ─────────────────────────────────────────────────
    public function destroy(Request $request, Reservation $reservation)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $this->proofService->delete($reservation->payment_proof);

        $reservation->update([
            'payment_proof'  => null,
            'payment_status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Preuve supprimée.'
        ]);
    }
}