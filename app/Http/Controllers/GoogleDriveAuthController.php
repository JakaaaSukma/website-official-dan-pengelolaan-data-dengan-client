<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleDriveAuthController extends Controller
{
    protected function getClient(string $redirectUri = null): ?Client
    {
        $secretPath = storage_path('app/google/client_secret.json');
        if (!file_exists($secretPath)) {
            return null;
        }

        $client = new Client();
        $client->setAuthConfig($secretPath);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->addScope(Drive::DRIVE);

        if ($redirectUri) {
            $client->setRedirectUri($redirectUri);
        }

        return $client;
    }

    /**
     * Tampilan status koneksi Google Drive & panduan otorisasi
     */
    public function index(Request $request, GoogleDriveService $driveService)
    {
        $tokenPath = storage_path('app/google/gdrive-token.json');
        $secretPath = storage_path('app/google/client_secret.json');

        $hasSecret = file_exists($secretPath);
        $hasToken = file_exists($tokenPath);
        $isConnected = false;
        $accountEmail = null;
        $tokenInfo = null;

        // Redirect URI yang disarankan berdasarkan domain & port saat ini
        $currentRedirectUri = $request->getSchemeAndHttpHost() . '/gdrive/callback';

        if ($hasToken) {
            $raw = file_get_contents($tokenPath);
            $tokenInfo = json_decode($raw, true);
            $isConnected = $driveService->isReady();
        }

        // Auto-redirect ke Google OAuth jika belum terhubung dan bukan kunjungan balik dari callback
        if (!$isConnected && $hasSecret && !$request->has('manual')) {
            $client = $this->getClient($currentRedirectUri);
            if ($client) {
                session(['gdrive_oauth_redirect_uri' => $currentRedirectUri]);
                return redirect()->away($client->createAuthUrl());
            }
        }

        $authUrl = null;
        if ($hasSecret) {
            $client = $this->getClient($currentRedirectUri);
            if ($client) {
                $authUrl = $client->createAuthUrl();
            }
        }

        $folderId = config('services.google.drive_folder_id', '1uI1JD7ep85E-c2ML1SuIrCKYZbVs6fp-');

        return view('company.gdrive.connect', compact(
            'hasSecret',
            'hasToken',
            'isConnected',
            'currentRedirectUri',
            'authUrl',
            'tokenInfo',
            'folderId'
        ));
    }

    /**
     * Redirect langsung ke Google OAuth consent screen
     */
    public function redirect(Request $request)
    {
        $redirectUri = $request->input('redirect_uri') ?: ($request->getSchemeAndHttpHost() . '/gdrive/callback');
        session(['gdrive_oauth_redirect_uri' => $redirectUri]);

        $client = $this->getClient($redirectUri);
        if (!$client) {
            return back()->with('error', 'File storage/app/google/client_secret.json tidak ditemukan.');
        }

        return redirect()->away($client->createAuthUrl());
    }

    /**
     * Callback dari Google OAuth
     */
    public function callback(Request $request)
    {
        if ($request->filled('error')) {
            return redirect()->route('gdrive.connect', ['manual' => 1])->with('error', 'Google OAuth Error: ' . $request->input('error'));
        }

        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('gdrive.connect', ['manual' => 1])->with('error', 'Authorization code tidak ditemukan.');
        }

        $redirectUri = session('gdrive_oauth_redirect_uri') ?: ($request->getSchemeAndHttpHost() . '/gdrive/callback');

        $client = $this->getClient($redirectUri);
        if (!$client) {
            return redirect()->route('gdrive.connect', ['manual' => 1])->with('error', 'client_secret.json tidak ditemukan.');
        }

        try {
            $accessToken = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($accessToken['error'])) {
                $errorMsg = $accessToken['error_description'] ?? $accessToken['error'];
                return redirect()->route('gdrive.connect', ['manual' => 1])->with('error', 'Gagal menukar token: ' . $errorMsg);
            }

            $tokenPath = storage_path('app/google/gdrive-token.json');
            file_put_contents($tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));

            return redirect()->route('gdrive.connect', ['manual' => 1])->with('success', '✅ Google Drive berhasil terhubung! File CV pelamar akan otomatis terunggah ke Google Drive.');
        } catch (\Exception $e) {
            Log::error('Google Drive callback exception: ' . $e->getMessage());
            return redirect()->route('gdrive.connect', ['manual' => 1])->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Manual simpan authorization code jika redirect gagal
     */
    public function manualAuth(Request $request)
    {
        $request->validate([
            'auth_code' => 'required|string',
            'redirect_uri' => 'nullable|string',
        ]);

        $redirectUri = $request->input('redirect_uri') ?: ($request->getSchemeAndHttpHost() . '/gdrive/callback');
        $client = $this->getClient($redirectUri);

        if (!$client) {
            return back()->with('error', 'client_secret.json tidak ditemukan.');
        }

        try {
            $accessToken = $client->fetchAccessTokenWithAuthCode(trim($request->input('auth_code')));

            if (isset($accessToken['error'])) {
                $errorMsg = $accessToken['error_description'] ?? $accessToken['error'];
                return back()->with('error', 'Gagal menukar kode: ' . $errorMsg);
            }

            $tokenPath = storage_path('app/google/gdrive-token.json');
            file_put_contents($tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));

            return redirect()->route('gdrive.connect', ['manual' => 1])->with('success', '✅ Token Google Drive berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Tes Upload File ke Google Drive
     */
    public function testUpload(GoogleDriveService $driveService)
    {
        if (!$driveService->isReady()) {
            return response()->json([
                'success' => false,
                'message' => 'Google Drive belum siap atau token belum tersedia.',
            ]);
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'gdrive_test_');
        $timestamp = date('d-m-Y H:i:s');
        file_put_contents($tmpFile, "Uji coba upload Google Drive LPK Gyokai berhasil pada: {$timestamp}\nFolder: pelamar non member");

        $fileName = 'TES_KONEKSI_' . date('Ymd_His') . '.txt';
        $result = $driveService->uploadFile($tmpFile, $fileName, 'text/plain');

        if (file_exists($tmpFile)) {
            @unlink($tmpFile);
        }

        return response()->json($result);
    }

    /**
     * Putuskan koneksi Google Drive
     */
    public function disconnect()
    {
        $tokenPath = storage_path('app/google/gdrive-token.json');
        if (file_exists($tokenPath)) {
            @unlink($tokenPath);
        }

        return redirect()->route('gdrive.connect', ['manual' => 1])->with('success', 'Koneksi Google Drive berhasil diputuskan.');
    }
}
