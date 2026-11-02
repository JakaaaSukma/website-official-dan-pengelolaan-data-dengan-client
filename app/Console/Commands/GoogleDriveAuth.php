<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Console\Command;

class GoogleDriveAuth extends Command
{
    protected $signature = 'gdrive:auth';
    protected $description = 'Authenticate with Google Drive via OAuth2 to obtain a refresh token for file uploads';

    public function handle()
    {
        $secretPath = storage_path('app/google/client_secret.json');
        if (!file_exists($secretPath)) {
            $this->error('client_secret.json not found at: ' . $secretPath);
            return 1;
        }

        $client = new Client();
        $client->setAuthConfig($secretPath);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->addScope(Drive::DRIVE);
        $client->setRedirectUri(config('app.url') . '/gdrive/callback');

        $authUrl = $client->createAuthUrl();
        $this->info('');
        $this->info('=== Google Drive OAuth2 Authentication ===');
        $this->info('');
        $this->info('Langkah 1: Buka URL berikut di browser Anda:');
        $this->newLine();
        $this->line($authUrl);
        $this->newLine();
        $this->info('Langkah 2: Login dengan akun Google pemilik folder "pelamar non member"');
        $this->info('           (gyokairesepsionis@gmail.com)');
        $this->newLine();
        $this->info('Langkah 3: Setelah mengizinkan, Anda akan diredirect dan token akan otomatis tersimpan.');
        $this->info('           Atau copy authorization code dan paste di sini:');
        $this->newLine();

        $authCode = $this->ask('Paste authorization code (atau tekan Enter jika sudah via callback)');

        if (!empty($authCode)) {
            return $this->processAuthCode($client, trim($authCode));
        }

        // Check if token was saved via callback
        $tokenPath = storage_path('app/google/gdrive-token.json');
        if (file_exists($tokenPath)) {
            $this->info('✅ Token sudah tersimpan via callback route!');
            return 0;
        }

        $this->warn('Token belum tersimpan. Silakan coba lagi.');
        return 1;
    }

    protected function processAuthCode(Client $client, string $authCode): int
    {
        try {
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            if (isset($accessToken['error'])) {
                $this->error('Error: ' . ($accessToken['error_description'] ?? $accessToken['error']));
                return 1;
            }

            $tokenPath = storage_path('app/google/gdrive-token.json');
            file_put_contents($tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));

            $this->info('');
            $this->info('✅ Token berhasil disimpan ke: ' . $tokenPath);
            $this->info('');
            $this->info('Google Drive sekarang siap digunakan untuk upload file CV!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error fetching token: ' . $e->getMessage());
            return 1;
        }
    }
}
