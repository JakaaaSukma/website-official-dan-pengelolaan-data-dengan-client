<?php

namespace App\Http\Controllers;

use App\Models\HrContract;
use App\Models\HrViolationReport;
use App\Models\HrViolationStatusLog;
use App\Models\HrMessage;
use App\Helpers\HrAuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\ValidatesFileUpload;

class HrViolationController extends Controller
{
    use ValidatesFileUpload;
    // ==================== HR COMPANY SIDE METHODS ====================

    public function indexHrd(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $search = $request->query('search');
        $status = $request->query('status');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = HrViolationReport::with(['intern', 'reportedBy', 'statusLogs'])
            ->orderByDesc('created_at');

        if ($search) {
            $s = '%' . trim($search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s)
                    ->orWhereHas('intern', function ($q2) use ($s) {
                        $q2->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s)
                            ->orWhere('employee_id', 'like', $s);
                    })
                    ->orWhere('description', 'like', $s);
            });
        }
        if ($status && in_array($status, ['pending_review', 'contacted', 'in_process', 'resolved', 'rejected'])) {
            $query->where('status', $status);
        }
        if ($fromDate) {
            $query->whereDate('incident_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('incident_date', '<=', $toDate);
        }

        $reports = $query->paginate(15);
        $statusCounts = [
            'pending_review' => HrViolationReport::where('status', 'pending_review')->count(),
            'in_process' => HrViolationReport::where('status', 'in_process')->count(),
            'resolved' => HrViolationReport::where('status', 'resolved')->count(),
        ];

        $pageTitle = 'Laporan Pelanggaran Peserta';
        $sMenu = 'violations';
        return view('gik-hrd.violations', compact(
            'reports', 'search', 'status', 'fromDate', 'toDate',
            'statusCounts', 'pageTitle', 'sMenu'
        ));
    }

    private function parseViolationBatchIds(Request $request): array
    {
        if (!$request->filled('ids')) return [];
        $raw = explode(',', $request->input('ids'));
        $ids = [];
        foreach ($raw as $r) {
            $v = (int) trim($r);
            if ($v > 0) $ids[] = $v;
        }
        return array_values(array_unique($ids));
    }

    public function createHrd($contract = null, Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $batchIds = $this->parseViolationBatchIds($request);
        $isBatch = count($batchIds) > 0;

        if ($isBatch) {
            $contracts = HrContract::whereIn('id', $batchIds)->get();
            if ($contracts->count() !== count($batchIds)) abort(404);
            foreach ($contracts as $c) $this->authorizeAccessIntern($c);

            $selectedContracts = $contracts;
            $contract = null;
            $existingReports = collect();
            $pageTitle = "Laporkan Pelanggaran Massal ({$contracts->count()} peserta)";
            $sMenu = 'violations';
            return view('gik-hrd.violation-create', compact(
                'contract', 'existingReports', 'pageTitle', 'sMenu',
                'selectedContracts', 'isBatch', 'batchIds'
            ));
        }

        if (!$contract) abort(404);
        $contract = HrContract::findOrFail($contract);
        $this->authorizeAccessIntern($contract);

        $existingReports = HrViolationReport::where('intern_id', $contract->id)
            ->where('company_id', $u->company_id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $isBatch = false;
        $selectedContracts = null;
        $batchIds = [];
        $pageTitle = "Laporkan Pelanggaran: {$contract->full_name}";
        $sMenu = 'violations';
        return view('gik-hrd.violation-create', compact(
            'contract', 'existingReports', 'pageTitle', 'sMenu',
            'selectedContracts', 'isBatch', 'batchIds'
        ));
    }

    public function storeHrd($contract = null, Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $validated = $request->validate([
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_time' => 'nullable|date_format:H:i',
            'description' => 'required|string|min:10|max:5000',
            'proposed_sanction' => 'nullable|string|max:2000',
            'evidence_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            '_batch_ids' => ['nullable', 'string'],
        ]);

        $batchIds = [];
        if ($request->filled('_batch_ids')) {
            $raw = explode(',', $request->input('_batch_ids'));
            foreach ($raw as $r) { $v = (int) trim($r); if ($v > 0) $batchIds[] = $v; }
            $batchIds = array_values(array_unique($batchIds));
        }

        $isBatch = count($batchIds) > 0;
        $contracts = collect();

        if ($isBatch) {
            $contracts = HrContract::whereIn('id', $batchIds)->get();
            if ($contracts->count() !== count($batchIds)) abort(404);
            foreach ($contracts as $c) $this->authorizeAccessIntern($c);
        } else {
            if (!$contract) abort(404);
            $contract = HrContract::findOrFail($contract);
            $this->authorizeAccessIntern($contract);
            $contracts = collect([$contract]);
        }

        $evidencePath = null;
        $createdReports = [];
        $evidencePaths = [];
        DB::beginTransaction();
        try {
            if ($request->hasFile('evidence_file')) {
                $evidencePath = $request->file('evidence_file')->store('violation-evidence', 'public');
                $evidencePaths[] = $evidencePath;
            }

            foreach ($contracts as $c) {
                $report = HrViolationReport::create([
                    'company_id' => $u->company_id,
                    'intern_id' => $c->id,
                    'reported_by' => $u->id,
                    'incident_date' => $validated['incident_date'],
                    'incident_time' => $validated['incident_time'] ?? null,
                    'description' => $validated['description'],
                    'proposed_sanction' => $validated['proposed_sanction'] ?? null,
                    'evidence_file' => $evidencePath,
                    'status' => 'pending_review',
                ]);

                HrViolationStatusLog::create([
                    'violation_report_id' => $report->id,
                    'old_status' => null,
                    'new_status' => 'pending_review',
                    'notes' => $isBatch ? 'Laporan pelanggaran massal diajukan oleh HR Company.' : 'Laporan pelanggaran diajukan oleh HR Company.',
                    'updated_by' => $u->id,
                ]);

                HrAuditHelper::log(
                    'hrd_submit_violation_report',
                    'hr_violation_reports',
                    $report->id,
                    null,
                    array_merge($report->toArray(), ['batch' => $isBatch]),
                    'warning'
                );

                $createdReports[] = $report;
            }

            $companyName = $u->company?->company_name ?? 'Perusahaan';
            if ($isBatch) {
                $namesList = $contracts->pluck('full_name')->implode(', ');
                HrMessage::create([
                    'company_id' => $u->company_id,
                    'from_user_id' => $u->id,
                    'to_user_id' => null,
                    'message_type' => 'violation_report',
                    'subject' => "🚨 LAPORAN PELANGGARAN MASSAL dari {$companyName} — {$contracts->count()} peserta",
                    'body' => "HR Company {$companyName} melaporkan pelanggaran SECARA MASSAL untuk {$contracts->count()} peserta: {$namesList}. "
                        . "Tanggal kejadian: " . date('d M Y', strtotime($validated['incident_date'])) . ". "
                        . "Segera lakukan tinjauan dan tindak lanjut pada menu Manajemen Pelanggaran.",
                    'priority' => 'high',
                    'status' => 'unread',
                ]);
            } else {
                HrMessage::create([
                    'company_id' => $u->company_id,
                    'from_user_id' => $u->id,
                    'to_user_id' => null,
                    'message_type' => 'violation_report',
                    'subject' => "🚨 Laporan Pelanggaran dari {$companyName} — {$contract->full_name}",
                    'body' => "HR Company {$companyName} melaporkan pelanggaran oleh peserta \"{$contract->full_name}\" ({$contract->job_title}). "
                        . "Tanggal kejadian: " . date('d M Y', strtotime($validated['incident_date'])) . ". "
                        . "Segera lakukan tinjauan dan tindak lanjut pada menu Manajemen Pelanggaran.",
                    'priority' => 'high',
                    'status' => 'unread',
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($evidencePaths as $ep) {
                if ($ep) Storage::disk('public')->delete($ep);
            }
            return redirect()->back()->with('error', 'Gagal mengirim laporan pelanggaran: ' . $e->getMessage())->withInput();
        }

        if ($isBatch) {
            return redirect()->route('gik-hrd.violations.index')->with(
                'success',
                "✅ Berhasil mengirim LAPORAN PELANGGARAN MASSAL untuk {$contracts->count()} peserta sekaligus. Semua laporan berstatus Menunggu Tinjauan Tim Rekrutmen."
            );
        }

        $report = $createdReports[0];
        return redirect()->route('gik-hrd.violations.show', $report)->with(
            'success',
            'Laporan pelanggaran berhasil dikirim ke Tim Rekrutmen. Silakan pantau status penanganan pada halaman ini.'
        );
    }

    public function showHrd(HrViolationReport $report)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);
        $this->authorizeAccess($report);

        HrAuditHelper::log('hrd_view_violation_report', 'hr_violation_reports', $report->id);

        $pageTitle = "Detail Laporan Pelanggaran #{$report->report_number}";
        $sMenu = 'violations';
        return view('gik-hrd.violation-show', compact('report', 'pageTitle', 'sMenu'));
    }

    // ==================== SUPER ADMIN SIDE METHODS ====================

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $search = $request->query('search');
        $status = $request->query('status');
        $companyId = $request->query('company_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = HrViolationReport::withoutTenant()
            ->with(['intern', 'company', 'reportedBy', 'handledBy', 'statusLogs'])
            ->orderByDesc('created_at');

        if ($search) {
            $s = '%' . trim($search) . '%';
            $query->where(function ($q) use ($s) {
                $q->whereHas('intern', function ($q2) use ($s) {
                    $q2->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s)
                        ->orWhere('employee_id', 'like', $s);
                })->orWhereHas('company', function ($q2) use ($s) {
                    $q2->where('company_name', 'like', $s);
                })->orWhere('description', 'like', $s);
            });
        }
        if ($status && in_array($status, ['pending_review', 'contacted', 'in_process', 'resolved', 'rejected'])) {
            $query->where('status', $status);
        }
        if ($companyId && $companyId !== 'all') {
            $query->where('company_id', $companyId);
        }
        if ($fromDate) {
            $query->whereDate('incident_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('incident_date', '<=', $toDate);
        }

        $reports = $query->paginate(20);
        $companies = \App\Models\Client::orderBy('company_name')->get();

        $statusCounts = [
            'pending_review' => HrViolationReport::withoutTenant()->where('status', 'pending_review')->count(),
            'contacted' => HrViolationReport::withoutTenant()->where('status', 'contacted')->count(),
            'in_process' => HrViolationReport::withoutTenant()->where('status', 'in_process')->count(),
            'resolved' => HrViolationReport::withoutTenant()->where('status', 'resolved')->count(),
            'rejected' => HrViolationReport::withoutTenant()->where('status', 'rejected')->count(),
            'total' => HrViolationReport::withoutTenant()->count(),
        ];

        $pageTitle = 'Manajemen Pelanggaran — Semua Perusahaan';
        $sMenu = 'violations';
        return view('gik-superadmin.violations', compact(
            'reports', 'companies', 'search', 'status', 'companyId',
            'fromDate', 'toDate', 'statusCounts', 'pageTitle', 'sMenu'
        ));
    }

    public function show(HrViolationReport $report)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        HrAuditHelper::log('sa_view_violation_report', 'hr_violation_reports', $report->id);

        $companies = \App\Models\Client::orderBy('company_name')->get();
        $pageTitle = "Detail Laporan #{$report->report_number} — {$report->intern?->full_name}";
        $sMenu = 'violations';
        return view('gik-superadmin.violation-show', compact(
            'report', 'companies', 'pageTitle', 'sMenu'
        ));
    }

    public function updateStatus(HrViolationReport $report, Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $validated = $request->validate([
            'status' => 'required|in:pending_review,contacted,in_process,resolved,rejected',
            'notes' => 'nullable|string|min:3|max:3000',
        ]);

        $oldStatus = $report->status;
        $newStatus = $validated['status'];

        DB::beginTransaction();
        try {
            $report->status = $newStatus;
            $report->handled_by = $user->id;
            $report->handled_at = now();
            if ($newStatus === 'resolved' || $newStatus === 'rejected') {
                $report->resolution_notes = $validated['notes'] ?? $report->resolution_notes;
            }
            $report->save();

            HrViolationStatusLog::create([
                'violation_report_id' => $report->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $validated['notes'] ?? null,
                'updated_by' => $user->id,
            ]);

            HrAuditHelper::log(
                'sa_update_violation_status',
                'hr_violation_reports',
                $report->id,
                ['status' => $oldStatus],
                ['status' => $newStatus, 'notes' => $validated['notes'] ?? null],
                'warning'
            );

            $statusLabel = match ($newStatus) {
                'pending_review' => 'Menunggu Tinjauan',
                'contacted' => 'Sudah Dihubungi',
                'in_process' => 'Sedang Diproses',
                'resolved' => 'Selesai',
                'rejected' => 'Ditolak',
                default => $newStatus,
            };

            HrMessage::create([
                'company_id' => $report->company_id,
                'from_user_id' => $user->id,
                'to_user_id' => $report->reported_by,
                'message_type' => 'violation_status_update',
                'subject' => "📋 Update Status Laporan Pelanggaran #{$report->report_number} — {$statusLabel}",
                'body' => "Status laporan pelanggaran untuk \"{$report->intern?->full_name}\" telah diperbarui menjadi: **{$statusLabel}**. "
                    . (($validated['notes'] ?? null) ? "Catatan dari Tim Rekrutmen: {$validated['notes']}. " : '')
                    . "Lihat detail pada menu Laporan Pelanggaran di HR Portal Anda.",
                'priority' => ($newStatus === 'resolved' || $newStatus === 'rejected') ? 'high' : 'normal',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage())->withInput();
        }

        return redirect()->back()->with(
            'success',
            "Status laporan berhasil diperbarui menjadi {$statusLabel}. Notifikasi otomatis telah dikirim ke HR Company."
        );
    }

    // ==================== AUTHORIZATION HELPERS (IDOR PROTECTION) ====================

    private function authorizeAccess(HrViolationReport $report): void
    {
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) return;
        if (($user->role === 'hrd_company' || $user->role === 'company') && $report->company_id === $user->company_id) return;
        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat laporan pelanggaran ini.');
    }

    private function authorizeAccessIntern(HrContract $contract): void
    {
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) return;
        if (($user->role === 'hrd_company' || $user->role === 'company') && $contract->company_id === $user->company_id) return;
        abort(403, 'Akses ditolak. Anda tidak dapat melaporkan pelanggaran untuk peserta dari perusahaan lain.');
    }
}
