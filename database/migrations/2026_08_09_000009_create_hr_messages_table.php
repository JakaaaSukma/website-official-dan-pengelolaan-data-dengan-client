<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->enum('message_type', ['request_access', 'quota_request', 'account_issue', 'performance_report', 'general'])->default('general');
            $table->string('subject', 255);
            $table->text('body');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['unread', 'read', 'replied', 'resolved', 'closed'])->default('unread');
            $table->text('reply')->nullable();
            $table->unsignedBigInteger('replied_by')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();
            $table->index('company_id');
            $table->index('status');
            $table->index('message_type');
            $table->foreign('company_id')->references('id')->on('clients')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_messages');
    }
};
