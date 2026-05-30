<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Tersedia   = 'tersedia';
    case Disewa     = 'disewa';
    case Perawatan  = 'perawatan';
    case Nonaktif   = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia  => 'Tersedia',
            self::Disewa    => 'Disewa',
            self::Perawatan => 'Perawatan',
            self::Nonaktif  => 'Nonaktif',
        };
    }
}