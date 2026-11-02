<?php

namespace Tests\Feature;

use App\Models\BpjsRateSetting;
use App\Services\BpjsF2aService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BpjsF2aCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_contributions_with_rates()
    {
        $service = new BpjsF2aService();
        $salary = 1000000; // 1 million

        $rate = new BpjsRateSetting([
            'jkk_rate' => 0.24,
            'jkm_rate' => 0.30,
            'jht_employer_rate' => 3.70,
            'jht_employee_rate' => 2.00,
            'jp_employer_rate' => 2.00,
            'jp_employee_rate' => 1.00,
            'jkp_employer_rate' => 0,
            'jkp_government_rate' => 0,
        ]);

        $result = $service->calculateContributions($salary, $rate, 0);

        $this->assertEquals(2400, $result['jkk']); // 0.24% of 1M
        $this->assertEquals(3000, $result['jkm']); // 0.30% of 1M
        $this->assertEquals(37000, $result['jht_employer']); // 3.70% of 1M
        $this->assertEquals(20000, $result['jht_employee']); // 2.00% of 1M
        $this->assertEquals(20000, $result['jp_employer']); // 2.00% of 1M
        $this->assertEquals(10000, $result['jp_employee']); // 1.00% of 1M
        
        $expectedTotal = 2400 + 3000 + 37000 + 20000 + 20000 + 10000;
        $this->assertEquals($expectedTotal, $result['total']);
    }

    public function test_calculate_contributions_without_rates_uses_default()
    {
        $service = new BpjsF2aService();
        $salary = 5000000; // 5 million

        // Using null rate should fallback to default JKK 0.24 and JKM 0.30
        $result = $service->calculateContributions($salary, null, 0);

        $this->assertEquals(12000, $result['jkk']); // 0.24% of 5M
        $this->assertEquals(15000, $result['jkm']); // 0.30% of 5M
        $this->assertEquals(0, $result['jht_employer']);
        $this->assertEquals(0, $result['jht_employee']);
        
        $this->assertEquals(27000, $result['total']);
    }
}
