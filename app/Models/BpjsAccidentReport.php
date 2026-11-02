<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BpjsAccidentReport extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'bpjs_accident_reports';

    protected $fillable = [
        'report_number',
        'company_id',
        'hr_contract_id',
        'incident_date',
        'incident_time',
        'incident_location',
        'chronology',
        'accident_category',
        'action_taken',
        'handling_status',
        'documents',
        'reported_by',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'documents' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function hrContract()
    {
        return $this->belongsTo(HrContract::class, 'hr_contract_id');
    }

    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function getReportNumberDisplayAttribute(): string
    {
        return $this->report_number ?: ('KK-' . str_pad($this->id ?? 0, 5, '0', STR_PAD_LEFT));
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->accident_category) {
            'ringan' => 'Ringan',
            'sedang' => 'Sedang',
            'berat' => 'Berat',
            default => ucfirst($this->accident_category),
        };
    }

    public function getCategoryBadgeClassAttribute(): string
    {
        return match ($this->accident_category) {
            'ringan' => 'bg-yellow-100 text-yellow-800',
            'sedang' => 'bg-orange-100 text-orange-800',
            'berat' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->handling_status) {
            'dilaporkan' => 'Dilaporkan',
            'dalam_proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'klaim_diajukan' => 'Klaim Diajukan ke BPJS',
            default => ucfirst(str_replace('_', ' ', $this->handling_status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->handling_status) {
            'dilaporkan' => 'bg-amber-100 text-amber-800',
            'dalam_proses' => 'bg-blue-100 text-blue-800',
            'selesai' => 'bg-green-100 text-green-800',
            'klaim_diajukan' => 'bg-purple-100 text-purple-800',
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

    /**
     * Generate a unique report number for a new accident report.
     */
    public static function generateReportNumber(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid('', true)), 0, 4));
        return "KK-{$date}-{$random}";
    }
}
