<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smtp_mailers', function (Blueprint $table) {
            $table->id();
            $table->string('mailer_name')->unique();
            $table->string('from_address')->unique();
            $table->string('from_name');
            $table->string('smtp_host');
            $table->unsignedSmallInteger('smtp_port')->default(587);
            $table->string('smtp_encryption', 20)->default('tls');
            $table->string('smtp_username')->unique();
            $table->text('smtp_password');
            $table->string('status', 20)->default('active')->index();
            $table->unsignedInteger('daily_limit')->default(400);
            $table->unsignedInteger('daily_sent_count')->default(0);
            $table->unsignedSmallInteger('priority')->default(1)->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('last_limit_reached_at')->nullable();
            $table->timestamp('cooldown_until')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smtp_mailers');
    }
};
