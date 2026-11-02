<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {email?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset admin password or create admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@gyokai.com';
        $password = $this->argument('password') ?? 'admin123';

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->password = Hash::make($password);
            $user->save();
            $this->info("Password untuk user {$email} berhasil direset!");
        } else {
            User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $this->info("User admin {$email} berhasil dibuat!");
        }

        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
    }
}












