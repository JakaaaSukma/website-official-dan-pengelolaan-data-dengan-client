<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_add_contract_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number', 50)->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('created_by');
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'completed'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
            $table->index(['company_id', 'status']);
            $table->index('batch_number');
        });

        Schema::create('hr_candidate_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('national_id', 50);
            $table->date('birth_date')->nullable();
            $table->string('job_title', 150);
            $table->text('address')->nullable();
            $table->enum('selection_status', ['pending', 'selected', 'not_selected'])->default('pending');
            $table->unsignedBigInteger('resulting_contract_id')->nullable();
            $table->timestamps();
            $table->foreign('batch_id')->references('id')->on('hr_add_contract_batches')->onDelete('cascade');
            $table->foreign('resulting_contract_id')->references('id')->on('hr_contracts')->onDelete('set null');
            $table->unique(['batch_id', 'national_id']);
        });

        Schema::create('hr_batch_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('contract_id');
            $table->date('new_contract_end')->nullable();
            $table->text('notes')->nullable();
            $table->date('off_boarding_effective_date')->nullable();
            $table->text('off_boarding_reason')->nullable();
            $table->timestamps();
            $table->foreign('request_id')->references('id')->on('hr_requests')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('hr_contracts')->onDelete('cascade');
            $table->index(['request_id', 'contract_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_batch_items');
        Schema::dropIfExists('hr_candidate_items');
        Schema::dropIfExists('hr_add_contract_batches');
    }
};
