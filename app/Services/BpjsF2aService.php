<?php

namespace App\Services;

use App\Models\BpjsKetenagakerjaan;
use App\Models\BpjsRateSetting;
use App\Models\HrContract;
use App\Models\BpjsPocketMoney;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Cell;

class BpjsF2aService
{
    /**
     * Calculate all BPJS contributions for a given salary and rate settings.
     * Pure function — no side effects.
     */
    public function calculateContributions(float $salary, ?BpjsRateSetting $rate, float $rapel = 0): array
    {
        $base = $salary + $rapel;

        $jkkRate = $rate ? (float) $rate->jkk_rate : 0.24;
        $jkmRate = $rate ? (float) $rate->jkm_rate : 0.30;
        $jhtEmployerRate = $rate ? (float) $rate->jht_employer_rate : 0;
        $jhtEmployeeRate = $rate ? (float) $rate->jht_employee_rate : 0;
        $jpEmployerRate = $rate ? (float) $rate->jp_employer_rate : 0;
        $jpEmployeeRate = $rate ? (float) $rate->jp_employee_rate : 0;
        $jkpEmployerRate = $rate ? (float) $rate->jkp_employer_rate : 0;
        $jkpGovernmentRate = $rate ? (float) $rate->jkp_government_rate : 0;

        $jkk = round($base * $jkkRate / 100, 0);
        $jkm = round($base * $jkmRate / 100, 0);
        $jhtEmployer = round($base * $jhtEmployerRate / 100, 0);
        $jhtEmployee = round($base * $jhtEmployeeRate / 100, 0);
        $jpEmployer = round($base * $jpEmployerRate / 100, 0);
        $jpEmployee = round($base * $jpEmployeeRate / 100, 0);
        $jkpEmployer = round($base * $jkpEmployerRate / 100, 0);
        $jkpGovernment = round($base * $jkpGovernmentRate / 100, 0);

        $total = $jkk + $jkm + $jhtEmployer + $jhtEmployee + $jpEmployer + $jpEmployee + $jkpEmployer + $jkpGovernment;

        return [
            'jkk' => $jkk,
            'jkm' => $jkm,
            'jht_employer' => $jhtEmployer,
            'jht_employee' => $jhtEmployee,
            'jp_employer' => $jpEmployer,
            'jp_employee' => $jpEmployee,
            'jkp_employer' => $jkpEmployer,
            'jkp_government' => $jkpGovernment,
            'total' => $total,
        ];
    }

    /**
     * Generate F2A report data for a specific company and period.
     * Uses historical rate from the given period, not current rate.
     */
    public function generateF2aData(int $companyId, int $month, int $year): array
    {
        $periodDate = Carbon::create($year, $month, 1)->toDateString();

        // Get rate effective at that period
        $rate = BpjsRateSetting::getEffectiveRate($companyId, $periodDate);

        // Get active interns with active BPJS in this period
        $contracts = HrContract::withoutTenant()
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->where('contract_start', '<=', Carbon::create($year, $month)->endOfMonth())
            ->where('contract_end', '>=', Carbon::create($year, $month, 1))
            ->with('bpjsKetenagakerjaan')
            ->orderBy('first_name')
            ->get();

        // Get pocket money for this period
        $pocketMoneys = BpjsPocketMoney::withoutTenant()
            ->where('company_id', $companyId)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('hr_contract_id');

        $rows = [];
        $totals = [
            'salary' => 0, 'rapel' => 0, 'jkk' => 0, 'jkm' => 0,
            'jht_employer' => 0, 'jht_employee' => 0,
            'jp_employer' => 0, 'jp_employee' => 0,
            'jkp_employer' => 0, 'jkp_government' => 0,
            'total' => 0,
        ];

        foreach ($contracts as $i => $contract) {
            $bpjs = $contract->bpjsKetenagakerjaan;
            
            $pocketMoney = $pocketMoneys->get($contract->id);
            $salary = $pocketMoney ? (float) $pocketMoney->amount : (float) ($contract->salary ?? 0);
            
            $rapel = 0; // Rapel can be extended later

            $contributions = $this->calculateContributions($salary, $rate, $rapel);

            $row = [
                'no' => $i + 1,
                'nomor_referensi' => $bpjs->nomor_referensi ?? '-',
                'nik' => $contract->national_id ?? '-',
                'nomor_pegawai' => $bpjs->nomor_pegawai ?? ($contract->employee_id ?? '-'),
                'nama' => trim($contract->first_name . ' ' . $contract->last_name),
                'tanggal_lahir' => optional($contract->birth_date)->format('d/m/Y') ?? '-',
                'tgl_kepesertaan' => $bpjs?->bpjs_join_date?->format('d/m/Y') ?? '-',
                'salary' => $salary,
                'rapel' => $rapel,
                'jkk' => $contributions['jkk'],
                'jkm' => $contributions['jkm'],
                'jht_employer' => $contributions['jht_employer'],
                'jht_employee' => $contributions['jht_employee'],
                'jp_employer' => $contributions['jp_employer'],
                'jp_employee' => $contributions['jp_employee'],
                'jkp_employer' => $contributions['jkp_employer'],
                'jkp_government' => $contributions['jkp_government'],
                'total' => $contributions['total'],
            ];

            $rows[] = $row;

            // Accumulate totals
            $totals['salary'] += $salary;
            $totals['rapel'] += $rapel;
            foreach (['jkk', 'jkm', 'jht_employer', 'jht_employee', 'jp_employer', 'jp_employee', 'jkp_employer', 'jkp_government', 'total'] as $key) {
                $totals[$key] += $contributions[$key];
            }
        }

        return [
            'rows' => $rows,
            'totals' => $totals,
            'rate' => $rate,
            'period' => Carbon::create($year, $month, 1)->translatedFormat('F Y'),
            'period_month' => $month,
            'period_year' => $year,
            'company_id' => $companyId,
        ];
    }

