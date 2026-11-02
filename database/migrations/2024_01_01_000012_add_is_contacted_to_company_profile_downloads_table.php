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
        Schema::table('company_profile_downloads', function (Blueprint $table) {
            $table->boolean('is_contacted')->default(false)->after('contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_profile_downloads', function (Blueprint $table) {
            $table->dropColumn('is_contacted');
        });
    }
};

