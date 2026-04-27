<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\StripeService;
use App\Services\ReservationService;
use App\Http\Requests\StoreReservationRequest;
use App\Services\MailService;
use App\Services\PaymentProofService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function __construct(
        protected StripeService      $stripeService,
        protected ReservationService $reservationService,
        protected PaymentProofService $proofService,
        protected MailService         $mailService
    ) {}

    // ─────────────────────────────────────────────────
    // POST /api/stripe/create-intent
    // Créer un PaymentIntent + réservation provisoire
    // ─────────────────────────────────────────────────
    public function createIntent(StoreReservationRequest $request)
    {
        $user = $request->user();

        // 1. Calculer les montants
        try {
            $calc = $this->reservationService->calculate(
                $request->start_date,
                $request->end_date
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // 2. Vérifier disponibilité
        if (!$this->reservationService->checkAvailability(
            $request->start_date,
            $request->end_date
        )) {
            return response()->json([
                'message' => 'La ferme est déjà réservée sur ces dates.'
            ], 422);
        }

        // 3. Créer le PaymentIntent Stripe
        try {
            $stripeData = $this->stripeService->createPaymentIntent(
                $calc['advance_amount']
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        // 4. Créer la réservation en "pending"
        $reservation = Reservation::create([
            'user_id'                  => $user->id,
            'farm_id'                  => $request->farm_id,
            'start_date'               => $request->start_date,
            'end_date'                 => $request->end_date,
            'total_days'               => $calc['total_days'],
            'total_price'              => $calc['total_price'],
            'advance_amount'           => $calc['advance_amount'],
            'payment_method'           => 'card',
            'payment_status'           => 'pending',
            'status'                   => 'pending',
            'stripe_payment_intent_id' => $stripeData['payment_intent_id'],
        ]);

        return response()->json([
            'client_secret'  => $stripeData['client_secret'],
            'reservation_id' => $reservation->id,
            'calculation'    => $calc,
        ]);
    }

    // ─────────────────────────────────────────────────
    // POST /api/stripe/confirm/{reservation}
    // Confirmer après paiement Stripe réussi
    // ─────────────────────────────────────────────────
    public function confirm(Request $request, Reservation $reservation)
    {
        // Vérifier propriétaire
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // Vérifier le paiement côté Stripe
        try {
            $status = $this->stripeService->verifyPayment(
                $reservation->stripe_payment_intent_id
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        if ($status === 'succeeded') {
            $reservation->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
            ]);
            try {
                $this->mailService->sendReservationEmails($reservation->fresh(['user', 'farm']));
            } catch (\Exception $e) {
                Log::warning('Email non envoyé: ' . $e->getMessage());
            }
            return response()->json([
                'message'     => 'Paiement confirmé ! Réservation validée.',
                'reservation' => $reservation->fresh(['user', 'farm']),
            ]);
        }

        // Paiement échoué → annuler la réservation
        $reservation->update([
            'payment_status' => 'rejected',
            'status'         => 'cancelled',
        ]);


        return response()->json([
            'message' => 'Paiement échoué. Réservation annulée.',
        ], 422);
    }
}
