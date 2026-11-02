<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_rate_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->decimal('jkk_rate', 6, 4)->default(0.2400);
            $table->decimal('jkm_rate', 6, 4)->default(0.3000);
            $table->decimal('jht_employer_rate', 6, 4)->default(0.0000);
            $table->decimal('jht_employee_rate', 6, 4)->default(0.0000);
            $table->decimal('jp_employer_rate', 6, 4)->default(0.0000);
            $table->decimal('jp_employee_rate', 6, 4)->default(0.0000);
            $table->decimal('jkp_employer_rate', 6, 4)->default(0.0000);
            $table->decimal('jkp_government_rate', 6, 4)->default(0.0000);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('effective_from');
            $table->unique(['company_id', 'effective_from']);
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_rate_settings');
    }
};
