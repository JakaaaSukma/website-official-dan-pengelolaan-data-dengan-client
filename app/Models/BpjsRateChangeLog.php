<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsRateChangeLog extends Model
{
    use HasFactory;

    protected $table = 'bpjs_rate_change_logs';

    protected $fillable = [
        'company_id',
        'field_name',
        'old_value',
        'new_value',
        'effective_from',
        'changed_by',
        'reason',
    ];

    protected $casts = [
        'old_value' => 'decimal:4',
        'new_value' => 'decimal:4',
        'effective_from' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function getFieldLabelAttribute(): string
    {
        return BpjsRateSetting::rateFields()[$this->field_name] ?? $this->field_name;
    }
}
