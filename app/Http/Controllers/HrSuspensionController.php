<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\HrContract;
use App\Models\HrSuspensionRequest;
use App\Models\HrSuspensionStatusLog;
use App\Models\HrMessage;
use App\Helpers\HrAuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HrSuspensionController extends Controller
{
    // ==================== HR COMPANY SIDE METHODS ====================

    public function indexHrd(Request $request)
    {
        $u = Auth::user();
        if (!$u->isHrdCompany() && !$u->isCompanyAdmin()) abort(403);

        $statuses = ['pending_approval', 'under_review', 'approved', 'rejected'];
        $statusLabels = collect($statuses)->mapWithKeys(fn($s) => [$s => (new HrSuspensionRequest(['status' => $s]))->status_label]);

        $q = HrSuspensionRequest::with(['intern', 'requestedBy']);

        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = "%{$request->input('search')}%";
            $q->whereHas('intern', function ($sq) use ($search) {
                $sq->whereRaw("CONCAT_WS(' ',first_name,last_name) like ?", [$search]);
            });
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = $request->input('date_from');
            $to = $request->input('date_to');
            $q->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        } elseif ($request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $request->input('date_from'));
        } elseif ($request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $reports = $q->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'pending_approval' => HrSuspensionRequest::where('status', 'pending_approval')->count(),
            'under_review' => HrSuspensionRequest::where('status', 'under_review')->count(),
            'approved' => HrSuspensionRequest::where('status', 'approved')->count(),
        ];

        HrAuditHelper::log('hrd_view_suspensions_list', 'hr_suspension_requests');

        return view('gik-hrd.suspensions', compact('reports', 'statusLabels', 'stats', 'u'));
    }

    private function parseIdsFromRequest(Request $request): array
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
        if (!$u->isHrdCompany() && !$u->isCompanyAdmin()) abort(403);

        $batchIds = $this->parseIdsFromRequest($request);
        $isBatch = count($batchIds) > 0;

        if ($isBatch) {
            $contracts = HrContract::whereIn('id', $batchIds)->get();
            if ($contracts->count() !== count($batchIds)) abort(404);
            foreach ($contracts as $c) $this->authorizeAccessIntern($c);

            $failed = [];
            $today = Carbon::today();
            foreach ($contracts as $c) {
                $activeOverlapping = HrSuspensionRequest::where('intern_id', $c->id)
                    ->whereIn('status', ['pending_approval', 'under_review'])
                    ->orWhere(function ($q) use ($c, $today) {
                        $q->where('intern_id', $c->id)
                            ->where('status', 'approved')
                            ->whereDate('end_date', '>=', $today);
                    })
                    ->orderByDesc('created_at')
                    ->first();
                if ($activeOverlapping) {
                    $failed[] = "• {$c->full_name} ({$activeOverlapping->status_label})";
                }
            }
            if (count($failed) > 0) {
                return redirect()
                    ->route('gik-hrd.dashboard')
                    ->with('warning', "Terdapat " . count($failed) . " peserta yang masih memiliki pengajuan pemberhentian sementara aktif / menunggu persetujuan:<br>" . implode('<br>', $failed) . "<br>Harap selesaikan terlebih dahulu sebelum mengajukan secara massal.");
            }

            $selectedContracts = $contracts;
            $contract = null;
            $previousRequests = collect();
            $pageTitle = "Ajukan Pemberhentian Sementara Massal ({$contracts->count()} peserta)";
            return view('gik-hrd.suspension-create', compact(
                'contract', 'previousRequests', 'pageTitle', 'u', 'selectedContracts', 'isBatch', 'batchIds'
            ));
        }

        if (!$contract) abort(404);
        $contract = HrContract::findOrFail($contract);
        $this->authorizeAccessIntern($contract);

        $activeOverlapping = HrSuspensionRequest::where('intern_id', $contract->id)
            ->whereIn('status', ['pending_approval', 'under_review'])
            ->orWhere(function ($q) use ($contract) {
                $today = Carbon::today();
                $q->where('intern_id', $contract->id)
                    ->where('status', 'approved')
                    ->whereDate('end_date', '>=', $today);
            })
            ->orderByDesc('created_at')
            ->first();

        if ($activeOverlapping) {
            return redirect()
                ->route('gik-hrd.suspensions.show', $activeOverlapping->id)
                ->with('warning', "Masih ada pengajuan pemberhentian sementara dengan status '{$activeOverlapping->status_label}' untuk peserta ini. Harap menunggu sampai selesai ditinjau sebelum membuat pengajuan baru.");
        }

        $previousRequests = HrSuspensionRequest::where('intern_id', $contract->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $isBatch = false;
        $selectedContracts = null;
        $batchIds = [];
        $pageTitle = "Ajukan Pemberhentian Sementara";
        return view('gik-hrd.suspension-create', compact(
            'contract', 'previousRequests', 'pageTitle', 'u', 'selectedContracts', 'isBatch', 'batchIds'
        ));
    }

    public function storeHrd($contract = null, Request $request)
    {
        $u = Auth::user();
        if (!$u->isHrdCompany() && !$u->isCompanyAdmin()) abort(403);

        $valid = $request->validate([
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:20'],
            '_batch_ids' => ['nullable', 'string'],
        ], [
            'start_date.before_or_equal' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.',
            'reason.min' => 'Alasan pemberhentian minimal 20 karakter.',
        ]);

        $start = Carbon::parse($request->input('start_date'));
        $end = Carbon::parse($request->input('end_date'));

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

            $failed = [];
            foreach ($contracts as $c) {
                $overlappingExists = HrSuspensionRequest::where('intern_id', $c->id)
                    ->whereIn('status', ['pending_approval', 'under_review'])
                    ->orWhere(function ($q) use ($c, $start, $end) {
                        $q->where('intern_id', $c->id)
                            ->where('status', 'approved')
                            ->where(function ($rq) use ($start, $end) {
                                $rq->whereDate('start_date', '<=', $end->format('Y-m-d'))
                                    ->whereDate('end_date', '>=', $start->format('Y-m-d'));
                            });
                    })
                    ->exists();
                if ($overlappingExists) $failed[] = "• {$c->full_name}";
            }
            if (count($failed) > 0) {
                return back()
                    ->withInput()
                    ->withErrors(['reason' => '❌ Terdapat ' . count($failed) . ' peserta yang sudah memiliki pengajuan aktif dengan periode tumpang tindih:<br>' . implode('<br>', $failed)]);
            }
        } else {
            if (!$contract) abort(404);
            $contract = HrContract::findOrFail($contract);
            $this->authorizeAccessIntern($contract);
            $contracts = collect([$contract]);

            $overlappingExists = HrSuspensionRequest::where('intern_id', $contract->id)
                ->whereIn('status', ['pending_approval', 'under_review'])
                ->orWhere(function ($q) use ($contract, $start, $end) {
                    $q->where('intern_id', $contract->id)
                        ->where('status', 'approved')
                        ->where(function ($rq) use ($start, $end) {
                            $rq->whereDate('start_date', '<=', $end->format('Y-m-d'))
                                ->whereDate('end_date', '>=', $start->format('Y-m-d'));
                        });
                })
                ->exists();

            if ($overlappingExists) {
                return back()
                    ->withInput()
                    ->withErrors(['reason' => '❌ Sudah ada pengajuan pemberhentian sementara yang aktif / masih diproses untuk peserta ini dengan rentang tanggal yang tumpang tindih. Harap selesaikan pengajuan sebelumnya terlebih dahulu.']);
            }
        }

        $createdReports = [];
        DB::beginTransaction();
        try {
            foreach ($contracts as $c) {
                $report = HrSuspensionRequest::create([
                    'company_id' => $c->company_id,
                    'intern_id' => $c->id,
                    'requested_by' => $u->id,
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                    'reason' => $request->input('reason'),
                    'status' => 'pending_approval',
                ]);

                HrSuspensionStatusLog::create([
                    'suspension_request_id' => $report->id,
                    'old_status' => null,
                    'new_status' => 'pending_approval',
                    'notes' => $isBatch ? 'Pengajuan massal oleh HRD Company' : 'Pengajuan awal oleh HRD Company',
                    'updated_by' => $u->id,
                ]);

                HrAuditHelper::log(
                    'hrd_submit_suspension_request',
                    'hr_suspension_requests',
                    $report->id,
                    [
                        'intern_name' => $c->full_name,
                        'start_date' => $request->input('start_date'),
                        'end_date' => $request->input('end_date'),
                        'batch' => $isBatch,
                    ]
                );

                $createdReports[] = $report;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['reason' => 'Gagal menyimpan pengajuan: ' . $e->getMessage()]);
        }

        $companyName = $u->company?->company_name ?? 'Perusahaan';
        $namesList = $contracts->pluck('full_name')->implode(', ');
        $firstReport = $createdReports[0];
        $periodLabel = "{$start->format('d M Y')} s/d {$end->format('d M Y')}";

        if ($isBatch) {
            HrMessage::create([
                'company_id' => null,
                'from_user_id' => $u->id,
                'to_user_id' => null,
                'subject' => "🔔 Pengajuan Pemberhentian Sementara MASSAL ({$contracts->count()} peserta)",
                'body' => "{$u->name} dari [{$companyName}] mengajukan pemberhentian sementara SECARA MASSAL untuk {$contracts->count()} peserta: {$namesList}. Periode {$periodLabel}. Alasan: " . mb_substr($request->input('reason'), 0, 200),
                'priority' => 'high',
                'message_type' => 'general',
            ]);
        } else {
            HrMessage::create([
                'company_id' => null,
                'from_user_id' => $u->id,
                'to_user_id' => null,
                'subject' => '🔔 Pengajuan Pemberhentian Sementara Baru',
                'body' => "{$u->name} dari [{$companyName}] mengajukan pemberhentian sementara untuk {$contract->full_name} periode {$firstReport->period_label}. Alasan: " . mb_substr($firstReport->reason, 0, 120),
                'priority' => 'high',
                'message_type' => 'general',
            ]);
        }

        if ($isBatch) {
            return redirect()
                ->route('gik-hrd.suspensions.index')
                ->with('success', "✅ Berhasil mengajukan pemberhentian sementara MASSAL untuk {$contracts->count()} peserta sekaligus. Periode: {$periodLabel}. Semua pengajuan status saat ini: Menunggu Persetujuan.");
        }

        return redirect()
            ->route('gik-hrd.suspensions.show', $firstReport->id)
            ->with('success', "✅ Pengajuan pemberhentian sementara berhasil dikirim. Nomor: {$firstReport->request_number}. Status saat ini: Menunggu Persetujuan.");
    }

    public function showHrd(HrSuspensionRequest $report)
    {
        $u = Auth::user();
        if (!$u->isHrdCompany() && !$u->isCompanyAdmin()) abort(403);
        $this->authorizeAccess($report);

        HrAuditHelper::log('hrd_view_suspension_request_detail', 'hr_suspension_requests', $report->id);

        $pageTitle = "Detail Pengajuan #{$report->request_number}";
        return view('gik-hrd.suspension-show', compact('report', 'pageTitle', 'u'));
    }

    // ==================== SUPER ADMIN SIDE METHODS ====================

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $companies = Client::orderBy('company_name')->get();
        $statuses = ['pending_approval', 'under_review', 'approved', 'rejected'];
        $statusLabels = collect($statuses)->mapWithKeys(fn($s) => [$s => (new HrSuspensionRequest(['status' => $s]))->status_label]);

        $q = HrSuspensionRequest::withoutTenant()
            ->with(['company', 'intern', 'requestedBy']);

        if ($request->filled('company_id')) {
            $q->where('company_id', $request->input('company_id'));
        }

        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = "%{$request->input('search')}%";
            $q->whereHas('intern', function ($sq) use ($search) {
                $sq->whereRaw("CONCAT_WS(' ',first_name,last_name) like ?", [$search]);
            });
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $from = $request->input('date_from');
            $to = $request->input('date_to');
            $q->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        } elseif ($request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $request->input('date_from'));
        } elseif ($request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $tenantStats = HrSuspensionRequest::withoutTenant()
            ->select('company_id', 'status', DB::raw('count(*) as total'))
            ->groupBy('company_id', 'status')
            ->get();

        $reports = $q->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'pending_approval' => HrSuspensionRequest::withoutTenant()->where('status', 'pending_approval')->count(),
            'under_review' => HrSuspensionRequest::withoutTenant()->where('status', 'under_review')->count(),
            'approved' => HrSuspensionRequest::withoutTenant()->where('status', 'approved')->count(),
            'rejected' => HrSuspensionRequest::withoutTenant()->where('status', 'rejected')->count(),
            'active_today' => HrSuspensionRequest::withoutTenant()
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', Carbon::today())
                ->whereDate('end_date', '>=', Carbon::today())
                ->count(),
        ];

        HrAuditHelper::log('sa_view_suspensions_list', 'hr_suspension_requests');

        $pageTitle = "Manajemen Pemberhentian Sementara";
        return view('gik-superadmin.suspensions', compact('reports', 'companies', 'statusLabels', 'stats', 'tenantStats', 'user', 'pageTitle'));
    }

    public function show(HrSuspensionRequest $report)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $report = HrSuspensionRequest::withoutTenant()->with([
            'company', 'intern', 'requestedBy', 'handledBy',
            'statusLogs.updatedBy'
        ])->findOrFail($report->id);

        $companies = Client::orderBy('company_name')->get();
        $statuses = ['pending_approval', 'under_review', 'approved', 'rejected'];
        $statusLabels = collect($statuses)->mapWithKeys(fn($s) => [$s => (new HrSuspensionRequest(['status' => $s]))->status_label]);

        HrAuditHelper::log('sa_view_suspension_detail', 'hr_suspension_requests', $report->id);

        $pageTitle = "Detail Pengajuan #{$report->request_number}";
        return view('gik-superadmin.suspension-show', compact('report', 'companies', 'statusLabels', 'pageTitle', 'user'));
    }

    public function updateStatus(HrSuspensionRequest $report, Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $report = HrSuspensionRequest::withoutTenant()->findOrFail($report->id);

        $valid = $request->validate([
            'status' => ['required', 'in:pending_approval,under_review,approved,rejected'],
            'notes' => ['nullable', 'string', 'max:1500'],
        ]);

        $oldStatus = $report->status;
        $newStatus = $request->input('status');

        if ($oldStatus === $newStatus && !$request->filled('notes')) {
            return back()->with('info', 'Tidak ada perubahan status atau catatan.');
        }

        DB::beginTransaction();
        try {
            $report->status = $newStatus;
            $report->handled_by = $user->id;
            $report->handled_at = Carbon::now();
            if ($newStatus === 'rejected' && $request->filled('notes')) {
                $report->resolution_notes = $request->input('notes');
            }
            if ($newStatus === 'approved' && $request->filled('notes')) {
                $report->resolution_notes = $request->input('notes');
            }
            $report->save();

            HrSuspensionStatusLog::create([
                'suspension_request_id' => $report->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $request->input('notes'),
                'updated_by' => $user->id,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['status' => 'Gagal update status: ' . $e->getMessage()]);
        }

        HrAuditHelper::log(
            'sa_update_suspension_status',
            'hr_suspension_requests',
            $report->id,
            [
                'from' => $oldStatus,
                'to' => $newStatus,
                'notes' => $request->input('notes'),
            ]
        );

        if (in_array($newStatus, ['approved', 'rejected'])) {
            $icon = $newStatus === 'approved' ? '✅' : '❌';
            $label = $newStatus === 'approved' ? 'Disetujui' : 'Ditolak';
            $bodyExtra = $newStatus === 'approved'
                ? "Periode pemberhentian: {$report->period_label} ({$report->total_days} hari). Peserta otomatis berstatus Diberhentikan Sementara mulai tanggal {$report->start_date->format('d M Y')}."
                : "Catatan: " . ($request->input('notes') ?? '(tidak ada catatan)') . ". Status peserta tetap Aktif seperti biasa.";

            HrMessage::create([
                'company_id' => $report->company_id,
                'from_user_id' => $user->id,
                'to_user_id' => $report->requested_by,
                'subject' => "{$icon} Status Pengajuan Pemberhentian Sementara: {$label}",
                'body' => "Pengajuan #{$report->request_number} ({$report->intern?->full_name}) berubah status menjadi **{$label}**. {$bodyExtra}",
                'priority' => $newStatus === 'approved' ? 'high' : 'normal',
                'message_type' => 'general',
            ]);

            if (!$report->requested_by) {
                HrMessage::create([
                    'company_id' => $report->company_id,
                    'from_user_id' => $user->id,
                    'to_user_id' => null,
                    'subject' => "{$icon} Status Pengajuan Pemberhentian Sementara: {$label}",
                    'body' => "Pengajuan #{$report->request_number} ({$report->intern?->full_name}) berubah status menjadi **{$label}**. {$bodyExtra}",
                    'priority' => $newStatus === 'approved' ? 'high' : 'normal',
                    'message_type' => 'general',
                ]);
            }
        }

        return back()->with('success', "✅ Status berhasil diubah dari '" . optional(new HrSuspensionRequest(['status' => $oldStatus]))->status_label . "' menjadi '" . (new HrSuspensionRequest(['status' => $newStatus]))->status_label . "'. Notifikasi sudah dikirim ke HRD pelapor.");
    }

    // ==================== IDOR DOUBLE CHECK ====================

    private function authorizeAccess(HrSuspensionRequest $report): void
    {
        $u = Auth::user();
        if (!$u) return;
        if ($u->isSuperAdminRecruitment()) return;
        if ($u->company_id && (int) $u->company_id === (int) $report->company_id) return;
        abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
    }

    private function authorizeAccessIntern(HrContract $contract): void
    {
        $u = Auth::user();
        if (!$u) return;
        if ($u->isSuperAdminRecruitment()) return;
        if ($u->company_id && (int) $u->company_id === (int) $contract->company_id) return;
        abort(403, 'Anda tidak memiliki akses ke peserta magang ini.');
    }
}
