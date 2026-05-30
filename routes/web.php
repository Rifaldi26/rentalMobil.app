<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Public\CarController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureNotSuspended;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════════════
// PUBLIC — Tidak perlu login
// ═══════════════════════════════════════════════════════════════

Route::get('/', fn () => view('welcome'))->name('home');

// Katalog kendaraan — bisa dilihat semua orang
Route::prefix('kendaraan')->name('cars.')->group(function () {
    Route::get('/',                               [CarController::class, 'index'])->name('index');
    Route::get('/cari',                           [CarController::class, 'index'])->name('search');
    Route::get('/{vehicle}',                      [CarController::class, 'show'])->name('show');
    Route::get('/{vehicle}/ketersediaan',         [CarController::class, 'availability'])->name('availability');
});

// Payment webhooks — dikecualikan dari CSRF
Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/webhook/midtrans', [PaymentController::class, 'midtransWebhook'])->name('webhook.midtrans');
    Route::post('/webhook/xendit',   [PaymentController::class, 'xenditWebhook'])->name('webhook.xendit');
});
Route::get('/payment/finish/{booking}', [PaymentController::class, 'finish'])->name('payment.finish');

// ═══════════════════════════════════════════════════════════════
// AUTH — Butuh login
// ═══════════════════════════════════════════════════════════════

// Dashboard redirect sesuai role
Route::get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('customer.bookings.index');
})->middleware(['auth', 'verified', EnsureNotSuspended::class])->name('dashboard');

