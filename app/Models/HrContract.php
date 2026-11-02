<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrContract extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'hr_contracts';

    protected $fillable = [
        'company_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'national_id',
        'department',
        'job_title',
        'work_type',
        'contract_type',
        'contract_start',
        'contract_end',
        'salary',
        'address',
        'avatar_path',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'contract_start' => 'date',
        'contract_end' => 'date',
        'salary' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requests()
    {
        return $this->hasMany(HrRequest::class, 'contract_id');
    }

    public function violationReports()
    {
        return $this->hasMany(HrViolationReport::class, 'intern_id');
    }

    public function suspensionRequests()
    {
        return $this->hasMany(HrSuspensionRequest::class, 'intern_id');
    }

    public function bpjsKetenagakerjaan()
    {
        return $this->hasOne(BpjsKetenagakerjaan::class, 'hr_contract_id');
    }

    public function bpjsAccidentReports()
    {
        return $this->hasMany(BpjsAccidentReport::class, 'hr_contract_id');
    }

    public function activeApprovedSuspension()
    {
        $today = Carbon::today();
        return $this->hasOne(HrSuspensionRequest::class, 'intern_id')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->latest('start_date');
    }

    public function pendingOrActiveSuspensionRequests()
    {
        return $this->hasMany(HrSuspensionRequest::class, 'intern_id')
            ->whereIn('status', ['pending_approval', 'under_review'])
            ->orWhere(function ($q) {
                $today = Carbon::today();
                $q->where('status', 'approved')
                    ->whereDate('end_date', '>=', $today);
            });
    }

    public function getIsCurrentlySuspendedAttribute(): bool
    {
        return (bool) $this->activeApprovedSuspension;
    }

    public function getCurrentSuspensionAttribute(): ?HrSuspensionRequest
    {
        return $this->activeApprovedSuspension;
    }

    public function getCurrentSuspensionPeriodLabelAttribute(): string
    {
        $s = $this->currentSuspension;
        if (!$s) return '';
        return $s->period_label . ' (' . $s->days_remaining . ' hari lagi)';
    }

    public function getHasPendingSuspensionRequestAttribute(): bool
    {
        return $this->suspensionRequests()
            ->whereIn('status', ['pending_approval', 'under_review'])
            ->exists();
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getInitialsAttribute(): string
    {
        $initials = '';
        if ($this->first_name) $initials .= strtoupper($this->first_name[0]);
        if ($this->last_name) $initials .= strtoupper($this->last_name[0]);
        return $initials ?: '?';
    }

    public function isExpired(): bool
    {
        return $this->contract_end->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        $now = Carbon::now();
        $threshold = $now->copy()->addDays($days);
        return !$this->isExpired() && $this->contract_end->lte($threshold);
    }

    public function daysUntilExpiry(): int
    {
        return (int) Carbon::now()->diffInDays($this->contract_end, false);
    }

    public function contractTotalDays(): int
    {
        return (int) $this->contract_start->diffInDays($this->contract_end);
    }

    public function contractElapsedDays(): int
    {
        $now = Carbon::now();
        $start = $this->contract_start;
        if ($now->lt($start)) return 0;
        if ($this->isExpired()) return $this->contractTotalDays();
        return (int) $start->diffInDays($now);
    }

    public function contractProgressPercentage(): int
    {
        $total = $this->contractTotalDays();
        if ($total <= 0) return 0;
        $elapsed = $this->contractElapsedDays();
        return min(100, max(0, (int) round(($elapsed / $total) * 100)));
    }

    public function contractRemainingLabel(): string
    {
        $days = $this->daysUntilExpiry();
        if ($days < 0) return 'Expired';
        if ($days === 0) return 'Hari ini';
        if ($days < 7) return "{$days} Hari Left";
        if ($days < 30) {
            $weeks = (int) floor($days / 7);
            return "{$weeks} Weeks Left";
        }
        $months = (int) round($days / 30);
        return "{$months} Mos Left";
    }

    public function getProgressBarClassAttribute(): string
    {
        $percentage = $this->contractProgressPercentage();
        if ($percentage >= 90) return 'bg-amber-500';
        if ($percentage >= 60) return 'bg-blue-600';
        return 'bg-blue-700';
    }

    public function getWorkTypeLabelAttribute(): string
    {
        return match ($this->work_type) {
            'on_site' => 'On-Site',
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
            default => ucfirst($this->work_type),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->is_currently_suspended) {
            return 'bg-fuchsia-900 text-white shadow-sm';
        }
        return match ($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'expired' => 'bg-gray-100 text-gray-800',
            'extended' => 'bg-blue-100 text-blue-800',
            'offboarded' => 'bg-orange-100 text-orange-800',
            'terminated' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusDisplayLabelAttribute(): string
    {
        if ($this->is_currently_suspended) {
            return 'Diberhentikan Sementara';
        }
        return match ($this->status) {
            'active' => 'Aktif',
            'expired' => 'Expired',
            'extended' => 'Extended',
            'offboarded' => 'Off-boarded',
            'terminated' => 'Terminated',
            default => ucfirst($this->status),
        };
    }
}
