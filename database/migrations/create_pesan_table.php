<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penerima_id')->constrained('users')->cascadeOnDelete();
            $table->text('isi');
            $table->boolean('dibaca')->default(false);
            $table->timestamps();

            // Index untuk query percakapan antar dua user
            $table->index(['pengirim_id', 'penerima_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesans');
    }
};
