<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@rentwheels.id'],
            [
                'name'     => 'Admin RentWheels',
                'password' => bcrypt('password'),
                'role'     => UserRole::Admin,
                'no_hp'    => '081200000000',
            ]
        );

        // Demo Customer
        User::firstOrCreate(
            ['email' => 'customer@rentwheels.id'],
            [
                'name'     => 'Budi Santoso',
                'password' => bcrypt('password'),
                'role'     => UserRole::Customer,
                'no_hp'    => '081211111111',
            ]
        );
    }
}
