<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\FavoritController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Rental Mobil
|--------------------------------------------------------------------------
*/

// ─── Halaman Landing ─────────────────────────────────────────────────────
// Menampilkan halaman selamat datang
Route::get('/', function () {
    return view('welcome');
});

// ─── Dashboard ───────────────────────────────────────────────────────────
// Redirect otomatis setelah login berdasarkan role:
// Admin     → /admin/dashboard
// Pelanggan → view users.dashboard
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('users.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Route Pelanggan ─────────────────────────────────────────────────────
// Membutuhkan login + email terverifikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Halaman User ──────────────────────────────────────────────────
    // Halaman pemesanan milik pelanggan yang login
    Route::get('/pemesanan',  [PemesananController::class, 'userIndex'])->name('user.pemesanan.index');
    // Halaman chat pelanggan dengan admin
    Route::get('/chat',       fn() => view('users.chat'))->name('user.chat');
    // Halaman profil pelanggan — statistik & menu akun
    Route::get('/profil',     fn() => view('users.profil'))->name('user.profil');
    // Halaman wishlist / mobil yang difavoritkan pelanggan
    Route::get('/favorit',    fn() => view('users.favorit'))->name('user.favorit');
    // Halaman detail mobil untuk pelanggan
    Route::get('/mobil/{mobil}', [MobilController::class, 'show'])->name('user.mobil.show');

    // ── Favorit ───────────────────────────────────────────────────────
    // Toggle tambah/hapus mobil dari daftar favorit pelanggan
    Route::post('/favorit/{mobil}/toggle', [FavoritController::class, 'toggle'])->name('user.favorit.toggle');

    // ── Profil (Edit) ─────────────────────────────────────────────────
    // Menampilkan form edit data profil pelanggan
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    // Menyimpan perubahan data profil (nama, email, no_hp)
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    // Menghapus akun pelanggan secara permanen
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Pemesanan ─────────────────────────────────────────────────────
    // PENTING: route /pemesanan/create harus didefinisikan SEBELUM /pemesanan/{id}
    // agar Laravel tidak menganggap 'create' sebagai parameter {pemesanan}

    // Menampilkan form buat pemesanan baru (butuh ?mobil_id=X di query string)
    Route::get('/pemesanan/create',              [PemesananController::class, 'create'])->name('pemesanan.create');
    // Menyimpan pemesanan baru — cek konflik tanggal & hitung total harga otomatis
    Route::post('/pemesanan',                    [PemesananController::class, 'store'])->name('pemesanan.store');
    // Membatalkan pemesanan oleh pelanggan (hanya jika status masih pending)
    Route::patch('/pemesanan/{pemesanan}/cancel',[PemesananController::class, 'cancel'])->name('pemesanan.cancel');
});

// ─── Route Admin ─────────────────────────────────────────────────────────
// Membutuhkan login + email terverifikasi + role admin
// Prefix URL: /admin/... | Prefix name: admin....
Route::middleware(['auth', 'verified', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────
    // Halaman utama admin — statistik, konfirmasi pemesanan, ringkasan bulan ini
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    // ── CRUD Mobil ────────────────────────────────────────────────────
    // Resource otomatis menghasilkan 7 route:
    // GET    /admin/mobil            → index   (daftar + filter status)
    // GET    /admin/mobil/create     → create  (form tambah)
    // POST   /admin/mobil            → store   (simpan + upload foto)
    // GET    /admin/mobil/{id}/edit  → edit    (form edit)
    // PUT    /admin/mobil/{id}       → update  (simpan perubahan + ganti foto)
    // DELETE /admin/mobil/{id}       → destroy (hapus + hapus foto dari storage)
    Route::resource('mobil', MobilController::class);
    // Toggle status mobil antara 'tersedia' ↔ 'disewa'
    Route::patch('mobil/{mobil}/toggle', [MobilController::class, 'toggleStatus'])->name('mobil.toggle');

    // ── Pemesanan Admin ───────────────────────────────────────────────
    // Daftar semua pemesanan — support filter status & pencarian nama
    Route::get('pemesanan',                                  [PemesananController::class, 'index'])->name('pemesanan.index');
    // Konfirmasi pemesanan: status pending → dikonfirmasi, mobil → disewa
    Route::patch('pemesanan/{pemesanan}/konfirmasi',         [PemesananController::class, 'konfirmasi'])->name('pemesanan.konfirmasi');
    // Tolak pemesanan: status pending → dibatalkan
    Route::patch('pemesanan/{pemesanan}/tolak',              [PemesananController::class, 'tolak'])->name('pemesanan.tolak');
    // Selesaikan pemesanan: status dikonfirmasi → selesai, mobil → tersedia
    Route::patch('pemesanan/{pemesanan}/selesai',            [PemesananController::class, 'selesai'])->name('pemesanan.selesai');

    // ── Chat Admin ────────────────────────────────────────────────────
    // Daftar percakapan admin dengan pelanggan
    Route::get('chat',   fn() => view('admin.chat'))->name('chat');

    // ── Profil Admin ──────────────────────────────────────────────────
    // Halaman profil admin — statistik & menu kelola
    Route::get('profil', fn() => view('admin.profil'))->name('profil');

    // ── Data Pelanggan ────────────────────────────────────────────────
    // Daftar semua akun pelanggan (controller dibuat setelah UserController selesai)
    Route::get('user',   fn() => view('admin.user.index'))->name('user.index');
});

require __DIR__.'/auth.php';