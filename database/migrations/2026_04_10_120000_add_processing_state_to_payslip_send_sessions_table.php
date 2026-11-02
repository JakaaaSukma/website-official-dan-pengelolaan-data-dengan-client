<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslip_send_sessions', function (Blueprint $table) {
            $table->string('temp_dir_path')->nullable()->after('status');
            $table->json('pending_queue')->nullable()->after('temp_dir_path');
        });
    }

    public function down(): void
    {
        Schema::table('payslip_send_sessions', function (Blueprint $table) {
            $table->dropColumn(['temp_dir_path', 'pending_queue']);
        });
    }
};
