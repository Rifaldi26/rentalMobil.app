<?php

namespace App\Enums;

/**
 * Role pengguna dalam sistem rental tunggal.
 *
 * Admin    = pemilik usaha / operator — mengelola kendaraan & operasional
 * Customer = pelanggan yang menyewa kendaraan
 *
 * PERUBAHAN: Role "Partner" dihapus. Sistem ini bukan marketplace multi-mitra.
 * Semua kendaraan adalah milik satu usaha rental.
 * isPartner() dipertahankan sebagai alias sementara untuk migrasi bertahap.
 */
enum UserRole: string
{
    case Admin    = 'admin';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Admin    => 'Administrator',
            self::Customer => 'Pelanggan',
        };
    }

    public function isAdmin(): bool    { return $this === self::Admin; }
    public function isCustomer(): bool { return $this === self::Customer; }
}