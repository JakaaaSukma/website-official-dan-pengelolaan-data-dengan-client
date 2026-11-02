<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\HrContract;
use App\Models\BpjsKetenagakerjaan;
use App\Models\BpjsRateSetting;
use App\Models\BpjsAccidentReport;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BpjsDummySeeder extends Seeder
{
    public function run(): void
    {
        // Get or create Company A
        $company = Client::firstOrCreate(
            ['company_name' => 'Company A'],
            ['about_company' => 'Dummy company', 'qualifications' => 'N/A', 'company_logo' => '-']
        );

        // 1. Create BPJS Rate Setting for Company A
        BpjsRateSetting::updateOrCreate(
            ['company_id' => $company->id],
            [
                'jkk_rate' => 0.24,
                'jkm_rate' => 0.30,
                'jht_employer_rate' => 3.70,
                'jht_employee_rate' => 2.00,
                'jp_employer_rate' => 2.00,
                'jp_employee_rate' => 1.00,
                'jkp_employer_rate' => 0,
                'jkp_government_rate' => 0,
                'effective_from' => Carbon::now()->startOfMonth()->subMonths(6),
            ]
        );

        // 2. Create Interns (HrContracts)
        $internsData = [
            ['first_name' => 'Budi', 'last_name' => 'Santoso', 'national_id' => '3201010101900001', 'salary' => 4500000],
            ['first_name' => 'Siti', 'last_name' => 'Aminah', 'national_id' => '3201010202910002', 'salary' => 5000000],
            ['first_name' => 'Agus', 'last_name' => 'Setiawan', 'national_id' => '3201010303920003', 'salary' => 4000000],
            ['first_name' => 'Rini', 'last_name' => 'Wulandari', 'national_id' => '3201010404930004', 'salary' => 5500000],
            ['first_name' => 'Joko', 'last_name' => 'Widodo', 'national_id' => '3201010505940005', 'salary' => 4200000],
        ];

        foreach ($internsData as $index => $data) {
            $contract = HrContract::updateOrCreate(
                ['national_id' => $data['national_id']],
                [
                    'company_id' => $company->id,
                    'employee_id' => 'EMP-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'department' => 'IT',
                    'job_title' => 'Intern',
                    'contract_type' => 'internship',
                    'contract_start' => Carbon::now()->subMonths(3),
                    'contract_end' => Carbon::now()->addMonths(3),
                    'salary' => $data['salary'],
                    'status' => 'active',
                ]
            );

            // 3. Create BPJS Ketenagakerjaan entries
            $isBpjsActive = $index < 4; // First 4 active, 5th inactive/none
            if ($isBpjsActive) {
                BpjsKetenagakerjaan::updateOrCreate(
                    ['hr_contract_id' => $contract->id],
                    [
                        'company_id' => $company->id,
                        'bpjs_number' => '100' . rand(10000000, 99999999),
                        'bpjs_status' => 'aktif',
                        'bpjs_join_date' => Carbon::now()->subMonths(3),
                    ]
                );
            }
            
        $hrdUser = \App\Models\User::where('email', 'hrd_a@companya.gyokai')->first();
        $hrdUserId = $hrdUser ? $hrdUser->id : 1;

        // 4. Create Accident Reports (for a few of them)
        if ($index == 0) {
            BpjsAccidentReport::updateOrCreate(
                ['hr_contract_id' => $contract->id],
                [
                    'company_id' => $company->id,
                    'report_number' => 'ACC-' . date('Ym') . '-001',
                    'incident_date' => Carbon::now()->subDays(10)->toDateString(),
                    'incident_time' => '10:00:00',
                    'incident_location' => 'Kantor Pusat - Ruang Server',
                    'chronology' => 'Tersandung kabel di ruang server, luka lecet ringan di lutut.',
                    'accident_category' => 'ringan',
                    'handling_status' => 'selesai',
                    'reported_by' => $hrdUserId,
                    'documents' => []
                ]
            );
        }

        if ($index == 1) {
            BpjsAccidentReport::updateOrCreate(
                ['hr_contract_id' => $contract->id],
                [
                    'company_id' => $company->id,
                    'report_number' => 'ACC-' . date('Ym') . '-002',
                    'incident_date' => Carbon::now()->subDays(2)->toDateString(),
                    'incident_time' => '14:30:00',
                    'incident_location' => 'Luar Kantor - Perjalanan Dinas',
                    'chronology' => 'Mengalami kecelakaan lalu lintas saat perjalanan dinas. Dirawat di RS.',
                    'accident_category' => 'berat',
                    'handling_status' => 'klaim_diajukan',
                    'reported_by' => $hrdUserId,
                    'documents' => []
                ]
            );
        }
        
        if ($index == 2) {
            BpjsAccidentReport::updateOrCreate(
                ['hr_contract_id' => $contract->id],
                [
                    'company_id' => $company->id,
                    'report_number' => 'ACC-' . date('Ym') . '-003',
                    'incident_date' => Carbon::now()->subDays(5)->toDateString(),
                    'incident_time' => '09:15:00',
                    'incident_location' => 'Pabrik - Area Produksi',
                    'chronology' => 'Tangan terjepit mesin, butuh penanganan medis.',
                    'accident_category' => 'sedang',
                    'handling_status' => 'dalam_proses',
                    'reported_by' => $hrdUserId,
                    'documents' => []
                ]
            );
        }
        }
    }
}
