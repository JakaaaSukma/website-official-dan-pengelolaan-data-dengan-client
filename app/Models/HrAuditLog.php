<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrAuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'hr_audit_logs';

    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'severity',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function getSeverityBadgeClassAttribute(): string
    {
        return match ($this->severity) {
            'info' => 'bg-blue-100 text-blue-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'critical' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
