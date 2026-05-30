<?php

namespace Database\Seeders;

use App\Enums\MobilStatus;
use App\Models\Mobil;
use Illuminate\Database\Seeder;

class MobilSeeder extends Seeder
{
    public function run(): void
    {
        $mobils = [
            [
                'nama'           => 'Avanza G',
                'merek'          => 'Toyota',
                'tahun'          => 2022,
                'plat_nomor'     => 'B 1234 ABC',
                'harga_per_hari' => 350_000,
                'status'         => MobilStatus::Tersedia,
                'deskripsi'      => 'Toyota Avanza G 2022, AC, tape, kapasitas 7 penumpang.',
            ],
            [
                'nama'           => 'Brio Satya S',
                'merek'          => 'Honda',
                'tahun'          => 2023,
                'plat_nomor'     => 'B 5678 DEF',
                'harga_per_hari' => 280_000,
                'status'         => MobilStatus::Tersedia,
                'deskripsi'      => 'Honda Brio Satya S 2023, irit BBM, cocok untuk dalam kota.',
            ],
            [
                'nama'           => 'Xpander Cross',
                'merek'          => 'Mitsubishi',
                'tahun'          => 2023,
                'plat_nomor'     => 'B 9012 GHI',
                'harga_per_hari' => 450_000,
                'status'         => MobilStatus::Tersedia,
                'deskripsi'      => 'Mitsubishi Xpander Cross 2023, SUV MPV, kapasitas 7 penumpang.',
            ],
        ];

        foreach ($mobils as $data) {
            Mobil::firstOrCreate(['plat_nomor' => $data['plat_nomor']], $data);
        }
    }
}
