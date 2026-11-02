<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'tanggal_daftar',
        'name',
        'email',
        'phone',
        'nik_ktp',
        'tempat_lahir',
        'tanggal_lahir',
        'gender',
        'perusahaan_favorite',
        'status',
        'source',
        'google_sheet_row_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_daftar' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor for age
    public function getAgeAttribute()
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return $this->tanggal_lahir->age;
    }

    // Accessor for formatted age (with "tahun")
    public function getUmurAttribute(): string
    {
        if (!$this->tanggal_lahir) {
            return '-';
        }
        return $this->age . ' tahun';
    }

    // Accessor for how long the member has been registered
    public function getWaktuTerdaftarAttribute(): string
    {
        if (!$this->tanggal_daftar) {
            return '-';
        }

        $now = Carbon::now();
        $diff = $now->diff($this->tanggal_daftar);

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = $diff->y . ' tahun';
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m . ' bulan';
        }
        if (empty($parts) && $diff->d > 0) {
            $parts[] = $diff->d . ' hari';
        }

        if (empty($parts)) {
            return 'Baru saja';
        }

        return implode(' ', $parts);
    }

    // Get status label for display
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'sudah_kerja' => 'Sudah Kerja',
            'gagal' => 'Gagal',
            'belum_tes' => 'Belum Tes',
            'sudah_training' => 'Sudah Training',
            'belum_training' => 'Belum Training',
        ];
        return $statusLabels[$this->status] ?? $this->status;
    }
}
