<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrMessage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_messages';

    protected $fillable = [
        'company_id',
        'from_user_id',
        'to_user_id',
        'message_type',
        'subject',
        'body',
        'priority',
        'status',
        'reply',
        'replied_by',
        'replied_at',
        'resolved_at',
        'assigned_to',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(HrMessageReply::class, 'hr_message_id');
    }

    public function getMessageTypeLabelAttribute(): string
    {
        return match ($this->message_type) {
            'request_access' => 'Request Akses',
            'quota_request' => 'Permintaan Kuota',
            'account_issue' => 'Isu Akun',
            'performance_report' => 'Laporan Performa',
            'general' => 'Umum',
            default => ucfirst(str_replace('_', ' ', $this->message_type)),
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'bg-red-100 text-red-800',
            'high' => 'bg-orange-100 text-orange-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'low' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'unread' => 'bg-red-100 text-red-800',
            'read' => 'bg-blue-100 text-blue-800',
            'replied' => 'bg-green-100 text-green-800',
            'resolved' => 'bg-emerald-100 text-emerald-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getRelativeTimeAttribute(): string
    {
        return $this->created_at?->diffForHumans() ?? '';
    }
}
