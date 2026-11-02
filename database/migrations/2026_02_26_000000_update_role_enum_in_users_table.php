<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'company', 'finance') NOT NULL DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily remove 'finance' if there are users with that role,
        // but for rollback purposes we revert the definition.
        // Warning: This might fail if there are 'finance' users.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'company') NOT NULL DEFAULT 'admin'");
    }
};
