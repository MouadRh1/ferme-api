<?php

namespace App\Services;

use Carbon\Carbon;

class ReservationService
{
    // Prix journalier fixe
    const PRICE_PER_DAY = 800;

    // Règles avance
    const ADVANCE_SHORT = 100; // ≤ 5 jours
    const ADVANCE_LONG  = 50;  // > 5 jours
    const THRESHOLD     = 5;   // seuil en jours

    /**
     * Calcule tous les montants d'une réservation
     */
    public function calculate(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->startOfDay();

        // Nombre de jours
        $totalDays = $start->diffInDays($end);

        if ($totalDays < 1) {
            throw new \InvalidArgumentException(
                'La durée minimale est de 1 jour.'
            );
        }

        // Prix total
        $totalPrice = $totalDays * self::PRICE_PER_DAY;

        // Calcul de l'avance selon la règle métier
        $advancePerDay = $totalDays <= self::THRESHOLD
            ? self::ADVANCE_SHORT
            : self::ADVANCE_LONG;

        $advanceAmount = $totalDays * $advancePerDay;

        // Reste à payer
        $remainingAmount = $totalPrice - $advanceAmount;

        return [
            'total_days'       => $totalDays,
            'total_price'      => $totalPrice,
            'advance_amount'   => $advanceAmount,
            'remaining_amount' => $remainingAmount,
            'price_per_day'    => self::PRICE_PER_DAY,
            'advance_per_day'  => $advancePerDay,
        ];
    }

    /**
     * Vérifie si les dates sont disponibles (pas de conflit)
     */
    public function checkAvailability(
        string $startDate,
        string $endDate,
        ?int $excludeId = null
    ): bool {
        $query = \App\Models\Reservation::where('payment_status', '!=', 'rejected')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date',   '>=', $endDate);
                  });
            });

        // Exclure une réservation (pour les updates)
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists(); // true = disponible
    }
}