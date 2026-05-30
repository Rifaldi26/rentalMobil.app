<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('total_harga', 10, 2);
            $table->enum('status', ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'])
                  ->default('pending')
                  ->index();
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Composite index untuk query konflik tanggal (performa)
            $table->index(['mobil_id', 'status', 'tanggal_mulai', 'tanggal_selesai'], 'idx_pemesanan_konflik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
