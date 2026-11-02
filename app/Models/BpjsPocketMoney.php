<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsPocketMoney extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'bpjs_pocket_moneys';

    protected $fillable = [
        'hr_contract_id',
        'company_id',
        'month',
        'year',
        'amount',
        'updated_by',
    ];

    public function hrContract()
    {
        return $this->belongsTo(HrContract::class, 'hr_contract_id');
    }

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
