<?php

namespace Database\Seeders;

use App\Models\HrTalent;
use Illuminate\Database\Seeder;

class HrTalentSeeder extends Seeder
{
    public function run(): void
    {
        $talents = [
            [
                'first_name' => 'Ahmad',
                'last_name' => 'Fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'phone' => '081234567890',
                'national_id' => '3201010101900001',
                'current_job_title' => 'Senior Frontend Developer',
                'target_role' => 'Frontend Engineer',
                'years_experience' => 5.5,
                'education' => 'S1 Teknik Informatika, Universitas Indonesia',
                'skills' => 'React, Vue, Tailwind, TypeScript',
                'skill_tags' => '["React","Vue","Tailwind","TypeScript"]',
                'status' => 'ready_to_hire',
                'availability_status' => 'available',
                'location' => 'Jakarta Selatan',
                'expected_salary' => 15000000,
            ],
            [
                'first_name' => 'Bunga',
                'last_name' => 'Citra',
                'email' => 'bunga.citra@example.com',
                'phone' => '081234567891',
                'national_id' => '3201010101900002',
                'current_job_title' => 'Backend Engineer',
                'target_role' => 'Backend Developer',
                'years_experience' => 4.0,
                'education' => 'S1 Sistem Informasi, Telkom University',
                'skills' => 'Laravel, PHP, MySQL, Docker',
                'skill_tags' => '["Laravel","PHP","MySQL","Docker"]',
                'status' => 'ready_to_hire',
                'availability_status' => 'notice_period',
                'location' => 'Bandung',
                'expected_salary' => 12000000,
            ],
            [
                'first_name' => 'Reza',
                'last_name' => 'Rahadian',
                'email' => 'reza.rahadian@example.com',
                'phone' => '081234567892',
                'national_id' => '3201010101900003',
                'current_job_title' => 'DevOps Engineer',
                'target_role' => 'Cloud/DevOps Engineer',
                'years_experience' => 7.0,
                'education' => 'S1 Ilmu Komputer, UGM',
                'skills' => 'AWS, Kubernetes, CI/CD, Terraform',
                'skill_tags' => '["AWS","Kubernetes","CI/CD","Terraform"]',
                'status' => 'in_process',
                'availability_status' => 'available',
                'location' => 'Yogyakarta',
                'expected_salary' => 20000000,
            ],
            [
                'first_name' => 'Dian',
                'last_name' => 'Sastrowardoyo',
                'email' => 'dian.sastro@example.com',
                'phone' => '081234567893',
                'national_id' => '3201010101900004',
                'current_job_title' => 'Product Manager',
                'target_role' => 'Product Manager',
                'years_experience' => 6.5,
                'education' => 'S2 Manajemen, Binus',
                'skills' => 'Agile, Scrum, JIRA, Product Strategy',
                'skill_tags' => '["Agile","Scrum","JIRA","Strategy"]',
                'status' => 'ready_to_hire',
                'availability_status' => 'employed',
                'location' => 'Jakarta Pusat',
                'expected_salary' => 25000000,
            ],
            [
                'first_name' => 'Iqbaal',
                'last_name' => 'Ramadhan',
                'email' => 'iqbaal.ramadhan@example.com',
                'phone' => '081234567894',
                'national_id' => '3201010101900005',
                'current_job_title' => 'UI/UX Designer',
                'target_role' => 'Product Designer',
                'years_experience' => 3.0,
                'education' => 'S1 Desain Komunikasi Visual, ITB',
                'skills' => 'Figma, Adobe XD, User Research, Prototyping',
                'skill_tags' => '["Figma","Adobe XD","User Research","Prototyping"]',
                'status' => 'passive',
                'availability_status' => 'available',
                'location' => 'Bandung',
                'expected_salary' => 10000000,
            ]
        ];

        foreach ($talents as $data) {
            HrTalent::updateOrCreate(
                ['national_id' => $data['national_id']],
                $data
            );
        }
    }
}
