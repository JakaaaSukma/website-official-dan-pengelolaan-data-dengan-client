<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_suspension_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suspension_request_id')->index();
            $table->enum('old_status', [
                'pending_approval',
                'under_review',
                'approved',
                'rejected'
            ])->nullable();
            $table->enum('new_status', [
                'pending_approval',
                'under_review',
                'approved',
                'rejected'
            ])->index();
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->timestamps();

            $table->index(['suspension_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_suspension_status_logs');
    }
};
