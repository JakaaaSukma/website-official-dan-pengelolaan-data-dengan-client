<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'status',
        'atm_status',
        'reference',
    ];
}
