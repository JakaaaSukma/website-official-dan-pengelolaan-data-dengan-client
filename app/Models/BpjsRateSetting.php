<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpjsRateSetting extends Model
{
    use HasFactory;

    protected $table = 'bpjs_rate_settings';

    protected $fillable = [
        'company_id',
        'jkk_rate',
        'jkm_rate',
        'jht_employer_rate',
        'jht_employee_rate',
        'jp_employer_rate',
        'jp_employee_rate',
        'jkp_employer_rate',
        'jkp_government_rate',
        'effective_from',
        'effective_until',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'jkk_rate' => 'decimal:4',
        'jkm_rate' => 'decimal:4',
        'jht_employer_rate' => 'decimal:4',
        'jht_employee_rate' => 'decimal:4',
        'jp_employer_rate' => 'decimal:4',
        'jp_employee_rate' => 'decimal:4',
        'jkp_employer_rate' => 'decimal:4',
        'jkp_government_rate' => 'decimal:4',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the effective rate setting for a company on a specific date.
     * Returns the rate that was active on the given date (not the current rate).
     */
    public static function getEffectiveRate(int $companyId, string $date): ?self
    {
        return static::where('company_id', $companyId)
            ->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', $date);
            })
            ->orderByDesc('effective_from')
            ->first();
    }

    /**
     * Get the currently active rate setting for a company.
     */
    public static function getCurrentRate(int $companyId): ?self
    {
        return static::getEffectiveRate($companyId, now()->toDateString());
    }

    /**
     * Scope: filter by rates effective on a specific date.
     */
    public function scopeEffectiveOn($query, string $date)
    {
        return $query->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', $date);
            });
    }

    /**
     * Get all rate field names for iteration.
     */
    public static function rateFields(): array
    {
        return [
            'jkk_rate' => 'JKK',
            'jkm_rate' => 'JKM',
            'jht_employer_rate' => 'JHT Pemberi Kerja',
            'jht_employee_rate' => 'JHT Tenaga Kerja',
            'jp_employer_rate' => 'JP Pemberi Kerja',
            'jp_employee_rate' => 'JP Tenaga Kerja',
            'jkp_employer_rate' => 'JKP Pemberi Kerja',
            'jkp_government_rate' => 'JKP Pemerintah',
        ];
    }
}
