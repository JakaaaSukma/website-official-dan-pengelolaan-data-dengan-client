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
        // Update the ENUM column to include new statuses
        DB::statement("ALTER TABLE member_statuses MODIFY COLUMN status ENUM('sudah_kerja', 'gagal', 'belum_tes', 'sudah_training', 'belum_training') NOT NULL DEFAULT 'belum_tes'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values (be careful if data exists with new statuses)
        // We generally don't want to lose data, so we might skip reverting strictly or map them back.
        // For now, we'll keep the new values in down() to avoid data loss, or just comment it out.
        // But strictly speaking, down() should reverse up().
        // If we revert, rows with 'sudah_training' might become invalid or empty.
        // Let's just allow them to remain for safety in this specific context, 
        // or effectively do nothing if we don't want to risk data loss.
        
        // However, to be correct:
        // DB::statement("ALTER TABLE member_statuses MODIFY COLUMN status ENUM('sudah_kerja', 'gagal', 'belum_tes') NOT NULL DEFAULT 'belum_tes'");
    }
};
