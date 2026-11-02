<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_candidate_items', function (Blueprint $table) {
            $table->date('contract_start')->nullable()->after('address');
            $table->date('contract_end')->nullable()->after('contract_start');
            $table->unsignedSmallInteger('contract_duration_months')->nullable()->after('contract_end');
        });
    }

    public function down(): void
    {
        Schema::table('hr_candidate_items', function (Blueprint $table) {
            $table->dropColumn(['contract_start', 'contract_end', 'contract_duration_months']);
        });
    }
};
