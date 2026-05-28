<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Rental Mobil
|--------------------------------------------------------------------------
*/

// ─── Halaman Landing ─────────────────────────────────────────────────────
// Menampilkan halaman selamat datang (welcome.blade.php)
Route::get('/', function () {
    return view('welcome');
});

// ─── Dashboard ───────────────────────────────────────────────────────────
// Redirect otomatis ke dashboard sesuai role setelah login:
// - Admin     → /admin/dashboard
// - Pelanggan → /dashboard (view langsung)
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('users.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Route Pelanggan ─────────────────────────────────────────────────────
// Semua route berikut membutuhkan login + email terverifikasi
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/users/dashboard', fn() => view('users.dashboard'))->name('users.dashboard');

    Route::get('/users/chat', fn() => view('users.chat'))->name('users.chat');

    // ── Profil Pelanggan ──────────────────────────────────────────────
    // Menampilkan form edit profil pelanggan
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Menyimpan perubahan data profil (nama, email, no_hp)
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Menghapus akun pelanggan secara permanen
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Pemesanan Pelanggan ───────────────────────────────────────────
    // Menampilkan form buat pemesanan baru (butuh ?mobil_id=X di query string)
    Route::get('/pemesanan/create', [PemesananController::class, 'create'])->name('pemesanan.create');
    // Menyimpan pemesanan baru ke database, cek konflik tanggal, hitung total harga
    Route::post('/pemesanan', [PemesananController::class, 'store'])->name('pemesanan.store');
    // Membatalkan pemesanan oleh pelanggan (hanya jika status masih pending)
    Route::patch('/pemesanan/{pemesanan}/cancel', [PemesananController::class, 'cancel'])->name('pemesanan.cancel');
});

// ─── Route Admin ─────────────────────────────────────────────────────────
// Semua route berikut membutuhkan login + email terverifikasi + role admin
// Prefix URL: /admin/... | Prefix name: admin....
Route::middleware(['auth', 'verified', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────
    // Halaman utama admin — statistik, konfirmasi pemesanan, ringkasan bulan ini
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    // ── CRUD Mobil ────────────────────────────────────────────────────
    // Resource route otomatis: index, create, store, show, edit, update, destroy
    // GET    /admin/mobil          → index   (daftar semua mobil + filter status)
    // GET    /admin/mobil/create   → create  (form tambah mobil baru)
    // POST   /admin/mobil          → store   (simpan mobil baru + upload foto)
    // GET    /admin/mobil/{id}/edit → edit   (form edit data mobil)
    // PUT    /admin/mobil/{id}     → update  (simpan perubahan + ganti foto)
    // DELETE /admin/mobil/{id}     → destroy (hapus mobil + foto dari storage)
    Route::resource('mobil', MobilController::class);

    // Toggle status mobil antara 'tersedia' dan 'disewa'
    Route::patch('mobil/{mobil}/toggle', [MobilController::class, 'toggleStatus'])->name('mobil.toggle');

    // ── Pemesanan Admin ───────────────────────────────────────────────
    // Menampilkan semua pemesanan dengan filter status & pencarian
    Route::get('pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');
    // Mengubah status pemesanan dari 'pending' → 'dikonfirmasi' + mobil jadi 'disewa'
    Route::patch('pemesanan/{pemesanan}/konfirmasi', [PemesananController::class, 'konfirmasi'])->name('pemesanan.konfirmasi');
    // Mengubah status pemesanan dari 'pending' → 'dibatalkan'
    Route::patch('pemesanan/{pemesanan}/tolak', [PemesananController::class, 'tolak'])->name('pemesanan.tolak');
    // Mengubah status pemesanan dari 'dikonfirmasi' → 'selesai' + mobil kembali 'tersedia'
    Route::patch('pemesanan/{pemesanan}/selesai', [PemesananController::class, 'selesai'])->name('pemesanan.selesai');

    // ── Chat Admin ────────────────────────────────────────────────────
    // Halaman pesan — admin dapat melihat & membalas pesan dari pelanggan
    Route::get('chat', fn() => view('admin.chat'))->name('chat');

    // ── Profil Admin ──────────────────────────────────────────────────
    // Halaman profil admin — statistik, menu kelola, tombol logout
    Route::get('profil', fn() => view('admin.profil'))->name('profil');

    // ── Data Pelanggan ────────────────────────────────────────────────
    // Menampilkan semua akun pelanggan yang terdaftar (controller dibuat nanti)
    Route::get('user', fn() => view('admin.user.index'))->name('user.index');
});

require __DIR__.'/auth.php';