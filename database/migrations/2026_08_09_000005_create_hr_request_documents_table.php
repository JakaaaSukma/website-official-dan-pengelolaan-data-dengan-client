<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_request_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->string('document_name', 150);
            $table->string('document_type', 50);
            $table->string('file_path', 255);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            $table->foreign('request_id')->references('id')->on('hr_requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_request_documents');
    }
};
