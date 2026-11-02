<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use App\Models\PayslipLog;
use App\Models\LoginLog;
use App\Models\PayslipSendSession;
use App\Models\SmtpMailer;
use Smalot\PdfParser\Parser;
use App\Traits\ValidatesFileUpload;

class FinanceController extends Controller
{
    use ValidatesFileUpload;
    public function dashboard()
    {
        $this->ensureSmtpMailersAvailable();

        $logs = LoginLog::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $sessionStats = [
            'total_sessions' => PayslipSendSession::where('user_id', Auth::id())->count(),
            'processing_sessions' => PayslipSendSession::where('user_id', Auth::id())->where('status', 'processing')->count(),
            'successful_deliveries' => PayslipLog::where('user_id', Auth::id())->where('status', 'success')->count(),
            'failed_deliveries' => PayslipLog::where('user_id', Auth::id())->where('status', 'failed')->count(),
        ];

        $recentSessions = PayslipSendSession::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $smtpMailers = SmtpMailer::query()
            ->orderBy('priority')
            ->get()
            ->map(function ($mailer) {
                $statusLabel = 'Aktif';
                $statusClass = 'active';

                if ($mailer->cooldown_until && $mailer->cooldown_until->isFuture()) {
                    if ($mailer->last_limit_reached_at) {
                        $statusLabel = 'Limit Harian';
                        $statusClass = 'limit';
                    } else {
                        $statusLabel = 'Sementara Bermasalah';
                        $statusClass = 'temporary_issue';
                    }
                } elseif ($mailer->status !== 'active') {
                    $statusLabel = 'Nonaktif';
                    $statusClass = 'inactive';
                }

                return [
                    'id' => $mailer->id,
                    'mailer_name' => $mailer->mailer_name,
                    'from_address' => $mailer->from_address,
                    'priority' => $mailer->priority,
                    'sent_today' => $mailer->daily_sent_count,
                    'daily_limit' => $mailer->daily_limit,
                    'remaining_local_quota' => max(0, (int) $mailer->daily_limit - (int) $mailer->daily_sent_count),
                    'cooldown_until' => $mailer->cooldown_until && $mailer->cooldown_until->isFuture()
                        ? $mailer->cooldown_until
                        : null,
                    'status_label' => $statusLabel,
                    'status_class' => $statusClass,
                    'gmail_limit_detected_early' => (bool) $mailer->last_limit_reached_at && (int) $mailer->daily_sent_count < (int) $mailer->daily_limit,
                    'gmail_limit_gap' => max(0, (int) $mailer->daily_limit - (int) $mailer->daily_sent_count),
                ];
            });

        return view('finance.dashboard', compact('logs', 'sessionStats', 'recentSessions', 'smtpMailers'));
    }

    public function payslips(Request $request)
    {
        $this->ensureSmtpMailersAvailable();

        $query = PayslipLog::with(['sendSession', 'smtpMailer'])->where('user_id', Auth::id());
        $smtpMailerOptions = SmtpMailer::query()
            ->orderBy('priority')
            ->get(['id', 'mailer_name', 'from_address']);

        // Filter by Date
        if ($request->has('filter_date') && !empty($request->filter_date)) {
            $query->whereDate('created_at', $request->filter_date);
        }

        // Filter by Status
        if ($request->has('filter_status') && $request->filter_status !== 'all') {
            $query->where('status', $request->filter_status);
        }

        if ($request->has('filter_mailer') && $request->filter_mailer !== 'all' && !empty($request->filter_mailer)) {
            $query->where('smtp_mailer_id', $request->filter_mailer);
        }

        $sortBy = $request->input('sort_by', 'time_desc');

        if ($sortBy === 'time_asc') {
            $query->orderBy('created_at', 'asc')
                ->orderBy('filename', 'asc');
        } else {
            $query->orderBy('created_at', 'desc')
                ->orderBy('filename', 'asc');
        }

        $logs = $query->paginate(100, ['*'], 'logs_page')->appends($request->all());
        $sendSessions = PayslipSendSession::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'sessions_page')
            ->appends($request->all());
            
