<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->boolean('is_followed_up')->default(false)->after('kontrak_habis');
            $table->timestamp('followed_up_at')->nullable()->after('is_followed_up');
            $table->text('followup_notes')->nullable()->after('followed_up_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['is_followed_up', 'followed_up_at', 'followup_notes']);
        });
    }
};
