<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_suspension_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('intern_id')->index();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->longText('reason');
            $table->enum('status', [
                'pending_approval',
                'under_review',
                'approved',
                'rejected'
            ])->default('pending_approval')->index();
            $table->unsignedBigInteger('handled_by')->nullable()->index();
            $table->timestamp('handled_at')->nullable();
            $table->longText('resolution_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['company_id', 'status']);
            $table->index(['intern_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_suspension_requests');
    }
};
