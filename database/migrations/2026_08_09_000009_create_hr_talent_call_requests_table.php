<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_talent_call_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('talent_id')->index();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->enum('call_type', ['test', 'interview', 'other'])->default('interview')->index();
            $table->longText('notes')->nullable();
            $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled'])->default('pending')->index();
            $table->unsignedBigInteger('handled_by')->nullable()->index();
            $table->timestamp('handled_at')->nullable();
            $table->longText('reply_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['talent_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_talent_call_requests');
    }
};