// Profil pengguna
Route::middleware(['auth', 'verified', EnsureNotSuspended::class])->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ═══════════════════════════════════════════════════════════════
// CUSTOMER — Butuh login sebagai pelanggan
// ═══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified', EnsureNotSuspended::class])
    ->prefix('sewa')
    ->name('customer.')
    ->group(function () {

        // Booking
        Route::get('/pesan/{vehicle}',                [Customer\BookingController::class, 'create'])->name('bookings.create');
        Route::post('/pesan',                         [Customer\BookingController::class, 'store'])->name('bookings.store');
        Route::get('/pemesanan',                      [Customer\BookingController::class, 'index'])->name('bookings.index');
        Route::get('/pemesanan/{booking}',            [Customer\BookingController::class, 'show'])->name('bookings.show');
        Route::patch('/pemesanan/{booking}/batal',    [Customer\BookingController::class, 'cancel'])->name('bookings.cancel');
        
        Route::get('/pemesanan/{booking}/bayar',      [Customer\BookingController::class, 'pay'])->name('bookings.pay');
        Route::get('/pemesanan/{booking}/struk',      [Customer\BookingController::class, 'receipt'])->name('bookings.receipt');

        // Chat dengan admin
        Route::get('/chat/{booking}',                 [Customer\ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{booking}/kirim',          [Customer\ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/{booking}/pesan-baru',     [Customer\ChatController::class, 'newMessages'])->name('chat.new-messages');

        // Ulasan (hanya bisa setelah booking selesai)
        Route::get('/ulasan/{booking}',               [Customer\ReviewController::class, 'create'])->name('reviews.create');
        Route::post('/ulasan/{booking}',              [Customer\ReviewController::class, 'store'])->name('reviews.store');
    });

// ═══════════════════════════════════════════════════════════════
// ADMIN — Butuh login sebagai admin (pemilik usaha)
// ═══════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified', EnsureNotSuspended::class, IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // ─── Manajemen Kendaraan ──────────────────────────────────
        Route::prefix('kendaraan')->name('vehicles.')->group(function () {
            Route::get('/',                       [Admin\VehicleController::class, 'index'])->name('index');
            Route::get('/tambah',                 [Admin\VehicleController::class, 'create'])->name('create');
            Route::post('/',                      [Admin\VehicleController::class, 'store'])->name('store');
            Route::get('/{vehicle}',              [Admin\VehicleController::class, 'show'])->name('show');
            Route::get('/{vehicle}/ubah',         [Admin\VehicleController::class, 'edit'])->name('edit');
            Route::patch('/{vehicle}',            [Admin\VehicleController::class, 'update'])->name('update');
            Route::delete('/{vehicle}',           [Admin\VehicleController::class, 'destroy'])->name('destroy');
            Route::patch('/{vehicle}/toggle',     [Admin\VehicleController::class, 'toggleActive'])->name('toggle');
            Route::delete('/{vehicle}/foto/{photo}', [Admin\VehicleController::class, 'deletePhoto'])->name('photo.delete');
        });

        // ─── Jadwal & Ketersediaan Kendaraan ─────────────────────
        Route::prefix('jadwal')->name('schedule.')->group(function () {
            Route::get('/',                                 [Admin\ScheduleController::class, 'index'])->name('index');
            Route::get('/{vehicle}',                        [Admin\ScheduleController::class, 'index'])->name('vehicle');
            Route::get('/{vehicle}/data',                   [Admin\ScheduleController::class, 'availability'])->name('availability');
            Route::post('/{vehicle}/blokir',                [Admin\ScheduleController::class, 'block'])->name('block');
            Route::post('/{vehicle}/blokir-rentang',        [Admin\ScheduleController::class, 'blockRange'])->name('blockRange');
            Route::delete('/{vehicle}/blokir/{date}',       [Admin\ScheduleController::class, 'unblock'])->name('unblock');
        });

        // ─── Pemesanan ────────────────────────────────────────────
        Route::prefix('pemesanan')->name('bookings.')->group(function () {
            Route::get('/',                           [Admin\BookingController::class, 'index'])->name('index');
            Route::get('/{booking}',                  [Admin\BookingController::class, 'show'])->name('show');
            Route::patch('/{booking}/konfirmasi',     [Admin\BookingController::class, 'confirm'])->name('confirm');
            Route::patch('/{booking}/aktifkan',       [Admin\BookingController::class, 'activate'])->name('activate');
            Route::patch('/{booking}/tolak',          [Admin\BookingController::class, 'reject'])->name('reject');
            Route::patch('/{booking}/selesai',        [Admin\BookingController::class, 'finish'])->name('finish');
        });

        // ─── Chat dengan Pelanggan ────────────────────────────────
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/',                   [Admin\ChatController::class, 'index'])->name('index');
            Route::get('/unread-count',       [Admin\ChatController::class, 'unreadCount'])->name('unread');
            Route::get('/{booking}',          [Admin\ChatController::class, 'show'])->name('show');
            Route::post('/{booking}/kirim',   [Admin\ChatController::class, 'send'])->name('send');
        });

        // ─── Pengguna ─────────────────────────────────────────────
        Route::prefix('pengguna')->name('users.')->group(function () {
            Route::get('/',                   [Admin\UserController::class, 'index'])->name('index');
            Route::patch('/{user}/suspend',   [Admin\UserController::class, 'suspend'])->name('suspend');
            Route::patch('/{user}/aktifkan',  [Admin\UserController::class, 'unsuspend'])->name('unsuspend');
        });

        // ─── Keuangan & Penarikan ─────────────────────────────────
        Route::prefix('keuangan')->name('finance.')->group(function () {
            Route::get('/',                               [Admin\FinanceController::class, 'index'])->name('index');
        });

        Route::prefix('penarikan')->name('withdrawals.')->group(function () {
            Route::get('/',                               [Admin\WithdrawalController::class, 'index'])->name('index');
            Route::get('/ajukan',                         [Admin\WithdrawalController::class, 'create'])->name('create');
            Route::post('/',                              [Admin\WithdrawalController::class, 'store'])->name('store');
            Route::patch('/{withdrawal}/proses',          [Admin\WithdrawalController::class, 'approve'])->name('approve');
            Route::patch('/{withdrawal}/tolak',           [Admin\WithdrawalController::class, 'reject'])->name('reject');
        });

        // ─── Laporan & Audit ──────────────────────────────────────
        Route::get('/laporan',    [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/audit-log',  [Admin\AuditController::class, 'index'])->name('audit.index');
    });
    Route::get('/auth/google',          [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('auth.google.callback');

require __DIR__ . '/auth.php';