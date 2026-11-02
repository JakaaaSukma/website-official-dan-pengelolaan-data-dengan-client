<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfileDownload extends Model
{
    use HasFactory;

    protected $table = 'company_profile_downloads';

    protected $fillable = [
        'company_name',
        'full_name',
        'position',
        'contact',
        'service_type',
        'is_contacted',
    ];

    protected $casts = [
        'is_contacted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

