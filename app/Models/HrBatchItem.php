<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrBatchItem extends Model
{
    use HasFactory;

    protected $table = 'hr_batch_items';

    protected $fillable = [
        'request_id',
        'contract_id',
        'new_contract_end',
        'notes',
        'off_boarding_effective_date',
        'off_boarding_reason',
    ];

    protected $casts = [
        'new_contract_end' => 'date',
        'off_boarding_effective_date' => 'date',
    ];

    public function request()
    {
        return $this->belongsTo(HrRequest::class, 'request_id');
    }

    public function contract()
    {
        return $this->belongsTo(HrContract::class, 'contract_id');
    }
}
