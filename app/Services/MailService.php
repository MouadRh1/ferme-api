<?php

namespace App\Services;

use App\Mail\ReservationNotification;
use App\Mail\ReservationConfirmation;
use App\Mail\ContactReply;
use App\Models\Reservation;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    // Les 3 destinataires fixes depuis .env
    private function getNotifyEmails(): array
    {
        return array_filter([
            env('NOTIFY_EMAIL_1'),
            env('NOTIFY_EMAIL_2'),
            env('NOTIFY_EMAIL_3'),
        ]);
    }

    /**
     * Envoyer les emails après une réservation :
     * - Aux 3 admins : notification
     * - Au client   : confirmation
     */
    public function sendReservationEmails(Reservation $reservation): void
    {
        $reservation->load(['user', 'farm']);

        // 1. Email aux 3 admins
        foreach ($this->getNotifyEmails() as $email) {
            try {
                Mail::to($email)->send(
                    new ReservationNotification($reservation)
                );
                Log::info("Email notification envoyé à {$email}");
            } catch (\Exception $e) {
                Log::error("Erreur email admin {$email}: " . $e->getMessage());
            }
        }

        // 2. Email de confirmation au client
        try {
            Mail::to($reservation->user->email)->send(
                new ReservationConfirmation($reservation)
            );
            Log::info("Email confirmation envoyé à {$reservation->user->email}");
        } catch (\Exception $e) {
            Log::error("Erreur email client: " . $e->getMessage());
        }
    }

    /**
     * Envoyer la réponse à un contact
     */
    public function sendContactReply(Contact $contact, string $replyMessage): void
    {
        try {
            Mail::to($contact->email)->send(
                new ContactReply($contact, $replyMessage)
            );
            Log::info("Réponse contact envoyée à {$contact->email}");
        } catch (\Exception $e) {
            Log::error("Erreur réponse contact: " . $e->getMessage());
            throw new \RuntimeException('Impossible d\'envoyer l\'email : ' . $e->getMessage());
        }
    }
}