<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'hr_requests';

    protected $fillable = [
        'company_id',
        'contract_id',
        'request_number',
        'request_type',
        'status',
        'current_step',
        'total_steps',
        'first_name',
        'last_name',
        'email',
        'phone',
        'national_id',
        'contract_type',
        'department',
        'job_title',
        'contract_start',
        'contract_end',
        'off_boarding_date',
        'off_boarding_reason',
        'documents_json',
        'additional_data',
        'submitted_by',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'created_by',
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end' => 'date',
        'off_boarding_date' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'documents_json' => 'array',
        'additional_data' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function contract()
    {
        return $this->belongsTo(HrContract::class, 'contract_id');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function documents()
    {
        return $this->hasMany(HrRequestDocument::class, 'request_id');
    }

    public function batchItems()
    {
        return $this->hasMany(HrBatchItem::class, 'request_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function overallCompletionPercentage(): int
    {
        if ($this->total_steps <= 0) return 0;
        $step = min($this->current_step, $this->total_steps);
        return (int) round(($step / $this->total_steps) * 100);
    }

    public function getRequestTypeLabelAttribute(): string
    {
        return match ($this->request_type) {
            'add_contract' => 'Add New Contract',
            'extend_contract' => 'Extend Contract',
            'off_boarding' => 'Off-boarding',
            default => ucfirst(str_replace('_', ' ', $this->request_type)),
        };
    }

    public function getRequestTypeIconAttribute(): string
    {
        return match ($this->request_type) {
            'add_contract' => 'plus-circle',
            'extend_contract' => 'refresh',
            'off_boarding' => 'user-minus',
            default => 'file-text',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'in_review' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'completed' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStepsConfigAttribute(): array
    {
        $addSteps = [
            1 => ['name' => 'Personal Data', 'icon' => 'user'],
            2 => ['name' => 'Contract Details', 'icon' => 'file-text'],
            3 => ['name' => 'Documents', 'icon' => 'folder'],
        ];
        $offSteps = [
            1 => ['name' => 'Personal Data', 'icon' => 'user'],
            2 => ['name' => 'Off-boarding Details', 'icon' => 'user-minus'],
            3 => ['name' => 'Documents', 'icon' => 'folder'],
        ];
        return match ($this->request_type) {
            'off_boarding' => $offSteps,
            default => $addSteps,
        };
    }
}
