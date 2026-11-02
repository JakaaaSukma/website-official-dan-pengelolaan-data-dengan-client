<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslip_logs', function (Blueprint $table) {
            $table->foreignId('payslip_send_session_id')->nullable()->after('user_id')->constrained('payslip_send_sessions')->nullOnDelete();
            $table->string('period')->nullable()->after('recipient_name');
        });
    }

    public function down(): void
    {
        Schema::table('payslip_logs', function (Blueprint $table) {
            $table->dropForeign(['payslip_send_session_id']);
            $table->dropColumn(['payslip_send_session_id', 'period']);
        });
    }
};
