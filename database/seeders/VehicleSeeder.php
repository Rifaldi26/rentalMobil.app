<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\VehicleCategory;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@rentwheels.id')->first();
        $adminId = $admin->id;

        $vehicles = [
            [
                'brand'         => 'Toyota',
                'model'         => 'Avanza',
                'year'          => 2022,
                'plate_number'  => 'B 1234 RW1',
                'price_per_day' => 350000,
                'city'          => 'Jakarta Selatan',
                'category'      => VehicleCategory::MPV->value,
            ],
            [
                'brand'         => 'Honda',
                'model'         => 'Brio',
                'year'          => 2023,
                'plate_number'  => 'B 5678 RW2',
                'price_per_day' => 280000,
                'city'          => 'Jakarta Pusat',
                'category'      => VehicleCategory::Mobil->value,
            ],
            [
                'brand'         => 'Toyota',
                'model'         => 'Innova',
                'year'          => 2022,
                'plate_number'  => 'B 9012 RW3',
                'price_per_day' => 550000,
                'city'          => 'Jakarta Selatan',
                'category'      => VehicleCategory::MPV->value,
            ],
        ];

        foreach ($vehicles as $data) {
            Vehicle::firstOrCreate(
                ['plate_number' => $data['plate_number']],
                array_merge($data, [
                    'user_id'         => $adminId,
                    'deposit'         => 200000,
                    'capacity'        => 5,
                    'transmission'    => 'matic',
                    'fuel_type'       => 'bensin',
                    'description'     => 'Kendaraan terawat, AC dingin, siap jalan. Cocok untuk perjalanan keluarga maupun bisnis.',
                    'min_rental_days' => 1,
                    'max_rental_days' => 30,
                    'is_active'       => true,
                    'is_verified'     => true,
                    'verified_at'     => now(),
                    'features'        => ['ac', 'music', 'usb_charger'],
                ])
            );
        }
    }
}