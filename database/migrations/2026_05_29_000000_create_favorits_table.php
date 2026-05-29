<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mobil_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Satu user hanya bisa favoritkan satu mobil satu kali
            $table->unique(['user_id', 'mobil_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorits');
    }
};