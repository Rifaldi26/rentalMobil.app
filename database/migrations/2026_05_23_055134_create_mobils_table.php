<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('merek', 100);
            $table->year('tahun');
            $table->string('plat_nomor', 20)->unique();
            $table->decimal('harga_per_hari', 10, 2);
            $table->enum('status', ['tersedia', 'disewa'])->default('tersedia')->index();
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Agar data historis booking tidak hilang saat mobil dihapus
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobils');
    }
};
