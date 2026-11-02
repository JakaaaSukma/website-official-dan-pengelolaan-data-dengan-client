<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrTalentCallRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'hr_talent_call_requests';

    protected $fillable = [
        'company_id',
        'talent_id',
        'requested_by',
        'call_type',
        'notes',
        'status',
        'handled_by',
        'handled_at',
        'reply_notes',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function talent()
    {
        return $this->belongsTo(HrTalent::class, 'talent_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getCallTypeLabelAttribute(): string
    {
        return match ($this->call_type) {
            'test' => 'Test / Ujian',
            'interview' => 'Interview',
            'other' => 'Lainnya',
            default => ucfirst($this->call_type),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getCallTypeBadgeClassAttribute(): string
    {
        return match ($this->call_type) {
            'test' => 'bg-purple-100 text-purple-800',
            'interview' => 'bg-blue-100 text-blue-800',
            'other' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
