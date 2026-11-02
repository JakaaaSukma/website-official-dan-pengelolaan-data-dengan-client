<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrSuspensionStatusLog extends Model
{
    use HasFactory;

    protected $table = 'hr_suspension_status_logs';

    protected $fillable = [
        'suspension_request_id',
        'old_status',
        'new_status',
        'notes',
        'updated_by',
    ];

    public function request()
    {
        return $this->belongsTo(HrSuspensionRequest::class, 'suspension_request_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getOldStatusLabelAttribute(): string
    {
        if (!$this->old_status) return '-';
        return match ($this->old_status) {
            'pending_approval' => 'Menunggu Persetujuan',
            'under_review' => 'Sedang Ditinjau',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->old_status)),
        };
    }

    public function getNewStatusLabelAttribute(): string
    {
        return match ($this->new_status) {
            'pending_approval' => 'Menunggu Persetujuan',
            'under_review' => 'Sedang Ditinjau',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->new_status)),
        };
    }

    public function getNewStatusBadgeClassAttribute(): string
    {
        return match ($this->new_status) {
            'pending_approval' => 'bg-amber-100 text-amber-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-purple-100 text-purple-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
