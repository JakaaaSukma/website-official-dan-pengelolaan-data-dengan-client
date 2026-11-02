<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BpjsAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_bpjs_routes()
    {
        $response = $this->get('/hr-portal/bpjs');
        $response->assertRedirect('/admin/login');

        $response = $this->get('/hr-superadmin/bpjs');
        $response->assertRedirect('/admin/login');
    }

    public function test_hrd_cannot_access_superadmin_bpjs_routes()
    {
        $company = Client::create([
            'company_name' => 'Test Company',
            'about_company' => 'Test about company',
            'qualifications' => 'Test qualifications',
        ]);
        $hrd = User::factory()->create([
            'role' => 'company',
            'company_id' => $company->id
        ]);

        $response = $this->actingAs($hrd)->get('/hr-superadmin/bpjs');
        $response->assertStatus(403);
    }
}
