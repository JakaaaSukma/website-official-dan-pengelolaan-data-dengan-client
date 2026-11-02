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
            // Remove old constraints and columns
            try {
                if (Schema::hasColumn('members', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('members', 'position')) {
                    $table->dropColumn('position');
                }
                if (Schema::hasColumn('members', 'address')) {
                    $table->dropColumn('address');
                }
            } catch (\Exception $e) {
                // Ignore errors if columns don't exist
            }

            // Add new columns
            if (!Schema::hasColumn('members', 'reference')) {
                $table->string('reference')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('members', 'tanggal_daftar')) {
                $table->date('tanggal_daftar')->nullable()->after('reference');
            }
            
            if (!Schema::hasColumn('members', 'nik_ktp')) {
                $table->string('nik_ktp')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('members', 'gender')) {
                $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable()->after('tanggal_lahir');
            }
            
            if (!Schema::hasColumn('members', 'status')) {
                $table->enum('status', ['sudah_kerja', 'gagal', 'belum_tes', 'sudah_training', 'belum_training'])->default('belum_tes')->after('gender');
            }
            
            if (!Schema::hasColumn('members', 'source')) {
                $table->string('source')->default('local')->comment('local or google_sheet')->after('status');
            }
            
            if (!Schema::hasColumn('members', 'google_sheet_row_id')) {
                $table->string('google_sheet_row_id')->nullable()->after('source')->comment('Row ID from Google Sheet if source is google_sheet');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'reference',
                'tanggal_daftar',
                'nik_ktp',
                'gender',
                'status',
                'source',
                'google_sheet_row_id'
            ]);
        });
    }
};
