<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrMessageReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'hr_message_id',
        'user_id',
        'body',
    ];

    public function message()
    {
        return $this->belongsTo(HrMessage::class, 'hr_message_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
