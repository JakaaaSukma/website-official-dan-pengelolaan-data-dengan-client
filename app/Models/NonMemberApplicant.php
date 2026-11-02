<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonMemberApplicant extends Model
{
    use HasFactory;

    protected $table = 'non_member_applicants';

    protected $fillable = [
        'nama_lengkap',
        'domisili',
        'pendidikan_terakhir',
        'jurusan_prodi',
        'ipk_nilai',
        'nama_institusi',
        'tipe_pengalaman',
        'deskripsi_pengalaman',
        'posisi_dilamar',
        'ekspektasi_gaji',
        'kapan_bisa_bekerja',
        'email',
        'no_hp',
        'cv_file_path',
        'cv_original_name',
        'gdrive_file_id',
        'gdrive_web_view_link',
        'pernyataan_benar',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'pernyataan_benar' => 'boolean',
    ];

    public function getCvDownloadUrlAttribute()
    {
        if ($this->cv_file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->cv_file_path)) {
            return \Illuminate\Support\Facades\Storage::url($this->cv_file_path);
        }
        return $this->gdrive_web_view_link;
    }
}
