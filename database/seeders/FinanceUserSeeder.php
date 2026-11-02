<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FinanceUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing finance user or create new one with role
        $finance = User::where('email', 'finance@gyokai.com')->first();
        
        if ($finance) {
            $finance->update([
                'role' => 'finance',
                'password' => Hash::make('finance123'),
            ]);
        } else {
            User::create([
                'name' => 'Finance',
                'email' => 'finance@gyokai.com',
                'password' => Hash::make('finance123'),
                'role' => 'finance',
            ]);
        }
    }
}
