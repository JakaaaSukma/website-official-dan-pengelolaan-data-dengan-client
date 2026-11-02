<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrViolationStatusLog extends Model
{
    use HasFactory;

    protected $table = 'hr_violation_status_logs';

    protected $fillable = [
        'violation_report_id',
        'old_status',
        'new_status',
        'notes',
        'updated_by',
    ];

    public function report()
    {
        return $this->belongsTo(HrViolationReport::class, 'violation_report_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getOldStatusLabelAttribute(): string
    {
        if (!$this->old_status) return '-';
        return match ($this->old_status) {
            'pending_review' => 'Menunggu Tinjauan',
            'contacted' => 'Sudah Dihubungi',
            'in_process' => 'Sedang Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->old_status)),
        };
    }

    public function getNewStatusLabelAttribute(): string
    {
        return match ($this->new_status) {
            'pending_review' => 'Menunggu Tinjauan',
            'contacted' => 'Sudah Dihubungi',
            'in_process' => 'Sedang Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->new_status)),
        };
    }

    public function getNewStatusBadgeClassAttribute(): string
    {
        return match ($this->new_status) {
            'pending_review' => 'bg-amber-100 text-amber-800',
            'contacted' => 'bg-blue-100 text-blue-800',
            'in_process' => 'bg-orange-100 text-orange-800',
            'resolved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
