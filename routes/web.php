<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;  // ← Ajoutez cette ligne

Route::get('/', function () {
    return view('welcome');
});

// Route pour servir les preuves de paiement (sans symlink)
Route::get('/proofs/{filename}', function ($filename) {
    // Nettoyer le nom du fichier pour éviter les attaques
    $filename = basename($filename);

    // Chercher le fichier dans le disque public
    $disk = Storage::disk('public');

    // Parcourir les dossiers possibles
    $possiblePaths = [
        'payment_proofs/' . $filename,
        'proofs/' . $filename,
        $filename,
    ];

    foreach ($possiblePaths as $path) {
        if ($disk->exists($path)) {
            $file = $disk->get($path);
            $mime = $disk->mimeType($path);

            return response($file, 200)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', 'inline')
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Access-Control-Allow-Origin', '*');
        }
    }

    // Log l'erreur pour déboguer
    \Log::warning('Payment proof not found: ' . $filename);

    // Retourner une erreur 404
    abort(404, 'Preuve de paiement non trouvée');
})->where('filename', '.*\.(png|jpg|jpeg|pdf|gif|webp)$')->name('payment.proof');

// Route de débogage (optionnelle - à retirer en production)
Route::get('/debug-proofs', function () {
    $disk = Storage::disk('public');
    $files = [];

    $directories = ['payment_proofs', 'proofs', ''];
    foreach ($directories as $dir) {
        if ($disk->exists($dir)) {
            $files[$dir ?: 'root'] = $disk->files($dir);
        }
    }

    return response()->json($files);
});
Route::get('/gallery-images/{filename}', function ($filename) {
    $filename = basename($filename);

    // Chercher le fichier dans le dossier gallery
    $disk = Storage::disk('public');
    $path = 'gallery/' . $filename;

    if (!$disk->exists($path)) {
        // Essaye sans le dossier gallery
        $path = $filename;
        if (!$disk->exists($path)) {
            \Log::warning('Gallery image not found: ' . $filename);
            abort(404, 'Image non trouvée');
        }
    }

    $file = $disk->get($path);
    $mime = $disk->mimeType($path);

    return response($file, 200)
        ->header('Content-Type', $mime)
        ->header('Cache-Control', 'public, max-age=86400')
        ->header('Access-Control-Allow-Origin', '*');
})->where('filename', '.*\.(png|jpg|jpeg|gif|webp)$');
