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
            // Drop old column and add new column
            $table->dropColumn('no_hp');
            $table->string('nik', 20)->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Drop new column and restore old column
            $table->dropColumn('nik');
            $table->string('no_hp', 20)->after('nama');
        });
    }
};
