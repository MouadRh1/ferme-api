<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentProofService
{
    // Dossier de stockage
    const DISK   = 'public';
    const FOLDER = 'payment_proofs';

    // Types autorisés
    const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    const MAX_SIZE_MB   = 5;

    /**
     * Upload une preuve de paiement
     * Retourne le chemin relatif stocké en base
     */
    public function upload(UploadedFile $file, int $userId): string
    {
        // Valider le type manuellement (double sécurité)
        $this->validateFile($file);

        // Générer un nom unique : proof_userId_timestamp_random.ext
        $extension = $file->getClientOriginalExtension();
        $filename  = sprintf(
            'proof_%d_%s_%s.%s',
            $userId,
            now()->format('Ymd_His'),
            Str::random(8),
            $extension
        );

        // Stocker dans storage/app/public/payment_proofs/
        $path = $file->storeAs(self::FOLDER, $filename, self::DISK);

        if (!$path) {
            throw new \RuntimeException('Erreur lors de l\'upload du fichier.');
        }

        return $path; // Ex: "payment_proofs/proof_1_20260501_120000_Ab3cDe4f.jpg"
    }

    /**
     * Supprimer une preuve de paiement
     */
    public function delete(?string $path): bool
    {
        if (!$path) return false;

        if (Storage::disk(self::DISK)->exists($path)) {
            return Storage::disk(self::DISK)->delete($path);
        }

        return false;
    }

    /**
     * Remplacer une ancienne preuve par une nouvelle
     */
    public function replace(UploadedFile $newFile, ?string $oldPath, int $userId): string
    {
        // Supprimer l'ancienne
        $this->delete($oldPath);

        // Uploader la nouvelle
        return $this->upload($newFile, $userId);
    }

    /**
     * Retourner l'URL publique d'une preuve
     */
    public function getUrl(?string $path): ?string
    {
        if (!$path) return null;

        return Storage::disk(self::DISK)->url($path);
        // → http://localhost:8000/storage/payment_proofs/proof_1_...jpg
    }

    /**
     * Vérifier si le fichier existe sur le disk
     */
    public function exists(?string $path): bool
    {
        if (!$path) return false;
        return Storage::disk(self::DISK)->exists($path);
    }

    /**
     * Validation manuelle du fichier
     */
    private function validateFile(UploadedFile $file): void
    {
        // Taille max
        $maxBytes = self::MAX_SIZE_MB * 1024 * 1024;
        if ($file->getSize() > $maxBytes) {
            throw new \InvalidArgumentException(
                'Le fichier ne doit pas dépasser ' . self::MAX_SIZE_MB . ' MB.'
            );
        }

        // Type MIME
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new \InvalidArgumentException(
                'Format non autorisé. Utilisez JPG, PNG ou PDF.'
            );
        }
    }
}