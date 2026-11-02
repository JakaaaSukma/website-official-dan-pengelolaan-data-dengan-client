<?php

namespace Database\Seeders;

use App\Models\SmtpMailer;
use Illuminate\Database\Seeder;

class SmtpMailerSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SmtpMailer::getDefaultMailers() as $mailer) {
            SmtpMailer::updateOrCreate(
                ['mailer_name' => $mailer['mailer_name']],
                $mailer
            );
        }
    }
}
