<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Créer un PaymentIntent pour l'avance
     */
    public function createPaymentIntent(float $amount, string $currency = 'mad'): array
    {
        try {
            $intent = PaymentIntent::create([
                'amount'   => (int)($amount * 100), // Stripe = centimes
                'currency' => $currency,
                'payment_method_types' => ['card'],
                'metadata' => ['integration' => 'ferme_khadija'],
            ]);

            return [
                'client_secret'      => $intent->client_secret,
                'payment_intent_id'  => $intent->id,
            ];

        } catch (ApiErrorException $e) {
            throw new \RuntimeException('Stripe error: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier le statut d'un PaymentIntent
     */
    public function verifyPayment(string $paymentIntentId): string
    {
        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);
            return $intent->status; // succeeded, pending, canceled...
        } catch (ApiErrorException $e) {
            throw new \RuntimeException('Stripe error: ' . $e->getMessage());
        }
    }
}