<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\FavoritController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ChatController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Rental Mobil App
|--------------------------------------------------------------------------
|
| Struktur route dibagi menjadi 4 bagian:
|   1. Publik          — halaman yang bisa diakses tanpa login
|   2. Pelanggan       — fitur untuk user yang sudah login & terverifikasi
|   3. Administrator   — fitur khusus admin (dijaga middleware IsAdmin)
|   4. Chat Shared     — endpoint WebSocket & chat untuk semua role
|
*/

/*
|--------------------------------------------------------------------------
| 1. PUBLIK
|--------------------------------------------------------------------------
*/

// Halaman landing — daftar mobil tersedia
Route::get('/', function () {
    return view('welcome');
});

// Detail mobil bisa dilihat tanpa login
Route::get('/mobil/{mobil}', [MobilController::class, 'show'])->name('user.mobil.show');

Route::get('/dashboard', function () {
    return auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : view('users.dashboard');
})->middleware(['auth'])->name('dashboard');
/*
|--------------------------------------------------------------------------
| 2. AREA PELANGGAN
| Middleware: auth + verified
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ── Halaman Utama Pelanggan ─────────────────────────────────────────
    Route::get('/pemesanan', [PemesananController::class, 'userIndex'])->name('user.pemesanan.index');
    Route::get('/favorit',   [FavoritController::class,   'index'])->name('user.favorit');
    Route::get('/chat',      [ChatController::class,      'userIndex'])->name('user.chat');
    Route::get('/profil',    fn() => view('users.profil'))->name('user.profil');

    // ── Favorit ────────────────────────────────────────────────────────
    Route::post('/favorit/{mobil}/toggle', [FavoritController::class, 'toggle'])->name('user.favorit.toggle');

    // ── Pemesanan ──────────────────────────────────────────────────────
    // PENTING: route '/pemesanan/create' harus di atas '/pemesanan/{pemesanan}'
    // agar kata 'create' tidak dianggap sebagai parameter ID pemesanan
    Route::get('/pemesanan/create',               [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan',                     [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::patch('/pemesanan/{pemesanan}/cancel', [PemesananController::class, 'cancel'])->name('pemesanan.cancel');

    // ── Notifikasi ─────────────────────────────────────────────────────
    // PENTING: route statis ('unread', 'hapus-semua') harus di atas route
    // dinamis '{notifikasi}' agar tidak tertangkap sebagai parameter
    Route::get('/notifikasi',                          [NotifikasiController::class, 'userIndex'])->name('user.notifikasi');
    Route::get('/notifikasi/unread',                   [NotifikasiController::class, 'unread'])->name('notifikasi.unread');
    Route::delete('/notifikasi/hapus-semua',           [NotifikasiController::class, 'hapusSemua'])->name('notifikasi.hapus');
    Route::post('/notifikasi/{notifikasi}/baca',       [NotifikasiController::class, 'baca'])->name('notifikasi.baca');

    // ── Invoice PDF ────────────────────────────────────────────────────
    // Bisa diakses pelanggan (hanya milik sendiri) dan admin (semua)
    // Authorization dilakukan di dalam LaporanController::invoicePdf()
    Route::get('/invoice/{pemesanan}/pdf', [LaporanController::class, 'invoicePdf'])->name('invoice.pdf');

    // ── Manajemen Akun (Breeze) ────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 3. AREA ADMINISTRATOR
| Middleware: auth + verified + IsAdmin
| Prefix: /admin | Name prefix: admin.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // ── Dashboard & Profil ─────────────────────────────────────────────
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::get('/profil',    fn() => view('admin.profil'))->name('profil');

    // ── Manajemen Mobil ────────────────────────────────────────────────
    // Resource: index, create, store, show, edit, update, destroy
    Route::resource('mobil', MobilController::class);
    // Toggle status mobil: Tersedia ↔ Disewa
    Route::patch('mobil/{mobil}/toggle', [MobilController::class, 'toggleStatus'])->name('mobil.toggle');

    // ── Manajemen Pemesanan ────────────────────────────────────────────
    Route::get('pemesanan',                              [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::patch('pemesanan/{pemesanan}/konfirmasi',     [PemesananController::class, 'konfirmasi'])->name('pemesanan.konfirmasi');
    Route::patch('pemesanan/{pemesanan}/tolak',          [PemesananController::class, 'tolak'])->name('pemesanan.tolak');
    Route::patch('pemesanan/{pemesanan}/selesai',        [PemesananController::class, 'selesai'])->name('pemesanan.selesai');

    // ── Notifikasi Admin ───────────────────────────────────────────────
    Route::get('/notifikasi',                      [NotifikasiController::class, 'adminIndex'])->name('notifikasi');
    Route::get('/notifikasi/unread',               [NotifikasiController::class, 'unread'])->name('notifikasi.unread');
    Route::delete('/notifikasi/hapus-semua',       [NotifikasiController::class, 'hapusSemua'])->name('notifikasi.hapus');
    Route::post('/notifikasi/{notifikasi}/baca',   [NotifikasiController::class, 'baca'])->name('notifikasi.baca');

    // ── Laporan ────────────────────────────────────────────────────────
    // PENTING: 'laporan/export-csv' dan 'laporan/chart-data' harus di atas
    // agar tidak tertangkap sebagai parameter dinamis di masa depan
    Route::get('laporan',              [LaporanController::class, 'index'])->name('laporan');
    Route::get('laporan/export-csv',   [LaporanController::class, 'exportCsv'])->name('laporan.export-csv');
    Route::get('laporan/chart-data',   [LaporanController::class, 'chartData'])->name('laporan.chart-data');

    // ── Data Pengguna ──────────────────────────────────────────────────
    Route::get('user', fn() => view('admin.user.index'))->name('user.index');

    // ── Chat ───────────────────────────────────────────────────────────
    Route::get('chat', [ChatController::class, 'adminIndex'])->name('chat');
});

/*
|--------------------------------------------------------------------------
| 4. CHAT SHARED — WebSocket & Endpoint (Admin & Pelanggan)
| Middleware: auth saja (tanpa verified, agar tidak blocking)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Registrasi channel WebSocket (Laravel Reverb / Pusher)
    Broadcast::routes();

    // PENTING: route statis 'unread' harus di atas route dinamis '{lawan}'
    // agar kata 'unread' tidak dianggap sebagai ID user lawan
    Route::get('/chat/unread',            [ChatController::class, 'unread'])->name('chat.unread');
    Route::get('/chat/{lawan}/pesan',     [ChatController::class, 'riwayat'])->name('chat.riwayat');
    Route::post('/chat/{lawan}/kirim',    [ChatController::class, 'kirim'])->name('chat.kirim');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google',          [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('auth.google.callback');

require __DIR__ . '/auth.php';