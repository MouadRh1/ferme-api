<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La vérification auth se fait via middleware
    }

    public function rules(): array
    {
        return [
            'farm_id'        => 'required|exists:farms,id',
            'start_date'     => 'required|date|after_or_equal:today',
            'end_date'       => 'required|date|after:start_date',
            'payment_method' => 'required|in:card,bank_transfer',

            // Preuve de virement : obligatoire seulement si bank_transfer
            'payment_proof'  => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                'image',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120', // 5 MB max
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'farm_id.required'          => 'La ferme est requise.',
            'farm_id.exists'            => 'La ferme sélectionnée est invalide.',
            'start_date.required'       => 'La date de début est obligatoire.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou plus tard.',
            'end_date.required'         => 'La date de fin est obligatoire.',
            'end_date.after'            => 'La date de fin doit être après la date de début.',
            'payment_method.required'   => 'Le moyen de paiement est obligatoire.',
            'payment_method.in'         => 'Moyen de paiement invalide (card ou bank_transfer).',
            'payment_proof.required_if' => 'La preuve de virement est obligatoire pour un paiement par virement.',
            'payment_proof.image'       => 'Le fichier doit être une image.',
            'payment_proof.max'         => 'L\'image ne doit pas dépasser 5 MB.',
        ];
    }
}
