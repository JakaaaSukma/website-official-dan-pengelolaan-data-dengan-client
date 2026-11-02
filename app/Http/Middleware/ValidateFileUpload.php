<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware that automatically scans ALL uploaded files in every request
 * for dangerous content before the request reaches any controller.
 *
 * This is a defense-in-depth layer that catches malicious uploads
 * regardless of whether individual controllers call validateUploadSecurity().
 *
 * Checks:
 * - Dangerous file extensions (.php, .exe, .sh, etc.)
 * - Double extension attacks (file.php.jpg)
 * - Magic bytes (disguised executables: MZ, ELF headers)
 * - Embedded PHP code in text files
 */
class ValidateFileUpload
{
    /**
     * Dangerous file extensions that should NEVER be uploaded.
     */
    protected array $dangerousExtensions = [
        'exe', 'bat', 'cmd', 'com', 'msi', 'scr', 'pif',
        'php', 'phtml', 'php3', 'php4', 'php5', 'phps',
        'sh', 'bash', 'cgi', 'pl', 'py', 'rb',
        'asp', 'aspx', 'jsp', 'jspx',
        'htaccess', 'htpasswd',
        'vbs', 'vbe', 'wsf', 'wsh', 'ps1',
        'svg', 'swf',
    ];

    /**
     * Magic bytes of known dangerous file types.
     */
    protected array $dangerousMagicBytes = [
        '4D5A'     => 'Windows Executable (MZ)',
        '7F454C46' => 'Linux Executable (ELF)',
        'CAFEBABE' => 'Java Class File',
        '23212F'   => 'Script with Shebang',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check requests that have files
        if (!$request->allFiles()) {
            return $next($request);
        }

        // Flatten all files (handles nested arrays like 'images[]')
        $allFiles = $this->flattenFiles($request->allFiles());

        foreach ($allFiles as $fieldName => $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            $result = $this->scanFile($file);

            if (!$result['safe']) {
                Log::channel('activity')->warning('UPLOAD BLOCKED by middleware', [
                    'field' => $fieldName,
                    'filename' => $file->getClientOriginalName(),
                    'reason' => $result['message'],
                    'ip' => $request->ip(),
                    'user_id' => auth()->id(),
                    'url' => $request->fullUrl(),
                ]);

                // Return error based on request type
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $result['message'],
                    ], 422);
                }

                return back()->with('error', $result['message'])->withInput();
            }
        }

        return $next($request);
    }

    /**
     * Scan a single file for security threats.
     */
    protected function scanFile(UploadedFile $file): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());

        // 1. Check dangerous extension
        if (in_array($extension, $this->dangerousExtensions, true)) {
            return ['safe' => false, 'message' => "Tipe file .{$extension} tidak diizinkan untuk alasan keamanan."];
        }

        // 2. Check double extension attacks (file.php.jpg)
        $nameParts = explode('.', $originalName);
        if (count($nameParts) > 2) {
            foreach ($nameParts as $part) {
                if (in_array(strtolower($part), $this->dangerousExtensions, true)) {
                    return ['safe' => false, 'message' => "Nama file mencurigakan terdeteksi. File ditolak."];
                }
            }
        }

        // 3. Check magic bytes for disguised executables
        $handle = @fopen($file->getRealPath(), 'rb');
        if ($handle) {
            $header = fread($handle, 8);
            fclose($handle);

            if ($header && strlen($header) >= 2) {
                $hexHeader = strtoupper(bin2hex($header));
                foreach ($this->dangerousMagicBytes as $signature => $description) {
                    if (str_starts_with($hexHeader, $signature)) {
                        return ['safe' => false, 'message' => "File terdeteksi sebagai {$description}. Upload ditolak."];
                    }
                }
            }
        }

        // 4. Check for PHP code in text-based files
        $mimeType = $file->getMimeType() ?? '';
        $isText = str_contains($mimeType, 'text/') || str_contains($mimeType, 'xml') || str_contains($mimeType, 'json');

        if ($isText) {
            $handle = @fopen($file->getRealPath(), 'r');
            if ($handle) {
                $content = fread($handle, 8192);
                fclose($handle);
                if ($content && (str_contains($content, '<?php') || str_contains($content, '<?='))) {
                    return ['safe' => false, 'message' => "File mengandung kode berbahaya dan ditolak."];
                }
            }
        }

        return ['safe' => true, 'message' => 'OK'];
    }

    /**
     * Flatten nested file arrays into a single-level array.
     */
    protected function flattenFiles(array $files, string $prefix = ''): array
    {
        $result = [];

        foreach ($files as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            if ($value instanceof UploadedFile) {
                $result[$fullKey] = $value;
            } elseif (is_array($value)) {
                $result = array_merge($result, $this->flattenFiles($value, $fullKey));
            }
        }

        return $result;
    }
}
