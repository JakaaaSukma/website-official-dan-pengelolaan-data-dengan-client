<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('job_title', 150);
            $table->string('department', 100)->nullable();
            $table->decimal('years_experience', 5, 2)->default(0);
            $table->text('education')->nullable();
            $table->text('skills')->nullable();
            $table->enum('status', ['new', 'interviewing', 'offer_sent', 'hired', 'rejected', 'passive'])->default('new');
            $table->string('cv_path', 255)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('company_id');
            $table->index('status');
            $table->index('job_title');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_candidates');
    }
};
