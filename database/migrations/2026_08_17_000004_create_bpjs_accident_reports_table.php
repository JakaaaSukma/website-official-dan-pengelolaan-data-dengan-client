<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_accident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number', 30)->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('hr_contract_id');
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->string('incident_location', 255);
            $table->longText('chronology');
            $table->enum('accident_category', ['ringan', 'sedang', 'berat'])->default('ringan');
            $table->longText('action_taken')->nullable();
            $table->enum('handling_status', ['dilaporkan', 'dalam_proses', 'selesai', 'klaim_diajukan'])->default('dilaporkan');
            $table->json('documents')->nullable();
            $table->unsignedBigInteger('reported_by');
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('hr_contract_id');
            $table->index('incident_date');
            $table->index('accident_category');
            $table->index('handling_status');
            $table->index(['company_id', 'incident_date']);
            $table->index(['company_id', 'handling_status']);
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('hr_contract_id')->references('id')->on('hr_contracts')->onDelete('cascade');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_accident_reports');
    }
};
