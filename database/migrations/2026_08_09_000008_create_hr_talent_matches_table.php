<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_talent_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('talent_id');
            $table->unsignedBigInteger('client_need_id');
            $table->unsignedBigInteger('company_id');
            $table->decimal('match_score', 5, 2)->default(0);
            $table->text('match_details')->nullable();
            $table->enum('status', ['suggested', 'reviewed', 'recommended', 'hired', 'rejected'])->default('suggested');
            $table->unsignedBigInteger('matched_by')->nullable();
            $table->timestamp('matched_at')->nullable();
            $table->timestamps();
            $table->index('talent_id');
            $table->index('client_need_id');
            $table->index('company_id');
            $table->foreign('talent_id')->references('id')->on('hr_talents')->onDelete('cascade');
            $table->foreign('client_need_id')->references('id')->on('hr_client_needs')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_talent_matches');
    }
};
