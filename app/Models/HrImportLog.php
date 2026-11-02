<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrImportLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_import_logs';

    protected $fillable = [
        'company_id',
        'user_id',
        'import_type',
        'file_name',
        'file_path',
        'total_rows',
        'success_rows',
        'failed_rows',
        'error_details',
        'validation_report',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'error_details' => 'array',
        'validation_report' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'validating' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'rollback' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
