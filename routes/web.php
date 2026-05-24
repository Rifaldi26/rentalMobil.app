<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

// ─── Halaman Utama ────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Dashboard — redirect berdasarkan role ────────────────
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Route Pelanggan ─────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Pemesanan
    Route::get('/pemesanan/create',                  [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan',                        [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::patch('/pemesanan/{pemesanan}/cancel',    [PemesananController::class, 'cancel'])->name('pemesanan.cancel');
});

// ─── Route Admin ──────────────────────────────────────────
Route::middleware(['auth', 'verified', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // CRUD Mobil
    Route::resource('mobil', MobilController::class);
    Route::patch('mobil/{mobil}/toggle', [MobilController::class, 'toggleStatus'])->name('mobil.toggle');

    // Pemesanan Admin (manual — bukan resource)
    Route::get('pemesanan',                                  [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::patch('pemesanan/{pemesanan}/konfirmasi',         [PemesananController::class, 'konfirmasi'])->name('pemesanan.konfirmasi');
    Route::patch('pemesanan/{pemesanan}/tolak',              [PemesananController::class, 'tolak'])->name('pemesanan.tolak');
    Route::patch('pemesanan/{pemesanan}/selesai',            [PemesananController::class, 'selesai'])->name('pemesanan.selesai');

    // User (sementara view langsung, controller dibuat nanti)
    Route::get('user', fn() => view('admin.user.index'))->name('user.index');
});

require __DIR__.'/auth.php';