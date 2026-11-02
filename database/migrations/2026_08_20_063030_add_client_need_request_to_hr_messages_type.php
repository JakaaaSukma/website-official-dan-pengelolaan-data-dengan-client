<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE hr_messages MODIFY COLUMN message_type ENUM(
            'request_access',
            'quota_request',
            'account_issue',
            'performance_report',
            'general',
            'client_need_request',
            'contract_request',
            'talent_call_request',
            'system_security',
            'violation_report',
            'violation_status_update',
            'accident_report'
        ) DEFAULT 'general'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE hr_messages MODIFY COLUMN message_type ENUM('request_access', 'quota_request', 'account_issue', 'performance_report', 'general') DEFAULT 'general'");
    }
};
