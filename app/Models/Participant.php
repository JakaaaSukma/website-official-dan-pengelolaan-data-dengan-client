<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nik',
        'tanggal_lahir',
        'alamat',
        'nama_perusahaan',
        'mulai_kontrak',
        'kontrak_habis',
        'user_id',
        'is_followed_up',
        'followed_up_at',
        'followup_notes',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'mulai_kontrak' => 'date',
        'kontrak_habis' => 'date',
        'is_followed_up' => 'boolean',
        'followed_up_at' => 'datetime',
    ];

    /**
     * Get the company user that owns the participant.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if contract is expired
     */
    public function isExpired()
    {
        return $this->kontrak_habis->isPast();
    }

    /**
     * Check if contract is expiring soon (within 1 month)
     */
    public function isExpiringSoon()
    {
        $now = \Carbon\Carbon::now();
        $oneMonthFromNow = $now->copy()->addMonth();
        
        return !$this->isExpired() && 
               $this->kontrak_habis >= $now && 
               $this->kontrak_habis <= $oneMonthFromNow;
    }

    /**
     * Get days until contract expires (negative if already expired)
     */
    public function daysUntilExpiry()
    {
        return \Carbon\Carbon::now()->diffInDays($this->kontrak_habis, false);
    }
}
