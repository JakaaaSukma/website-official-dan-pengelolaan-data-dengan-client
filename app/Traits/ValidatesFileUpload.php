<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Provides file upload security scanning for controllers.
 * 
 * Validates uploaded files against:
 * - Dangerous file extensions (executables, scripts)
 * - MIME type spoofing (double extension attacks)
 * - File content magic bytes (detects disguised executables)
 * - Maximum file size enforcement
 */
trait ValidatesFileUpload
{
    /**
     * Dangerous file extensions that should NEVER be uploaded.
     */
    protected array $dangerousExtensions = [
        'exe', 'bat', 'cmd', 'com', 'msi', 'scr', 'pif',  // Windows executables
        'php', 'phtml', 'php3', 'php4', 'php5', 'phps',     // PHP scripts
        'sh', 'bash', 'cgi', 'pl', 'py', 'rb',              // Server scripts
        'js', 'jsx', 'ts', 'tsx',                             // JavaScript (server-side)
        'asp', 'aspx', 'jsp', 'jspx',                        // Other server pages
        'htaccess', 'htpasswd',                                // Apache config
        'vbs', 'vbe', 'wsf', 'wsh', 'ps1',                   // Windows scripting
        'svg',                                                 // Can contain XSS
        'swf',                                                 // Flash (deprecated, dangerous)
    ];

    /**
     * Magic bytes signatures of known dangerous file types.
     * Format: [hex_signature => description]
     */
    protected array $dangerousMagicBytes = [
        '4D5A'       => 'Windows Executable (MZ)',        // .exe, .dll
        '7F454C46'   => 'Linux Executable (ELF)',          // Linux binary
        'CAFEBABE'   => 'Java Class File',                 // .class
        '23212F'     => 'Script with Shebang (#!/)',       // #!/bin/bash, #!/usr/bin/python
    ];

    /**
     * Allowed MIME types per upload category.
     */
    protected array $allowedMimeTypes = [
        'image' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp',
        ],
        'document' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
            'text/plain',
        ],
        'archive' => [
            'application/zip',
            'application/x-zip-compressed',
            'application/gzip',
        ],
        'any' => [], // Skip MIME validation if empty
    ];

    /**
     * Validate an uploaded file for security threats.
     *
     * @param UploadedFile $file The uploaded file to validate
     * @param string $category One of: 'image', 'document', 'archive', 'any'
     * @param int $maxSizeMB Maximum file size in megabytes (default: 10)
     * @return array ['safe' => bool, 'message' => string]
     */
    protected function validateUploadSecurity(UploadedFile $file, string $category = 'any', int $maxSizeMB = 10): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        // 1. Check file size
        $maxBytes = $maxSizeMB * 1024 * 1024;
        if ($fileSize > $maxBytes) {
            $this->logSecurityEvent('file_too_large', $originalName, "Size: {$fileSize} bytes, Max: {$maxBytes} bytes");
            return ['safe' => false, 'message' => "Ukuran file melebihi batas maksimum ({$maxSizeMB}MB)."];
        }

        // 2. Check dangerous extension
        if (in_array($extension, $this->dangerousExtensions, true)) {
            $this->logSecurityEvent('dangerous_extension', $originalName, "Extension: .{$extension}");
            return ['safe' => false, 'message' => "Tipe file .{$extension} tidak diizinkan untuk alasan keamanan."];
        }

        // 3. Check double extension attacks (e.g., "file.php.jpg" or "file.exe.pdf")
        $nameParts = explode('.', $originalName);
        if (count($nameParts) > 2) {
            foreach ($nameParts as $part) {
                $lowerPart = strtolower($part);
                if (in_array($lowerPart, $this->dangerousExtensions, true)) {
                    $this->logSecurityEvent('double_extension_attack', $originalName, "Hidden extension: .{$lowerPart}");
                    return ['safe' => false, 'message' => "Nama file mencurigakan terdeteksi. Pastikan file tidak mengandung ekstensi berbahaya."];
                }
            }
        }

        // 4. Check MIME type if category is specified
        if ($category !== 'any' && isset($this->allowedMimeTypes[$category]) && !empty($this->allowedMimeTypes[$category])) {
            if (!in_array($mimeType, $this->allowedMimeTypes[$category], true)) {
                $this->logSecurityEvent('invalid_mime_type', $originalName, "MIME: {$mimeType}, Expected: {$category}");
                return ['safe' => false, 'message' => "Tipe file {$mimeType} tidak sesuai dengan kategori yang diizinkan ({$category})."];
            }
        }

        // 5. Check magic bytes (file content header) for disguised executables
        $magicBytesResult = $this->checkMagicBytes($file);
        if (!$magicBytesResult['safe']) {
            $this->logSecurityEvent('malicious_magic_bytes', $originalName, $magicBytesResult['message']);
            return $magicBytesResult;
        }

        // 6. Check for PHP code inside uploaded files (prevents PHP injection)
        if ($this->containsPhpCode($file)) {
            $this->logSecurityEvent('php_code_injection', $originalName, 'File contains PHP opening tags');
            return ['safe' => false, 'message' => "File mengandung kode berbahaya dan ditolak."];
        }

        return ['safe' => true, 'message' => 'File aman.'];
    }

    /**
     * Check file magic bytes to detect disguised executables.
     */
    protected function checkMagicBytes(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            return ['safe' => true, 'message' => 'Unable to read file header'];
        }

        $header = fread($handle, 8);
        fclose($handle);

        if ($header === false || strlen($header) < 2) {
            return ['safe' => true, 'message' => 'File too small to check'];
        }

        $hexHeader = strtoupper(bin2hex($header));

        foreach ($this->dangerousMagicBytes as $signature => $description) {
            if (str_starts_with($hexHeader, $signature)) {
                return [
                    'safe' => false,
                    'message' => "File terdeteksi sebagai {$description}. Upload ditolak.",
                ];
            }
        }

        return ['safe' => true, 'message' => 'Magic bytes check passed'];
    }

    /**
     * Check if file content contains PHP code (prevents PHP injection attacks).
     */
    protected function containsPhpCode(UploadedFile $file): bool
    {
        // Only check text-based files, not binary
        $mimeType = $file->getMimeType();
        $textMimes = ['text/', 'application/xml', 'application/json', 'image/svg'];
        
        $isTextFile = false;
        foreach ($textMimes as $textMime) {
            if (str_contains($mimeType, $textMime)) {
                $isTextFile = true;
                break;
            }
        }

        if (!$isTextFile) {
            return false;
        }

        // Read first 8KB and check for PHP tags
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) return false;

        $content = fread($handle, 8192);
        fclose($handle);

        return str_contains($content, '<?php') || str_contains($content, '<?=');
    }

    /**
     * Log file upload security events.
     */
    protected function logSecurityEvent(string $type, string $filename, string $detail): void
    {
        Log::channel('activity')->warning("FILE SECURITY: [{$type}] {$filename}", [
            'type' => $type,
            'filename' => $filename,
            'detail' => $detail,
            'ip' => request()->ip(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
