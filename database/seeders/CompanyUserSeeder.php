<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CompanyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update company user
        $company = User::where('email', 'company@gyokai.com')->first();
        
        if ($company) {
            $company->update([
                'name' => 'User Perusahaan',
                'password' => Hash::make('company123'),
                'role' => 'company',
            ]);
        } else {
            User::create([
                'name' => 'User Perusahaan',
                'email' => 'company@gyokai.com',
                'password' => Hash::make('company123'),
                'role' => 'company',
            ]);
        }
    }
}
