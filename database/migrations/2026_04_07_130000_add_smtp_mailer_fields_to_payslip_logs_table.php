<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslip_logs', function (Blueprint $table) {
            $table->foreignId('smtp_mailer_id')->nullable()->after('payslip_send_session_id')->constrained('smtp_mailers')->nullOnDelete();
            $table->string('smtp_mailer_name')->nullable()->after('smtp_mailer_id');
            $table->string('smtp_mailer_address')->nullable()->after('smtp_mailer_name');
        });
    }

    public function down(): void
    {
        Schema::table('payslip_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('smtp_mailer_id');
            $table->dropColumn(['smtp_mailer_name', 'smtp_mailer_address']);
        });
    }
};