    /**
     * Export F2A data to Excel (.xlsx).
     */
    public function exportToExcel(array $data, string $companyName): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('F2A Report');

        // Title rows
        $sheet->mergeCells('A1:R1');
        $sheet->setCellValue('A1', 'Formulir 2a PU — BPJS Ketenagakerjaan');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:R2');
        $sheet->setCellValue('A2', 'RINCIAN IURAN TENAGA KERJA');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:R3');
        $sheet->setCellValue('A3', "Perusahaan: {$companyName} — Periode: {$data['period']}");
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Headers
        $headers = [
            'No', 'Nomor Referensi', 'NIK', 'Nomor Pegawai', 'Nama Tenaga Kerja',
            'Tanggal Lahir', 'Tgl Kepesertaan', 'Jumlah Upah (Rp)', 'Jumlah Rapel (Rp)',
            'Iuran JKK (Rp)', 'Iuran JKM (Rp)',
            'Iuran JHT PK (Rp)', 'Iuran JHT TK (Rp)',
            'Iuran JP PK (Rp)', 'Iuran JP TK (Rp)',
            'Iuran JKP PK (Rp)', 'Iuran JKP Pemerintah (Rp)',
            'Total Iuran (Rp)',
        ];

        $row = 5;
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . $row;
            if ($col > 25) {
                $cell = 'A' . chr(65 + $col - 26) . $row;
            }
            $sheet->setCellValueByColumnAndRow($col + 1, $row, $header);
        }

        // Style header row
        $headerRange = 'A5:R5';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E53E3E');
        $sheet->getStyle($headerRange)->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);

        // Data rows
        $rowNum = 6;
        foreach ($data['rows'] as $r) {
            $sheet->setCellValueByColumnAndRow(1, $rowNum, $r['no']);
            $sheet->setCellValueByColumnAndRow(2, $rowNum, $r['nomor_referensi']);
            $sheet->setCellValueByColumnAndRow(3, $rowNum, $r['nik']);
            $sheet->setCellValueByColumnAndRow(4, $rowNum, $r['nomor_pegawai']);
            $sheet->setCellValueByColumnAndRow(5, $rowNum, $r['nama']);
            $sheet->setCellValueByColumnAndRow(6, $rowNum, $r['tanggal_lahir']);
            $sheet->setCellValueByColumnAndRow(7, $rowNum, $r['tgl_kepesertaan']);
            $sheet->setCellValueByColumnAndRow(8, $rowNum, $r['salary']);
            $sheet->setCellValueByColumnAndRow(9, $rowNum, $r['rapel']);
            $sheet->setCellValueByColumnAndRow(10, $rowNum, $r['jkk']);
            $sheet->setCellValueByColumnAndRow(11, $rowNum, $r['jkm']);
            $sheet->setCellValueByColumnAndRow(12, $rowNum, $r['jht_employer']);
            $sheet->setCellValueByColumnAndRow(13, $rowNum, $r['jht_employee']);
            $sheet->setCellValueByColumnAndRow(14, $rowNum, $r['jp_employer']);
            $sheet->setCellValueByColumnAndRow(15, $rowNum, $r['jp_employee']);
            $sheet->setCellValueByColumnAndRow(16, $rowNum, $r['jkp_employer']);
            $sheet->setCellValueByColumnAndRow(17, $rowNum, $r['jkp_government']);
            $sheet->setCellValueByColumnAndRow(18, $rowNum, $r['total']);

            // Number format for currency columns
            for ($c = 8; $c <= 18; $c++) {
                $sheet->getStyleByColumnAndRow($c, $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            }

            $rowNum++;
        }

        // Totals row
        $sheet->setCellValueByColumnAndRow(1, $rowNum, '');
        $sheet->mergeCells("A{$rowNum}:G{$rowNum}");
        $sheet->setCellValueByColumnAndRow(1, $rowNum, 'TOTAL');
        $sheet->setCellValueByColumnAndRow(8, $rowNum, $data['totals']['salary']);
        $sheet->setCellValueByColumnAndRow(9, $rowNum, $data['totals']['rapel']);
        $sheet->setCellValueByColumnAndRow(10, $rowNum, $data['totals']['jkk']);
        $sheet->setCellValueByColumnAndRow(11, $rowNum, $data['totals']['jkm']);
        $sheet->setCellValueByColumnAndRow(12, $rowNum, $data['totals']['jht_employer']);
        $sheet->setCellValueByColumnAndRow(13, $rowNum, $data['totals']['jht_employee']);
        $sheet->setCellValueByColumnAndRow(14, $rowNum, $data['totals']['jp_employer']);
        $sheet->setCellValueByColumnAndRow(15, $rowNum, $data['totals']['jp_employee']);
        $sheet->setCellValueByColumnAndRow(16, $rowNum, $data['totals']['jkp_employer']);
        $sheet->setCellValueByColumnAndRow(17, $rowNum, $data['totals']['jkp_government']);
        $sheet->setCellValueByColumnAndRow(18, $rowNum, $data['totals']['total']);

        $totalRange = "A{$rowNum}:R{$rowNum}";
        $sheet->getStyle($totalRange)->getFont()->setBold(true);
        $sheet->getStyle($totalRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');
        for ($c = 8; $c <= 18; $c++) {
            $sheet->getStyleByColumnAndRow($c, $rowNum)->getNumberFormat()->setFormatCode('#,##0');
        }

        // Border all data
        $dataRange = "A5:R{$rowNum}";
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto-size columns
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save to temp file
        $filename = 'F2A_' . str_replace(' ', '_', $companyName) . '_' . $data['period_year'] . '_' . str_pad($data['period_month'], 2, '0', STR_PAD_LEFT) . '.xlsx';
        $path = storage_path('app/temp/' . $filename);
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }

    /**
     * Export F2A data to Word (.docx).
     */
    public function exportToWord(array $data, string $companyName): string
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(9);

        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop' => 600,
            'marginBottom' => 600,
            'marginLeft' => 600,
            'marginRight' => 600,
        ]);

        // Title
        $section->addText('Formulir 2a PU — BPJS Ketenagakerjaan', ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        $section->addText('RINCIAN IURAN TENAGA KERJA', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $section->addText("Perusahaan: {$companyName}", ['size' => 10], ['alignment' => 'center']);
        $section->addText("Periode: {$data['period']}", ['size' => 10], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Table
        $tableStyle = [
            'borderSize' => 4,
            'borderColor' => '999999',
            'cellMarginTop' => 40,
            'cellMarginBottom' => 40,
            'cellMarginLeft' => 60,
            'cellMarginRight' => 60,
        ];
        $phpWord->addTableStyle('F2ATable', $tableStyle);
        $table = $section->addTable('F2ATable');

        // Header row
        $headerStyle = ['bold' => true, 'size' => 7, 'color' => 'FFFFFF'];
        $headerCellStyle = ['bgColor' => 'E53E3E', 'valign' => 'center'];

        $headers = [
            'No', 'Nomor Referensi', 'NIK', 'No. Pegawai', 'Nama Tenaga Kerja',
            'Tgl Lahir', 'Tgl Kepesertaan', 'Upah (Rp)', 'Rapel (Rp)',
            'JKK (Rp)', 'JKM (Rp)', 'JHT PK (Rp)', 'JHT TK (Rp)',
            'JP PK (Rp)', 'JP TK (Rp)', 'JKP PK (Rp)', 'JKP Pem. (Rp)',
            'Total (Rp)',
        ];

        $colWidths = [400, 1000, 1100, 800, 1600, 800, 800, 900, 700, 700, 700, 700, 700, 700, 700, 700, 700, 900];

        $table->addRow(null, ['tblHeader' => true]);
        foreach ($headers as $i => $h) {
            $table->addCell($colWidths[$i] ?? 700, $headerCellStyle)->addText($h, $headerStyle, ['alignment' => 'center']);
        }

        // Data rows
        $dataFont = ['size' => 7];
        $numFont = ['size' => 7];
        foreach ($data['rows'] as $r) {
            $table->addRow();
            $table->addCell($colWidths[0])->addText($r['no'], $dataFont, ['alignment' => 'center']);
            $table->addCell($colWidths[1])->addText($r['nomor_referensi'], $dataFont);
            $table->addCell($colWidths[2])->addText($r['nik'], $dataFont);
            $table->addCell($colWidths[3])->addText($r['nomor_pegawai'], $dataFont);
            $table->addCell($colWidths[4])->addText($r['nama'], $dataFont);
            $table->addCell($colWidths[5])->addText($r['tanggal_lahir'], $dataFont, ['alignment' => 'center']);
            $table->addCell($colWidths[6])->addText($r['tgl_kepesertaan'], $dataFont, ['alignment' => 'center']);
            $table->addCell($colWidths[7])->addText(number_format($r['salary'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[8])->addText(number_format($r['rapel'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[9])->addText(number_format($r['jkk'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[10])->addText(number_format($r['jkm'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[11])->addText(number_format($r['jht_employer'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[12])->addText(number_format($r['jht_employee'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[13])->addText(number_format($r['jp_employer'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[14])->addText(number_format($r['jp_employee'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[15])->addText(number_format($r['jkp_employer'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[16])->addText(number_format($r['jkp_government'], 0, ',', '.'), $numFont, ['alignment' => 'right']);
            $table->addCell($colWidths[17])->addText(number_format($r['total'], 0, ',', '.'), ['size' => 7, 'bold' => true], ['alignment' => 'right']);
        }

        // Totals row
        $totalCellStyle = ['bgColor' => 'F3F4F6'];
        $totalFont = ['bold' => true, 'size' => 7];
        $table->addRow();
        $mergedCell = $table->addCell(array_sum(array_slice($colWidths, 0, 7)), array_merge($totalCellStyle, ['gridSpan' => 7]));
        $mergedCell->addText('TOTAL', $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[7], $totalCellStyle)->addText(number_format($data['totals']['salary'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[8], $totalCellStyle)->addText(number_format($data['totals']['rapel'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[9], $totalCellStyle)->addText(number_format($data['totals']['jkk'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[10], $totalCellStyle)->addText(number_format($data['totals']['jkm'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[11], $totalCellStyle)->addText(number_format($data['totals']['jht_employer'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[12], $totalCellStyle)->addText(number_format($data['totals']['jht_employee'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[13], $totalCellStyle)->addText(number_format($data['totals']['jp_employer'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[14], $totalCellStyle)->addText(number_format($data['totals']['jp_employee'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[15], $totalCellStyle)->addText(number_format($data['totals']['jkp_employer'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[16], $totalCellStyle)->addText(number_format($data['totals']['jkp_government'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);
        $table->addCell($colWidths[17], $totalCellStyle)->addText(number_format($data['totals']['total'], 0, ',', '.'), $totalFont, ['alignment' => 'right']);

        // Rate info
        if ($data['rate']) {
            $section->addTextBreak(1);
            $section->addText('Rate Iuran yang Berlaku pada Periode Ini:', ['bold' => true, 'size' => 9]);
            $section->addText("JKK: {$data['rate']->jkk_rate}% | JKM: {$data['rate']->jkm_rate}%", ['size' => 9]);
        }

        // Save
        $filename = 'F2A_' . str_replace(' ', '_', $companyName) . '_' . $data['period_year'] . '_' . str_pad($data['period_month'], 2, '0', STR_PAD_LEFT) . '.docx';
        $path = storage_path('app/temp/' . $filename);
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($path);

        return $path;
    }
}
