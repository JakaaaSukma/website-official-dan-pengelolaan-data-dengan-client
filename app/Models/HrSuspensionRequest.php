<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrSuspensionRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'hr_suspension_requests';

    protected $fillable = [
        'company_id',
        'intern_id',
        'requested_by',
        'start_date',
        'end_date',
        'reason',
        'status',
        'handled_by',
        'handled_at',
        'resolution_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(HrSuspensionStatusLog::class, 'suspension_request_id')->orderByDesc('created_at');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_approval' => 'Menunggu Persetujuan',
            'under_review' => 'Sedang Ditinjau',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending_approval' => 'bg-amber-100 text-amber-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-purple-100 text-purple-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPeriodLabelAttribute(): string
    {
        $start = optional($this->start_date)->format('d M Y') ?? '-';
        $end = optional($this->end_date)->format('d M Y') ?? '-';
        return "{$start} s/d {$end}";
    }

    public function getRequestNumberAttribute(): string
    {
        $date = optional($this->created_at)->format('Ymd') ?? date('Ymd');
        $id = str_pad($this->id, 5, '0', STR_PAD_LEFT);
        return "SUS-{$date}-{$id}";
    }

    public function getTotalDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) return 0;
        return (int) $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function isCurrentlyActive(): bool
    {
        if ($this->status !== 'approved') return false;
        $today = Carbon::today();
        return $today->gte($this->start_date) && $today->lte($this->end_date);
    }

    public function isFuture(): bool
    {
        if ($this->status !== 'approved') return false;
        return Carbon::today()->lt($this->start_date);
    }

    public function isAlreadyExpired(): bool
    {
        if ($this->status !== 'approved') return false;
        return Carbon::today()->gt($this->end_date);
    }

    public function getActiveStatusTextAttribute(): string
    {
        if ($this->status !== 'approved') return $this->status_label;
        if ($this->isCurrentlyActive()) return 'BERLANSUNG';
        if ($this->isFuture()) return 'AKAN DATANG';
        if ($this->isAlreadyExpired()) return 'SELESAI';
        return $this->status_label;
    }

    public function getActiveStatusBadgeClassAttribute(): string
    {
        if ($this->status !== 'approved') return $this->status_badge_class;
        if ($this->isCurrentlyActive()) return 'bg-fuchsia-900 text-white';
        if ($this->isFuture()) return 'bg-indigo-100 text-indigo-800';
        if ($this->isAlreadyExpired()) return 'bg-gray-200 text-gray-700';
        return $this->status_badge_class;
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->isCurrentlyActive()) return 0;
        return max(0, (int) Carbon::today()->diffInDays($this->end_date, false) + 1);
    }
}
