<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Tandai semua akun lama (email_verified_at = null) sebagai terverifikasi.
     *
     * Alasan: fitur verifikasi email baru diaktifkan pada Juni 2026.
     * Akun yang dibuat sebelum tanggal ini diasumsikan valid karena
     * sudah pernah digunakan secara aktif.
     */
    public function up(): void
    {
        User::whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    /**
     * Tidak bisa di-rollback — kita tidak tahu mana yang
     * memang belum verified vs yang baru saja di-set.
     */
    public function down(): void
    {
        // intentionally empty
    }
};
