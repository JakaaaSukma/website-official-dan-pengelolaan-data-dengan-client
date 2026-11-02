<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_violation_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('violation_report_id')->index();
            $table->enum('old_status', [
                'pending_review',
                'contacted',
                'in_process',
                'resolved',
                'rejected'
            ])->nullable();
            $table->enum('new_status', [
                'pending_review',
                'contacted',
                'in_process',
                'resolved',
                'rejected'
            ])->index();
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->timestamps();

            $table->index(['violation_report_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_violation_status_logs');
    }
};
