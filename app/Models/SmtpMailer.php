<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpMailer extends Model
{
    use HasFactory;

    protected $fillable = [
        'mailer_name',
        'from_address',
        'from_name',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
        'status',
        'daily_limit',
        'daily_sent_count',
        'priority',
        'last_used_at',
        'last_limit_reached_at',
        'cooldown_until',
    ];

    protected $casts = [
        'smtp_password' => 'encrypted',
        'smtp_port' => 'integer',
        'daily_limit' => 'integer',
        'daily_sent_count' => 'integer',
        'priority' => 'integer',
        'last_used_at' => 'datetime',
        'last_limit_reached_at' => 'datetime',
        'cooldown_until' => 'datetime',
    ];

    public static function getDefaultMailers()
    {
        return [
            [
                'mailer_name' => 'mailer_finance_3',
                'from_address' => 'gyokaislipgaji3@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'gyokaislipgaji3@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_3_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 1,
            ],
            [
                'mailer_name' => 'mailer_finance_4',
                'from_address' => 'gyokaislipgaji4@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'gyokaislipgaji4@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_4_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 2,
            ],
            [
                'mailer_name' => 'mailer_finance_5',
                'from_address' => 'gyokaislipgaji5@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'gyokaislipgaji5@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_5_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 3,
            ],
            [
                'mailer_name' => 'mailer_finance_6',
                'from_address' => 'gyokaislipgaji6@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'gyokaislipgaji6@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_6_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 4,
            ],
            [
                'mailer_name' => 'mailer_finance_7',
                'from_address' => 'gyokaiuniversal@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'gyokaiuniversal@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_7_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 5,
            ],
            [
                'mailer_name' => 'mailer_finance_8',
                'from_address' => 'gyokairesepsionis@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'gyokairesepsionis@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_8_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 6,
            ],
            [
                'mailer_name' => 'mailer_finance_9',
                'from_address' => 'Gyokaiindonesiabatam@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'Gyokaiindonesiabatam@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_9_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 7,
            ],
            [
                'mailer_name' => 'mailer_finance_10',
                'from_address' => 'ubdxgyokai@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'ubdxgyokai@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_10_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 8,
            ],
            [
                'mailer_name' => 'mailer_finance_11',
                'from_address' => 'Generasimerdeka14@gmail.com',
                'from_name' => 'Finance Gyokai',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_encryption' => 'tls',
                'smtp_username' => 'Generasimerdeka14@gmail.com',
                'smtp_password' => env('MAILER_FINANCE_11_PASSWORD', ''),
                'status' => 'active',
                'daily_limit' => 400,
                'daily_sent_count' => 0,
                'priority' => 9,
            ],
        ];
    }

    public static function ensureDefaultMailersExist()
    {
        foreach (static::getDefaultMailers() as $mailer) {
            static::query()->firstOrCreate(
                ['mailer_name' => $mailer['mailer_name']],
                $mailer
            );
        }
    }
}
