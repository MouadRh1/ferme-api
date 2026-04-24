<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'farm_id',
        'start_date',
        'end_date',
        'total_days',
        'total_price',
        'advance_amount',
        'payment_method',
        'payment_status',
        'payment_proof',
        'stripe_payment_intent_id',
        'status',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'total_price'    => 'decimal:2',
        'advance_amount' => 'decimal:2',
    ];

    // ── Accesseur : montant restant ──────────────────
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_price - $this->advance_amount;
    }

    // ── Accesseur : URL de la preuve de paiement ────
    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof
            ? asset('storage/' . $this->payment_proof)
            : null;
    }

    // ── Relations ────────────────────────────────────
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function farm(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    // ── Scopes utiles ────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}