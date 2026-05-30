# RentWheels — Panduan Arsitektur & Refactoring

> Dokumen ini menjelaskan struktur, pola, dan keputusan desain pada codebase yang telah direfactor.

---

## Daftar Isi

1. [Struktur Direktori](#struktur-direktori)
2. [Lapisan Arsitektur](#lapisan-arsitektur)
3. [Enums — Sumber Kebenaran Tunggal](#enums)
4. [Repository Pattern](#repository-pattern)
5. [Service Layer](#service-layer)
6. [Form Requests](#form-requests)
7. [Policies & Otorisasi](#policies)
8. [Custom Exceptions](#custom-exceptions)
9. [Model Enhancements](#model-enhancements)
10. [Routes](#routes)
11. [Testing](#testing)
12. [Changelog dari Versi Lama](#changelog)

---

## Struktur Direktori

```
app/
├── Enums/
│   ├── MobilStatus.php          # Status mobil: tersedia | disewa
│   ├── PemesananStatus.php      # Status pemesanan: pending | dikonfirmasi | selesai | dibatalkan
│   └── UserRole.php             # Role user: admin | customer
│
├── Exceptions/
│   ├── MobilTidakTersediaException.php
│   ├── PemesananKonflikException.php
│   └── UnauthorizedPemesananException.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── MobilController.php       # CRUD mobil, hanya admin
│   │   │   └── PemesananController.php   # Kelola pemesanan, hanya admin
│   │   ├── PemesananController.php       # Aksi pelanggan (buat, batalkan)
│   │   └── ProfileController.php
│   │
│   ├── Middleware/
│   │   └── IsAdmin.php
│   │
│   └── Requests/
│       ├── Admin/
│       │   ├── StoreMobilRequest.php
│       │   └── UpdateMobilRequest.php
│       └── Pemesanan/
│           └── StorePemesananRequest.php
│
├── Models/
│   ├── Mobil.php
│   ├── Pemesanan.php
│   └── User.php
│
├── Policies/
│   └── PemesananPolicy.php
│
├── Providers/
│   └── AppServiceProvider.php   # Bind interface → implementasi
│
├── Repositories/
│   ├── Contracts/
│   │   └── MobilRepositoryInterface.php
│   └── MobilRepository.php
│
└── Services/
    └── BookingService.php       # Seluruh logika bisnis pemesanan
```

---

## Lapisan Arsitektur

```
Request → FormRequest (validasi) → Controller (tipis) → Service (logika bisnis)
                                                              ↓
                                                       Repository (data access)
                                                              ↓
                                                           Model (Eloquent)
```

**Prinsip:** Controller hanya mengorkestrasi — tidak mengandung logika bisnis.
Logika ada di Service. Akses data ada di Repository.

---

## Enums

Sebelumnya status disimpan sebagai string literal yang berserakan di controller,
model, dan view. Sekarang semua nilai statusnya terpusat di Enum:

```php
// ❌ Sebelum — string literal di mana-mana
$mobil->status === 'tersedia'
$pemesanan->update(['status' => 'dikonfirmasi'])

// ✅ Sesudah — type-safe, autocomplete, mudah di-refactor
$mobil->isTersedia()
$pemesanan->update(['status' => PemesananStatus::Dikonfirmasi])
```

Setiap Enum juga membawa method helper (`label()`, `badgeColor()`, `canBeCancelledByUser()`)
sehingga logika kondisional tidak tersebar di view.

---

## Repository Pattern

`MobilRepository` mengimplementasikan `MobilRepositoryInterface`.
Interface-nya di-bind di `AppServiceProvider`:

```php
$this->app->bind(MobilRepositoryInterface::class, MobilRepository::class);
```

**Keuntungan:**
- Controller tidak bergantung pada implementasi konkret
- Mudah di-mock saat unit testing
- Bisa diganti implementasinya (misal: cache layer, API eksternal) tanpa ubah controller

---

## Service Layer

`BookingService` mengandung **semua** logika bisnis pemesanan:

- Validasi mobil tersedia
- Validasi konflik tanggal
- Hitung total harga
- Konfirmasi / tolak / selesai (dengan DB transaction)

```php
// Controller cukup panggil service:
$this->bookingService->createBooking($user, $request->validated());

// Semua logika di service:
private function validateMobilTersedia(Mobil $mobil): void { ... }
private function validateTidakAdaKonflik(...): void { ... }
```

---

## Form Requests

Validasi dipindah ke Form Request class khusus:

| Request Class | Digunakan di |
|---|---|
| `StoreMobilRequest` | `Admin\MobilController::store()` |
| `UpdateMobilRequest` | `Admin\MobilController::update()` |
| `StorePemesananRequest` | `PemesananController::store()` |

Setiap Form Request juga mengandung:
- `authorize()` — cek hak akses
- `messages()` — pesan error dalam Bahasa Indonesia

---

## Policies

`PemesananPolicy` mendefinisikan siapa yang boleh melakukan apa:

```php
// Di controller — bersih dan deklaratif:
$this->authorize('confirm', $pemesanan);
$this->authorize('cancel', $pemesanan);
```

---

## Custom Exceptions

Alih-alih return dengan `with('error', ...)` langsung dari dalam logika bisnis,
sekarang service melempar exception yang ditangkap di controller:

```php
// Service melempar:
throw new MobilTidakTersediaException("Mobil {$mobil->nama} sedang tidak tersedia.");

// Controller menangkap dan memutuskan response:
} catch (MobilTidakTersediaException $e) {
    return redirect()->route('dashboard')->with('error', $e->getMessage());
}
```

Ini memisahkan **logika bisnis** (service) dari **logika response HTTP** (controller).

---

## Model Enhancements

### Scopes yang bisa dichain
```php
Pemesanan::filterStatus($request->status)
    ->filterBulan($request->bulan)
    ->search($request->search)
    ->latest()
    ->paginate(15);
```

### Accessors untuk formatting
```php
$mobil->harga_formatted    // "Rp 350.000"
$mobil->foto_url           // URL lengkap atau null
$pemesanan->durasi_hari    // int: jumlah hari
$pemesanan->total_harga_formatted  // "Rp 700.000"
```

### Helper methods
```php
$mobil->isTersedia()                    // bool
$user->isAdmin()                        // bool
$pemesanan->canBeCancelledBy($user)     // bool
```

---

## Routes

Sebelumnya controller di-import satu per satu tanpa namespace yang jelas.
Sekarang namespace Admin digunakan untuk memisahkan route admin dari pelanggan:

```php
use App\Http\Controllers\Admin;     // namespace alias

Route::group(..., function () {
    Route::resource('mobil', Admin\MobilController::class);
    Route::patch(...,        [Admin\PemesananController::class, 'konfirmasi']);
});
```

---

## Testing

`BookingServiceTest` mengcover skenario utama:

- ✅ Pemesanan berhasil dibuat
- ✅ Exception saat mobil tidak tersedia
- ✅ Exception saat tanggal konflik
- ✅ Admin konfirmasi — status mobil berubah ke `disewa`
- ✅ Admin tandai selesai — status mobil kembali ke `tersedia`

Jalankan test:
```bash
php artisan test --filter BookingServiceTest
```

---

## Changelog dari Versi Lama

| Area | Sebelum | Sesudah |
|---|---|---|
| Status nilai | String literal `'tersedia'`, `'pending'` | PHP 8.1 Enum (`MobilStatus`, `PemesananStatus`) |
| Validasi | Di dalam `store()` / `update()` controller | Form Request class terpisah |
| Logika bisnis | Campur di controller | `BookingService` |
| File storage | Langsung di controller | Dikapsulasi di `MobilRepository` |
| Otorisasi | `if ($pemesanan->user_id !== Auth::id()) abort(403)` | Policy + `$this->authorize()` |
| Error handling | Return langsung dari logika | Custom Exception → ditangkap controller |
| Admin namespace | Semua controller di folder yang sama | Namespace `Admin\` di subfolder |
| Soft delete | Tidak ada | `SoftDeletes` pada model `Mobil` |
| Database index | Tidak ada | Index pada kolom `status` + composite index konflik tanggal |
| Seeder | Hanya 1 user test | `UserSeeder` + `MobilSeeder` yang terpisah |
| Testing | Tidak ada | `BookingServiceTest` dengan 5 test case |
