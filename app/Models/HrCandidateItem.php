<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrCandidateItem extends Model
{
    use HasFactory;

    protected $table = 'hr_candidate_items';

    protected $fillable = [
        'batch_id',
        'first_name',
        'last_name',
        'national_id',
        'birth_date',
        'job_title',
        'address',
        'contract_start',
        'contract_end',
        'contract_duration_months',
        'selection_status',
        'resulting_contract_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function batch()
    {
        return $this->belongsTo(HrAddContractBatch::class, 'batch_id');
    }

    public function resultingContract()
    {
        return $this->belongsTo(HrContract::class, 'resulting_contract_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->selection_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'selected' => 'bg-green-100 text-green-800',
            'not_selected' => 'bg-gray-100 text-gray-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->selection_status) {
            'pending' => 'Pending Review',
            'selected' => 'Lolos / Selected',
            'not_selected' => 'Tidak Lolos',
            default => ucfirst($this->selection_status),
        };
    }
}
