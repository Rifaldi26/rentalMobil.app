<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\FavoritController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Rental Mobil
|--------------------------------------------------------------------------
*/

// ─── Halaman Utama ─────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── Gateaway Dashboard ───────────────────────────────────────────────────────────
// Mengarahkan pengguna ke dashboard yang sesuai berdasarkan role
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } return view('users.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Area Pelanggan (User)
|--------------------------------------------------------------------------
*/
Route::get('/mobil/{mobil}', [MobilController::class, 'show'])->name('user.mobil.show');

Route::middleware(['auth', 'verified'])->group(function () {

    // -- Navigasi Utama Pelanggan --
    Route::get('/pemesanan',  [PemesananController::class, 'userIndex'])->name('user.pemesanan.index');
    Route::get('/favorit', [FavoritController::class, 'index'])->name('user.favorit');
    Route::get('/chat', [ChatController::class, 'userIndex'])->name('user.chat');
    Route::get('/profil',     fn() => view('users.profil'))->name('user.profil');
    
    // -- Interaksi Favorit --
    Route::post('/favorit/{mobil}/toggle', [FavoritController::class, 'toggle'])->name('user.favorit.toggle');
    
    // -- Manajemen Profil Akun --
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // -- Manajemen Pemesanan --
    Route::get('/pemesanan/create',              [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan',                    [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::patch('/pemesanan/{pemesanan}/cancel',[PemesananController::class, 'cancel'])->name('pemesanan.cancel');

    Route::get('/notifikasi',              [NotifikasiController::class, 'userIndex'])->name('user.notifikasi');
    Route::get('/notifikasi/unread',       [NotifikasiController::class, 'unread'])->name('notifikasi.unread');
    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::delete('/notifikasi/hapus-semua',    [NotifikasiController::class, 'hapusSemua'])->name('notifikasi.hapus');
});
    
/*
|--------------------------------------------------------------------------
| Area Administrator
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    
    // -- Dashboard dan Profil Admin --
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::get('profil', fn() => view('admin.profil'))->name('profil');
    
    // -- Manajemen Mobil --
    // Menyediakan route index, create, store, edit, update, destroy
    Route::resource('mobil', MobilController::class);
    
    // Mengubah status mobil (Tersedia <-> Disewa)
    Route::patch('mobil/{mobil}/toggle', [MobilController::class, 'toggleStatus'])->name('mobil.toggle');
    
    // -- Manajemen Pemesanan --
    Route::get('pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::patch('pemesanan/{pemesanan}/konfirmasi', [PemesananController::class, 'konfirmasi'])->name('pemesanan.konfirmasi');
    Route::patch('pemesanan/{pemesanan}/tolak', [PemesananController::class, 'tolak'])->name('pemesanan.tolak');
    Route::patch('pemesanan/{pemesanan}/selesai', [PemesananController::class, 'selesai'])->name('pemesanan.selesai');
    
    // -- Komunikasi & Data Pengguna --
    Route::get('chat', [ChatController::class, 'adminIndex'])->name('chat');
    Route::get('user',   fn() => view('admin.user.index'))->name('user.index');

    Route::get('/notifikasi', [NotifikasiController::class, 'adminIndex'])->name('notifikasi');
});

/*
|--------------------------------------------------------------------------
| Shared Routes: Chat System (Admin & Pelanggan)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Registrasi route untuk WebSockets (misal: Pusher)
    Broadcast::routes();
    
    // -- Endpoint Data Chat --
    // PENTING: Route 'unread' diletakkan sebelum '{lawan}' agar kata 'unread' tidak dianggap sebagai parameter lawan
    Route::get('/chat/unread', [ChatController::class, 'unread'])->name('chat.unread');
    Route::get('/chat/{lawan}/pesan',[ChatController::class, 'riwayat'])->name('chat.riwayat');
    Route::post('/chat/{lawan}/kirim',[ChatController::class, 'kirim'])->name('chat.kirim');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';