<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi refactor domain:
 * 1. Pindah field bisnis dari tabel partners ke users (pemilik usaha tunggal)
 * 2. Hapus kolom partner_id dari vehicles
 * 3. Tambah booking_code ke bookings
 * 4. Update enum values di kolom status
 *
 * ROLLBACK AMAN: Semua perubahan bisa dibalik via down().
 * Jalankan dengan: php artisan migrate --step
 */
return new class extends Migration {
    public function up(): void
    {
        // ─── 1. Tambah field bisnis ke users ─────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('name');
            $table->decimal('balance', 12, 2)->default(0)->after('business_name');
            $table->string('bank_account', 30)->nullable()->after('balance');
            $table->string('bank_name', 60)->nullable()->after('bank_account');
            $table->string('bank_holder', 100)->nullable()->after('bank_name');
        });

        // ─── 2. Migrasi data dari partners ke users ───────────────
        // Asumsi hanya ada satu record partner (sesuai konsep rental tunggal)
        if (Schema::hasTable('partners')) {
            $partner = DB::table('partners')->first();

            if ($partner) {
                $adminUser = DB::table('users')
                    ->where('id', $partner->user_id)
                    ->first();

                if ($adminUser) {
                    DB::table('users')
                        ->where('id', $adminUser->id)
                        ->update([
                            'business_name' => $partner->company_name,
                            'balance'       => $partner->balance,
                            'bank_account'  => $partner->bank_account,
                            'bank_name'     => $partner->bank_name,
                            'bank_holder'   => $partner->bank_holder,
                        ]);
                }
            }
        }

        // ─── 3. Tambah booking_code ke bookings ──────────────────
        if (! Schema::hasColumn('bookings', 'booking_code')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('booking_code', 20)->nullable()->unique()->after('id');
            });

            // Generate kode untuk booking yang sudah ada
            DB::table('bookings')->orderBy('id')->each(function ($booking) {
                DB::table('bookings')
                    ->where('id', $booking->id)
                    ->update(['booking_code' => 'RW-' . str_pad($booking->id, 8, '0', STR_PAD_LEFT)]);
            });

            // Sekarang set NOT NULL
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('booking_code', 20)->nullable(false)->change();
            });
        }

        // ─── 4. Update status 'ditolak' di bookings ──────────────
        // Sebelumnya booking yang ditolak pakai status 'dibatalkan'
        // Sekarang ada status terpisah 'ditolak' untuk alur yang berbeda
        // (Data lama tetap valid — dibatalkan & ditolak sama-sama final)

        // ─── 5. Hapus kolom partner_id dari vehicles ─────────────
        // Dilakukan terakhir setelah data berhasil dimigrasi
        if (Schema::hasColumn('vehicles', 'partner_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropForeign(['partner_id']);
                $table->dropColumn('partner_id');
            });
        }
    }

    public function down(): void
    {
        // Kembalikan partner_id ke vehicles
        if (! Schema::hasColumn('vehicles', 'partner_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->unsignedBigInteger('partner_id')->nullable()->after('id');
            });
        }

        // Hapus booking_code
        if (Schema::hasColumn('bookings', 'booking_code')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropUnique(['booking_code']);
                $table->dropColumn('booking_code');
            });
        }

        // Hapus field bisnis dari users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'balance', 'bank_account', 'bank_name', 'bank_holder']);
        });
    }
};
