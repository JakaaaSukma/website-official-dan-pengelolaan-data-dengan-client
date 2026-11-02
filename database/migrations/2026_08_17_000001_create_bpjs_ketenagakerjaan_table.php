<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_ketenagakerjaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hr_contract_id')->unique();
            $table->unsignedBigInteger('company_id');
            $table->enum('bpjs_status', ['aktif', 'tidak_aktif'])->default('tidak_aktif');
            $table->string('bpjs_number', 16)->nullable();
            $table->date('bpjs_join_date')->nullable();
            $table->string('nomor_referensi', 50)->nullable();
            $table->string('nomor_pegawai', 50)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('bpjs_status');
            $table->foreign('hr_contract_id')->references('id')->on('hr_contracts')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_ketenagakerjaan');
    }
};
