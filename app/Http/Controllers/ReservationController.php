<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Farm;
use App\Services\MailService;
use App\Services\PaymentProofService;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class ReservationController extends Controller
{
    // Injection du service dans le constructeur
    public function __construct(
        protected ReservationService $reservationService,
        protected PaymentProofService $proofService,
        protected MailService $mailService
    ) {}

    // ─────────────────────────────────────────────────
    // GET /api/reservations (admin → toutes | user → les siennes)
    // ─────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Reservation::with(['user', 'farm'])
            ->orderBy('created_at', 'desc');

        // Si pas admin → seulement ses réservations
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // Filtre par statut de paiement (optionnel)
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $reservations = $query->get()->map(function ($r) {
            return $this->formatReservation($r);
        });

        return response()->json($reservations);
    }

    // ─────────────────────────────────────────────────
    // GET /api/reservations/{id}
    // ─────────────────────────────────────────────────
    public function show(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        // Un user ne peut voir que sa propre réservation
        if (!$user->isAdmin() && $reservation->user_id !== $user->id) {
            return response()->json([
                'message' => 'Accès refusé.'
            ], 403);
        }

        $reservation->load(['user', 'farm']);

        return response()->json($this->formatReservation($reservation));
    }

    // ─────────────────────────────────────────────────
    // POST /api/reservations
    // ─────────────────────────────────────────────────
    public function store(StoreReservationRequest $request)
    {
        $user = $request->user();

        // 1. Calculer
        try {
            $calc = $this->reservationService->calculate(
                $request->start_date,
                $request->end_date
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        // 2. Disponibilité
        if (!$this->reservationService->checkAvailability($request->start_date, $request->end_date)) {
            return response()->json([
                'message' => 'La ferme est déjà réservée sur ces dates.'
            ], 422);
        }

        // 3. Upload preuve si virement ──────────────────
        $proofPath = null;
        if ($request->payment_method === 'bank_transfer' && $request->hasFile('payment_proof')) {
            try {
                $proofPath = $this->proofService->upload(
                    $request->file('payment_proof'),
                    $user->id
                );
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }

        // 4. Créer la réservation
        $reservation = Reservation::create([
            'user_id'        => $user->id,
            'farm_id'        => $request->farm_id,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'total_days'     => $calc['total_days'],
            'total_price'    => $calc['total_price'],
            'advance_amount' => $calc['advance_amount'],
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'payment_proof'  => $proofPath,
            'status'         => 'pending',
        ]);

        $reservation->load(['user', 'farm']);

        try {
            $this->mailService->sendReservationEmails($reservation);
        } catch (\Exception $e) {
            Log::warning('Emails non envoyés: ' . $e->getMessage());
            // Ne pas bloquer la réponse si mail échoue
        }
        return response()->json([
            'message'     => 'Réservation créée avec succès.',
            'reservation' => $this->formatReservation($reservation),
            'calculation' => $calc,
        ], 201);
    }

    // ─────────────────────────────────────────────────
    // PUT /api/reservations/{id}/status  (admin only)
    // ─────────────────────────────────────────────────
    public function updateStatus(Request $request, Reservation $reservation)
    {
        // Validation avec payment_status optionnel
        $validated = $request->validate([
            'status'         => 'required|in:pending,confirmed,cancelled',
            'payment_status' => 'nullable|in:pending,paid,rejected,verified',
        ]);

        $oldStatus = $reservation->status;
        $newStatus = $validated['status'];

        // Mettre à jour le statut de la réservation
        $reservation->status = $newStatus;

        // Gestion automatique du payment_status
        if ($newStatus === 'confirmed') {
            $reservation->payment_status = 'paid';
        } elseif ($newStatus === 'cancelled') {
            $reservation->payment_status = 'rejected';
        }

        // Si payment_status est spécifié manuellement, il override
        if (isset($validated['payment_status']) && $validated['payment_status']) {
            $reservation->payment_status = $validated['payment_status'];
        }

        $reservation->save();

        return response()->json([
            'message' => "Réservation passée de {$oldStatus} à {$newStatus}",
            'reservation' => $this->formatReservation($reservation->fresh(['user', 'farm'])),
        ]);
    }

    // ─────────────────────────────────────────────────
    // DELETE /api/reservations/{id}  (admin only)
    // ─────────────────────────────────────────────────
    public function destroy(Reservation $reservation)
    {
        // Supprimer la preuve via le service
        $this->proofService->delete($reservation->payment_proof);

        $reservation->delete();

        return response()->json([
            'message' => 'Réservation supprimée avec succès.'
        ]);
    }

    // ─────────────────────────────────────────────────
    // GET /api/reservations/booked-dates
    // ─────────────────────────────────────────────────
    public function bookedDates(Request $request)
    {
        $reservations = Reservation::where('farm_id', $request->query('farm_id', 1))
            ->where('payment_status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->select('start_date', 'end_date', 'status', 'payment_status')
            ->get();

        return response()->json($reservations);
    }

    // ─────────────────────────────────────────────────
    // GET /api/calculate  (calcul dynamique frontend)
    // ─────────────────────────────────────────────────
    public function calculate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        try {
            $calc = $this->reservationService->calculate(
                $request->start_date,
                $request->end_date
            );
            return response()->json($calc);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // ─────────────────────────────────────────────────
    // Helper : formater une réservation pour l'API
    // ─────────────────────────────────────────────────
    private function formatReservation(Reservation $reservation): array
    {
        return [
            'id'                       => $reservation->id,
            'user'                     => $reservation->user ? [
                'id'    => $reservation->user->id,
                'name'  => $reservation->user->name,
                'email' => $reservation->user->email,
            ] : null,
            'farm'                     => $reservation->farm ? [
                'id'   => $reservation->farm->id,
                'name' => $reservation->farm->name,
            ] : null,
            'start_date'               => $reservation->start_date?->format('Y-m-d'),
            'end_date'                 => $reservation->end_date?->format('Y-m-d'),
            'total_days'               => $reservation->total_days,
            'total_price'              => (float) $reservation->total_price,
            'advance_amount'           => (float) $reservation->advance_amount,
            'remaining_amount'         => (float) $reservation->remaining_amount,
            'payment_method'           => $reservation->payment_method,
            'payment_status'           => $reservation->payment_status,
            'payment_proof_url'        => $reservation->payment_proof_url,
            'stripe_payment_intent_id' => $reservation->stripe_payment_intent_id,
            'status'                   => $reservation->status,
            'created_at'               => $reservation->created_at?->format('Y-m-d H:i'),
        ];
    }
}
