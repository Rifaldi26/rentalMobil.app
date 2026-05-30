<?php
// 2026_05_23_060003_add_partner_fields_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('no_hp');
            $table->string('ktp_path')->nullable()->after('avatar');
            $table->boolean('is_suspended')->default(false)->after('ktp_path');
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'ktp_path', 'is_suspended']);
        });
    }
};