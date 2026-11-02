<?php

namespace App\Helpers;

class ExcelReader
{
    /**
     * Read Excel/CSV file and return array of data
     */
    public static function readFile($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if (in_array($extension, ['csv', 'txt'])) {
            return self::readCSV($filePath);
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            return self::readExcel($filePath);
        } else {
            // Try to read as CSV
            return self::readCSV($filePath);
        }
    }

    /**
     * Read CSV file
     */
    protected static function readCSV($filePath)
    {
        $data = [];
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception('Cannot open file: ' . $filePath);
        }

        // Auto-detect encoding and convert to UTF-8
        $firstBytes = fread($handle, 3);
        rewind($handle);
        
        // Detect delimiter
        $delimiter = self::detectDelimiter($handle);
        rewind($handle);

        // Skip BOM if present (UTF-8 BOM: EF BB BF)
        if (substr($firstBytes, 0, 3) === "\xEF\xBB\xBF") {
            fseek($handle, 3);
        }

        $row = 0;
        while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Convert to UTF-8 if needed and filter empty rows
            $line = array_map(function($cell) {
                // Try to detect encoding and convert to UTF-8
                if (!mb_check_encoding($cell, 'UTF-8')) {
                    $cell = mb_convert_encoding($cell, 'UTF-8', 'auto');
                }
                return trim($cell);
            }, $line);
            
            if (!empty(array_filter($line))) { // Skip empty rows
                $data[] = $line;
                $row++;
            }
        }
        
        fclose($handle);
        return $data;
    }

    /**
     * Read Excel file (XLS/XLSX) - Simple implementation
     * Note: For full Excel support, install PhpSpreadsheet library
     * This implementation converts Excel to CSV-like reading
     */
    protected static function readExcel($filePath)
    {
        // Try to use PhpSpreadsheet if available
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
                
                // Filter empty rows
                return array_filter($data, function($row) {
                    return !empty(array_filter($row));
                });
            } catch (\Exception $e) {
                throw new \Exception('Error reading Excel file. Please convert to CSV format or install PhpSpreadsheet library: ' . $e->getMessage());
            }
        } else {
            // If PhpSpreadsheet is not available, suggest converting to CSV
            throw new \Exception('Excel file reading requires PhpSpreadsheet library. Please convert your Excel file to CSV format (File > Save As > CSV UTF-8).');
        }
    }

    /**
     * Detect CSV delimiter
     */
    protected static function detectDelimiter($handle)
    {
        $delimiters = [',', ';', '\t', '|'];
        $delimiterCounts = [];
        
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            return ',';
        }
        
        foreach ($delimiters as $delimiter) {
            $delimiterCounts[$delimiter] = substr_count($firstLine, $delimiter);
        }
        
        $maxCount = max($delimiterCounts);
        $detectedDelimiter = array_search($maxCount, $delimiterCounts);
        
        return $detectedDelimiter ?: ',';
    }

    /**
     * Smart mapping: automatically detect column positions based on header or common patterns
     */
    public static function mapData($rows, $skipHeader = true)
    {
        if (empty($rows)) {
            return [];
        }

        $data = [];
        $headerRow = $skipHeader ? array_shift($rows) : null;
        
        // If header exists, map columns intelligently
        $columnMap = [];
        if ($headerRow) {
            $columnMap = self::detectColumns($headerRow);
        }

        foreach ($rows as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            $mappedRow = [];
            if (!empty($columnMap)) {
                // Use intelligent mapping
                foreach ($columnMap as $field => $index) {
                    $mappedRow[$field] = isset($row[$index]) ? trim($row[$index]) : '';
                }
            } else {
                // Use default positional mapping (assuming standard order, NIK removed)
                $mappedRow = [
                    'nama' => isset($row[0]) ? trim($row[0]) : '',
                    'tanggal_lahir' => isset($row[1]) ? trim($row[1]) : '',
                    'alamat' => isset($row[2]) ? trim($row[2]) : '',
                    'nama_perusahaan' => isset($row[3]) ? trim($row[3]) : '',
                    'mulai_kontrak' => isset($row[4]) ? trim($row[4]) : '',
                    'kontrak_habis' => isset($row[5]) ? trim($row[5]) : '',
                    'nik' => isset($row[6]) ? trim($row[6]) : '', // NIK optional, bisa di posisi terakhir jika ada
                ];
            }

            // Only add if at least nama is filled
            if (!empty($mappedRow['nama'])) {
                $data[] = $mappedRow;
            }
        }

        return $data;
    }

    /**
     * Detect column positions based on header row
     */
    protected static function detectColumns($headerRow)
    {
        $mapping = [
            'nama' => ['nama', 'name', 'name', 'peserta', 'nama peserta', 'nama_peserta'],
            'nik' => ['nik', 'nomor induk kependudukan', 'n.i.k', 'no induk kependudukan', 'ktp', 'no ktp'],
            'tanggal_lahir' => ['tanggal lahir', 'tanggal_lahir', 'tgl lahir', 'tgl_lahir', 'birth date', 'birthdate', 'tanggal lahir', 'tgl lahir'],
            'alamat' => ['alamat', 'address', 'address', 'lokasi', 'location'],
            'nama_perusahaan' => ['nama perusahaan', 'nama_perusahaan', 'perusahaan', 'company', 'company name', 'nama_perusahaan'],
            'mulai_kontrak' => ['mulai kontrak', 'mulai_kontrak', 'start date', 'start', 'tanggal mulai', 'tgl mulai', 'mulai'],
            'kontrak_habis' => ['kontrak habis', 'kontrak_habis', 'end date', 'end', 'tanggal habis', 'tgl habis', 'habis', 'kontrak berakhir'],
        ];

        $columnMap = [];
        
        foreach ($headerRow as $index => $header) {
            $headerLower = strtolower(trim($header));
            
            foreach ($mapping as $field => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($headerLower, $keyword) !== false || $headerLower === $keyword) {
                        if (!isset($columnMap[$field])) {
                            $columnMap[$field] = $index;
                            break 2;
                        }
                    }
                }
            }
        }

        return $columnMap;
    }
}

