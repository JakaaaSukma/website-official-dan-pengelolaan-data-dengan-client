<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_talents', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('current_job_title', 150)->nullable();
            $table->string('target_role', 150);
            $table->decimal('years_experience', 5, 2)->default(0);
            $table->text('education')->nullable();
            $table->text('skills')->nullable();
            $table->text('skill_tags')->nullable();
            $table->decimal('match_score', 5, 2)->nullable();
            $table->enum('status', ['ready_to_hire', 'in_process', 'passive', 'hired', 'not_available'])->default('ready_to_hire');
            $table->enum('availability_status', ['available', 'notice_period', 'employed'])->default('available');
            $table->string('cv_path', 255)->nullable();
            $table->string('location', 150)->nullable();
            $table->decimal('expected_salary', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->index('target_role');
            $table->index('availability_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_talents');
    }
};
