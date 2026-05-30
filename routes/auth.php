<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// ─── Guest only (redirect ke dashboard jika sudah login) ──────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register',  [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login',  [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/lupa-password',   [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/lupa-password',  [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password',        [NewPasswordController::class, 'store'])->name('password.store');
});

// ─── Auth only ────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/verifikasi-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('/verifikasi-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verifikasi-notifikasi', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/konfirmasi-password',  [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/konfirmasi-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
