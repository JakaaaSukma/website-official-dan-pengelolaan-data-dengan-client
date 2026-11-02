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
        Schema::create('non_member_applicants', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('domisili');
            $table->string('pendidikan_terakhir', 20); // D1, D2, D3, D4, S1, S2, SMA, SMK, SMP, SD
            $table->string('jurusan_prodi')->nullable();
            $table->string('ipk_nilai', 20)->nullable();
            $table->string('nama_institusi'); // Nama Universitas / Sekolah
            $table->enum('tipe_pengalaman', ['pengalaman', 'non_pengalaman'])->default('non_pengalaman');
            $table->text('deskripsi_pengalaman')->nullable();
            $table->string('posisi_dilamar');
            $table->string('ekspektasi_gaji');
            $table->string('kapan_bisa_bekerja', 50); // secepatnya, 1 minggu, 1 bulan, dll
            $table->string('email');
            $table->string('no_hp'); // No handphone / whatsapp
            $table->string('cv_file_path')->nullable(); // local storage path
            $table->string('cv_original_name')->nullable();
            $table->string('gdrive_file_id')->nullable(); // Google Drive File ID
            $table->string('gdrive_web_view_link')->nullable(); // Google Drive Web View Link
            $table->boolean('pernyataan_benar')->default(true);
            $table->string('status', 30)->default('menunggu_review'); // menunggu_review, diproses, dihubungi, ditolak, dll
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_member_applicants');
    }
};
