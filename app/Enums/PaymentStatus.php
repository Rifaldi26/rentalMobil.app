<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Paid       = 'paid';
    case Failed     = 'failed';
    case Expired    = 'expired';
    case Refunded   = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'Menunggu Pembayaran',
            self::Processing => 'Diproses',
            self::Paid       => 'Lunas',
            self::Failed     => 'Gagal',
            self::Expired    => 'Kedaluwarsa',
            self::Refunded   => 'Dikembalikan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending    => 'yellow',
            self::Processing => 'blue',
            self::Paid       => 'green',
            self::Failed     => 'red',
            self::Expired    => 'gray',
            self::Refunded   => 'purple',
        };
    }

    public function isPaid(): bool     { return $this === self::Paid; }
    public function isPending(): bool  { return in_array($this, [self::Pending, self::Processing], true); }
    public function isFailed(): bool   { return in_array($this, [self::Failed, self::Expired], true); }
    public function isRefunded(): bool { return $this === self::Refunded; }
}
