<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Models\Member;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    protected $apiKey;
    protected $spreadsheetId;
    protected $templateReportSpreadsheetId;
    protected $templateReportSheetGid;
    protected $cacheTime = 30; // 30 minutes cache
    protected $client;
    protected $service;

    public function __construct()
    {
        \Log::info('=== GoogleSheetService __construct called ===');
        // Try to decrypt, if fails, use plain text
        try {
            $apiKeyConfig = config('services.google.sheets_api_key');
            \Log::info('apiKeyConfig length: ' . strlen($apiKeyConfig) . ', starts with eyJpdiI6: ' . (strpos($apiKeyConfig, 'eyJpdiI6') !== false ? 'Yes' : 'No'));
            
            // Check if it looks like an encrypted string (starts with eyJ for JWT-like or has iv/value/mac)
            if (strpos($apiKeyConfig, 'eyJpdiI6') !== false || strpos($apiKeyConfig, '"iv"') !== false) {
                \Log::info('Attempting to decrypt apiKeyConfig');
                $this->apiKey = Crypt::decryptString($apiKeyConfig);
            } else {
                \Log::info('Using plain text apiKeyConfig');
                $this->apiKey = $apiKeyConfig;
            }
            \Log::info('this->apiKey is now: ' . (empty($this->apiKey) ? 'empty' : 'set, length ' . strlen($this->apiKey)));

            $sheetIdConfig = config('services.google.sheet_id');
            \Log::info('sheetIdConfig: ' . $sheetIdConfig);
            if (strpos($sheetIdConfig, 'eyJpdiI6') !== false || strpos($sheetIdConfig, '"iv"') !== false) {
                $this->spreadsheetId = Crypt::decryptString($sheetIdConfig);
            } else {
                $this->spreadsheetId = $sheetIdConfig;
            }
            \Log::info('this->spreadsheetId: ' . $this->spreadsheetId);

            \Log::info('Google Sheets credentials loaded successfully');
        } catch (\Exception $e) {
            \Log::warning("Failed to process Google Sheets credentials: " . $e->getMessage() . " - using plain text values");
            $this->apiKey = config('services.google.sheets_api_key');
            $this->spreadsheetId = config('services.google.sheet_id');
        }

        $this->templateReportSpreadsheetId = config('services.google.template_report_sheet_id');
        $this->templateReportSheetGid = config('services.google.template_report_sheet_gid');
        \Log::info('templateReportSpreadsheetId: ' . $this->templateReportSpreadsheetId);
        \Log::info('templateReportSheetGid: ' . $this->templateReportSheetGid);
        
        // Initialize Google Client with service account if available
        $this->initializeGoogleClient();
    }
    
    /**
     * Initialize Google API Client with Service Account or OAuth2
     */
    protected function initializeGoogleClient()
    {
        // Check if Google Client classes exist before using them
        if (!class_exists('Google\Client') || !class_exists('Google\Service\Sheets')) {
            \Log::error('Google API Client library is not installed. Please install google/apiclient:^2.0 via composer.');
            $this->client = null;
            $this->service = null;
            return;
        }

        $serviceAccountPath = config('services.google.service_account_path');
        $oauthSecretPath = config('services.google.oauth_secret_path');
        $tokenPath = storage_path('app/google/token.json');
        
        // Try Service Account first
        if ($serviceAccountPath && Storage::exists($serviceAccountPath)) {
            try {
                $this->client = new \Google\Client();
                $this->client->setAuthConfig(Storage::path($serviceAccountPath));
                $this->client->addScope(\Google\Service\Sheets::SPREADSHEETS);
                $this->service = new \Google\Service\Sheets($this->client);
                \Log::info('Google Sheets API client initialized successfully with service account');
                return;
            } catch (\Exception $e) {
                \Log::error('Failed to initialize Google Sheets API client with service account: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            }
        }
        
        // Then try OAuth2
        if ($oauthSecretPath && Storage::exists($oauthSecretPath)) {
            try {
                $this->client = new \Google\Client();
                $this->client->setAuthConfig(Storage::path($oauthSecretPath));
                $this->client->addScope(\Google\Service\Sheets::SPREADSHEETS);
                $this->client->setAccessType('offline');
                $this->client->setPrompt('select_account consent');
                $this->client->setRedirectUri(route('google.callback'));
                
                // Load token if available
                if (file_exists($tokenPath)) {
                    $accessToken = json_decode(file_get_contents($tokenPath), true);
                    $this->client->setAccessToken($accessToken);
                    
                    // Refresh token if expired
                    if ($this->client->isAccessTokenExpired()) {
                        if ($this->client->getRefreshToken()) {
                            $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                            $this->client->setAccessToken($newToken);
                            file_put_contents($tokenPath, json_encode($newToken));
                        }
                    }
                }
                
                if ($this->client->getAccessToken()) {
                    $this->service = new \Google\Service\Sheets($this->client);
                    \Log::info('Google Sheets API client initialized successfully with OAuth2');
                } else {
                    \Log::info('Google Sheets API OAuth2 client initialized, but no token yet');
                }
                return;
            } catch (\Exception $e) {
                \Log::error('Failed to initialize Google Sheets API client with OAuth2: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            }
        }
        
        \Log::warning('No Google credentials configured, write operations will fail');
        $this->client = null;
        $this->service = null;
    }
    
    /**
     * Get authorization URL for OAuth2
     */
    public function getAuthorizationUrl()
    {
        $oauthSecretPath = config('services.google.oauth_secret_path');
        if (!$oauthSecretPath || !Storage::exists($oauthSecretPath)) {
            \Log::error('OAuth secret file not found at: ' . $oauthSecretPath);
            return null;
        }

        try {
            // Initialize a fresh OAuth2 client specifically for getting the auth URL
            $oauthClient = new \Google\Client();
            $oauthClient->setAuthConfig(Storage::path($oauthSecretPath));
            $oauthClient->addScope(\Google\Service\Sheets::SPREADSHEETS);
            $oauthClient->setAccessType('offline');
            $oauthClient->setPrompt('select_account consent');
            $oauthClient->setRedirectUri(route('google.callback'));
            return $oauthClient->createAuthUrl();
        } catch (\Exception $e) {
            \Log::error('Failed to create Google OAuth2 authorization URL: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return null;
        }
    }
    
    /**
     * Handle OAuth2 callback and save token
     */
    public function handleCallback($code)
    {
        $oauthSecretPath = config('services.google.oauth_secret_path');
        if (!$oauthSecretPath || !Storage::exists($oauthSecretPath)) {
            \Log::error('OAuth secret file not found at: ' . $oauthSecretPath);
            return false;
        }

        try {
            $oauthClient = new \Google\Client();
            $oauthClient->setAuthConfig(Storage::path($oauthSecretPath));
            $oauthClient->addScope(\Google\Service\Sheets::SPREADSHEETS);
            $oauthClient->setAccessType('offline');
            $oauthClient->setPrompt('select_account consent');
            $oauthClient->setRedirectUri(route('google.callback'));
            
            $token = $oauthClient->fetchAccessTokenWithAuthCode($code);
            $oauthClient->setAccessToken($token);
            
            $tokenPath = storage_path('app/google/token.json');
            file_put_contents($tokenPath, json_encode($token));
            
            // Also update our main client and service
            $this->client = $oauthClient;
            $this->service = new \Google\Service\Sheets($this->client);
            
            \Log::info('Google OAuth2 token saved successfully');
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to handle Google OAuth2 callback: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Sync members from Google Sheet Template Laporan to local database
     */
    public function syncTemplateReportToDatabase($refreshCache = false)
    {
        \Log::info('=== Starting syncTemplateReportToDatabase ===');
        if (!$this->apiKey) {
            \Log::error('Google Sheet apiKey is empty!');
        }
        if (!$this->templateReportSpreadsheetId) {
            \Log::error('Google Sheet templateReportSpreadsheetId is empty!');
        }

        if (!$this->apiKey || !$this->templateReportSpreadsheetId) {
            \Log::error('Google Sheet credentials or Template Report ID not configured');
            return false;
        }

        \Log::info('Credentials configured, proceeding');

        try {
            $cacheKey = 'google_sheet_template_report_' . md5($this->templateReportSpreadsheetId);
            if ($refreshCache) {
                \Log::info('Refresh cache requested, forgetting cache key');
                Cache::forget($cacheKey);
            }

            $rows = Cache::remember($cacheKey, now()->addMinutes($this->cacheTime), function () {
                \Log::info('Fetching fresh data from Google Sheets API');
                $sheetName = $this->resolveSheetTitleByGid($this->templateReportSpreadsheetId, $this->templateReportSheetGid) ?: 'Sheet1';
                \Log::info('Resolved sheet name: ' . $sheetName);
                $range = rawurlencode($sheetName . '!A1:ZZ');
                $url = "https://sheets.googleapis.com/v4/spreadsheets/{$this->templateReportSpreadsheetId}/values/{$range}?key={$this->apiKey}";

                $logUrl = str_replace($this->apiKey, '***HIDDEN_API_KEY***', $url);
                \Log::info("Template Laporan API URL: " . $logUrl);

                $response = Http::get($url);

                if ($response->failed()) {
                    \Log::error('Template Laporan API Error: ' . $response->status() . ' - ' . $response->body());
                    return [];
                }

                $data = $response->json('values', []);
                \Log::info('Fetched ' . count($data) . ' rows from Google Sheets');

                return $data;
            });

            \Log::info('Total rows from cache/API: ' . count($rows));
            if (empty($rows) || count($rows) < 3) {
                \Log::warning('Not enough rows found, returning 0');
                return 0;
            }

            // Actual header is at index 4
            $headers = array_map(fn ($h) => $this->normalizeSpreadsheetHeader($h), $rows[4]);
            
            // Pre-fetch all existing members for quick lookup (reduces DB queries)
            $existingMembers = Member::all()->keyBy('google_sheet_row_id');
            $syncedCount = 0;

            // Data starts from index 6
            for ($i = 6; $i < count($rows); $i++) {
                try {
                    $row = $rows[$i];

                    // Extract fields from the row - directly map columns since we know the structure
                    $reference = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[1] ?? '')); // Column B - REFERENSI
                    $tanggalDaftarStr = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[2] ?? '')); // Column C - Tgl Daftar
                    $name = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[3] ?? '')); // Column D - Nama Lengkap
                    $email = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[6] ?? '')); // Column G - EMAIL (EMAIL AKTIF)
                    $phone = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[7] ?? '')); // Column H - NOMOR WA
                    $nikKtp = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[8] ?? '')); // Column I - NIK KTP
                    $tempatLahir = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[9] ?? '')); // Column J - Tempat Lahir
                    $tanggalLahirStr = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[10] ?? '')); // Column K - Tanggal Lahir
                    $genderStr = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[11] ?? '')); // Column L - Jenis Kelamin
                    
                    // Find Perusahaan Favorite from header
                    $perusahaanFavorite = null;
                    foreach ($headers as $idx => $header) {
                        if (str_contains(strtolower($header), 'perusahaan')) {
                            $perusahaanFavorite = preg_replace('/[\x{200C}-\x{200D}\x{FEFF}]/u', '', trim($row[$idx] ?? ''));
                            break;
                        }
                    }

                    if (empty($name)) {
                        continue;
                    }

                    // Parse tanggal daftar
                    $tanggalDaftar = $this->parseDate($tanggalDaftarStr, true);

                    // Parse tanggal lahir
                    $tanggalLahir = $this->parseDate($tanggalLahirStr, true);

                    // Map gender
                    $gender = $this->mapGender($genderStr);

                    // Generate unique key - use actual spreadsheet row number (index + 1)
                    $spreadsheetRowNumber = $i + 1;
                    $uniqueKey = 'sheet1_row_' . $spreadsheetRowNumber;

                    // Check if member exists using pre-fetched collection first
                    $member = $existingMembers->get($uniqueKey);
                    
                    if (!$member) {
                        // If not in pre-fetched collection, query database once
                        $member = Member::where('google_sheet_row_id', $uniqueKey)->first();
                        
                        if (!$member) {
                            // Check by other identifiers to avoid duplicates
                            $member = Member::where(function ($q) use ($name, $nikKtp) {
                                if (!empty($nikKtp)) {
                                    $q->where('nik_ktp', $nikKtp);
                                }
                                if (!empty($name)) {
                                    $q->orWhere('name', $name);
                                }
                            })->first();
                        }
                    }

                    if (!$member) {
                        $member = new Member();
                        $member->google_sheet_row_id = $uniqueKey;
                        $member->source = 'google_sheet';
                    } else {
                        $member->google_sheet_row_id = $uniqueKey;
                        $member->source = 'google_sheet';
                    }

                    $member->reference = empty($reference) ? null : $reference;
                    $member->tanggal_daftar = $tanggalDaftar ?? null;
                    $member->name = $name;
                    $member->email = empty($email) ? null : $email;
                    $member->phone = empty($phone) ? null : $phone;
                    $member->nik_ktp = empty($nikKtp) ? null : $nikKtp;
                    $member->tempat_lahir = empty($tempatLahir) ? null : $tempatLahir;
                    $member->tanggal_lahir = $tanggalLahir;
                    $member->gender = $gender;
                    $member->perusahaan_favorite = empty($perusahaanFavorite) ? null : $perusahaanFavorite;

                    $member->save();
                    $syncedCount++;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    continue;
                } catch (\Exception $e) {
                    // Log only critical errors
                    \Log::error("Error processing row {$i}: " . $e->getMessage());
                    continue;
                }
            }

            \Log::info("Successfully synced {$syncedCount} members from Template Laporan");
            return $syncedCount;
        } catch (\Exception $e) {
            \Log::error('Failed to sync Template Laporan: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Append a new member to the Google Sheet
     */
    public function appendMemberToSheet(Member $member)
    {
        if (!$this->service) {
            \Log::error('Google Sheets Service not initialized, cannot append member');
            return false;
        }
        
        try {
            // Get spreadsheet properties using the service client (more reliable)
            $spreadsheet = $this->service->spreadsheets->get($this->templateReportSpreadsheetId);
            $sheetName = null;
            $sheetId = null;
            
            foreach ($spreadsheet->getSheets() as $sheet) {
                $props = $sheet->getProperties();
                if ((string)$props->getSheetId() === (string)$this->templateReportSheetGid) {
                    $sheetName = $props->getTitle();
                    $sheetId = $props->getSheetId();
                    break;
                }
            }
            
            // If no match by GID, use first sheet
            if ($sheetName === null) {
                $firstSheet = $spreadsheet->getSheets()[0];
                $sheetName = $firstSheet->getProperties()->getTitle();
                $sheetId = $firstSheet->getProperties()->getSheetId();
            }
            
            \Log::info('Using sheet for append', ['sheet_name' => $sheetName, 'sheet_id' => $sheetId]);
            
            // Prepare the row data
            $tanggalDaftar = $member->tanggal_daftar ? $member->tanggal_daftar->format('j F Y') : '';
            $tanggalLahir = $member->tanggal_lahir ? $member->tanggal_lahir->format('j F Y') : '';
            
            $row = [
                '', // Column A: No (auto increment)
                $member->reference ?? '', // B: Referensi
                $tanggalDaftar, // C: Tgl Daftar
                $member->name, // D: Nama Lengkap
                '', // E
                '', // F
                $member->email ?? '', // G: Email
                $member->phone ?? '', // H: Nomor WA
                $member->nik_ktp ?? '', // I: NIK KTP
                $member->tempat_lahir ?? '', // J: Tempat Lahir
                $tanggalLahir, // K: Tanggal Lahir
                $member->gender ?? '', // L: Jenis Kelamin
                $member->perusahaan_favorite ?? '', // M: Perusahaan Favorite
                '', // N
            ];
            
            // To append at the bottom, we need to find the last row first by getting the entire sheet data
            $response = $this->service->spreadsheets_values->get(
                $this->templateReportSpreadsheetId,
                $sheetName . '!A:ZZ' // Get all values to find the very last row with data
            );
            $values = $response->getValues();
            $lastRow = $values ? count($values) : 0;
            
            \Log::info('Last row found in sheet', ['last_row' => $lastRow, 'values_count' => count($values)]);
            
            $range = $sheetName . '!A' . ($lastRow + 1); // Append after last row
            $valueRange = new ValueRange(['values' => [$row]]);
            
            $params = [
                'valueInputOption' => 'USER_ENTERED', // Use USER_ENTERED so dates are parsed correctly
                'insertDataOption' => 'INSERT_ROWS'
            ];
            
            $appendResponse = $this->service->spreadsheets_values->append(
                $this->templateReportSpreadsheetId,
                $range,
                $valueRange,
                $params
            );
            
            // Extract the new row number from the response
            $updatedRange = $appendResponse->getUpdates()->getUpdatedRange();
            if ($updatedRange) {
                // Parse the range to get the row number (e.g., "Sheet1!A4:N4" -> 4)
                if (preg_match('/!A(\d+):/', $updatedRange, $matches)) {
                    $rowNumber = (int)$matches[1];
                    $member->google_sheet_row_id = 'sheet1_row_' . $rowNumber;
                    $member->save();
                    \Log::info('Updated member google_sheet_row_id', ['member_id' => $member->id, 'row_id' => $member->google_sheet_row_id]);
                }
            }
            
            \Log::info('Successfully appended member to Google Sheet', ['member' => $member->name, 'updated_cells' => $appendResponse->getUpdates()->getUpdatedCells()]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to append member to Google Sheet: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Update an existing member in Google Sheet
     */
    public function updateMemberInSheet(Member $member)
    {
        if (!$this->service) {
            \Log::error('Google Sheets Service not initialized, cannot update member');
            return false;
        }
        
        try {
            if (!$member->google_sheet_row_id) {
                \Log::warning('Member does not have google_sheet_row_id, cannot update', ['member_id' => $member->id]);
                return false;
            }
            
            // Extract row number
            $matches = [];
            if (preg_match('/sheet1_row_(\d+)/', $member->google_sheet_row_id, $matches)) {
                $rowNumber = (int)$matches[1];
                
                // Get spreadsheet properties directly using service client
                $spreadsheet = $this->service->spreadsheets->get($this->templateReportSpreadsheetId);
                $sheetName = null;
                
                foreach ($spreadsheet->getSheets() as $sheet) {
                    $props = $sheet->getProperties();
                    if ((string)$props->getSheetId() === (string)$this->templateReportSheetGid) {
                        $sheetName = $props->getTitle();
                        break;
                    }
                }
                
                // If no match by GID, use first sheet
                if (!$sheetName) {
                    $firstSheet = $spreadsheet->getSheets()[0];
                    $sheetName = $firstSheet->getProperties()->getTitle();
                }
                
                \Log::info('Using sheet for update', ['sheet_name' => $sheetName]);
                
                $tanggalDaftar = $member->tanggal_daftar ? $member->tanggal_daftar->format('j F Y') : '';
                $tanggalLahir = $member->tanggal_lahir ? $member->tanggal_lahir->format('j F Y') : '';
                
                $row = [
                    '', // A: No
                    $member->reference ?? '', // B
                    $tanggalDaftar, // C
                    $member->name, // D
                    '', // E
                    '', // F
                    $member->email ?? '', // G
                    $member->phone ?? '', // H
                    $member->nik_ktp ?? '', // I
                    $member->tempat_lahir ?? '', // J: Tempat Lahir
                    $tanggalLahir, // K
                    $member->gender ?? '', // L
                    $member->perusahaan_favorite ?? '', // M: Perusahaan Favorite
                    '', // N
                ];
                
                $range = $sheetName . '!A' . $rowNumber . ':N' . $rowNumber;
                $valueRange = new ValueRange(['values' => [$row]]);
                
                $params = ['valueInputOption' => 'USER_ENTERED'];
                
                $response = $this->service->spreadsheets_values->update(
                    $this->templateReportSpreadsheetId,
                    $range,
                    $valueRange,
                    $params
                );
                
                \Log::info('Successfully updated member in Google Sheet', ['member' => $member->name, 'row_number' => $rowNumber]);
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to update member in Google Sheet: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Delete a member from Google Sheet
     */
    public function deleteMemberFromSheet(Member $member)
    {
        if (!$this->service) {
            \Log::error('Google Sheets Service not initialized, cannot delete member');
            return false;
        }
        
        try {
            \Log::info('Starting delete process for member', ['member_id' => $member->id, 'name' => $member->name, 'google_sheet_row_id' => $member->google_sheet_row_id]);
            
            if (!$member->google_sheet_row_id) {
                \Log::warning('Member does not have google_sheet_row_id, cannot delete', ['member_id' => $member->id]);
                return false;
            }
            
            // Extract row number
            $matches = [];
            if (preg_match('/sheet1_row_(\d+)/', $member->google_sheet_row_id, $matches)) {
                $rowNumber = (int)$matches[1];
                \Log::info('Extracted row number from google_sheet_row_id', ['row_number' => $rowNumber]);
                
                // Get spreadsheet properties directly using service client
                $spreadsheet = $this->service->spreadsheets->get($this->templateReportSpreadsheetId);
                $sheetName = null;
                $sheetId = null;
                
                foreach ($spreadsheet->getSheets() as $sheet) {
                    $props = $sheet->getProperties();
                    if ((string)$props->getSheetId() === (string)$this->templateReportSheetGid) {
                        $sheetName = $props->getTitle();
                        $sheetId = $props->getSheetId();
                        break;
                    }
                }
                
                // If no match by GID, use first sheet
                if ($sheetName === null) {
                    $firstSheet = $spreadsheet->getSheets()[0];
                    $sheetName = $firstSheet->getProperties()->getTitle();
                    $sheetId = $firstSheet->getProperties()->getSheetId();
                }
                
                \Log::info('Found sheet for deletion', ['sheet_name' => $sheetName, 'sheet_id' => $sheetId]);
                
                if ($sheetId === null) {
                    \Log::error('Could not find sheet ID for deletion');
                    return false;
                }
                
                // Create batch update request to delete the row
                $requests = [
                    new \Google\Service\Sheets\Request([
                        'deleteDimension' => [
                            'range' => [
                                'sheetId' => $sheetId,
                                'dimension' => 'ROWS',
                                'startIndex' => $rowNumber - 1, // Google Sheets API uses 0-based index
                                'endIndex' => $rowNumber
                            ]
                        ]
                    ])
                ];
                
                $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                    'requests' => $requests
                ]);
                
                \Log::info('Sending delete request to Google Sheets API', ['row_number' => $rowNumber, 'start_index' => $rowNumber - 1, 'end_index' => $rowNumber, 'sheet_id' => $sheetId]);
                $response = $this->service->spreadsheets->batchUpdate($this->templateReportSpreadsheetId, $batchUpdateRequest);
                \Log::info('Google Sheets API delete response received', ['response' => $response]);
                
                \Log::info('Successfully deleted member from Google Sheet', ['member' => $member->name, 'row_number' => $rowNumber]);
                
                return true;
            } else {
                \Log::error('Failed to extract row number from google_sheet_row_id', ['google_sheet_row_id' => $member->google_sheet_row_id]);
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to delete member from Google Sheet: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function getMembers($filters = [], $perPage = 10, $refreshCache = false)
    {
        // If refresh cache requested, sync from Google Sheet first
        if ($refreshCache) {
            $this->syncTemplateReportToDatabase(true);
        }

        // Query ALL members from database - Optimized: select only needed columns
        $query = Member::select([
            'id', 'reference', 'tanggal_daftar', 'name', 'email', 'phone', 
            'nik_ktp', 'tempat_lahir', 'tanggal_lahir', 'gender', 
            'perusahaan_favorite', 'status', 'google_sheet_row_id', 'created_at'
        ]);

        // Filter: only members from last 1 year
        $oneYearAgo = now()->subYear();
        $query->where(function ($q) use ($oneYearAgo) {
            $q->where('tanggal_daftar', '>=', $oneYearAgo)
              ->orWhereNull('tanggal_daftar');
        });

        // Apply filters
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                  ->orWhere('nik_ktp', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['reference'])) {
            $references = is_array($filters['reference']) ? $filters['reference'] : [$filters['reference']];
            $query->where(function ($q) use ($references) {
                foreach ($references as $ref) {
                    $q->orWhereRaw('LOWER(reference) LIKE ?', ["%" . strtolower($ref) . "%"]);
                }
            });
        }

        if (!empty($filters['gender'])) {
            $genderMap = [
                'L' => 'Laki-laki',
                'P' => 'Perempuan'
            ];
            $query->where('gender', $genderMap[$filters['gender']] ?? $filters['gender']);
        }

        if (!empty($filters['status'])) {
            $statuses = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $query->whereIn('status', $statuses);
        }

        if (!empty($filters['age'])) {
            if ($filters['age'] === 'under_25') {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 25');
            } elseif ($filters['age'] === 'above_25') {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 25');
            }
        }
        
        if (!empty($filters['perusahaan_favorite'])) {
            $perusahaanFavorites = is_array($filters['perusahaan_favorite']) ? $filters['perusahaan_favorite'] : [$filters['perusahaan_favorite']];
            $query->where(function ($q) use ($perusahaanFavorites) {
                foreach ($perusahaanFavorites as $pf) {
                    $q->orWhereRaw('LOWER(perusahaan_favorite) LIKE ?', ["%" . strtolower($pf) . "%"]);
                }
            });
        }

        // Sort: newest first based on tanggal_daftar
        $members = $query->orderBy('tanggal_daftar', 'desc')->orderBy('id', 'desc')->paginate($perPage);
        return $members;
    }

    private function mapGender($code)
    {
        if (!$code) return null;
        $code = strtoupper(trim($code));
        $code = preg_replace('/\s+/', '', $code); // Remove all spaces
        if ($code === 'L' || $code === 'LAKI-LAKI') return 'Laki-laki';
        if ($code === 'P' || $code === 'PEREMPUAN') return 'Perempuan';
        return $code;
    }

    private function parseDate($dateString, $dateOnly = false)
    {
        if (!$dateString) return null;
        $dateString = trim($dateString);
        
        // Replace Indonesian month names with English
        $indonesianMonths = [
            'Januari' => 'January',
            'Februari' => 'February',
            'Maret' => 'March',
            'April' => 'April',
            'Mei' => 'May',
            'Juni' => 'June',
            'Juli' => 'July',
            'Agustus' => 'August',
            'September' => 'September',
            'Oktober' => 'October',
            'November' => 'November',
            'Desember' => 'December',
        ];
        
        $englishDateString = strtr($dateString, $indonesianMonths);
        
        // Try specific formats first without normalization
        $formats = [
            'j F Y', // Format like 5 Juni 2026 → becomes 5 June 2026
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'd/m/Y',
            'j M Y',
            'd-M-Y',
            'Y-m-d',
            'd-F-Y', // Format like 13-Jul-2021
        ];
        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $englishDateString);
                if ($date) {
                    return $date;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Try normalized formats
        $normalizedDateString = str_replace(['-', '.'], '/', $englishDateString);
        $normalizedFormats = [
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'd/m/Y',
            'j F Y',
            'j M Y',
        ];
        foreach ($normalizedFormats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $normalizedDateString);
                if ($date) {
                    return $date;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Fallback
        try {
            $date = Carbon::parse($englishDateString);
            if ($date) {
                return $date;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    private function resolveSheetTitleByGid($spreadsheetId, $gid)
    {
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}?fields=sheets(properties(sheetId,title))&key={$this->apiKey}";
        $response = Http::get($url);
        if ($response->failed()) {
            \Log::error('Template Laporan sheet metadata error: ' . $response->body());
            return null;
        }
        $sheets = $response->json('sheets', []);
        foreach ($sheets as $sheet) {
            $properties = $sheet['properties'] ?? [];
            if ($gid !== null && $gid !== '' && (string)($properties['sheetId'] ?? '') === (string)$gid) {
                return $properties['title'] ?? null;
            }
        }
        return $sheets[0]['properties']['title'] ?? null;
    }

    private function normalizeSpreadsheetHeader($header)
    {
        if (!$header) return '';
        $normalized = strtolower(trim((string)$header));
        $normalized = str_replace('_', ' ', $normalized);
        $normalized = preg_replace('/[^a-z0-9]+/i', ' ', $normalized);
        return preg_replace('/\s+/', ' ', $normalized);
    }
}
