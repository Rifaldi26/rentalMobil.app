# RentWheels — Platform Rental Kendaraan

> Laravel 11 · PHP 8.2+ · Vite · Alpine.js · Tailwind (CSS vars)

Platform sewa kendaraan multi-role (Admin, Mitra, Pelanggan) dengan pembayaran Midtrans, real-time chat (Laravel Echo + Pusher), sistem ulasan, dan manajemen keuangan mitra.

---

## Instalasi Cepat

```bash
# 1. Clone / extract project
cd rentalwheels-refactored

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 5. Edit .env — isi DB, Pusher, Midtrans
# DB_DATABASE=rentalwheels
# PUSHER_APP_ID=xxx  PUSHER_APP_KEY=xxx  PUSHER_APP_SECRET=xxx
# MIDTRANS_SERVER_KEY=xxx  MIDTRANS_CLIENT_KEY=xxx

# 6. Jalankan migrasi + seeder
php artisan migrate --seed

# 7. Build assets
npm run build          # production
# atau
npm run dev            # development (hot reload)

# 8. Storage link
php artisan storage:link

# 9. Jalankan server
php artisan serve
```

**Akun demo setelah seeder:**

| Role     | Email                     | Password  |
|----------|---------------------------|-----------|
| Admin    | admin@rentwheels.id       | password  |
| Mitra    | mitra@rentwheels.id       | password  |
| Pelanggan| customer@rentwheels.id    | password  |

---

## Menjalankan Tests

```bash
php artisan test                          # semua test
php artisan test --testsuite=Unit         # unit saja
php artisan test --testsuite=Feature      # feature saja
php artisan test --filter BookingService  # filter per class
```

---

## Struktur Arsitektur

```
app/
├── Enums/           # PHP 8.1 Enums — sumber kebenaran tunggal untuk status
├── Events/          # Domain events (BookingConfirmed, MessageSent, dll.)
├── Exceptions/      # Custom exceptions untuk business logic
├── Http/
│   ├── Controllers/
│   │   ├── Admin/   # Controllers khusus admin
│   │   ├── Partner/ # Controllers khusus mitra
│   │   ├── Customer/# Controllers khusus pelanggan
│   │   ├── Auth/    # Auth controllers (register dengan role)
│   │   └── Public/  # CarController — halaman publik
│   ├── Middleware/  # IsAdmin, IsPartner, EnsureNotSuspended
│   └── Requests/    # Form Requests per domain
├── Listeners/       # Event listeners (notifikasi email, dll.)
├── Models/          # Eloquent models dengan scopes & accessors
├── Policies/        # Authorization policies per model
├── Providers/       # AppServiceProvider, AuthServiceProvider, EventServiceProvider
├── Repositories/    # Repository pattern untuk MobilController lama
└── Services/        # Business logic layer
    ├── BookingService.php    # Buat, konfirmasi, tolak, selesai booking
    ├── PaymentService.php    # Midtrans/Xendit integration
    ├── VehicleService.php    # CRUD kendaraan + foto management
    ├── ReviewService.php     # Submit & moderasi ulasan
    ├── ChatService.php       # Real-time messaging
    ├── WithdrawalService.php # Penarikan saldo mitra
    └── AuditLogService.php   # Log aksi kritis
```

---

## Aliran Data Utama

### Booking Flow
```
Pelanggan pilih kendaraan
  → Cek ketersediaan (Vehicle::isAvailableOn)
  → BookingService::createBooking()
    → Validasi vehicle active & verified
    → Cek konflik tanggal (Booking::konflikTanggal scope)
    → Hitung total (harga × hari + 5% service fee + deposit)
    → Buat Booking (status: pending)
    → PaymentService::createTransaction() → Midtrans Snap token
  → Redirect ke halaman checkout
  → Pelanggan bayar via Midtrans
  → Webhook PaymentConfirmed → NotifyPartnerNewBooking listener
  → Mitra konfirmasi → BookingService::confirm()
  → Sewa selesai → BookingService::markFinished()
    → Status = selesai
    → Partner::creditBalance(total × 95%)
  → Pelanggan bisa submit review
```

### Partner Payout Flow
```
Booking selesai → Partner balance += 95% harga
Partner ajukan withdrawal → WithdrawalService::request()
  → Validasi saldo cukup
  → Debit saldo (kunci dana)
  → Buat Withdrawal (status: pending)
Admin proses → WithdrawalService::approve()
  → Transfer bank (manual/otomatis via Xendit Disbursement)
  → Status: processed
```

---

## Environment Variables Penting

```env
# Database
DB_CONNECTION=mysql
DB_DATABASE=rentalwheels
DB_USERNAME=root
DB_PASSWORD=

# Pusher (real-time chat)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=ap1
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# Midtrans
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SNAP_URL=https://app.sandbox.midtrans.com/snap/snap.js

# Platform fee (default 5%)
PLATFORM_FEE_PERCENT=5
PARTNER_COMMISSION_PERCENT=95
```

---

## Fitur Lengkap

### 👥 Multi-Role
- **Admin** — dashboard statistik, moderasi kendaraan & mitra, manajemen user, laporan, audit log
- **Mitra** — CRUD armada + multi-foto, inbox pemesanan, konfirmasi/tolak, laporan keuangan, penarikan saldo
- **Pelanggan** — cari & filter kendaraan, pesan, bayar online, chat mitra, ulasan

### 💳 Pembayaran
- Midtrans Snap (transfer bank, e-wallet, kartu kredit, QRIS)
- Webhook handler untuk payment confirmation
- Refund otomatis saat booking ditolak/dibatalkan

### 💬 Real-time Chat
- Laravel Echo + Pusher
- Private channel per booking
- Read receipts

### ⭐ Ulasan
- Rating 1–5 bintang + komentar
- Rata-rata rating otomatis diperbarui di vehicle
- Admin bisa hide/show ulasan

### 📊 Laporan
- Tren booking harian (bar chart CSS)
- Revenue per kota
- Top kendaraan terpopuler
- Filter periode 7/30/90/365 hari

### 🔒 Keamanan
- Policy-based authorization (tiap model punya Policy)
- EnsureNotSuspended middleware
- Audit log semua aksi kritis
- CSRF protection
- Force HTTPS di production

---

## Changelog dari Versi Lama

| Area | Sebelum | Sesudah |
|---|---|---|
| Models | Mobil, Pemesanan | Vehicle, Booking (+ Partner, Payment, Review, Message, Withdrawal) |
| Status | String literal | PHP 8.1 Enums |
| Roles | Admin, Customer | Admin, Partner, Customer |
| Pembayaran | Tidak ada | Midtrans Snap + webhook |
| Chat | Tidak ada | Real-time via Laravel Echo |
| Ulasan | Tidak ada | Rating + komentar + avg otomatis |
| Keuangan Mitra | Tidak ada | Saldo, komisi 95%, withdrawal |
| File Storage | Di controller | VehicleService (multi-foto) |
| Authorization | Manual if/abort | Laravel Policies |
| Exception handling | Return langsung | Custom exceptions + Handler |
| Events | Tidak ada | 5 domain events + listeners |
| Testing | Tidak ada | 5 Unit + 4 Feature test classes |
| Audit | Tidak ada | AuditLogService + tabel |
| Laporan | Tidak ada | Dashboard + reports + bar chart |
