<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_rate_change_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('field_name', 50);
            $table->decimal('old_value', 6, 4)->nullable();
            $table->decimal('new_value', 6, 4);
            $table->date('effective_from');
            $table->unsignedBigInteger('changed_by');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('changed_by');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_rate_change_logs');
    }
};