        return view('finance.payslips.index', compact('logs', 'sendSessions', 'smtpMailerOptions'));
    }

    public function showChangePasswordForm()
    {
        return view('finance.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|max:255',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password.regex' => 'Password baru harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak benar.'])->withInput();
        }

        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'Password baru harus berbeda dengan password saat ini.'])->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        Log::channel('activity')->info('Password changed (finance)', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return redirect()->route('finance.change-password')->with('success', 'Password berhasil diubah.');
    }

    public function uploadPayslips(Request $request)
    {
        $request->validate([
            'payslip_file' => 'required|file|mimes:pdf,zip|max:102400', // Max 100MB
        ]);

        // Create a unique temporary directory for this session
        $sessionId = session()->getId();
        $timestamp = time();
        $tempDirName = "payslips_{$timestamp}_{$sessionId}";
        $storagePath = storage_path('app/temp/' . $tempDirName);
        
        // Ensure directory exists
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        try {
            if ($request->hasFile('payslip_file')) {
                $file = $request->file('payslip_file');
                
                // Security: validate file before processing
                $securityCheck = $this->validateUploadSecurity($file, 'any', 50);
                if (!$securityCheck['safe']) {
                    return back()->with('error', $securityCheck['message']);
                }
                
                $extension = $file->getClientOriginalExtension();
                
                if ($extension === 'pdf') {
                    $filename = $file->getClientOriginalName();
                    $file->move($storagePath, $filename);
                } elseif ($extension === 'zip') {
                    $zipPath = $file->storeAs('temp', 'upload.zip');
                    $zip = new \ZipArchive;
                    if ($zip->open(storage_path('app/' . $zipPath)) === TRUE) {
                        $zip->extractTo($storagePath);
                        $zip->close();
                        Storage::delete($zipPath);
                    } else {
                        return back()->with('error', 'Gagal mengekstrak file ZIP.');
                    }
                }
                
                // Store temp path in session for the next step
                session(['payslip_temp_dir' => $storagePath]);
                
                // Use Smalot/PdfParser for robust PDF text extraction
                $parser = new Parser();
                $files = [];
                // Regex updated to be more permissive with surrounding characters but strict on format
                $emailRegex = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

                // Scan directory for PDFs
                $pdfFiles = glob($storagePath . '/*.pdf');
                
                foreach ($pdfFiles as $pdfFile) {
                    $filename = basename($pdfFile);
                    
                    try {
                        $pdf = $parser->parseFile($pdfFile);
                        $text = $pdf->getText();
                        $period = $this->extractPayslipPeriodFromText($text);
                        
                        // Extract email
                        $email = '';
                        if (preg_match($emailRegex, $text, $matches)) {
                            $email = $matches[0];
                        }
                    } catch (\Exception $e) {
                        Log::warning("Failed to parse PDF $filename: " . $e->getMessage());
                        $text = ""; // Fallback
                        $email = "";
                        $period = null;
                    }
                    
                    // Extract name from filename (Simple logic matching Python script)
                    // Remove extension
                    $namePart = pathinfo($filename, PATHINFO_FILENAME);
                    // Remove leading digits/symbols
                    $name = preg_replace('/^[\d\W_]+/', '', $namePart);
                    $name = trim($name);
                    if (empty($name)) {
                        $name = "Karyawan";
                    }

                    $files[] = [
                        "filename" => $filename,
                        "filepath" => $pdfFile, // internal path
                        "email" => $email,
                        "period" => $period,
                        "name" => $name,
                        "status" => $email ? "valid" : "invalid",
                        "message" => $email ? "Email found" : "Email not found"
                    ];
                }
                
                if (empty($files)) {
                    return back()->with('error', 'Tidak ada file PDF ditemukan dalam upload.');
                }

                return view('finance.payslips.preview', compact('files'));
            }
        } catch (\Exception $e) {
            Log::error('Payslip Preview Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }

        return back()->with('error', 'Gagal mengupload file.');
    }

    public function startPayslipSending(Request $request)
    {
        $tempDir = session('payslip_temp_dir');
        if (!$tempDir || !file_exists($tempDir)) {
            return redirect()->route('finance.payslips')->with('error', 'Sesi kadaluarsa atau file tidak ditemukan. Silakan upload ulang.');
        }

        $globalCc = $request->input('global_cc');
        $globalBcc = $request->input('global_bcc');
        $incoming = $request->input('selected_files', []);
        $queue = [];

        foreach ($incoming as $fileData) {
            if (isset($fileData['filename']) && !empty($fileData['email'])) {
                $queue[] = [
                    'filename' => $fileData['filename'],
                    'email' => $fileData['email'],
                    'cc' => $globalCc,
                    'bcc' => $globalBcc,
                    'period' => $fileData['period'] ?? null,
                ];
            }
        }

        if (empty($queue)) {
            return back()->with('warning', 'Tidak ada antrian pengiriman ditemukan.');
        }

        $sendSession = PayslipSendSession::create([
            'user_id' => Auth::id(),
            'session_code' => 'PS-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5)),
            'total_files' => count($queue),
            'success_count' => 0,
            'failed_count' => 0,
            'status' => 'processing',
            'temp_dir_path' => $tempDir,
            'pending_queue' => array_values($queue),
            'started_at' => now(),
        ]);

        session([
            'sending_queue' => array_values($queue),
            'sending_total' => count($queue),
            'sending_stats' => ['success' => 0, 'failed' => 0],
            'current_payslip_send_session_id' => $sendSession->id,
            'last_smtp_mailer_id' => null,
        ]);

        return redirect()->route('finance.payslips.sending', ['send_session_id' => $sendSession->id]);
    }

    public function showPayslipSending(Request $request)
    {
        $requestedSendSessionId = (int) $request->query('send_session_id');
        $tempDir = session('payslip_temp_dir');
        $queue = session('sending_queue', []);
        $total = session('sending_total');
        $sendSessionId = session('current_payslip_send_session_id');

        if ($requestedSendSessionId && $requestedSendSessionId !== (int) $sendSessionId) {
            $sendSession = PayslipSendSession::find($requestedSendSessionId);

            if ($sendSession) {
                $this->restorePayslipSendingSessionState($sendSession);
                $tempDir = session('payslip_temp_dir');
                $queue = session('sending_queue', []);
                $total = session('sending_total');
                $sendSessionId = session('current_payslip_send_session_id');
            }
        }

        if (!$tempDir || !file_exists($tempDir) || $total === null) {
            $sendSession = $requestedSendSessionId ? PayslipSendSession::find($requestedSendSessionId) : null;

            if ($sendSession && $sendSession->status === 'processing') {
                return view('finance.payslips.sending', array_merge(
                    $this->getPayslipStoredProgressData($sendSession),
                    [
                        'canCancelSending' => !empty($sendSession->pending_queue),
                        'activeSendSessionId' => $sendSession->id,
                        'reportUrl' => route('finance.payslips.reports.show', $sendSession),
                        'autoProcessEnabled' => false,
                        'sendingUnavailableReason' => 'Halaman proses berhasil dibuka kembali, tetapi state sementara browser untuk melanjutkan batch sudah hilang setelah error sebelumnya. Silakan batalkan sesi ini lalu lanjutkan pengiriman dari sisa file yang belum berhasil.',
                    ]
                ));
            }

            return redirect()->route('finance.payslips')->with('error', 'Sesi pengiriman tidak ditemukan. Silakan upload ulang.');
        }

        if ($total === 0) {
            $this->clearPayslipSendingSession($tempDir, $sendSessionId);

            return redirect()->route('finance.payslips')->with('warning', 'Tidak ada data pengiriman yang dapat diproses.');
        }

        return view('finance.payslips.sending', array_merge(
            $this->getPayslipSendingProgressData($queue, $total),
            [
                'canCancelSending' => !empty($queue) && !empty($sendSessionId),
                'activeSendSessionId' => $sendSessionId,
                'reportUrl' => $sendSessionId ? route('finance.payslips.reports.show', $sendSessionId) : route('finance.payslips'),
                'autoProcessEnabled' => true,
                'sendingUnavailableReason' => null,
            ]
        ));
    }

    public function showPayslipReport(Request $request, PayslipSendSession $sendSession)
    {
        if (!Auth::user() || Auth::user()->role !== 'finance') {
            abort(403);
        }

        $filenameSort = $request->input('filename_sort', 'time_desc');
        $logsQuery = $sendSession->logs()->with('smtpMailer');

        if ($filenameSort === 'filename_asc') {
            $logsQuery->reorder()
                ->orderByRaw("CASE WHEN filename REGEXP '^[0-9]+' THEN 0 ELSE 1 END ASC")
                ->orderByRaw("CASE WHEN filename REGEXP '^[0-9]+' THEN CAST(SUBSTRING_INDEX(filename, '_', 1) AS UNSIGNED) END ASC")
                ->orderBy('filename', 'asc')
                ->orderBy('created_at', 'asc');
        } elseif ($filenameSort === 'filename_desc') {
            $logsQuery->reorder()
                ->orderByRaw("CASE WHEN filename REGEXP '^[0-9]+' THEN 0 ELSE 1 END ASC")
                ->orderByRaw("CASE WHEN filename REGEXP '^[0-9]+' THEN CAST(SUBSTRING_INDEX(filename, '_', 1) AS UNSIGNED) END DESC")
                ->orderBy('filename', 'desc')
                ->orderBy('created_at', 'desc');
        } else {
            $logsQuery->reorder()
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc');
        }

        $logs = $logsQuery->paginate(100)->appends($request->all());
        $currentSendSessionId = session('current_payslip_send_session_id');
        $tempDir = session('payslip_temp_dir');
        $canOpenSendingPage = $sendSession->status === 'processing';
        $hasActiveQueue = $tempDir && file_exists($tempDir) && !empty(session('sending_queue', [])) && session('sending_total') !== null;
        $canResumeSending = $hasActiveQueue && (int) $currentSendSessionId === (int) $sendSession->id;
        $canCancelSending = $canResumeSending || ($sendSession->status === 'processing' && !empty($sendSession->pending_queue));
        $remainingQueueCount = $canResumeSending ? count(session('sending_queue', [])) : 0;

        return view('finance.payslips.report', compact('sendSession', 'logs', 'filenameSort', 'canOpenSendingPage', 'canResumeSending', 'canCancelSending', 'remainingQueueCount'));
    }

    public function cancelPayslipSending(Request $request)
    {
        $tempDir = session('payslip_temp_dir');
        $sendSessionId = session('current_payslip_send_session_id');
        $requestedSessionId = (int) $request->input('send_session_id');
        $targetSendSessionId = $requestedSessionId ?: (int) $sendSessionId;

        if (!$targetSendSessionId) {
            return redirect()->route('finance.payslips')->with('warning', 'Tidak ada proses pengiriman aktif yang cocok untuk dibatalkan.');
        }

        $sendSession = PayslipSendSession::find($targetSendSessionId);

        if (!$sendSession) {
            $this->clearPayslipSendingSession($tempDir);

            return redirect()->route('finance.payslips')->with('warning', 'Sesi pengiriman aktif tidak ditemukan. Data sesi sementara sudah dibersihkan.');
        }

        if ((int) $sendSessionId !== (int) $sendSession->id) {
            $this->restorePayslipSendingSessionState($sendSession);
            $tempDir = session('payslip_temp_dir');
            $sendSessionId = session('current_payslip_send_session_id');
        }

        $queue = session('sending_queue', []);
        $stats = session('sending_stats', [
            'success' => $sendSession->success_count,
            'failed' => $sendSession->failed_count,
        ]);

        if (empty($queue)) {
            $queue = $sendSession->pending_queue ?? [];
        }

        foreach ($queue as $pendingFile) {
            $stats['failed']++;
            $this->createPayslipLogEntry([
                'user_id' => Auth::id(),
                'payslip_send_session_id' => $sendSession->id,
                'filename' => $pendingFile['filename'] ?? 'N/A',
                'recipient_email' => $pendingFile['email'] ?? 'N/A',
                'recipient_name' => $this->getPayslipRecipientNameFromFilename($pendingFile['filename'] ?? null),
                'period' => !empty($pendingFile['period']) ? trim($pendingFile['period']) : null,
                'status' => 'failed',
                'error_message' => 'Pengiriman dibatalkan oleh user finance sebelum file diproses.',
            ]);
        }

        $sendSession->update([
            'success_count' => $stats['success'] ?? 0,
            'failed_count' => $stats['failed'] ?? 0,
            'status' => 'cancelled',
            'pending_queue' => null,
            'temp_dir_path' => null,
            'finished_at' => now(),
        ]);

        $remainingQueueCount = count($queue);
        $this->clearPayslipSendingSession($tempDir, $sendSession->id);

        return redirect()->route('finance.payslips.reports.show', $sendSession)->with(
            'warning',
            "Proses pengiriman dibatalkan. {$remainingQueueCount} file yang belum diproses ditandai gagal."
        );
    }

    public function sendPayslips(Request $request)
    {
        try {
            set_time_limit(3600);
            ini_set('memory_limit', '512M');

            $tempDir = session('payslip_temp_dir');
            $sendSessionId = session('current_payslip_send_session_id');
            $requestedSendSessionId = (int) $request->input('send_session_id');

            if (($requestedSendSessionId && $requestedSendSessionId !== (int) $sendSessionId) || !$tempDir || !file_exists($tempDir) || !$sendSessionId) {
                $sendSession = $requestedSendSessionId ? PayslipSendSession::find($requestedSendSessionId) : null;

                if ($sendSession) {
                    $this->restorePayslipSendingSessionState($sendSession);
                    $tempDir = session('payslip_temp_dir');
                    $sendSessionId = session('current_payslip_send_session_id');
                }
            }

            if (!$tempDir || !file_exists($tempDir) || !$sendSessionId) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sesi kadaluarsa atau file tidak ditemukan. Silakan upload ulang.',
                        'redirectUrl' => route('finance.payslips'),
                    ], 422);
                }

                return redirect()->route('finance.payslips')->with('error', 'Sesi kadaluarsa atau file tidak ditemukan. Silakan upload ulang.');
            }

            $queue = session('sending_queue', []);
            $total = session('sending_total', 0);

            if (empty($queue)) {
                if ($total > 0) {
                    $this->updatePayslipSendSessionSummary($sendSessionId, session('sending_stats', ['success' => 0, 'failed' => 0]), 0);
                    $this->clearPayslipSendingSession($tempDir, $sendSessionId);

                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Antrian pengiriman sudah selesai diproses.',
                            'redirectUrl' => route('finance.payslips'),
                        ], 200);
                    }

                    return redirect()->route('finance.payslips.reports.show', $sendSessionId)->with('success', 'Pengiriman sudah selesai diproses. Berikut laporan sesi terakhir.');
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Tidak ada antrian pengiriman ditemukan.',
                        'redirectUrl' => route('finance.payslips'),
                    ], 422);
                }

                return redirect()->route('finance.payslips')->with('warning', 'Tidak ada antrian pengiriman ditemukan.');
            }

            $batchSize = $this->getPayslipBatchSize();
            $maxBatchExecutionSeconds = $this->getPayslipBatchExecutionLimit();
            $currentBatch = array_slice($queue, 0, $batchSize);
            $remainingQueue = array_slice($queue, $batchSize);

            $stats = session('sending_stats', ['success' => 0, 'failed' => 0]);
            $batchStartedAt = microtime(true);
            $unprocessedCurrentBatch = [];
            $dailyLimitReached = false;
            $dailyLimitMessage = null;

            foreach ($currentBatch as $index => $file) {
                if ($index > 0 && (microtime(true) - $batchStartedAt) >= $maxBatchExecutionSeconds) {
                    $unprocessedCurrentBatch = array_slice($currentBatch, $index);
                    break;
                }

                $filename = $file['filename'];
                $email = $file['email'];
                $cc = $file['cc'] ?? null;
                $bcc = $file['bcc'] ?? null;
                $filePath = $tempDir . '/' . $filename;
                $period = trim($file['period'] ?? '');

                if (!file_exists($filePath)) {
                    Log::warning("File not found for sending: $filename");
                    $stats['failed']++;
                    $this->createPayslipLogEntry([
                        'user_id' => Auth::id(),
                        'payslip_send_session_id' => $sendSessionId,
                        'filename' => $filename,
                        'recipient_email' => $email ?: 'N/A',
                        'recipient_name' => null,
                        'period' => $period ?: null,
                        'status' => 'failed',
                        'error_message' => 'File lampiran tidak ditemukan',
                    ]);
                    continue;
                }

                try {
                    $namePart = pathinfo($filename, PATHINFO_FILENAME);
                    $name = preg_replace('/^[\d\W_]+/', '', $namePart);
                    $name = trim($name) ?: "Karyawan";
                    $period = $period ?: ($this->extractPayslipPeriodFromFile($filePath) ?: date('F Y'));
                    $mailSendResult = [
                        'smtp_mailer_id' => null,
                        'mailer_name' => null,
                        'mailer_address' => null,
                    ];

                    if ($email) {
                        try {
                            $realPath = realpath($filePath) ?: $filePath;
                            $fileContent = @file_get_contents($realPath);
                            
                            if ($fileContent === false) {
                                throw new \RuntimeException("Tidak dapat membuka file lampiran: $filename pada path $realPath");
                            }

                            $mailSendResult = $this->sendPayslipViaRotatingMailers(
                                $email,
                                $cc,
                                $bcc,
                                $name,
                                $period,
                                $fileContent,
                                $filename
                            );

                            if (!$mailSendResult['success']) {
                                throw new \RuntimeException($mailSendResult['message'] ?? 'Pengiriman email gagal diproses.');
                            }

                            $stats['success']++;
                            Log::info("Email sent to $email for file $filename using {$mailSendResult['mailer_name']}");

                            $this->createPayslipLogEntry([
                                'user_id' => Auth::id(),
                                'payslip_send_session_id' => $sendSessionId,
                                'smtp_mailer_id' => $mailSendResult['smtp_mailer_id'] ?? null,
                                'smtp_mailer_name' => $mailSendResult['mailer_name'] ?? null,
                                'smtp_mailer_address' => $mailSendResult['mailer_address'] ?? null,
                                'filename' => $filename,
                                'recipient_email' => $email,
                                'recipient_name' => $name,
                                'period' => $period,
                                'status' => 'success',
                            ]);
                        } catch (\Exception $e) {
                            $errorMessage = $e->getMessage();

                            if ($this->isAllMailersUnavailableMessage($errorMessage)) {
                                $dailyLimitReached = true;
                                $dailyLimitMessage = $errorMessage;
                            } elseif (strpos($errorMessage, '452') !== false || strpos(strtolower($errorMessage), 'quota') !== false || strpos(strtolower($errorMessage), 'storage') !== false) {
                                $errorMessage = "GAGAL KIRIM: Inbox Penerima Penuh (Quota Exceeded). Harap hubungi karyawan untuk membersihkan email mereka.";
                            }

                            Log::error("Failed to send email for $filename: " . $errorMessage);
                            $stats['failed']++;
                            $this->createPayslipLogEntry([
                                'user_id' => Auth::id(),
                                'payslip_send_session_id' => $sendSessionId,
                                'smtp_mailer_id' => $mailSendResult['smtp_mailer_id'] ?? null,
                                'smtp_mailer_name' => $mailSendResult['mailer_name'] ?? null,
                                'smtp_mailer_address' => $mailSendResult['mailer_address'] ?? null,
                                'filename' => $filename,
                                'recipient_email' => $email,
                                'recipient_name' => $name,
                                'period' => $period,
                                'status' => 'failed',
                                'error_message' => $errorMessage,
                            ]);

                            if ($dailyLimitReached) {
                                $unprocessedCurrentBatch = array_slice($currentBatch, $index + 1);
                                break;
                            }
                        }
                    } else {
                        $stats['failed']++;
                        $this->createPayslipLogEntry([
                            'user_id' => Auth::id(),
                            'payslip_send_session_id' => $sendSessionId,
                            'filename' => $filename,
                            'recipient_email' => 'N/A',
                            'recipient_name' => $name,
                            'period' => $period,
                            'status' => 'failed',
                            'error_message' => 'Email tidak ditemukan/kosong',
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send email for $filename: " . $e->getMessage());
                    $stats['failed']++;
                    $this->createPayslipLogEntry([
                        'user_id' => Auth::id(),
                        'payslip_send_session_id' => $sendSessionId,
                        'filename' => $filename,
                        'recipient_email' => $email ?: 'N/A',
                        'recipient_name' => null,
                        'period' => $period ?: null,
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }

            $remainingQueue = array_values(array_merge($unprocessedCurrentBatch, $remainingQueue));

            if ($dailyLimitReached && !empty($remainingQueue)) {
                foreach ($remainingQueue as $pendingFile) {
                    $stats['failed']++;
                    $this->createPayslipLogEntry([
                        'user_id' => Auth::id(),
                        'payslip_send_session_id' => $sendSessionId,
                        'filename' => $pendingFile['filename'] ?? 'N/A',
                        'recipient_email' => $pendingFile['email'] ?? 'N/A',
                        'recipient_name' => null,
                        'period' => !empty($pendingFile['period']) ? trim($pendingFile['period']) : null,
                        'status' => 'failed',
                        'error_message' => $dailyLimitMessage ?: 'Tidak dikirim karena semua akun SMTP aktif sedang tidak tersedia.',
                    ]);
                }

                $remainingQueue = [];
            }

            session([
                'sending_queue' => $remainingQueue,
                'sending_stats' => $stats,
            ]);

            $this->syncPayslipSendSessionState($sendSessionId, $tempDir, $remainingQueue);
            $this->updatePayslipSendSessionSummary($sendSessionId, $stats, count($remainingQueue));

            $processed = $stats['success'] + $stats['failed'];
            $remaining = count($remainingQueue);
            $total = $total ?: ($processed + $remaining);
            $progressData = $this->getPayslipSendingProgressData($remainingQueue, $total);

            if ($remaining > 0) {
                if ($request->expectsJson()) {
                    return response()->json(array_merge($progressData, [
                        'completed' => false,
                        'message' => "Batch selesai diproses. {$processed} dari {$total} email sudah ditangani.",
                    ]));
                }

                return redirect()->route('finance.payslips.sending', ['send_session_id' => $sendSessionId]);
            }

            $this->clearPayslipSendingSession($tempDir, $sendSessionId);

            if ($request->expectsJson()) {
                return response()->json([
                    'processed' => $processed,
                    'remaining' => 0,
                    'total' => $total,
                    'success' => $stats['success'],
                    'failed' => $stats['failed'],
                    'progressPercent' => 100,
                    'nextBatchCount' => 0,
                    'completed' => true,
                    'redirectUrl' => route('finance.payslips.reports.show', $sendSessionId),
                    'message' => $dailyLimitReached && $dailyLimitMessage
                        ? $dailyLimitMessage
                        : "Pengiriman selesai. Sukses: {$stats['success']}, Gagal: {$stats['failed']}.",
                ]);
            }

            if ($dailyLimitReached && $dailyLimitMessage) {
                return redirect()->route('finance.payslips.reports.show', $sendSessionId)->with('warning', $dailyLimitMessage);
            }

            if ($stats['success'] > 0) {
                return redirect()->route('finance.payslips.reports.show', $sendSessionId)->with('success', "Email berhasil dikirim! Sukses: {$stats['success']}, Gagal: {$stats['failed']}.");
            }
            return redirect()->route('finance.payslips.reports.show', $sendSessionId)->with('warning', "Proses selesai. Sukses: {$stats['success']}, Gagal: {$stats['failed']}. Cek log untuk detail.");
        } catch (\Throwable $e) {
            Log::error('Payslip sending batch error: ' . $e->getMessage());
            $sendSessionId = session('current_payslip_send_session_id');

            if ($sendSessionId) {
                $this->updatePayslipSendSessionSummary($sendSessionId, session('sending_stats', ['success' => 0, 'failed' => 0]), count(session('sending_queue', [])));
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat memproses batch pengiriman. Silakan klik Coba Lagi.',
                    'redirectUrl' => $sendSessionId ? route('finance.payslips.sending', ['send_session_id' => $sendSessionId]) : route('finance.payslips'),
                ], 500);
            }

            return redirect()->route('finance.payslips.sending', ['send_session_id' => $sendSessionId])->with('error', 'Terjadi kesalahan saat memproses pengiriman: ' . $e->getMessage());
        }
    }
    
    // Removed executeCommand as it is no longer used

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
    
        if (!is_dir($dir)) {
            return unlink($dir);
        }
    
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
    
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
    
        return rmdir($dir);
    }

    private function getPayslipSendingProgressData($queue, $total)
    {
        $stats = session('sending_stats', ['success' => 0, 'failed' => 0]);
        $processed = $stats['success'] + $stats['failed'];
        $remaining = count($queue);
        $batchSize = 10;
        $sendSessionId = session('current_payslip_send_session_id');
        $sendSession = $sendSessionId ? PayslipSendSession::find($sendSessionId) : null;

        return [
            'processed' => $processed,
            'remaining' => $remaining,
            'total' => $total,
            'success' => $stats['success'],
            'failed' => $stats['failed'],
            'progressPercent' => $total > 0 ? floor(($processed / $total) * 100) : 0,
            'nextBatchCount' => min($batchSize, $remaining),
            'sessionCode' => optional($sendSession)->session_code,
        ];
    }

    private function getPayslipStoredProgressData(PayslipSendSession $sendSession)
    {
        $processed = (int) $sendSession->success_count + (int) $sendSession->failed_count;
        $remaining = max(0, (int) $sendSession->total_files - $processed);
        $batchSize = $this->getPayslipBatchSize();

        return [
            'processed' => $processed,
            'remaining' => $remaining,
            'total' => (int) $sendSession->total_files,
            'success' => (int) $sendSession->success_count,
            'failed' => (int) $sendSession->failed_count,
            'progressPercent' => $sendSession->total_files > 0 ? floor(($processed / $sendSession->total_files) * 100) : 0,
            'nextBatchCount' => min($batchSize, $remaining),
            'sessionCode' => $sendSession->session_code,
        ];
    }

    private function getPayslipBatchSize()
    {
        return 10;
    }

    private function getPayslipBatchExecutionLimit()
    {
        return 20;
    }

    private function sendPayslipViaRotatingMailers($email, $cc, $bcc, $name, $period, $fileContent, $filename)
    {
        $attemptedMailerIds = [];
        $dailyLimitTriggered = false;

        while ($smtpMailer = $this->getNextAvailableSmtpMailer($attemptedMailerIds)) {
            try {
                $this->sendPayslipEmailUsingMailer($smtpMailer, $email, $cc, $bcc, $name, $period, $fileContent, $filename);
                $this->markSmtpMailerAsUsed($smtpMailer);
                session(['last_smtp_mailer_id' => $smtpMailer->id]);

                return [
                    'success' => true,
                    'mailer_name' => $smtpMailer->mailer_name,
                    'mailer_address' => $smtpMailer->from_address,
                    'smtp_mailer_id' => $smtpMailer->id,
                    'message' => null,
                ];
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();

                if ($this->isDailySendingLimitExceeded($errorMessage)) {
                    $dailyLimitTriggered = true;
                    $this->markSmtpMailerDailyLimitReached($smtpMailer);
                    $attemptedMailerIds[] = $smtpMailer->id;
                    Log::warning("SMTP mailer {$smtpMailer->mailer_name} reached daily sending limit.");
                    continue;
                }

                if ($this->isSmtpTransportIssue($errorMessage)) {
                    $this->markSmtpMailerTemporarilyUnavailable($smtpMailer);
                    $attemptedMailerIds[] = $smtpMailer->id;
                    Log::warning("SMTP mailer {$smtpMailer->mailer_name} temporarily disabled due to transport issue: {$errorMessage}");
                    continue;
                }

                return [
                    'success' => false,
                    'mailer_name' => $smtpMailer->mailer_name,
                    'mailer_address' => $smtpMailer->from_address,
                    'smtp_mailer_id' => $smtpMailer->id,
                    'message' => $this->normalizePayslipMailErrorMessage($errorMessage),
                ];
            }
        }

        return [
            'success' => false,
            'mailer_name' => null,
            'mailer_address' => null,
            'smtp_mailer_id' => null,
            'message' => $dailyLimitTriggered
                ? $this->getDailySendingLimitMessage()
                : $this->getNoAvailableMailerMessage(),
        ];
    }

    private function getNextAvailableSmtpMailer(array $excludedMailerIds = [])
    {
        $this->ensureSmtpMailersAvailable();
        $this->releaseExpiredSmtpMailerCooldowns();

        $availableMailers = SmtpMailer::query()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('cooldown_until')
                    ->orWhere('cooldown_until', '<=', now());
            })
            ->whereColumn('daily_sent_count', '<', 'daily_limit')
            ->orderBy('priority')
            ->get()
            ->reject(fn ($mailer) => in_array($mailer->id, $excludedMailerIds, true))
            ->values();

        if ($availableMailers->isEmpty()) {
            return null;
        }

        $lastMailerId = session('last_smtp_mailer_id');

        if (!$lastMailerId) {
            return $availableMailers->first();
        }

        $lastIndex = $availableMailers->search(fn ($mailer) => $mailer->id === $lastMailerId);

        if ($lastIndex === false) {
            return $availableMailers->first();
        }

        $nextIndex = ($lastIndex + 1) % $availableMailers->count();

        return $availableMailers->get($nextIndex) ?: $availableMailers->first();
    }

    private function ensureSmtpMailersAvailable()
    {
        $existingCount = SmtpMailer::query()->count();

        SmtpMailer::ensureDefaultMailersExist();
        $this->syncSmtpMailerDailySentCounts();

        if ($existingCount === 0 && SmtpMailer::query()->exists()) {
            Log::info('Default SMTP mailers were restored automatically because the smtp_mailers table was empty.');
        }
    }

    private function syncSmtpMailerDailySentCounts()
    {
        $sentCounts = PayslipLog::query()
            ->selectRaw('smtp_mailer_id, COUNT(*) as total_sent')
            ->whereNotNull('smtp_mailer_id')
            ->where('status', 'success')
            ->whereDate('created_at', today())
            ->groupBy('smtp_mailer_id')
            ->pluck('total_sent', 'smtp_mailer_id');

        SmtpMailer::query()->get()->each(function ($mailer) use ($sentCounts) {
            $actualSentCount = (int) ($sentCounts[$mailer->id] ?? 0);

            if ((int) $mailer->daily_sent_count !== $actualSentCount) {
                $mailer->update([
                    'daily_sent_count' => $actualSentCount,
                ]);
            }
        });
    }

    private function sendPayslipEmailUsingMailer(SmtpMailer $smtpMailer, $email, $cc, $bcc, $name, $period, $fileContent, $filename)
    {
        $runtimeMailerName = 'runtime_' . $smtpMailer->mailer_name;

        Config::set("mail.mailers.{$runtimeMailerName}", [
            'transport' => 'smtp',
            'host' => $smtpMailer->smtp_host,
            'port' => $smtpMailer->smtp_port,
            'encryption' => $smtpMailer->smtp_encryption,
            'username' => $smtpMailer->smtp_username,
            'password' => $smtpMailer->smtp_password,
            'timeout' => null,
            'local_domain' => config('mail.mailers.smtp.local_domain'),
        ]);

        app('mail.manager')->purge($runtimeMailerName);

        Mail::mailer($runtimeMailerName)->send([], [], function ($message) use ($smtpMailer, $email, $cc, $bcc, $name, $period, $fileContent, $filename) {
            $message->from($smtpMailer->from_address, $smtpMailer->from_name)
                ->to($email);

            if (!empty($cc)) {
                $message->cc($cc);
            }

            if (!empty($bcc)) {
                $message->bcc($bcc);
            }

            $message->subject("Slip Gaji - $period")
                ->html("Yth. $name,<br><br>Berikut kami lampirkan slip gaji Anda untuk periode $period.<br>Mohon diperiksa kembali.<br><br>Terima kasih,<br>Finance Department")
                ->attachData($fileContent, $filename, [
                    'mime' => 'application/pdf',
                ]);
        });

        app('mail.manager')->purge($runtimeMailerName);
    }

    private function markSmtpMailerAsUsed(SmtpMailer $smtpMailer)
    {
        $smtpMailer->update([
            'daily_sent_count' => $smtpMailer->daily_sent_count + 1,
            'last_used_at' => now(),
        ]);
    }

    private function markSmtpMailerDailyLimitReached(SmtpMailer $smtpMailer)
    {
        $smtpMailer->update([
            'last_used_at' => now(),
            'last_limit_reached_at' => now(),
            'cooldown_until' => now()->addDay(),
        ]);
    }

    private function markSmtpMailerTemporarilyUnavailable(SmtpMailer $smtpMailer)
    {
        $smtpMailer->update([
            'last_used_at' => now(),
            'cooldown_until' => now()->addMinutes(30),
        ]);
    }

    private function releaseExpiredSmtpMailerCooldowns()
    {
        SmtpMailer::query()
            ->whereNotNull('cooldown_until')
            ->where('cooldown_until', '<=', now())
            ->get()
            ->each(function ($smtpMailer) {
                $updates = [
                    'cooldown_until' => null,
                ];

                if ($smtpMailer->last_limit_reached_at) {
                    $updates['daily_sent_count'] = 0;
                    $updates['last_limit_reached_at'] = null;
                }

                $smtpMailer->update($updates);
            });
    }

    private function isDailySendingLimitExceeded($message)
    {
        $normalizedMessage = strtolower($message);

        return Str::contains($normalizedMessage, [
            'daily user sending limit exceeded',
            'gmail sending limit',
            'you reached a gmail sending limit',
            '550-5.4.5',
            '550 5.4.5',
        ]);
    }

    private function isSmtpTransportIssue($message)
    {
        $normalizedMessage = strtolower($message);

        return Str::contains($normalizedMessage, [
            'failed to authenticate on smtp server',
            'expected response code "235" but got code "535"',
            'connection could not be established',
            'stream_socket_client',
            'connection timed out',
            'authentication failed',
        ]);
    }

    private function normalizePayslipMailErrorMessage($message)
    {
        if (strpos($message, '452') !== false || strpos(strtolower($message), 'quota') !== false || strpos(strtolower($message), 'storage') !== false) {
            return 'GAGAL KIRIM: Inbox Penerima Penuh (Quota Exceeded). Harap hubungi karyawan untuk membersihkan email mereka.';
        }

        return $message;
    }

    private function getNoAvailableMailerMessage()
    {
        return 'Pengiriman dihentikan karena tidak ada akun SMTP aktif yang tersedia. Periksa status mailer, cooldown, atau batas kirim harian sebelum melanjutkan.';
    }

    private function isAllMailersUnavailableMessage($message)
    {
        return $message === $this->getDailySendingLimitMessage()
            || $message === $this->getNoAvailableMailerMessage();
    }

    private function getDailySendingLimitMessage()
    {
        return 'Pengiriman dihentikan karena semua akun SMTP aktif sudah mencapai batas kirim Gmail atau masih dalam masa cooldown. Batas Gmail memakai rolling 24 jam, sehingga kuota riil akun bisa habis lebih cepat daripada hitungan lokal aplikasi.';
    }

    private function clearPayslipSendingSession($tempDir = null, $sendSessionId = null)
    {
        if ($tempDir && file_exists($tempDir)) {
            $this->deleteDirectory($tempDir);
        }

        $targetSendSessionId = $sendSessionId ?: session('current_payslip_send_session_id');

        if ($targetSendSessionId) {
            PayslipSendSession::whereKey($targetSendSessionId)->update([
                'temp_dir_path' => null,
                'pending_queue' => null,
            ]);
        }

        session()->forget('payslip_temp_dir');
        session()->forget('sending_queue');
        session()->forget('sending_stats');
        session()->forget('sending_total');
        session()->forget('current_payslip_send_session_id');
        session()->forget('last_smtp_mailer_id');
    }

    private function updatePayslipSendSessionSummary($sendSessionId, $stats, $remaining)
    {
        $sendSession = PayslipSendSession::find($sendSessionId);

        if (!$sendSession) {
            return;
        }

        $status = 'processing';
        $finishedAt = null;

        if ($remaining === 0) {
            if (($stats['success'] ?? 0) > 0 && ($stats['failed'] ?? 0) > 0) {
                $status = 'completed_with_errors';
            } elseif (($stats['failed'] ?? 0) > 0) {
                $status = 'failed';
            } else {
                $status = 'completed';
            }

            $finishedAt = now();
        }

        $sendSession->update([
            'success_count' => $stats['success'] ?? 0,
            'failed_count' => $stats['failed'] ?? 0,
            'status' => $status,
            'finished_at' => $finishedAt,
        ]);
    }

    private function syncPayslipSendSessionState($sendSessionId, $tempDir, array $queue)
    {
        if (!$sendSessionId) {
            return;
        }

        PayslipSendSession::whereKey($sendSessionId)->update([
            'temp_dir_path' => $tempDir,
            'pending_queue' => array_values($queue),
        ]);
    }

    private function restorePayslipSendingSessionState(PayslipSendSession $sendSession)
    {
        $tempDir = $sendSession->temp_dir_path;
        $queue = $sendSession->pending_queue ?? [];
        $processed = (int) $sendSession->success_count + (int) $sendSession->failed_count;
        $total = max((int) $sendSession->total_files, $processed + count($queue));

        if (!$tempDir || !file_exists($tempDir)) {
            return false;
        }

        session([
            'payslip_temp_dir' => $tempDir,
            'sending_queue' => array_values($queue),
            'sending_total' => $total,
            'sending_stats' => [
                'success' => (int) $sendSession->success_count,
                'failed' => (int) $sendSession->failed_count,
            ],
            'current_payslip_send_session_id' => $sendSession->id,
        ]);

        return true;
    }

    private function createPayslipLogEntry(array $data)
    {
        PayslipLog::create($data);
    }

    private function getPayslipRecipientNameFromFilename($filename)
    {
        if (!$filename) {
            return null;
        }

        $namePart = pathinfo($filename, PATHINFO_FILENAME);
        $name = preg_replace('/^[\d\W_]+/', '', $namePart);
        $name = trim($name);

        return $name !== '' ? $name : null;
    }

    private function extractPayslipPeriodFromFile($filePath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);

            return $this->extractPayslipPeriodFromText($pdf->getText());
        } catch (\Exception $e) {
            Log::warning("Failed to extract payslip period from " . basename($filePath) . ": " . $e->getMessage());

            return null;
        }
    }

    private function extractPayslipPeriodFromText($text)
    {
        if (empty($text)) {
            return null;
        }

        $lines = preg_split('/\R+/u', $text);

        foreach ($lines as $index => $line) {
            $normalizedLine = trim(preg_replace('/\s+/u', ' ', $line));

            if ($normalizedLine === '') {
                continue;
            }

            if (preg_match('/^PERIODE\s*[:\-]?\s*(.+)$/iu', $normalizedLine, $matches)) {
                return $this->cleanPayslipPeriod($matches[1]);
            }

            if (preg_match('/^PERIODE\s*[:\-]?$/iu', $normalizedLine)) {
                for ($nextIndex = $index + 1; $nextIndex < count($lines); $nextIndex++) {
                    $nextLine = trim(preg_replace('/\s+/u', ' ', $lines[$nextIndex]));

                    if ($nextLine !== '') {
                        return $this->cleanPayslipPeriod($nextLine);
                    }
                }
            }
        }

        if (preg_match('/PERIODE\s*[:\-]?\s*([^\r\n]+)/iu', $text, $matches)) {
            return $this->cleanPayslipPeriod($matches[1]);
        }

        return null;
    }

    private function cleanPayslipPeriod($period)
    {
        $period = trim(preg_replace('/\s+/u', ' ', $period));
        $period = preg_replace('/^(?:PERIODE|PERIOD)\s*[:\-]?\s*/iu', '', $period);
        $period = trim($period, " \t\n\r\0\x0B:-");

        return $period !== '' ? $period : null;
    }
}
