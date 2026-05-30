<?php

namespace App\Enums;

/**
 * Status siklus hidup pemesanan.
 *
 * Alur normal : Pending → Dikonfirmasi → Aktif → Selesai
 * Alur batal  : Pending → Dibatalkan (oleh pelanggan)
 * Alur tolak  : Pending → Ditolak (oleh admin, langsung refund)
 *
 * PERUBAHAN dari PemesananStatus:
 * - Tambah Aktif (kendaraan sudah diambil pelanggan)
 * - Tambah Ditolak (terpisah dari Dibatalkan agar bisa beda alur refund)
 * - Tambah conflictStatuses() untuk cek ketersediaan
 */
enum BookingStatus: string
{
    case Pending      = 'pending';
    case Dikonfirmasi = 'dikonfirmasi';
    case Aktif        = 'aktif';
    case Selesai      = 'selesai';
    case Dibatalkan   = 'dibatalkan';
    case Ditolak      = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Pending      => 'Menunggu Konfirmasi',
            self::Dikonfirmasi => 'Dikonfirmasi',
            self::Aktif        => 'Aktif / Dalam Perjalanan',
            self::Selesai      => 'Selesai',
            self::Dibatalkan   => 'Dibatalkan',
            self::Ditolak      => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending      => 'yellow',
            self::Dikonfirmasi => 'blue',
            self::Aktif        => 'indigo',
            self::Selesai      => 'green',
            self::Dibatalkan,
            self::Ditolak      => 'red',
        };
    }

    // ─── Transition Guards ────────────────────────────────────────

    public function canBeCancelledByUser(): bool { return $this === self::Pending; }
    public function canBeConfirmed(): bool        { return $this === self::Pending; }
    public function canBeRejected(): bool         { return $this === self::Pending; }
    public function canBeActivated(): bool        { return $this === self::Dikonfirmasi; }
    public function canBeFinished(): bool         { return $this === self::Aktif; }

    public function isFinal(): bool
    {
        return in_array($this, [self::Selesai, self::Dibatalkan, self::Ditolak], true);
    }

    /**
     * Status yang memblokir ketersediaan kendaraan pada cek konflik tanggal.
     * Booking dengan status ini mencegah booking baru di rentang tanggal yang sama.
     */
    public static function conflictStatuses(): array
    {
        return [self::Pending, self::Dikonfirmasi, self::Aktif];
    }
}
