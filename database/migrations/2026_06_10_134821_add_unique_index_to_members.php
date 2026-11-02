<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if index exists on table
     */
    protected function indexExists($table, $indexName): bool
    {
        $table = DB::connection()->getTablePrefix() . $table;
        try {
            $indexes = DB::select("SHOW INDEX FROM `$table` WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip if both indexes already exist
        $index1Exists = $this->indexExists('members', 'members_google_sheet_row_id_unique');
        $index2Exists = $this->indexExists('members', 'members_name_nik_ktp_unique');
        
        if ($index1Exists && $index2Exists) {
            return;
        }
        
        // Add unique index on google_sheet_row_id if not exists
        if (!$index1Exists) {
            try {
                Schema::table('members', function (Blueprint $table) {
                    $table->unique('google_sheet_row_id', 'members_google_sheet_row_id_unique');
                });
            } catch (\Exception $e) {
                // Ignore if index already exists or there are duplicate google_sheet_row_id entries
            }
        }
        
        // Add unique index on name + nik_ktp if not exists
        if (!$index2Exists) {
            try {
                Schema::table('members', function (Blueprint $table) {
                    $table->unique(['name', 'nik_ktp'], 'members_name_nik_ktp_unique');
                });
            } catch (\Exception $e) {
                // Ignore if index already exists or there are still duplicates
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->indexExists('members', 'members_google_sheet_row_id_unique')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropUnique('members_google_sheet_row_id_unique');
            });
        }
        
        if ($this->indexExists('members', 'members_name_nik_ktp_unique')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropUnique('members_name_nik_ktp_unique');
            });
        }
    }
};
