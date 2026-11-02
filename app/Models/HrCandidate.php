<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrCandidate extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'hr_candidates';

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'national_id',
        'job_title',
        'department',
        'years_experience',
        'education',
        'skills',
        'status',
        'cv_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'years_experience' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->skills)) return [];
        return array_filter(array_map('trim', explode(',', $this->skills)));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'new' => 'bg-green-100 text-green-800',
            'interviewing' => 'bg-blue-100 text-blue-800',
            'offer_sent' => 'bg-yellow-100 text-yellow-800',
            'hired' => 'bg-emerald-100 text-emerald-800',
            'rejected' => 'bg-red-100 text-red-800',
            'passive' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
