<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;

class ResetHrAccounts extends Command
{
    protected $signature = 'hr:reset-accounts';
    protected $description = 'Create or update HR Portal user accounts (recruitment & HRD company) without affecting other data';

    public function handle()
    {
        $password = 'gyokaihr123';

        // 1. Ensure client companies exist
        $companies = collect();
        foreach (['Company A', 'Company B', 'Company C', 'Company D'] as $i => $name) {
            $c = Client::firstOrCreate(['company_name' => $name], [
                'about_company' => "{$name} adalah perusahaan mitra Gyokai yang aktif menggunakan HR Portal.",
                'qualifications' => json_encode(['Berbadan hukum', 'SIUP/SIUPK', 'NPWP aktif']),
                'is_active' => true,
                'is_hidden' => false,
            ]);
            $companies->put($i, $c);
        }

        // 2. Create/update HRD Company users
        $hrdData = [
            ['Company A', 'Ibu HRD A', 'hrd_a@companya.gyokai'],
            ['Company B', 'Bapak HRD B', 'hrd_b@companyb.gyokai'],
            ['Company C', 'Ibu HRD C', 'hrd_c@companyc.gyokai'],
            ['Company D', 'Bapak HRD D', 'hrd_d@companyd.gyokai'],
        ];

        foreach ($hrdData as $i => $data) {
            list($compName, $personName, $email) = $data;
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $personName,
                    'password' => Hash::make($password),
                    'role' => 'hrd_company',
                    'company_id' => $companies[$i]->id,
                    'account_status' => 'active',
                    'phone' => '081' . str_pad(strval($i + 1), 9, '0', STR_PAD_LEFT),
                ]
            );
            $this->info("  ✅ {$email} → OK");
        }

        // 3. Create/update Super Admin Recruitment
        User::updateOrCreate(
            ['email' => 'recruitment@gyokai.com'],
            [
                'name' => 'Tim Rekrutmen Gyokai',
                'password' => Hash::make($password),
                'role' => 'super_admin_recruitment',
                'company_id' => null,
                'account_status' => 'active',
                'phone' => '081999999999',
            ]
        );
        $this->info("  ✅ recruitment@gyokai.com → OK");

        // 4. Ensure admin account exists
        User::updateOrCreate(
            ['email' => 'admin@gyokai.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );
        $this->info("  ✅ admin@gyokai.com → OK");

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('  All accounts ready! Login credentials:');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->table(
            ['Email', 'Password', 'Role'],
            [
                ['recruitment@gyokai.com', 'gyokaihr123', 'Super Admin Recruitment'],
                ['hrd_a@companya.gyokai', 'gyokaihr123', 'HRD Company A'],
                ['hrd_b@companyb.gyokai', 'gyokaihr123', 'HRD Company B'],
                ['hrd_c@companyc.gyokai', 'gyokaihr123', 'HRD Company C'],
                ['hrd_d@companyd.gyokai', 'gyokaihr123', 'HRD Company D'],
                ['admin@gyokai.com', 'admin123', 'Admin'],
            ]
        );

        return Command::SUCCESS;
    }
}
