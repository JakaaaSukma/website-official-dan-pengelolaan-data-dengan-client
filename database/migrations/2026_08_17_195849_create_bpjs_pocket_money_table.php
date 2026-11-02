<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_pocket_moneys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hr_contract_id');
            $table->unsignedBigInteger('company_id');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('amount', 15, 2)->default(0);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Prevent duplicate pocket money entry for the same participant in the same month/year
            $table->unique(['hr_contract_id', 'month', 'year'], 'bpjs_pocket_money_unique');
            
            $table->index('company_id');
            $table->foreign('hr_contract_id')->references('id')->on('hr_contracts')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_pocket_moneys');
    }
};
