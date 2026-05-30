<?php
// 2026_05_23_060001_create_vehicles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category', 30)->index();
            $table->string('brand', 80);
            $table->string('model', 80);
            $table->year('year');
            $table->string('plate_number', 20)->unique();
            $table->decimal('price_per_day', 10, 2);
            $table->decimal('deposit', 10, 2)->default(0);
            $table->unsignedTinyInteger('capacity')->default(4);
            $table->string('transmission', 10)->default('matic'); // matic|manual
            $table->string('fuel_type', 15)->default('bensin');   // bensin|diesel|listrik
            $table->text('description')->nullable();
            $table->text('rental_terms')->nullable();
            $table->json('features')->nullable();                  // ['ac','gps','baby_seat']
            $table->unsignedTinyInteger('min_rental_days')->default(1);
            $table->unsignedTinyInteger('max_rental_days')->default(30);
            $table->string('city', 80)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_verified')->default(false)->index();
            $table->timestamp('verified_at')->nullable();
            $table->decimal('avg_rating', 3, 2)->nullable();
            $table->unsignedInteger('review_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Composite index for search
            $table->index(['is_active', 'is_verified', 'category', 'city'], 'idx_vehicle_search');
            $table->index(['is_active', 'is_verified', 'price_per_day'], 'idx_vehicle_price');
        });
    }
    public function down(): void { Schema::dropIfExists('vehicles'); }
};