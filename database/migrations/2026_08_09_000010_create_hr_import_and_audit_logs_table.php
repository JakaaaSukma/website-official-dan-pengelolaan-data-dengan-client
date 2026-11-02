<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_import_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('import_type', 50);
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->integer('total_rows')->default(0);
            $table->integer('success_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->text('error_details')->nullable();
            $table->text('validation_report')->nullable();
            $table->enum('status', ['pending', 'validating', 'processing', 'completed', 'failed', 'rollback'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index('company_id');
            $table->index('user_id');
            $table->index('import_type');
        });

        Schema::create('hr_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('action', 100);
            $table->string('entity_type', 100);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->timestamp('created_at');
            $table->index('user_id');
            $table->index('company_id');
            $table->index('action');
            $table->index('entity_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_audit_logs');
        Schema::dropIfExists('hr_import_logs');
    }
};
