<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayslipSendSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_code',
        'total_files',
        'success_count',
        'failed_count',
        'status',
        'temp_dir_path',
        'pending_queue',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'pending_queue' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(PayslipLog::class)->latest();
    }
}
