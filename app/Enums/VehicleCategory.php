<?php

namespace App\Enums;

enum VehicleCategory: string
{
    case Mobil   = 'mobil';
    case Motor   = 'motor';
    case MPV     = 'mpv';
    case SUV     = 'suv';
    case Minibus = 'minibus';
    case Truk    = 'truk';

    public function label(): string
    {
        return match ($this) {
            self::Mobil   => 'Mobil',
            self::Motor   => 'Motor',
            self::MPV     => 'MPV',
            self::SUV     => 'SUV',
            self::Minibus => 'Minibus',
            self::Truk    => 'Truk',
        };
    }
}
