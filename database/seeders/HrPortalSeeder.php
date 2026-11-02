<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Client;
use App\Models\User;
use App\Models\HrContract;
use App\Models\HrCandidate;
use App\Models\HrTalent;
use App\Models\HrClientNeed;
use App\Models\HrMessage;
use App\Models\HrRequest;

class HrPortalSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ============================================================
            // 1. CREATE / GET 4 CLIENT COMPANIES
            // ============================================================
            $companies = collect();
            foreach (['Company A', 'Company B', 'Company C', 'Company D'] as $i => $name) {
                $c = Client::firstOrCreate(['company_name' => $name], [
                    'about_company' => "{$name} adalah perusahaan mitra Gyokai02 yang aktif menggunakan HR Portal.",
                    'qualifications' => json_encode(['Berbadan hukum', 'SIUP/SIUPK', 'NPWP aktif']),
                    'is_active' => true,
                    'is_hidden' => false,
                ]);
                $companies->put($i, $c);
            }

            // ============================================================
            // 2. CREATE 4 HRD COMPANY USERS
            // ============================================================
            $password = 'gyokaihr123';
            $hrdData = [
                ['Company A', 'Ibu HRD A', 'hrd_a@companya.gyokai'],
                ['Company B', 'Bapak HRD B', 'hrd_b@companyb.gyokai'],
                ['Company C', 'Ibu HRD C', 'hrd_c@companyc.gyokai'],
                ['Company D', 'Bapak HRD D', 'hrd_d@companyd.gyokai'],
            ];
            $hrdUsers = collect();
            foreach ($hrdData as $i => $data) {
                list($compName, $personName, $email) = $data;
                $u = User::updateOrCreate(
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
                $hrdUsers->put($i, $u);
            }

            // ============================================================
            // 3. CREATE SUPER ADMIN RECRUITMENT USER
            // ============================================================
            $superAdmin = User::updateOrCreate(
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

            // ============================================================
            // 4. 24 ACTIVE CONTRACTS for COMPANY A
            // ============================================================
            $departments = ['Software Engineering', 'UI/UX Design', 'Product', 'Marketing', 'Finance', 'Operations', 'Sales', 'HR'];
            $jobTitles = [
                'Frontend Engineer', 'Backend Engineer', 'Full-Stack Engineer',
                'UI Designer', 'UX Researcher', 'Product Manager', 'Product Owner',
                'Digital Marketing', 'Finance Analyst', 'Ops Associate', 'Sales Development', 'HR Generalist'
            ];
            $types = ['internship', 'contract', 'probation', 'full_time'];
            $workTypes = ['on_site', 'remote', 'hybrid'];
            $firstNames = ['Andi', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Gilang', 'Hani', 'Indra', 'Joko', 'Kartika', 'Lina', 'Mira', 'Nanda', 'Oka', 'Putri', 'Rizki', 'Sari', 'Tono', 'Ulfa', 'Vino', 'Wulan', 'Xavier', 'Yuni'];
            $lastNames  = ['Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Permata', 'Sari', 'Wibowo', 'Santoso', 'Puspita', 'Hidayat', 'Nugroho', 'Sitorus', 'Manurung', 'Simanjuntak', 'Gunawan', 'Sihombing', 'Wongso', 'Susanto', 'Hakim', 'Setiawan', 'Tan', 'Ong', 'Hartono', 'Utami'];
            $addresses = [
                'Jl. Merdeka No. 10, Bandung',
                'Jl. Sudirman No. 5, Jakarta Selatan',
                'Jl. Ahmad Yani No. 22, Bogor',
                'Jl. Diponegoro No. 8, Surabaya',
                'Jl. Gajah Mada No. 45, Medan',
                'Jl. Asia Afrika No. 12, Bandung',
                'Jl. MH Thamrin No. 17, Jakarta Pusat',
                'Jl. Gatot Subroto No. 88, Jakarta Selatan',
                'Jl. Dago No. 99, Bandung',
                'Jl. Pahlawan No. 33, Yogyakarta',
                'Jl. Soekarno Hatta No. 56, Malang',
                'Jl. Pemuda No. 19, Semarang',
            ];

            // Bersihkan data contract lama untuk company A agar 24 records fresh
            HrContract::where('company_id', $companies[0]->id)->forceDelete();

            for ($i = 0; $i < 24; $i++) {
                // Buat 3 record expiring dalam 14 hari ke depan (untuk panel "Contract Expiring Soon")
                if ($i < 3) {
                    $start = Carbon::now()->subMonths(5 + $i);
                    $end = Carbon::now()->addDays(5 + ($i * 4));
                } else {
                    $months = rand(1, 9);
                    $start = Carbon::now()->subMonths($months);
                    $end = Carbon::now()->addMonths(rand(1, 10));
                }

                $dept = $departments[$i % count($departments)];
                $job = $jobTitles[$i % count($jobTitles)];

                HrContract::create([
                    'company_id' => $companies[0]->id,
                    'employee_id' => 'EMP-A-' . str_pad(strval($i + 1), 4, '0', STR_PAD_LEFT),
                    'first_name' => $firstNames[$i],
                    'last_name'  => $lastNames[$i],
                    'email' => strtolower($firstNames[$i] . '.' . $lastNames[$i]) . '@companya.co',
                    'phone' => '081' . str_pad(strval(1000000 + $i), 10, '0', STR_PAD_LEFT),
                    'birth_date' => Carbon::createFromDate(1995 + ($i % 6), 1 + ($i % 12), 1 + ($i % 28)),
                    'national_id' => '3201' . str_pad(strval(9876543210 + $i), 12, '0', STR_PAD_LEFT),
                    'department' => $dept,
                    'job_title' => $job,
                    'contract_type' => $types[$i % count($types)],
                    'work_type' => $workTypes[$i % count($workTypes)],
                    'salary' => (5 + ($i % 10)) * 1000000,
                    'address' => $addresses[$i % count($addresses)],
                    'contract_start' => $start,
                    'contract_end' => $end,
                    'status' => 'active',
                    'created_by' => $hrdUsers[0]->id,
                ]);
            }

            // ============================================================
            // 5. 12 CANDIDATES for COMPANY A
            // ============================================================
            HrCandidate::where('company_id', $companies[0]->id)->forceDelete();
            $candStatuses = ['new', 'interviewing', 'offer_sent', 'new', 'interviewing', 'passive', 'new', 'offer_sent', 'interviewing', 'new', 'hired', 'rejected'];
            $candEducations = ['S1 Informatika ITB', 'S1 Desain UI/UX UI', 'S1 Manajemen UI', 'D3 Teknik Komputer', 'S1 Teknik Elektro UGM', 'S1 Sistem Informasi UNS', 'S1 Sastra Inggris', 'S1 Informatika Tel-U'];
            for ($i = 0; $i < 12; $i++) {
                $skills = ['JavaScript', 'React', 'PHP', 'Laravel', 'Vue', 'Tailwind', 'Figma', 'Python', 'SQL', 'AWS'];
                $skillsPicked = implode(',', array_slice($skills, $i % 3, 3));
                HrCandidate::create([
                    'company_id' => $companies[0]->id,
                    'first_name' => $firstNames[($i + 3) % 24],
                    'last_name'  => $lastNames[($i + 5) % 24],
                    'email' => 'cand' . $i . '@mailinator.com',
                    'phone' => '0812' . str_pad(strval(5000000 + $i), 8, '0', STR_PAD_LEFT),
                    'national_id' => '320' . str_pad(strval(9000000 + $i), 13, '0', STR_PAD_LEFT),
                    'job_title' => $jobTitles[$i % count($jobTitles)],
                    'department' => $departments[$i % count($departments)],
                    'years_experience' => rand(0, 8),
                    'education' => $candEducations[$i % count($candEducations)],
                    'skills' => $skillsPicked,
                    'status' => $candStatuses[$i],
                    'created_by' => $hrdUsers[0]->id,
                ]);
            }

            // ============================================================
            // 6. 24 TALENTS (GLOBAL, no tenant)
            // ============================================================
            HrTalent::query()->forceDelete();
            $talentStatuses = ['ready_to_hire', 'ready_to_hire', 'in_process', 'passive', 'ready_to_hire', 'in_process', 'ready_to_hire', 'passive', 'passive', 'ready_to_hire'];
            $availStatuses = ['available', 'notice_period', 'employed', 'available'];
            $targetRoles = ['Frontend Engineer', 'Senior UI/UX Designer', 'Backend Go Engineer', 'Product Manager', 'Data Engineer', 'DevOps Engineer', 'Full-Stack Engineer', 'Lead UX Researcher', 'Digital Marketing Lead', 'Data Analyst'];
            $talentSkills = [
                ['React', 'TypeScript', 'Next.js', 'Jest'], ['Figma', 'Design Systems', 'Prototyping', 'User Research'],
                ['Golang', 'Kafka', 'gRPC', 'Docker'], ['Scrum', 'Jira', 'Roadmapping', 'SaaS'],
                ['Spark', 'Python', 'AWS Redshift', 'SQL dbt'], ['Kubernetes', 'Terraform', 'AWS', 'CI/CD'],
                ['Node.js', 'NestJS', 'PostgreSQL', 'Redis'], ['Maze', 'Interviews', 'Statistics', 'Miro'],
                ['SEO', 'Meta Ads', 'Google Analytics', 'Content'], ['SQL', 'Python Pandas', 'Tableau', 'Looker']
            ];
            for ($i = 0; $i < 24; $i++) {
                $roleIdx = $i % count($targetRoles);
                HrTalent::create([
                    'first_name' => $firstNames[($i + 7) % 24],
                    'last_name'  => $lastNames[($i + 2) % 24],
                    'current_job_title' => $targetRoles[$roleIdx],
                    'target_role' => $targetRoles[$roleIdx],
                    'email' => 'talent' . $i . '@gyokaitalent.com',
                    'phone' => '0812' . str_pad(strval(10000000 + $i), 8, '0', STR_PAD_LEFT),
                    'years_experience' => 1 + ($i % 9),
                    'education' => 'S' . (1 + ($i % 2)) . ' ' . ['Informatika', 'Desain Komunikasi Visual', 'Manajemen Bisnis', 'Sistem Informasi', 'Teknik Industri'][$i % 5],
                    'skills' => implode(', ', $talentSkills[$roleIdx]),
                    'skill_tags' => implode(',', $talentSkills[$roleIdx]),
                    'match_score' => 65 + ($i % 35),
                    'status' => $talentStatuses[$i % count($talentStatuses)],
                    'availability_status' => $availStatuses[$i % count($availStatuses)],
                    'expected_salary' => (8 + ($i % 20)) * 1000000,
                ]);
            }

            // ============================================================
            // 7. 2 CLIENT NEEDS (Mockup Gambar 4: FinTech Corp = A, HealthAI = B)
            // ============================================================
            HrClientNeed::query()->forceDelete();
            HrClientNeed::create([
                'company_id' => $companies[0]->id,
                'job_title' => 'Lead UI Designer',
                'department' => 'UI/UX Design',
                'required_skills' => 'Figma, Design System, Prototyping',
                'skill_tags' => 'Figma,Design Systems,Prototyping,UX',
                'quota' => 1,
                'urgency' => 'urgent',
                'status' => 'open',
                'deadline' => Carbon::now()->addDays(10),
            ]);
            HrClientNeed::create([
                'company_id' => $companies[1]->id,
                'job_title' => 'Data Engineer',
                'department' => 'Data / Engineering',
                'required_skills' => 'Apache Spark, AWS, SQL, Python',
                'skill_tags' => 'Spark,AWS,Python,SQL',
                'quota' => 2,
                'urgency' => 'open',
                'status' => 'open',
                'deadline' => Carbon::now()->addDays(21),
            ]);

            // ============================================================
            // 8. 3 MESSAGES (mockup dashboard inbox: A 10m, C 1h, B yesterday)
            // ============================================================
            HrMessage::query()->forceDelete();
            HrMessage::create([
                'company_id' => $companies[0]->id,
                'from_user_id' => $hrdUsers[0]->id,
                'to_user_id' => $superAdmin->id,
                'message_type' => 'quota_request',
                'priority' => 'urgent',
                'status' => 'unread',
                'subject' => 'Permintaan Tambah Kuota Magang',
                'body' => 'Mohon izin penambahan kuota intern untuk Departemen UI/UX Design dari 4 menjadi 6 orang. Terima kasih atas reviewnya.',
                'created_at' => Carbon::now()->subMinutes(10),
            ]);
            HrMessage::create([
                'company_id' => $companies[2]->id,
                'from_user_id' => $hrdUsers[2]->id,
                'to_user_id' => $superAdmin->id,
                'message_type' => 'account_issue',
                'priority' => 'high',
                'status' => 'unread',
                'subject' => 'HRD Lupa Password Akun',
                'body' => 'Admin, kami lupa password login untuk akun Finance. Mohon di-reset atau dikirimkan password baru melalui email terdaftar. Terima kasih.',
                'created_at' => Carbon::now()->subHour(),
            ]);
            HrMessage::create([
                'company_id' => $companies[1]->id,
                'from_user_id' => $hrdUsers[1]->id,
                'to_user_id' => $superAdmin->id,
                'message_type' => 'performance_report',
                'priority' => 'normal',
                'status' => 'unread',
                'subject' => 'Laporan Performa Batch Q1',
                'body' => 'Berikut kami kirimkan laporan evaluasi performa peserta magang Q1-2026. Mohon direview oleh tim agar bisa dipertimbangkan untuk perpanjangan kontrak. Attachment follow via email.',
                'created_at' => Carbon::now()->subDay(),
            ]);

            // ============================================================
            // 9. 2 DUMMY REQUESTS (Operational Forms)
            // ============================================================
            HrRequest::where('company_id', $companies[0]->id)->forceDelete();
            $hrReqAdd = HrRequest::create([
                'request_number' => 'ADD-' . date('Ymd') . '-00001',
                'company_id' => $companies[0]->id,
                'request_type' => 'add_contract',
                'status' => 'draft',
                'current_step' => 2,
                'total_steps' => 3,
                'first_name' => 'Siti Aisyah',
                'last_name' => 'Putri',
                'email' => 'siti.aisyah@companya.co',
                'phone' => '081777777777',
                'national_id' => '3201234567890123',
                'contract_type' => 'internship',
                'department' => 'UI/UX Design',
                'job_title' => 'Junior UI Designer',
                'contract_start' => Carbon::now()->addWeek(1),
                'contract_end' => Carbon::now()->addMonths(6),
                'created_by' => $hrdUsers[0]->id,
            ]);
            HrRequest::create([
                'request_number' => 'EXT-' . date('Ymd') . '-00002',
                'company_id' => $companies[0]->id,
                'request_type' => 'extend_contract',
                'status' => 'submitted',
                'current_step' => 3,
                'total_steps' => 3,
                'contract_id' => HrContract::where('company_id', $companies[0]->id)->first()->id ?? null,
                'first_name' => 'Rizky Akbar',
                'last_name' => 'Pratama',
                'email' => 'rizky.akbar@companya.co',
                'phone' => '081888888888',
                'national_id' => '3209876543210000',
                'contract_type' => 'full_time',
                'department' => 'Software Engineering',
                'job_title' => 'Senior Full-Stack Engineer',
                'contract_start' => Carbon::now()->addDays(30),
                'contract_end' => Carbon::now()->addYears(2),
                'submitted_by' => $hrdUsers[0]->id,
                'submitted_at' => Carbon::now()->subDays(2),
                'created_by' => $hrdUsers[0]->id,
            ]);

            // ============================================================
            // 10. RUN MATCH ENGINE for first client need (pre-populate results)
            // ============================================================
            try {
                if (class_exists(\App\Services\HrMatchingService::class)) {
                    $needFirst = HrClientNeed::first();
                    if ($needFirst) {
                        \App\Services\HrMatchingService::runMatchEngine($needFirst, 5);
                    }
                }
            } catch (\Throwable $e) { /* silent */ }
        });

        $this->command->info('✓ HR Portal Multi-Tenant Seeder selesai!');
        $this->command->info('  4 Perusahaan (Company A–D), 4 akun HRD, 1 akun Super Admin Recruitment');
        $this->command->info('  24 Contracts + 12 Candidates (Company A), 24 Talents, 2 Client Needs, 3 Messages');
        $this->command->info('  Password default untuk SEMUA akun: gyokaihr123');
        $this->command->info('');
        $this->command->info('  Contoh login:');
        $this->command->info('    HRD Company A : hrd_a@companya.gyokai / gyokaihr123');
        $this->command->info('    Super Admin   : recruitment@gyokai.com / gyokaihr123');
    }
}
