<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HrImportService
{
    private int $maxRows = 500;
    private int $maxFileSizeMb = 10;
    private array $safeFunctions = ['SUM', 'AVERAGE', 'COUNT', 'IF', 'VLOOKUP', 'HLOOKUP', 'CONCAT', 'TRIM'];

    public function validateFile($file): array
    {
        $errors = [];

        if (!$file || !$file->isValid()) {
            $errors[] = 'File tidak valid atau tidak terupload dengan benar.';
            return ['valid' => false, 'errors' => $errors];
        }

        if ($file->getSize() > $this->maxFileSizeMb * 1024 * 1024) {
            $errors[] = "Ukuran file maksimal {$this->maxFileSizeMb} MB.";
        }

        $allowedMimes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/octet-stream',
        ];
        if (!in_array($file->getMimeType(), $allowedMimes) && !str_ends_with(strtolower($file->getClientOriginalName()), '.xlsx')) {
            $errors[] = 'Format file harus .xlsx.';
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }

    public function sanitizeCellValue($value): mixed
    {
        if (is_string($value)) {
            if (preg_match('/^[=+\-@].*/', $value)) {
                $functionName = strtoupper(explode('(', substr($value, 1))[0] ?? '');
                if (!in_array($functionName, $this->safeFunctions, true)) {
                    return "'" . $value;
                }
            }
            $value = trim($value);
        }
        return $value;
    }

    public function readExcel(string $filePath): array
    {
        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = [];
        $header = null;
        $rowNum = 0;

        foreach ($sheet->getRowIterator() as $row) {
            $rowNum++;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];

            foreach ($cellIterator as $cell) {
                $val = $cell->getValue();
                if ($cell->isFormula()) {
                    $val = "'" . $val;
                } elseif (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                    try {
                        $timestamp = ExcelDate::excelToTimestamp($val);
                        $val = date('Y-m-d', $timestamp);
                    } catch (\Exception $e) {
                        $val = (string) $val;
                    }
                }
                $cells[] = $this->sanitizeCellValue($val);
            }

            $cells = array_map(function ($c) {
                return $c === '' ? null : $c;
            }, $cells);

            if ($rowNum === 1) {
                $header = $cells;
                continue;
            }

            if (count(array_filter($cells, fn($c) => $c !== null)) === 0) {
                continue;
            }

            if ($rowNum - 1 > $this->maxRows) {
                break;
            }

            $rowData = [];
            foreach ($header as $i => $colName) {
                $rowData[$colName] = $cells[$i] ?? null;
            }
            $rows[] = ['row_number' => $rowNum, 'data' => $rowData];
        }

        return ['header' => $header, 'rows' => $rows];
    }

    public function validateRows(array $rows, array $rules, array $fieldMapping = []): array
    {
        $valid = [];
        $invalid = [];

        foreach ($rows as $row) {
            $data = [];
            foreach ($fieldMapping as $excelCol => $dbCol) {
                if (!isset($data[$dbCol])) {
                    $data[$dbCol] = null; // Initialize
                }
                if (array_key_exists($excelCol, $row['data']) && $row['data'][$excelCol] !== null && $row['data'][$excelCol] !== '') {
                    $data[$dbCol] = (string) $row['data'][$excelCol];
                }
            }

            // Auto-convert numeric excel dates if the rule requires a date
            foreach ($data as $k => $v) {
                if (is_numeric($v) && isset($rules[$k]) && str_contains($rules[$k], 'date')) {
                    try {
                        $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($v);
                        $data[$k] = date('Y-m-d', $timestamp);
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            }

            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                $invalid[] = [
                    'row_number' => $row['row_number'],
                    'data' => $data,
                    'errors' => $validator->errors()->all(),
                ];
            } else {
                $valid[] = [
                    'row_number' => $row['row_number'],
                    'data' => $validator->validated(),
                ];
            }
        }

        return ['valid' => $valid, 'invalid' => $invalid];
    }

    public function createImportLog(
        string $importType,
        string $fileName,
        string $filePath,
        int $totalRows,
        string $status = 'pending'
    ): int {
        return DB::table('hr_import_logs')->insertGetId([
            'company_id' => Auth::user()?->company_id,
            'user_id' => Auth::id(),
            'import_type' => $importType,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'total_rows' => $totalRows,
            'success_rows' => 0,
            'failed_rows' => 0,
            'status' => $status,
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updateImportLog(int $logId, array $updates): void
    {
        $updates['updated_at'] = now();
        DB::table('hr_import_logs')->where('id', $logId)->update($updates);
    }

    public function generateCandidateTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['First Name', 'Last Name', 'Email', 'Phone Number', 'National ID/SSN', 'Job Title', 'Department', 'Years Experience', 'Education', 'Skills (comma separated)', 'Status (new/interviewing/offer_sent)'];
        $sheet->fromArray($headers, null, 'A1');
        $sample = ['Michael', 'Smith', 'michael@example.com', '+6281234567890', '3201010101900001', 'Senior Frontend Developer', 'Engineering', '8', 'B.S. Computer Science', 'Vue,React,JavaScript', 'new'];
        $sheet->fromArray($sample, null, 'A2');
        $highestColumn = $sheet->getHighestColumn();
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $fileName = 'template_candidates_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/templates/' . $fileName);
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }

    public function generateAddContractTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['Nama Lengkap', 'NIK', 'Tanggal Lahir (YYYY-MM-DD)', 'Posisi yang Dilamar', 'Tanggal Mulai (YYYY-MM-DD)', 'Durasi Kontrak (Bulan)', 'Alamat'];
        $sheet->fromArray($headers, null, 'A1');
        $sample = ['Budi Santoso', '3201000000000001', '1998-03-12', 'Software Engineer', '2026-10-07', '12', 'Jl. Sudirman No 1'];
        $sheet->fromArray($sample, null, 'A2');
        $highestColumn = $sheet->getHighestColumn();
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $fileName = 'template_pengajuan_kandidat_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/templates/' . $fileName);
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }

    public function generateEmployeeTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Peserta Aktif Template');

        // Header kolom (Company ID & Name di paling kiri!)
        $headers = [
            'Company ID *',
            'Company Name (Reference only, auto lookup by ID)',
            'Employee ID',
            'Nama Lengkap',
            'Email',
            'Phone Number',
            'National ID/SSN',
            'Department',
            'Job Title',
            'Work Type (on_site/remote/hybrid)',
            'Contract Type (internship/full_time/part_time/contract/probation)',
            'Contract Start (YYYY-MM-DD)',
            'Contract End (YYYY-MM-DD)',
            'Salary (IDR, optional)',
            'Address',
            'Notes',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Bold header warna MERAH GYOKAI branding
        $highestColumn = $sheet->getHighestColumn();
        $styleHeader = [
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E53E3E']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FECACA']]],
        ];
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray($styleHeader);
        $sheet->getRowDimension('1')->setRowHeight(30);

        // Sample row (Company ID = 25 = Company A berdasarkan ID existing User screenshot)
        $sample = [
            '25',
            'PT Gyokai Indonesia Kompeten / Company A (Reference name, tidak perlu exact)',
            'INT-2023-001',
            'Budi Santoso',
            'budi@example.com',
            '+6281234567890',
            '3201010505950001',
            'Software Engineering',
            'Frontend Developer',
            'on_site',
            'internship',
            '2023-06-01',
            '2023-12-15',
            '6000000',
            'Jl. Merdeka No. 123, Jakarta Pusat',
            'Sample data contoh, hapus baris ini jika tidak dipakai.',
        ];
        $sheet->fromArray($sample, null, 'A2');
        $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
            'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'FECACA']]],
            'font'    => ['color' => ['rgb' => '991B1B'], 'italic' => true],
        ]);

        // Panduan pengisian (baris 4 - 10)
        $guideRows = [
            ['⚠️ PANDUAN PENGISIAN TEMPLATE PESERTA AKTIF GYOKAI CONNECT (WAJIB DIBACA SEBELUM MENGISI!)', 'bold-red'],
            ['1. Company ID * (WAJIB DIISI JIKA ANDA SUPER ADMIN REKRUTMEN): Lihat ID di halaman All Active Participants > dropdown Company filter. Contoh: Company A = ID #25', ''],
            ['2. Work Type: HANYA BOLEH 3 PILIHAN: on_site | remote | hybrid. Contract Type: internship | probation | contract | part_time | full_time', ''],
            ['3. Format tanggal: YYYY-MM-DD (contoh: 2026-08-15). Salary: HANYA ANGKA SAJA (tanpa Rp / titik / koma). Contoh: 8500000', ''],
            ['4. Baris sample contoh BARIS 2 BISA DIHAPUS jika tidak dibutuhkan. MAKS 500 BARIS import per file.', ''],
            ['5. KHUSUS HR COMPANY: Kolom Company ID BISA DIKOSONGKAN, otomatis diisi ID perusahaan user login.', ''],
            ['6. SEMUA BARIS YANG KOSONG (Nama Lengkap kosong) otomatis di-skip sistem.', ''],
        ];
        $currentRow = 4;
        foreach ($guideRows as $idx => $g) {
            $sheet->mergeCells("A{$currentRow}:{$highestColumn}{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", $g[0]);
            $style = [];
            if ($idx === 0) {
                $style = [
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']],
                ];
            } else {
                $style = [
                    'font' => ['size' => 10, 'color' => ['rgb' => '1F2937']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF1F2']],
                ];
            }
            $sheet->getStyle("A{$currentRow}:{$highestColumn}{$currentRow}")->applyFromArray($style);
            $currentRow++;
        }

        // Layout final: freeze A2, column auto width
        $sheet->freezePane('A2');
        $highestColumn = $sheet->getHighestColumn();
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $fileName = 'template_employees_GyokaiConnect_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/templates/' . $fileName);
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }

    public function generateTalentTemplate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['First Name', 'Last Name', 'Email', 'Phone Number', 'National ID/SSN', 'Target Role', 'Years Experience', 'Education', 'Skills (comma separated)', 'Skill Tags (comma separated)', 'Status (ready_to_hire/in_process/passive)'];
        $sheet->fromArray($headers, null, 'A1');
        $sample = ['Alex', 'Santos', 'alex@example.com', '+6281234567890', '3201010101900002', 'Senior Frontend Engineer', '6', 'B.S. Computer Science', 'React,TypeScript,Next.js', 'React,TypeScript', 'ready_to_hire'];
        $sheet->fromArray($sample, null, 'A2');
        $highestColumn = $sheet->getHighestColumn();
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $fileName = 'template_talents_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = storage_path('app/templates/' . $fileName);
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return $filePath;
    }
}
