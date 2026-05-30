<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case Pending   = 'pending';
    case Approved = 'approved';
    case Rejected  = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Menunggu Proses',
            self::Approved => 'Disetujui',
            self::Rejected  => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending   => 'yellow',
            self::Approved => 'green',
            self::Rejected  => 'red',
        };
    }
}