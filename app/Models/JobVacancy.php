<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'responsibilities',
        'qualifications',
        'is_active',
        'photo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}












