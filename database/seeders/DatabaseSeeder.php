<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Regular User (role default: 'pelanggan')
        User::factory()->create([
            'name'              => 'Test User',
            'email'             => 'test@example.com',
            'password'          => Hash::make('password123'),
            'role'              => 'pelanggan',
            'email_verified_at' => now(), // ✅ Wajib karena User implements MustVerifyEmail
        ]);

        // Admin User
        User::factory()->create([
            'name'              => 'Admin User',
            'email'             => 'admin@example.com',
            'password'          => Hash::make('admin123'),
            'role'              => 'admin',
            'email_verified_at' => now(), // ✅ Wajib agar admin bisa langsung login
        ]);
    }
}