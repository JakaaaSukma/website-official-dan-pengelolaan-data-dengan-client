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
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('members', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('members', 'tempat_lahir')) {
                $table->dropColumn('tempat_lahir');
            }
        });
    }
};
