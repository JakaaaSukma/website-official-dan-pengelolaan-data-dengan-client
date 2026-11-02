<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'last_login_at',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Client::class, 'company_id');
    }

    public function hrCandidates()
    {
        return $this->hasMany(HrCandidate::class, 'created_by');
    }

    public function hrContractsCreated()
    {
        return $this->hasMany(HrContract::class, 'created_by');
    }

    public function hrRequestsSubmitted()
    {
        return $this->hasMany(HrRequest::class, 'submitted_by');
    }

    public function violationReportsReported()
    {
        return $this->hasMany(HrViolationReport::class, 'reported_by');
    }

    public function violationReportsHandled()
    {
        return $this->hasMany(HrViolationReport::class, 'handled_by');
    }

    public function suspensionRequestsRequested()
    {
        return $this->hasMany(HrSuspensionRequest::class, 'requested_by');
    }

    public function suspensionRequestsHandled()
    {
        return $this->hasMany(HrSuspensionRequest::class, 'handled_by');
    }

    public function isHrdCompany(): bool
    {
        return $this->role === 'hrd_company';
    }

    public function isSuperAdminRecruitment(): bool
    {
        return in_array($this->role, ['super_admin_recruitment', 'admin']);
    }

    /**
     * Generate a 6-digit OTP and save it to the user.
     * 
     * @param int $minutes Valid duration in minutes (default 5)
     * @return string The generated OTP code
     */
    public function generateOtp(int $minutes = 5): string
    {
        $code = str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'otp_code' => $code,
            'otp_expires_at' => now()->addMinutes($minutes),
        ]);
        
        return $code;
    }

    /**
     * Verify if the provided OTP code is valid and not expired.
     * 
     * @param string $code
     * @return bool
     */
    public function verifyOtp(string $code): bool
    {
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }

        if ($this->otp_code !== $code) {
            return false;
        }

        if (now()->greaterThan($this->otp_expires_at)) {
            return false; // expired
        }

        return true;
    }

    /**
     * Clear the OTP code.
     */
    public function clearOtp(): void
    {
        $this->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
    }
}
