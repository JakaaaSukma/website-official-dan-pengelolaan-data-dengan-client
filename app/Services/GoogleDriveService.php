<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleDriveService
{
    protected $client = null;
    protected $service = null;
    protected $isConfigured = false;
    protected $authType = 'none';
    protected $folderName = 'pelamar non member';

    public function __construct()
    {
        $this->initializeClient();
    }

    protected function initializeClient()
    {
        if (!class_exists('Google\Client') || !class_exists('Google\Service\Drive')) {
            Log::warning('Google Drive API classes not found.');
            return;
        }

        // Prioritas 1: OAuth2 user token (untuk akun Gmail biasa)
        $tokenPath = storage_path('app/google/gdrive-token.json');
        $secretPath = storage_path('app/google/client_secret.json');

        if (file_exists($tokenPath) && file_exists($secretPath)) {
            try {
                $this->client = new Client();
                $this->client->setAuthConfig($secretPath);
                $this->client->setAccessType('offline');
                $this->client->addScope(Drive::DRIVE);

                $token = json_decode(file_get_contents($tokenPath), true);
                $this->client->setAccessToken($token);

                // Refresh token jika expired
                if ($this->client->isAccessTokenExpired()) {
                    if ($this->client->getRefreshToken()) {
                        $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                        // Preserve refresh_token (Google tidak selalu mengembalikannya)
                        if (!isset($newToken['refresh_token']) && isset($token['refresh_token'])) {
                            $newToken['refresh_token'] = $token['refresh_token'];
                        }
                        file_put_contents($tokenPath, json_encode($newToken, JSON_PRETTY_PRINT));
                        $this->client->setAccessToken($newToken);
                    } else {
                        Log::error('Google Drive OAuth token expired and no refresh token available. Run: php artisan gdrive:auth');
                        return;
                    }
                }

                $this->service = new Drive($this->client);
                $this->isConfigured = true;
                $this->authType = 'oauth';
                return;
            } catch (\Exception $e) {
                Log::error('Failed to init Google Drive OAuth client: ' . $e->getMessage());
            }
        }

        // Fallback: Service Account (untuk Google Workspace / Shared Drive)
        $serviceAccountPath = config('services.google.service_account_path', 'google/service-account.json');
        $fullPath = null;

        if ($serviceAccountPath && Storage::exists($serviceAccountPath)) {
            $fullPath = Storage::path($serviceAccountPath);
        } elseif (file_exists(storage_path('app/' . $serviceAccountPath))) {
            $fullPath = storage_path('app/' . $serviceAccountPath);
        }

        if ($fullPath && file_exists($fullPath)) {
            try {
                $this->client = new Client();
                $this->client->setAuthConfig($fullPath);
                $this->client->addScope(Drive::DRIVE);
                $this->service = new Drive($this->client);
                $this->isConfigured = true;
                $this->authType = 'service_account';
            } catch (\Exception $e) {
                Log::error('Failed to init Google Drive client: ' . $e->getMessage());
                $this->isConfigured = false;
            }
        }
    }

    public function isReady(): bool
    {
        return $this->isConfigured && $this->service !== null;
    }

    public function getAuthType(): string
    {
        return $this->authType;
    }

    public function hasOAuthToken(): bool
    {
        return file_exists(storage_path('app/google/gdrive-token.json'));
    }

    /**
     * Get or create folder 'pelamar non member' in Google Drive
     * Searches all accessible folders including shared ones
     */
    public function getOrCreateFolder(string $folderName = null): ?string
    {
        if (!$this->isReady()) {
            return null;
        }

        $configuredFolderId = config('services.google.drive_folder_id');
        if ($configuredFolderId) {
            return $configuredFolderId;
        }

        $folderName = $folderName ?: $this->folderName;

        try {
            // Search for existing folder (including shared folders)
            $query = "mimeType = 'application/vnd.google-apps.folder' and name = '" . addslashes($folderName) . "' and trashed = false";
            $response = $this->service->files->listFiles([
                'q' => $query,
                'spaces' => 'drive',
                'fields' => 'files(id, name)',
                'pageSize' => 1,
                'includeItemsFromAllDrives' => true,
                'supportsAllDrives' => true,
            ]);

            $files = $response->getFiles();
            if (!empty($files)) {
                return $files[0]->getId();
            }

            // Create folder if not found
            $fileMetadata = new DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
            ]);

            $folder = $this->service->files->create($fileMetadata, [
                'fields' => 'id',
                'supportsAllDrives' => true,
            ]);

            return $folder->getId();
        } catch (\Exception $e) {
            Log::error('Google Drive getOrCreateFolder error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload a file to Google Drive folder
     * 
     * @param string $localFilePath Absolute path to local file
     * @param string $targetFileName Desired file name in Google Drive
     * @param string $mimeType Mime type
     * @return array [success => bool, file_id => ?string, web_view_link => ?string, error => ?string]
     */
    public function uploadFile(string $localFilePath, string $targetFileName, string $mimeType = 'application/pdf'): array
    {
        if (!$this->isReady()) {
            return [
                'success' => false,
                'file_id' => null,
                'web_view_link' => null,
                'error' => 'Google Drive not configured. Run: php artisan gdrive:auth',
            ];
        }

        try {
            $folderId = $this->getOrCreateFolder();

            if (!$folderId) {
                return [
                    'success' => false,
                    'file_id' => null,
                    'web_view_link' => null,
                    'error' => 'Folder "pelamar non member" not found and could not be created.',
                ];
            }

            $fileMetadata = new DriveFile([
                'name' => $targetFileName,
                'parents' => [$folderId],
            ]);

            $content = file_get_contents($localFilePath);

            $file = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id, name, webViewLink, webContentLink',
                'supportsAllDrives' => true,
            ]);

            // Make file accessible via link (reader)
            try {
                $permission = new \Google\Service\Drive\Permission([
                    'type' => 'anyone',
                    'role' => 'reader',
                ]);
                $this->service->permissions->create($file->getId(), $permission, [
                    'supportsAllDrives' => true,
                ]);
            } catch (\Exception $permEx) {
                Log::info('Could not set public permission on Drive file: ' . $permEx->getMessage());
            }

            return [
                'success' => true,
                'file_id' => $file->getId(),
                'web_view_link' => $file->getWebViewLink() ?: 'https://drive.google.com/file/d/' . $file->getId() . '/view',
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Google Drive uploadFile error: ' . $e->getMessage());
            return [
                'success' => false,
                'file_id' => null,
                'web_view_link' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
