<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PaymentProofController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────────
// 🔓 Routes publiques
// ──────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/farm',      [FarmController::class, 'index']);
Route::get('/gallery',           [GalleryController::class, 'index']);
Route::post('/gallery/{gallery}/like', [GalleryController::class, 'like']);
Route::post('/contact', [ContactController::class, 'store']);

// Dates bloquées (calendrier public)
Route::get('/reservations/booked-dates', [ReservationController::class, 'bookedDates']);

// Calcul dynamique des montants
Route::get('/calculate', [ReservationController::class, 'calculate']);


// ──────────────────────────────────────────
// 🔐 Routes authentifiées
// ──────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Réservations (user connecté)
    Route::get('/reservations',      [ReservationController::class, 'index']);
    Route::post('/reservations',     [ReservationController::class, 'store']);
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
    Route::post(
        '/reservations/{reservation}/upload-proof',
        [PaymentProofController::class, 'upload']
    );

    Route::get(
        '/reservations/{reservation}/proof',
        [PaymentProofController::class, 'show']
    );
    // Stripe
    Route::post('/stripe/create-intent', [StripeController::class, 'createIntent']);
    Route::post('/stripe/confirm/{reservation}', [StripeController::class, 'confirm']);
    // ──────────────────────────────────────
    // 👑 Routes Admin
    // ──────────────────────────────────────
    Route::middleware('admin')->group(function () {

        // Gestion ferme
        Route::put('/farm', [FarmController::class, 'update']);

        // Gestion réservations admin
        Route::put('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus']);
        Route::delete('/reservations/{reservation}',     [ReservationController::class, 'destroy']);

        // Liste utilisateurs
        Route::get('/admin/users', function () {
            return \App\Models\User::withCount('reservations')->get();
        });
        Route::delete(
            '/reservations/{reservation}/proof',
            [PaymentProofController::class, 'destroy']
        );

        //Gallery admin
        Route::get('/admin/gallery',              [GalleryController::class, 'adminIndex']);
        Route::post('/admin/gallery',             [GalleryController::class, 'store']);
        Route::post('/admin/gallery/{gallery}',   [GalleryController::class, 'update']); // POST car multipart
        Route::delete('/admin/gallery/{gallery}', [GalleryController::class, 'destroy']);
        // contact admin

        Route::get('/admin/contacts', [ContactController::class, 'index']);
        Route::get('/admin/contacts/{id}', [ContactController::class, 'show']);
        Route::delete('/admin/contacts/{id}', [ContactController::class, 'destroy']);
        Route::post('/admin/contacts/{id}/reply', [ContactController::class, 'reply']);
        Route::get('/admin/contacts/unread/count', [ContactController::class, 'unreadCount']);
    });
});
