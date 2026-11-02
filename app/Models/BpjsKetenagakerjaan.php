<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BpjsKetenagakerjaan extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'bpjs_ketenagakerjaan';

    protected $fillable = [
        'hr_contract_id',
        'company_id',
        'bpjs_status',
        'bpjs_number',
        'bpjs_join_date',
        'nomor_referensi',
        'nomor_pegawai',
        'updated_by',
    ];

    protected $casts = [
        'bpjs_join_date' => 'date',
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

    public function getStatusLabelAttribute(): string
    {
        return match ($this->bpjs_status) {
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            default => ucfirst($this->bpjs_status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->bpjs_status) {
            'aktif' => 'bg-green-100 text-green-800',
            'tidak_aktif' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
