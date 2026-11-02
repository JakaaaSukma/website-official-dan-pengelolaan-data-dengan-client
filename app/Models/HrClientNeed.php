<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrClientNeed extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'hr_client_needs';

    protected $fillable = [
        'company_id',
        'job_title',
        'department',
        'required_skills',
        'skill_tags',
        'quota',
        'urgency',
        'status',
        'description',
        'deadline',
        'valid_proof_path',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function matches()
    {
        return $this->hasMany(HrTalentMatch::class, 'client_need_id');
    }

    public function getSkillTagsArrayAttribute(): array
    {
        if (empty($this->skill_tags)) return [];
        return array_filter(array_map('trim', explode(',', $this->skill_tags)));
    }

    public function getUrgencyBadgeClassAttribute(): string
    {
        return match ($this->urgency) {
            'urgent' => 'bg-red-100 text-red-800',
            'open' => 'bg-blue-100 text-blue-800',
            'low' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'open' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'fulfilled' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
