<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrTalent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_talents';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'national_id',
        'current_job_title',
        'target_role',
        'years_experience',
        'education',
        'skills',
        'skill_tags',
        'match_score',
        'status',
        'availability_status',
        'cv_path',
        'location',
        'expected_salary',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'years_experience' => 'decimal:2',
        'match_score' => 'decimal:2',
        'expected_salary' => 'decimal:2',
    ];

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

    public function getSkillTagsArrayAttribute(): array
    {
        if (empty($this->skill_tags)) return [];
        return array_filter(array_map('trim', explode(',', $this->skill_tags)));
    }

    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->skills)) return [];
        return array_filter(array_map('trim', explode(',', $this->skills)));
    }

    public function matches()
    {
        return $this->hasMany(HrTalentMatch::class, 'talent_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'ready_to_hire' => 'bg-green-100 text-green-800',
            'in_process' => 'bg-blue-100 text-blue-800',
            'passive' => 'bg-gray-100 text-gray-800',
            'hired' => 'bg-emerald-100 text-emerald-800',
            'not_available' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getAvailabilityBadgeClassAttribute(): string
    {
        return match ($this->availability_status) {
            'available' => 'bg-green-100 text-green-800',
            'notice_period' => 'bg-yellow-100 text-yellow-800',
            'employed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getAvailabilityStatusLabelAttribute(): string
    {
        return match ($this->availability_status) {
            'available' => 'AVAILABLE',
            'notice_period' => 'NOTICE PERIOD',
            'employed' => 'EMPLOYED',
            default => strtoupper($this->availability_status),
        };
    }
}
