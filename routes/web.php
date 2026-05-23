<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth'])->name('admin.dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // ─── Pemesanan Pelanggan ───────────────────────────────
    Route::get('/pemesanan/create',   [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan',         [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::patch('/pemesanan/{pemesanan}/cancel', [PemesananController::class, 'cancel'])->name('pemesanan.cancel');
});

Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    
    // CRUD Mobil
    Route::resource('mobil', MobilController::class);
    Route::patch('mobil/{mobil}/toggle', [MobilController::class, 'toggleStatus'])->name('mobil.toggle');
 
    // Pemesanan & User
    Route::resource('pemesanan', PemesananController::class);
    Route::resource('user', UserController::class);

    // Pemesanan Admin
    Route::get('pemesanan',                          [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::patch('pemesanan/{pemesanan}/konfirmasi', [PemesananController::class, 'konfirmasi'])->name('pemesanan.konfirmasi');
    Route::patch('pemesanan/{pemesanan}/tolak',      [PemesananController::class, 'tolak'])->name('pemesanan.tolak');
    Route::patch('pemesanan/{pemesanan}/selesai',    [PemesananController::class, 'selesai'])->name('pemesanan.selesai');

    // User
    Route::get('user', fn() => view('admin.user.index'))->name('user.index');
});

Route::patch('pemesanan/{pemesanan}/konfirmasi', [PemesananController::class, 'konfirmasi'])
     ->name('pemesanan.konfirmasi');
Route::patch('pemesanan/{pemesanan}/tolak', [PemesananController::class, 'tolak'])
     ->name('pemesanan.tolak');
Route::patch('pemesanan/{pemesanan}/selesai', [PemesananController::class, 'selesai'])
     ->name('pemesanan.selesai');

require __DIR__.'/auth.php';
