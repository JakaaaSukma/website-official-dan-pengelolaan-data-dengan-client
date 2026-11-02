<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing admin user or create new one with role
        $admin = User::where('email', 'admin@gyokai.com')->first();
        
        if ($admin) {
            $admin->update([
                'role' => 'admin',
            ]);
        } else {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gyokai.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }
    }
}










