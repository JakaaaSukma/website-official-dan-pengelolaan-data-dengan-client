<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CheckAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check admin user in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->error('Tidak ada user di database!');
            $this->info('Jalankan: php artisan db:seed --class=AdminUserSeeder');
            return;
        }

        $this->info('Daftar User di Database:');
        $this->line('');

        foreach ($users as $user) {
            $this->line("ID: {$user->id}");
            $this->line("Name: {$user->name}");
            $this->line("Email: {$user->email}");
            $this->line("Password Hash: " . substr($user->password, 0, 20) . '...');
            
            // Test password
            if (Hash::check('admin123', $user->password)) {
                $this->info("✓ Password 'admin123' cocok!");
            } else {
                $this->warn("✗ Password 'admin123' TIDAK cocok!");
            }
            $this->line('---');
        }
    }
}












