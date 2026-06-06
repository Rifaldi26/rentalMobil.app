<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Pelanggan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoritController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. PUBLIK
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'));

Route::get('/mobil/{mobil}', [Pelanggan\MobilController::class, 'show'])
    ->name('user.mobil.show');

Route::get('/dashboard', function () {
    return auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| 2. AREA PELANGGAN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/beranda',   [Pelanggan\DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/pemesanan', [Pelanggan\PemesananController::class, 'index'])->name('user.pemesanan.index');
    Route::get('/favorit',   [FavoritController::class, 'index'])->name('user.favorit');
    Route::get('/chat',      [Pelanggan\ChatController::class, 'index'])->name('user.chat');
    Route::get('/profil',    fn() => view('users.profil'))->name('user.profil');

    Route::post('/favorit/{mobil}/toggle', [FavoritController::class, 'toggle'])
        ->name('user.favorit.toggle');

    Route::get('/pemesanan/create',               [Pelanggan\PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan',                     [Pelanggan\PemesananController::class, 'store'])->name('pemesanan.store');
    Route::patch('/pemesanan/{pemesanan}/cancel', [Pelanggan\PemesananController::class, 'cancel'])->name('pemesanan.cancel');

    Route::get('/notifikasi',                        [Pelanggan\NotifikasiController::class, 'index'])->name('user.notifikasi');
    Route::get('/notifikasi/unread',                 [NotifikasiController::class, 'unread'])->name('notifikasi.unread');
    Route::delete('/notifikasi/hapus-semua',         [NotifikasiController::class, 'hapusSemua'])->name('notifikasi.hapus');
    Route::post('/notifikasi/{notifikasi}/baca',     [NotifikasiController::class, 'baca'])->name('notifikasi.baca');

    Route::get('/invoice/{pemesanan}/pdf', [Admin\LaporanController::class, 'invoicePdf'])
        ->name('invoice.pdf');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 3. AREA ADMINISTRATOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Mobil
    Route::resource('mobil', Admin\MobilController::class);
    Route::patch('mobil/{mobil}/toggle', [Admin\MobilController::class, 'toggleStatus'])
        ->name('mobil.toggle');

    // Pemesanan
    Route::get('pemesanan',                          [Admin\PemesananController::class, 'index'])->name('pemesanan.index');
    Route::get('pemesanan/{pemesanan}',              [Admin\PemesananController::class, 'show'])->name('pemesanan.show');
    Route::patch('pemesanan/{pemesanan}/konfirmasi', [Admin\PemesananController::class, 'konfirmasi'])->name('pemesanan.konfirmasi');
    Route::patch('pemesanan/{pemesanan}/tolak',      [Admin\PemesananController::class, 'tolak'])->name('pemesanan.tolak');
    Route::patch('pemesanan/{pemesanan}/selesai',    [Admin\PemesananController::class, 'selesai'])->name('pemesanan.selesai');

    // Jadwal
    Route::get('jadwal', [Admin\JadwalController::class, 'index'])->name('jadwal.index');

    // Laporan — statis dulu sebelum dinamis
    Route::get('laporan',             [Admin\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/export-csv',  [Admin\LaporanController::class, 'exportCsv'])->name('laporan.export-csv');
    Route::get('laporan/chart-data',  [Admin\LaporanController::class, 'chartData'])->name('laporan.chart-data');

    // User
    Route::get('user',        [Admin\UserController::class, 'index'])->name('user.index');
    Route::get('user/{user}', [Admin\UserController::class, 'show'])->name('user.show');

    // Chat
    Route::get('chat', [Admin\ChatController::class, 'index'])->name('chat');
});

/*
|--------------------------------------------------------------------------
| 4. CHAT SHARED — WebSocket endpoint
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Broadcast::routes();

    Route::get('/chat/unread',         [ChatController::class, 'unread'])->name('chat.unread');
    Route::get('/chat/{lawan}/pesan',  [ChatController::class, 'riwayat'])->name('chat.riwayat');
    Route::post('/chat/{lawan}/kirim', [ChatController::class, 'kirim'])->name('chat.kirim');
});

/*
|--------------------------------------------------------------------------
| Authentication (Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/auth/google',          [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('auth.google.callback');

require __DIR__ . '/auth.php';