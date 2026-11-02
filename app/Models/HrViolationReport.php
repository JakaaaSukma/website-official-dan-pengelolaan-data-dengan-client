<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrViolationReport extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'hr_violation_reports';

    protected $fillable = [
        'company_id',
        'intern_id',
        'reported_by',
        'incident_date',
        'incident_time',
        'description',
        'proposed_sanction',
        'evidence_file',
        'status',
        'handled_by',
        'handled_at',
        'resolution_notes',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'handled_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function intern()
    {
        return $this->belongsTo(HrContract::class, 'intern_id');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(HrViolationStatusLog::class, 'violation_report_id')->orderByDesc('created_at');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_review' => 'Menunggu Tinjauan',
            'contacted' => 'Sudah Dihubungi',
            'in_process' => 'Sedang Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending_review' => 'bg-amber-100 text-amber-800',
            'contacted' => 'bg-blue-100 text-blue-800',
            'in_process' => 'bg-orange-100 text-orange-800',
            'resolved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getIncidentDateTimeLabelAttribute(): string
    {
        $date = optional($this->incident_date)->format('d M Y') ?? '-';
        if ($this->incident_time) {
            $date .= ' • ' . substr($this->incident_time, 0, 5);
        }
        return $date;
    }

    public function getReportNumberAttribute(): string
    {
        $date = optional($this->created_at)->format('Ymd') ?? date('Ymd');
        $id = str_pad($this->id, 5, '0', STR_PAD_LEFT);
        return "VIO-{$date}-{$id}";
    }
}
