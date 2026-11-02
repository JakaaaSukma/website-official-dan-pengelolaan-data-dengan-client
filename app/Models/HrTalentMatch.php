<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrTalentMatch extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_talent_matches';

    protected $fillable = [
        'talent_id',
        'client_need_id',
        'company_id',
        'match_score',
        'match_details',
        'status',
        'matched_by',
        'matched_at',
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'match_details' => 'array',
        'matched_at' => 'datetime',
    ];

    public function talent()
    {
        return $this->belongsTo(HrTalent::class, 'talent_id');
    }

    public function clientNeed()
    {
        return $this->belongsTo(HrClientNeed::class, 'client_need_id');
    }

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function matchedBy()
    {
        return $this->belongsTo(User::class, 'matched_by');
    }

    public function getProgressBarClassAttribute(): string
    {
        if ($this->match_score >= 90) return 'bg-green-600';
        if ($this->match_score >= 70) return 'bg-green-500';
        if ($this->match_score >= 50) return 'bg-amber-500';
        return 'bg-gray-400';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'suggested' => 'bg-blue-100 text-blue-800',
            'reviewed' => 'bg-yellow-100 text-yellow-800',
            'recommended' => 'bg-green-100 text-green-800',
            'hired' => 'bg-emerald-100 text-emerald-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
