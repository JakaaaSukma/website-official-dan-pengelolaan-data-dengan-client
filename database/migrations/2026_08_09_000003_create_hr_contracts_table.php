<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('employee_id', 50)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('department', 100);
            $table->string('job_title', 150);
            $table->enum('work_type', ['on_site', 'remote', 'hybrid'])->default('on_site');
            $table->enum('contract_type', ['internship', 'full_time', 'part_time', 'contract', 'probation'])->default('internship');
            $table->date('contract_start');
            $table->date('contract_end');
            $table->decimal('salary', 15, 2)->nullable();
            $table->text('address')->nullable();
            $table->string('avatar_path', 255)->nullable();
            $table->enum('status', ['active', 'expired', 'extended', 'offboarded', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('company_id');
            $table->index('status');
            $table->index('contract_end');
            $table->index('department');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_contracts');
    }
};
