<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partnerUser = User::firstOrCreate(
            ['email' => 'mitra@rentwheels.id'],
            [
                'name'     => 'Budi Kendaraan',
                'password' => bcrypt('password'),
                'role'     => UserRole::Partner,
                'no_hp'    => '081222222222',
            ]
        );

        Partner::firstOrCreate(
            ['user_id' => $partnerUser->id],
            [
                'company_name' => 'CV Budi Rental',
                'balance'      => 1_500_000,
                'bank_account' => '1234567890',
                'bank_name'    => 'BCA',
                'bank_holder'  => 'Budi Kendaraan',
                'is_verified'  => true,
                'verified_at'  => now(),
            ]
        );
    }
}
