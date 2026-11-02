<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->string('request_number', 50)->unique();
            $table->enum('request_type', ['add_contract', 'extend_contract', 'off_boarding']);
            $table->enum('status', ['draft', 'submitted', 'in_review', 'approved', 'rejected', 'completed'])->default('draft');
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->unsignedTinyInteger('total_steps')->default(3);

            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('national_id', 50)->nullable();

            $table->enum('contract_type', ['internship', 'full_time', 'part_time', 'contract', 'probation'])->nullable();
            $table->string('department', 100)->nullable();
            $table->string('job_title', 150)->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();

            $table->date('off_boarding_date')->nullable();
            $table->text('off_boarding_reason')->nullable();

            $table->text('documents_json')->nullable();
            $table->text('additional_data')->nullable();

            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('request_type');
            $table->index('status');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('hr_contracts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_requests');
    }
};
