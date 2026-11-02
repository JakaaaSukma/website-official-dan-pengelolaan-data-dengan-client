<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_violation_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('intern_id')->index();
            $table->unsignedBigInteger('reported_by')->nullable()->index();
            $table->date('incident_date')->index();
            $table->time('incident_time')->nullable();
            $table->longText('description');
            $table->longText('proposed_sanction')->nullable();
            $table->string('evidence_file', 255)->nullable();
            $table->enum('status', [
                'pending_review',
                'contacted',
                'in_process',
                'resolved',
                'rejected'
            ])->default('pending_review')->index();
            $table->unsignedBigInteger('handled_by')->nullable()->index();
            $table->timestamp('handled_at')->nullable();
            $table->longText('resolution_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['company_id', 'status']);
            $table->index(['intern_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_violation_reports');
    }
};
