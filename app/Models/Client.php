<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'company_name',
        'about_company',
        'qualifications',
        'company_logo',
        'is_active',
        'is_hidden',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    public function hrdUsers()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function hrCandidates()
    {
        return $this->hasMany(HrCandidate::class, 'company_id');
    }

    public function hrContracts()
    {
        return $this->hasMany(HrContract::class, 'company_id');
    }

    public function hrRequests()
    {
        return $this->hasMany(HrRequest::class, 'company_id');
    }

    public function hrClientNeeds()
    {
        return $this->hasMany(HrClientNeed::class, 'company_id');
    }

    public function hrTalentMatches()
    {
        return $this->hasMany(HrTalentMatch::class, 'company_id');
    }

    public function hrMessages()
    {
        return $this->hasMany(HrMessage::class, 'company_id');
    }

    public function violationReports()
    {
        return $this->hasMany(HrViolationReport::class, 'company_id');
    }

    public function suspensionRequests()
    {
        return $this->hasMany(HrSuspensionRequest::class, 'company_id');
    }

    public function bpjsRateSettings()
    {
        return $this->hasMany(BpjsRateSetting::class, 'company_id');
    }

    public function bpjsAccidentReports()
    {
        return $this->hasMany(BpjsAccidentReport::class, 'company_id');
    }

    public function activeHrContractsCount(): int
    {
        return $this->hrContracts()->where('status', 'active')->count();
    }
}












