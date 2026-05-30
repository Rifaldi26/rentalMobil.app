<?php
// 2026_05_23_060002_create_vehicle_photos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
            $table->index(['vehicle_id', 'is_primary']);
        });

        Schema::create('vehicle_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->date('blocked_date');
            $table->string('reason', 100)->nullable();
            $table->timestamps();
            $table->unique(['vehicle_id', 'blocked_date']);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_price', 10, 2);
            $table->decimal('deposit', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->string('status', 20)->default('pending')->index();
            $table->string('payment_status', 20)->default('pending')->index();
            $table->string('pickup_location')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamps();
            // For conflict detection
            $table->index(['vehicle_id', 'status', 'start_date', 'end_date'], 'idx_booking_conflict');
            // For user history
            $table->index(['user_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('gateway_ref')->nullable()->unique();
            $table->string('gateway_name', 30)->default('midtrans');
            $table->string('method', 30)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('status', 20)->default('pending')->index();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
            $table->index(['vehicle_id', 'is_visible', 'rating']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['booking_id', 'created_at']);
            $table->index(['receiver_id', 'read_at']);
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('status', 20)->default('pending')->index();
            $table->string('bank_account', 30);
            $table->string('bank_name', 60);
            $table->string('bank_holder', 100);
            $table->text('admin_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 80)->index();
            $table->string('model_type', 100)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('vehicle_availabilities');
        Schema::dropIfExists('vehicle_photos');
    }
};