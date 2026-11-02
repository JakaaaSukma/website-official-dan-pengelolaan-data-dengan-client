<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'ip_address',
        'user_agent',
        'location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
     protected static function booted()
     {
         static::saving(function ($model) {
             if ($model->exists) {
                 return false;
             }
         });
 
         static::updating(function ($model) {
             return false;
         });
 
         static::deleting(function ($model) {
             return false;
         });
     }
}
