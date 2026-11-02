<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrAddContractBatch extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'hr_add_contract_batches';

    protected $fillable = [
        'batch_number',
        'company_id',
        'created_by',
        'status',
        'notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function candidates()
    {
        return $this->hasMany(HrCandidateItem::class, 'batch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getSelectedCountAttribute()
    {
        return $this->candidates()->where('selection_status', 'selected')->count();
    }

    public function getNotSelectedCountAttribute()
    {
        return $this->candidates()->where('selection_status', 'not_selected')->count();
    }
}
