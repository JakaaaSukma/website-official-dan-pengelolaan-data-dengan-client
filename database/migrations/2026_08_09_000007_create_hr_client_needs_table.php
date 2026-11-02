<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_client_needs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('job_title', 150);
            $table->string('department', 100)->nullable();
            $table->text('required_skills')->nullable();
            $table->text('skill_tags')->nullable();
            $table->integer('quota')->default(1);
            $table->enum('urgency', ['urgent', 'open', 'low'])->default('open');
            $table->enum('status', ['open', 'in_progress', 'fulfilled', 'closed'])->default('open');
            $table->text('description')->nullable();
            $table->date('deadline')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('company_id');
            $table->index('status');
            $table->index('urgency');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_client_needs');
    }
};
