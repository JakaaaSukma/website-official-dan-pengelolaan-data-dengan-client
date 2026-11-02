<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'account_status')) {
                $table->enum('account_status', ['active', 'pending_review', 'suspended'])->default('active')->after('company_id');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('account_status');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('phone');
            }
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'company', 'finance', 'hrd_company', 'super_admin_recruitment') NOT NULL DEFAULT 'admin'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'account_status', 'phone', 'last_login_at']);
        });
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'company', 'finance') NOT NULL DEFAULT 'admin'");
    }
};
