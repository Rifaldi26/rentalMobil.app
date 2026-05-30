<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah dari ENUM jadi VARCHAR agar fleksibel sesuai UserRole enum PHP
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) NOT NULL DEFAULT 'customer'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pelanggan', 'admin') NOT NULL DEFAULT 'pelanggan'");
    }
};