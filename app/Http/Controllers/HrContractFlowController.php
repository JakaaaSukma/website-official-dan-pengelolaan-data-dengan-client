<?php

namespace App\Http\Controllers;

use App\Models\HrAddContractBatch;
use App\Models\HrCandidateItem;
use App\Models\HrContract;
use App\Models\HrBatchItem;
use App\Models\HrRequest;
use App\Helpers\HrAuditHelper;
use App\Services\HrImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HrContractFlowController extends Controller
{
    // ==================== FLOW 2: ADD NEW CONTRACT ====================

    public function createAddContractForm()
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);
        $companies = \App\Models\Client::where('is_active', true)->orderBy('company_name')->get();
        return view('gik-superadmin.add-contract-create', [
            'companies' => $companies,
            'pageTitle' => 'Add New Contract — Pengajuan Kandidat Baru',
        ]);
    }

    public function downloadAddContractTemplate()
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);
        $service = new HrImportService();
        $path = $service->generateAddContractTemplate();
        while (ob_get_level() > 0) { ob_end_clean(); }
        return response()->download($path, basename($path), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function importAddContractExcel(Request $request)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);
        
        try {
            $service = new HrImportService();
            $path = $request->file('excel_file')->store('temp');
            $fullPath = storage_path('app/' . $path);
            
            $rows = $service->readExcel($fullPath);
            \Illuminate\Support\Facades\Storage::delete($path);
            
            $candidates = [];
            foreach (array_slice($rows, 1) as $row) {
                if (empty($row[0])) continue;
                $candidates[] = [
                    'full_name' => $row[0] ?? '',
                    'national_id' => $row[1] ?? '',
                    'birth_date' => $row[2] ?? '',
                    'job_title' => $row[3] ?? '',
                    'contract_start' => $row[4] ?? '',
                    'contract_duration' => $row[5] ?? '',
                    'address' => $row[6] ?? '',
                ];
            }
            return response()->json(['success' => true, 'data' => $candidates]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function storeAddContract(Request $request)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);
        $validated = $request->validate([
            'company_id' => 'required|exists:clients,id',
            'notes' => 'nullable|string|max:2000',
            'candidates' => 'required|array|min:1',
            'candidates.*.full_name' => 'required|string|max:200',
            'candidates.*.national_id' => 'required|string|max:50',
            'candidates.*.birth_date' => 'nullable|date',
            'candidates.*.job_title' => 'required|string|max:150',
            'candidates.*.address' => 'nullable|string|max:1000',
            'candidates.*.contract_start' => 'required|date',
            'candidates.*.contract_duration' => 'required|integer|in:3,6,12,18,24,36',
        ]);

        DB::beginTransaction();
        try {
            $batch = HrAddContractBatch::create([
                'batch_number' => HrAuditHelper::generateRequestNumber('CAND'),
                'company_id' => $validated['company_id'],
                'created_by' => Auth::id(),
                'status' => 'submitted',
                'notes' => $validated['notes'] ?? null,
            ]);

            $seenNiks = [];
            foreach ($validated['candidates'] as $idx => $cand) {
                $nik = trim($cand['national_id']);
                if (isset($seenNiks[$nik])) continue;
                $seenNiks[$nik] = true;

                $contractStart = \Carbon\Carbon::parse($cand['contract_start']);
                $contractEnd = $contractStart->copy()->addMonths((int) $cand['contract_duration']);

                // Auto-split name for backward compatibility with schema
                $namaParts = explode(' ', trim($cand['full_name']), 2);

                HrCandidateItem::create([
                    'batch_id' => $batch->id,
                    'first_name' => $namaParts[0],
                    'last_name' => $namaParts[1] ?? null,
                    'national_id' => $nik,
                    'birth_date' => $cand['birth_date'] ?? null,
                    'job_title' => $cand['job_title'],
                    'address' => $cand['address'] ?? null,
                    'contract_start' => $contractStart,
                    'contract_end' => $contractEnd,
                    'contract_duration_months' => (int) $cand['contract_duration'],
                    'selection_status' => 'pending',
                ]);
            }

            HrAuditHelper::log('create_add_contract_batch', 'hr_add_contract_batches', $batch->id, null, $batch->toArray(), 'warning');

            $hrdUsers = \App\Models\User::where('company_id', $batch->company_id)
                ->whereIn('role', ['hrd_company', 'company'])
                ->get();
            foreach ($hrdUsers as $hrdU) {
                \App\Models\HrMessage::create([
                    'company_id' => $batch->company_id,
                    'from_user_id' => Auth::id(),
                    'to_user_id' => $hrdU->id,
                    'message_type' => 'contract_request',
                    'subject' => '📩 Pengajuan kandidat BARU dari Super Admin untuk direview',
                    'body' => "Batch {$batch->batch_number} berisi {$batch->candidates()->count()} kandidat baru telah dikirim untuk proses seleksi HR Company. Segera lakukan review dan seleksi kandidat.",
                    'priority' => 'high',
                    'status' => 'unread',
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan pengajuan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-superadmin.add-contract.show', $batch)->with('success', 'Pengajuan kandidat berhasil dikirim ke HR Company tujuan.');
    }

    public function showAddContract(HrAddContractBatch $batch)
    {
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) {
            // boleh lintas company
        } else if ($user->isHrdCompany() || $user->role === 'company') {
            if ($batch->company_id !== $user->company_id) abort(403);
        } else {
            abort(403);
        }

        $batch->load(['candidates', 'company', 'createdBy', 'reviewedBy']);
        return view('gik-superadmin.add-contract-show', [
            'batch' => $batch,
            'pageTitle' => 'Review Pengajuan Kandidat ' . $batch->batch_number,
        ]);
    }

    public function listAddContractForSuperAdmin(Request $request)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);
        $status = $request->query('status', 'all');
        $query = HrAddContractBatch::withoutTenant()->with(['company']);
        if ($status !== 'all') $query->where('status', $status);
        $batches = $query->withCount('candidates')->orderByDesc('created_at')->paginate(10);
        return view('gik-hrd.add-contract-list', [
            'batches' => $batches,
            'status' => $status,
            'pageTitle' => 'Semua Pengajuan Kandidat Baru (Add New Contract) — Super Admin',
            'isSuperAdmin' => true,
            'sMenu' => 'add-contract-list',
        ]);
    }

    public function listAddContractForHrd(Request $request)
    {
        if (!(Auth::user()->isHrdCompany() || Auth::user()->role === 'company')) abort(403);
        $companyId = Auth::user()->company_id;
        $status = $request->query('status', 'all');
        $query = HrAddContractBatch::where('company_id', $companyId);
        if ($status !== 'all') $query->where('status', $status);
        $batches = $query->withCount('candidates')->orderByDesc('created_at')->paginate(10);
        return view('gik-hrd.add-contract-list', [
            'batches' => $batches,
            'status' => $status,
            'pageTitle' => 'Pengajuan Kandidat Baru (Add New Contract)',
        ]);
    }

    public function hrdSubmitSelection(HrAddContractBatch $batch, Request $request)
    {
        if (!(Auth::user()->isHrdCompany() || Auth::user()->role === 'company')) abort(403);
        if ($batch->company_id !== Auth::user()->company_id) abort(403);
        if ($batch->status !== 'submitted') return redirect()->back()->with('error', 'Batch ini tidak bisa diproses.');

        $validated = $request->validate([
            'selections' => 'required|array',
            'selections.*' => 'in:selected,not_selected',
        ]);

        DB::beginTransaction();
        try {
            $oldBatch = $batch->toArray();
            $selections = $validated['selections'];

            foreach ($batch->candidates as $cand) {
                $oldCand = $cand->toArray();
                if (isset($selections[$cand->id]) && $selections[$cand->id] === 'selected') {
                    $cand->selection_status = 'selected';
                    $contract = HrContract::updateOrCreate(
                        ['national_id' => $cand->national_id],
                        [
                            'company_id' => $batch->company_id,
                            'employee_id' => 'EMP-NEW-' . str_pad((string)$cand->id, 5, '0', STR_PAD_LEFT),
                            'first_name' => $cand->first_name,
                            'last_name' => $cand->last_name,
                            'birth_date' => $cand->birth_date,
                            'address' => $cand->address,
                            'job_title' => $cand->job_title,
                            'department' => 'General',
                            'contract_start' => $cand->contract_start ?? now(),
                            'contract_end' => $cand->contract_end ?? now()->addMonths(6),
                            'contract_type' => 'internship',
                            'work_type' => 'on_site',
                            'status' => 'active',
                            'created_by' => Auth::id(),
                        ]
                    );
                    $cand->resulting_contract_id = $contract->id;
                    HrAuditHelper::log('hrd_selected_candidate', 'hr_candidate_items', $cand->id, $oldCand, $cand->toArray(), 'warning');
                } else {
                    $cand->selection_status = 'not_selected';
                    HrAuditHelper::log('hrd_rejected_candidate', 'hr_candidate_items', $cand->id, $oldCand, $cand->toArray(), 'info');
                }
                $cand->save();
            }

            $batch->status = 'reviewed';
            $batch->reviewed_by = Auth::id();
            $batch->reviewed_at = now();
            $batch->save();

            $batch->status = 'completed';
            $batch->save();

            HrAuditHelper::log('hrd_completed_candidate_batch', 'hr_add_contract_batches', $batch->id, $oldBatch, $batch->toArray(), 'warning');

            $companyName = $batch->company?->company_name ?? 'Perusahaan';
            $selectedCount = collect($validated['selections'])->filter(fn($v) => $v === 'selected')->count();
            $totalCount = $batch->candidates->count();
            \App\Models\HrMessage::create([
                'company_id' => $batch->company_id,
                'from_user_id' => Auth::id(),
                'to_user_id' => null,
                'message_type' => 'contract_request',
                'subject' => "✅ HR {$companyName} selesai seleksi batch {$batch->batch_number}",
                'body' => "Proses seleksi kandidat selesai dilakukan oleh HR Company. Hasil: {$selectedCount}/{$totalCount} kandidat terpilih dan otomatis masuk ke Data Peserta Aktif.",
                'priority' => 'high',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses seleksi: ' . $e->getMessage());
        }

        $selected = collect($validated['selections'])->filter(fn($v) => $v === 'selected')->count();
        $total = $batch->candidates->count();
        return redirect()->route('gik-hrd.add-contract.list')->with('success', "Seleksi selesai: {$selected}/{$total} kandidat lolos & masuk Data Peserta Aktif.");
    }

    // ==================== FLOW 3 & 4: BATCH EXTEND / OFF-BOARDING ====================

    public function createBatchForm(Request $request, string $type)
    {
        if (!in_array($type, ['extend_contract', 'off_boarding'])) abort(404);
        if (!(Auth::user()->isHrdCompany() || Auth::user()->role === 'company')) abort(403);

        $companyId = Auth::user()->company_id;
        $search = $request->query('search');
        $query = HrContract::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('contract_end', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('national_id', 'LIKE', "%{$search}%")
                    ->orWhere('job_title', 'LIKE', "%{$search}%");
            });
        }

        $contracts = $query->paginate(10);
        return view('gik-hrd.batch-select', [
            'contracts' => $contracts,
            'type' => $type,
            'search' => $search,
            'pageTitle' => $type === 'extend_contract' ? 'Extend Contract — Pilih Peserta' : 'Off-boarding — Pilih Peserta',
        ]);
    }

    public function storeBatchRequest(Request $request, string $type)
    {
        if (!in_array($type, ['extend_contract', 'off_boarding'])) abort(404);
        if (!(Auth::user()->isHrdCompany() || Auth::user()->role === 'company')) abort(403);
        $companyId = Auth::user()->company_id;

        $rules = [
            'contract_ids' => 'required|array|min:1',
            'contract_ids.*' => 'exists:hr_contracts,id,company_id,' . $companyId . ',status,active',
        ];
        if ($type === 'extend_contract') {
            $rules['items'] = 'required|array';
            $rules['items.*.contract_id'] = 'required|integer';
            $rules['items.*.new_contract_end'] = 'required|date';
            $rules['items.*.notes'] = 'nullable|string|max:1000';
        } else {
            $rules['items'] = 'required|array';
            $rules['items.*.contract_id'] = 'required|integer';
            $rules['items.*.off_boarding_effective_date'] = 'required|date';
            $rules['items.*.off_boarding_reason'] = 'required|string|min:3|max:1000';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $hrReq = HrRequest::create([
                'company_id' => $companyId,
                'request_number' => HrAuditHelper::generateRequestNumber($type === 'extend_contract' ? 'EXT' : 'OFF'),
                'request_type' => $type,
                'status' => 'submitted',
                'current_step' => 3,
                'total_steps' => 3,
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
                'created_by' => Auth::id(),
            ]);

            $itemMap = [];
            foreach ($validated['items'] ?? [] as $it) {
                $itemMap[$it['contract_id']] = $it;
            }

            foreach ($validated['contract_ids'] as $cid) {
                if (!isset($itemMap[$cid])) continue;
                $it = $itemMap[$cid];
                HrBatchItem::create(array_merge([
                    'request_id' => $hrReq->id,
                    'contract_id' => $cid,
                ], [
                    'new_contract_end' => $it['new_contract_end'] ?? null,
                    'notes' => $it['notes'] ?? null,
                    'off_boarding_effective_date' => $it['off_boarding_effective_date'] ?? null,
                    'off_boarding_reason' => $it['off_boarding_reason'] ?? null,
                ]));
            }

            HrAuditHelper::log('submit_batch_' . $type, 'hr_request', $hrReq->id, null, array_merge($hrReq->toArray(), ['batch_items_count' => count($validated['contract_ids'])]), 'warning');

            $labelType = $type === 'extend_contract' ? 'Extend Contract' : 'Off-boarding';
            $iconType = $type === 'extend_contract' ? '📨' : '🚪';
            $companyName = Auth::user()->company?->company_name ?? 'Perusahaan';
            \App\Models\HrMessage::create([
                'company_id' => $companyId,
                'from_user_id' => Auth::id(),
                'to_user_id' => null,
                'message_type' => 'contract_request',
                'subject' => "{$iconType} HR {$companyName} mengirim request batch {$labelType}",
                'body' => "Request {$labelType} {$hrReq->request_number} berisi " . count($validated['contract_ids']) . " peserta telah dikirim ke Super Admin Rekrutmen untuk menunggu approval.",
                'priority' => 'high',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat request batch: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-hrd.operations')->with('success', 'Request batch ' . ($type === 'extend_contract' ? 'Extend Contract' : 'Off-boarding') . ' berhasil dikirim menunggu approval.');
    }

    // ==================== QUICK ACTION LANGSUNG DARI DASHBOARD: Single + Batch via ?ids= ====================

    private function parseQuickIds(Request $request): array
    {
        if ($request->filled('ids')) {
            $raw = explode(',', $request->input('ids'));
        } else if ($request->filled('contract_id')) {
            $raw = [$request->input('contract_id')];
        } else {
            $raw = [];
        }
        $ids = [];
        foreach ($raw as $r) { $v = (int) trim($r); if ($v > 0) $ids[] = $v; }
        return array_values(array_unique($ids));
    }

    public function quickAction(Request $request, string $type)
    {
        if (!in_array($type, ['extend_contract', 'off_boarding'])) abort(404);
        if (!(Auth::user()->isHrdCompany() || Auth::user()->role === 'company')) abort(403);

        $companyId = Auth::user()->company_id;
        $ids = $this->parseQuickIds($request);
        if (count($ids) === 0) {
            // Fallback: redirect ke halaman batch select lama untuk pilih manual
            return redirect()->route('gik-hrd.batch.select', $type)->with('info', 'Silakan pilih peserta terlebih dahulu.');
        }

        $contracts = HrContract::whereIn('id', $ids)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('contract_end', 'asc')
            ->get();

        if ($contracts->count() !== count($ids)) {
            $found = $contracts->pluck('id')->toArray();
            $missed = array_diff($ids, $found);
            return redirect()->route('gik-hrd.dashboard')
                ->with('warning', 'Beberapa peserta tidak ditemukan / sudah tidak aktif (ID: ' . implode(',', $missed) . '). Silakan refresh dan coba lagi.');
        }

        $isBatch = $contracts->count() > 1;
        $batchIdsStr = implode(',', $contracts->pluck('id')->toArray());

        $pageTitle = match ($type) {
            'extend_contract' => $isBatch
                ? "📈 Extend Kontrak Massal ({$contracts->count()} peserta)"
                : "📈 Extend Kontrak: {$contracts->first()->full_name}",
            'off_boarding' => $isBatch
                ? "🚪 Off-boarding Massal ({$contracts->count()} peserta)"
                : "🚪 Off-boarding: {$contracts->first()->full_name}",
        };

        $typeLabel = match ($type) {
            'extend_contract' => 'Perpanjang Masa Kontrak',
            'off_boarding' => 'Pengajuan Pengakhiran (Off-boarding)',
        };

        return view('gik-hrd.quick-action-form', compact(
            'contracts', 'type', 'typeLabel', 'isBatch', 'batchIdsStr', 'pageTitle'
        ));
    }

    public function quickActionStore(Request $request, string $type)
    {
        if (!in_array($type, ['extend_contract', 'off_boarding'])) abort(404);
        if (!(Auth::user()->isHrdCompany() || Auth::user()->role === 'company')) abort(403);
        $companyId = Auth::user()->company_id;
        $u = Auth::user();

        $baseRules = [
            '_batch_ids' => 'required|string',
            'mode' => 'required|in:global,per_person',
        ];
        if ($type === 'extend_contract') {
            if ($request->input('mode') === 'global') {
                $baseRules['global_new_contract_end'] = 'required|date';
                $baseRules['global_notes'] = 'nullable|string|max:1000';
            } else {
                $baseRules['items'] = 'required|array';
                $baseRules['items.*.contract_id'] = 'required|integer';
                $baseRules['items.*.new_contract_end'] = 'required|date';
                $baseRules['items.*.notes'] = 'nullable|string|max:1000';
            }
        } else {
            // off_boarding
            if ($request->input('mode') === 'global') {
                $baseRules['global_off_date'] = 'required|date';
                $baseRules['global_off_reason'] = 'required|string|min:5|max:1000';
            } else {
                $baseRules['items'] = 'required|array';
                $baseRules['items.*.contract_id'] = 'required|integer';
                $baseRules['items.*.off_boarding_effective_date'] = 'required|date';
                $baseRules['items.*.off_boarding_reason'] = 'required|string|min:5|max:1000';
            }
        }
        $validated = $request->validate($baseRules);

        $raw = explode(',', $validated['_batch_ids']);
        $ids = [];
        foreach ($raw as $r) { $v = (int) trim($r); if ($v > 0) $ids[] = $v; }
        $ids = array_values(array_unique($ids));
        if (count($ids) === 0) return back()->with('error', 'Tidak ada peserta terpilih.');

        $contracts = HrContract::whereIn('id', $ids)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->get();
        if ($contracts->count() !== count($ids)) return back()->with('error', 'Beberapa peserta tidak valid / sudah tidak aktif.');

        // Build items array
        $items = [];
        $mode = $validated['mode'];

        if ($type === 'extend_contract') {
            if ($mode === 'global') {
                $gDate = $validated['global_new_contract_end'];
                $gNotes = $validated['global_notes'] ?? null;
                foreach ($contracts as $c) {
                    $items[] = [
                        'contract_id' => $c->id,
                        'new_contract_end' => $gDate,
                        'notes' => $gNotes,
                    ];
                }
            } else {
                $rawItems = $validated['items'];
                $itemMap = [];
                foreach ($rawItems as $it) $itemMap[$it['contract_id']] = $it;
                foreach ($contracts as $c) {
                    if (!isset($itemMap[$c->id])) continue;
                    $it = $itemMap[$c->id];
                    $items[] = [
                        'contract_id' => $c->id,
                        'new_contract_end' => $it['new_contract_end'],
                        'notes' => $it['notes'] ?? null,
                    ];
                }
            }
        } else {
            // off_boarding
            if ($mode === 'global') {
                $gDate = $validated['global_off_date'];
                $gReason = $validated['global_off_reason'];
                foreach ($contracts as $c) {
                    $items[] = [
                        'contract_id' => $c->id,
                        'off_boarding_effective_date' => $gDate,
                        'off_boarding_reason' => $gReason,
                    ];
                }
            } else {
                $rawItems = $validated['items'];
                $itemMap = [];
                foreach ($rawItems as $it) $itemMap[$it['contract_id']] = $it;
                foreach ($contracts as $c) {
                    if (!isset($itemMap[$c->id])) continue;
                    $it = $itemMap[$c->id];
                    $items[] = [
                        'contract_id' => $c->id,
                        'off_boarding_effective_date' => $it['off_boarding_effective_date'],
                        'off_boarding_reason' => $it['off_boarding_reason'],
                    ];
                }
            }
        }

        if (count($items) === 0) return back()->with('error', 'Tidak ada item data yang valid.');

        DB::beginTransaction();
        try {
            $hrReq = HrRequest::create([
                'company_id' => $companyId,
                'contract_id' => $items[0]['contract_id'] ?? null,
                'request_number' => HrAuditHelper::generateRequestNumber($type === 'extend_contract' ? 'EXT' : 'OFF'),
                'request_type' => $type,
                'status' => 'submitted',
                'current_step' => 3,
                'total_steps' => 3,
                'submitted_by' => $u->id,
                'submitted_at' => now(),
                'created_by' => $u->id,
                'off_boarding_date' => $type === 'off_boarding' && $mode === 'global' ? ($validated['global_off_date'] ?? null) : null,
                'off_boarding_reason' => $type === 'off_boarding' && $mode === 'global' ? ($validated['global_off_reason'] ?? null) : null,
                'contract_end' => $type === 'extend_contract' && $mode === 'global' ? ($validated['global_new_contract_end'] ?? null) : null,
                'review_notes' => null,
            ]);

            foreach ($items as $it) {
                HrBatchItem::create(array_merge([
                    'request_id' => $hrReq->id,
                    'contract_id' => $it['contract_id'],
                ], [
                    'new_contract_end' => $it['new_contract_end'] ?? null,
                    'notes' => $it['notes'] ?? null,
                    'off_boarding_effective_date' => $it['off_boarding_effective_date'] ?? null,
                    'off_boarding_reason' => $it['off_boarding_reason'] ?? null,
                ]));
            }

            HrAuditHelper::log(
                'quick_submit_' . $type . ($contracts->count() > 1 ? '_batch' : ''),
                'hr_request',
                $hrReq->id,
                null,
                array_merge($hrReq->toArray(), [
                    'items_count' => count($items),
                    'mode' => $mode,
                    'contract_ids' => $ids,
                ]),
                'warning'
            );

            $labelType = $type === 'extend_contract' ? 'Extend Contract' : 'Off-boarding';
            $iconType = $type === 'extend_contract' ? '📈' : '🚪';
            $companyName = $u->company?->company_name ?? 'Perusahaan';
            \App\Models\HrMessage::create([
                'company_id' => $companyId,
                'from_user_id' => $u->id,
                'to_user_id' => null,
                'message_type' => 'contract_request',
                'subject' => "{$iconType} HR {$companyName} mengirim QUICK request {$labelType} (dari Dashboard)",
                'body' => "Request {$labelType} {$hrReq->request_number} via Quick Action Dashboard berisi " . count($items) . " peserta telah dikirim ke Super Admin Rekrutmen untuk menunggu approval. Mode: " . ($mode === 'global' ? 'Global (data sama untuk semua)' : 'Per Individu') . ".",
                'priority' => 'high',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan quick request: ' . $e->getMessage())->withInput();
        }

        $successText = match ($type) {
            'extend_contract' => $contracts->count() > 1
                ? "✅ Berhasil mengirim QUICK request Extend Contract MASSAL untuk {$contracts->count()} peserta sekaligus. Nomor request: {$hrReq->request_number}. Menunggu approval Super Admin."
                : "✅ Berhasil mengirim request Extend Contract untuk {$contracts->first()->full_name}. Nomor: {$hrReq->request_number}. Menunggu approval.",
            'off_boarding' => $contracts->count() > 1
                ? "✅ Berhasil mengirim QUICK request Off-boarding MASSAL untuk {$contracts->count()} peserta sekaligus. Nomor request: {$hrReq->request_number}. Menunggu approval Super Admin."
                : "✅ Berhasil mengirim request Off-boarding untuk {$contracts->first()->full_name}. Nomor: {$hrReq->request_number}. Menunggu approval.",
        };

        return redirect()->route('gik-hrd.operations')->with('success', $successText);
    }

    // ===== Approve BATCH Extend / Off-boarding (Super Admin) =====
    public function reviewBatchRequest(HrRequest $hrRequest, Request $request)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'review_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $old = $hrRequest->toArray();
            $hrRequest->status = $validated['action'] === 'approve' ? 'approved' : 'rejected';
            $hrRequest->reviewed_by = Auth::id();
            $hrRequest->reviewed_at = now();
            $hrRequest->review_notes = $validated['review_notes'] ?? null;
            $hrRequest->save();

            if ($validated['action'] === 'approve') {
                $this->applyBatchRequest($hrRequest);
                $hrRequest->status = 'completed';
                $hrRequest->save();
            }

            HrAuditHelper::log(
                'batch_request_' . $validated['action'],
                'hr_request',
                $hrRequest->id,
                $old,
                $hrRequest->toArray(),
                'warning'
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal review request: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Request batch telah ' . ($validated['action'] === 'approve' ? 'disetujui & di-apply ke Peserta Aktif' : 'ditolak'));
    }

    private function applyBatchRequest(HrRequest $req): void
    {
        if ($req->batchItems && $req->batchItems->isNotEmpty()) {
            foreach ($req->batchItems as $item) {
                $contract = $item->contract;
                if (!$contract) continue;
                $oldC = $contract->toArray();

                if ($req->request_type === 'extend_contract') {
                    $contract->contract_end = $item->new_contract_end ?? $contract->contract_end;
                    $contract->status = 'extended';
                    $contract->updated_by = Auth::id();
                    $contract->save();
                    HrAuditHelper::log('batch_extend_contract', 'hr_contract', $contract->id, $oldC, $contract->toArray(), 'warning');
                } else if ($req->request_type === 'off_boarding') {
                    $contract->status = 'offboarded';
                    $contract->updated_by = Auth::id();
                    $contract->save();
                    HrAuditHelper::log('batch_off_boarding', 'hr_contract', $contract->id, $oldC, $contract->toArray(), 'critical');
                }
            }
        } elseif ($req->contract) {
            $contract = $req->contract;
            $oldC = $contract->toArray();
            if ($req->request_type === 'extend_contract') {
                $contract->contract_end = $req->contract_end ?? $contract->contract_end;
                $contract->status = 'extended';
                $contract->updated_by = Auth::id();
                $contract->save();
                HrAuditHelper::log('extend_contract', 'hr_contract', $contract->id, $oldC, $contract->toArray(), 'warning');
            } elseif ($req->request_type === 'off_boarding') {
                $contract->status = 'offboarded';
                $contract->updated_by = Auth::id();
                $contract->save();
                HrAuditHelper::log('offboard_contract', 'hr_contract', $contract->id, $oldC, $contract->toArray(), 'critical');
            }
        }
    }

    // ==================== FLOW 4: MANAJEMEN OFF-BOARDING (SUPER ADMIN) ====================

    public function listOffboardingForSuperAdmin(Request $request)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);

        $activeTab = $request->query('tab', 'requests'); // 'requests' or 'history'
        $selectedCompany = $request->query('company_id');
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search');

        // Tab 1: Requests (Pengajuan Off-boarding dari HR Company)
        $reqQuery = HrRequest::withoutTenant()
            ->where('request_type', 'off_boarding')
            ->with(['company', 'contract', 'batchItems.contract', 'submittedBy', 'reviewedBy'])
            ->orderByDesc('created_at');

        if ($selectedCompany && $selectedCompany !== 'all') {
            $reqQuery->where('company_id', $selectedCompany);
        }
        if ($statusFilter && $statusFilter !== 'all') {
            $reqQuery->where('status', $statusFilter);
        }
        if ($search) {
            $s = '%' . trim($search) . '%';
            $reqQuery->where(function ($q) use ($s) {
                $q->where('request_number', 'like', $s)
                    ->orWhere('first_name', 'like', $s)
                    ->orWhere('last_name', 'like', $s)
                    ->orWhereHas('company', fn($q2) => $q2->where('company_name', 'like', $s));
            });
        }
        $requests = $reqQuery->paginate(12, ['*'], 'req_page')->withQueryString();

        // Tab 2: History Peserta yang sudah di-offboard
        $histQuery = HrContract::withoutTenant()
            ->whereIn('status', ['offboarded', 'terminated'])
            ->with('company')
            ->orderByDesc('updated_at');

        if ($selectedCompany && $selectedCompany !== 'all') {
            $histQuery->where('company_id', $selectedCompany);
        }
        if ($search) {
            $s = '%' . trim($search) . '%';
            $histQuery->where(function ($q) use ($s) {
                $q->where('first_name', 'like', $s)
                    ->orWhere('last_name', 'like', $s)
                    ->orWhere(DB::raw("CONCAT_WS(' ', first_name, last_name)"), 'like', $s)
                    ->orWhere('national_id', 'like', $s)
                    ->orWhere('employee_id', 'like', $s);
            });
        }
        $offboardedParticipants = $histQuery->paginate(15, ['*'], 'hist_page')->withQueryString();

        // Stats
        $pendingReviewsCount = HrRequest::withoutTenant()
            ->where('request_type', 'off_boarding')
            ->whereIn('status', ['submitted', 'in_review'])
            ->count();
        $totalOffboardedCount = HrContract::withoutTenant()
            ->whereIn('status', ['offboarded', 'terminated'])
            ->count();
        $offboardedThisMonth = HrContract::withoutTenant()
            ->whereIn('status', ['offboarded', 'terminated'])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $totalActiveCount = HrContract::withoutTenant()
            ->whereIn('status', ['active', 'extended'])
            ->count();

        $companies = \App\Models\Client::where('is_active', true)->orderBy('company_name')->get();

        return view('gik-superadmin.offboarding-index', [
            'requests' => $requests,
            'offboardedParticipants' => $offboardedParticipants,
            'activeTab' => $activeTab,
            'selectedCompany' => $selectedCompany,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'pendingReviewsCount' => $pendingReviewsCount,
            'totalOffboardedCount' => $totalOffboardedCount,
            'offboardedThisMonth' => $offboardedThisMonth,
            'totalActiveCount' => $totalActiveCount,
            'companies' => $companies,
            'pageTitle' => 'Manajemen Off-boarding Peserta — Super Admin',
            'sMenu' => 'offboarding',
        ]);
    }

    public function createDirectOffboardingForm(Request $request)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);

        $companies = \App\Models\Client::where('is_active', true)->orderBy('company_name')->get();
        $selectedCompanyId = $request->query('company_id');
        $participants = collect();

        if ($selectedCompanyId) {
            $participants = HrContract::withoutTenant()
                ->where('company_id', $selectedCompanyId)
                ->whereIn('status', ['active', 'extended'])
                ->orderBy('first_name')
                ->get();
        }

        return view('gik-superadmin.offboarding-create', [
            'companies' => $companies,
            'selectedCompanyId' => $selectedCompanyId,
            'participants' => $participants,
            'pageTitle' => 'Proses Off-boarding Langsung — Super Admin',
            'sMenu' => 'offboarding',
        ]);
    }

    public function storeDirectOffboarding(Request $request)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);

        $validated = $request->validate([
            'company_id' => 'required|exists:clients,id',
            'contract_ids' => 'required|array|min:1',
            'contract_ids.*' => 'required|exists:hr_contracts,id',
            'off_boarding_effective_date' => 'required|date',
            'off_boarding_reason' => 'required|string|min:3|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $company = \App\Models\Client::find($validated['company_id']);
        $contracts = HrContract::withoutTenant()
            ->whereIn('id', $validated['contract_ids'])
            ->where('company_id', $validated['company_id'])
            ->get();

        if ($contracts->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data peserta valid yang dipilih.')->withInput();
        }

        DB::beginTransaction();
        try {
            $hrReq = HrRequest::create([
                'company_id' => $validated['company_id'],
                'contract_id' => $contracts->count() === 1 ? $contracts->first()->id : null,
                'request_number' => HrAuditHelper::generateRequestNumber('OFF'),
                'request_type' => 'off_boarding',
                'status' => 'completed',
                'current_step' => 3,
                'total_steps' => 3,
                'off_boarding_date' => $validated['off_boarding_effective_date'],
                'off_boarding_reason' => $validated['off_boarding_reason'],
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => 'Diproses langsung oleh Tim Rekrutmen Super Admin. ' . ($validated['notes'] ?? ''),
                'created_by' => Auth::id(),
            ]);

            foreach ($contracts as $c) {
                HrBatchItem::create([
                    'request_id' => $hrReq->id,
                    'contract_id' => $c->id,
                    'off_boarding_effective_date' => $validated['off_boarding_effective_date'],
                    'off_boarding_reason' => $validated['off_boarding_reason'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                $oldC = $c->toArray();
                $c->status = 'offboarded';
                $c->updated_by = Auth::id();
                $c->save();

                HrAuditHelper::log('direct_offboard_participant', 'hr_contract', $c->id, $oldC, $c->toArray(), 'critical');
            }

            // Kirim notifikasi pesan ke HR Company
            \App\Models\HrMessage::create([
                'company_id' => $validated['company_id'],
                'from_user_id' => Auth::id(),
                'to_user_id' => null,
                'message_type' => 'contract_request',
                'subject' => "🚪 Super Admin memproses Off-boarding {$contracts->count()} peserta",
                'body' => "Tim Rekrutmen Super Admin telah memproses status Off-boarding untuk " . $contracts->count() . " peserta di perusahaan {$company->company_name}. Nomor referensi: {$hrReq->request_number}. Alasan: {$validated['off_boarding_reason']}.",
                'priority' => 'high',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses off-boarding: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-superadmin.offboarding.index', ['tab' => 'history'])
            ->with('success', "Berhasil memproses off-boarding untuk {$contracts->count()} peserta.");
    }

    public function singleOffboarding(Request $request, HrContract $hrContract)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);

        $validated = $request->validate([
            'off_boarding_effective_date' => 'required|date',
            'off_boarding_reason' => 'required|string|min:3|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $hrReq = HrRequest::create([
                'company_id' => $hrContract->company_id,
                'contract_id' => $hrContract->id,
                'request_number' => HrAuditHelper::generateRequestNumber('OFF'),
                'request_type' => 'off_boarding',
                'status' => 'completed',
                'current_step' => 3,
                'total_steps' => 3,
                'off_boarding_date' => $validated['off_boarding_effective_date'],
                'off_boarding_reason' => $validated['off_boarding_reason'],
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => 'Single off-boarding oleh Super Admin. ' . ($validated['notes'] ?? ''),
                'created_by' => Auth::id(),
            ]);

            HrBatchItem::create([
                'request_id' => $hrReq->id,
                'contract_id' => $hrContract->id,
                'off_boarding_effective_date' => $validated['off_boarding_effective_date'],
                'off_boarding_reason' => $validated['off_boarding_reason'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $oldC = $hrContract->toArray();
            $hrContract->status = 'offboarded';
            $hrContract->updated_by = Auth::id();
            $hrContract->save();

            HrAuditHelper::log('single_offboard_participant', 'hr_contract', $hrContract->id, $oldC, $hrContract->toArray(), 'critical');

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses off-boarding: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "Peserta {$hrContract->full_name} berhasil di-offboard.");
    }

    public function getRequestDetailJson(HrRequest $hrRequest)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) {
            if ($hrRequest->company_id !== $user->company_id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        $hrRequest->load(['company', 'contract', 'batchItems.contract', 'submittedBy', 'reviewedBy']);

        $items = [];
        if ($hrRequest->batchItems->isNotEmpty()) {
            foreach ($hrRequest->batchItems as $bi) {
                $c = $bi->contract;
                $items[] = [
                    'contract_id' => $bi->contract_id,
                    'full_name' => $c ? $c->full_name : '—',
                    'employee_id' => $c ? $c->employee_id : '—',
                    'national_id' => $c ? $c->national_id : '—',
                    'job_title' => $c ? $c->job_title : '—',
                    'contract_end' => $c && $c->contract_end ? $c->contract_end->format('d M Y') : '—',
                    'new_contract_end' => $bi->new_contract_end ? $bi->new_contract_end->format('d M Y') : null,
                    'off_boarding_effective_date' => $bi->off_boarding_effective_date ? $bi->off_boarding_effective_date->format('d M Y') : null,
                    'off_boarding_reason' => $bi->off_boarding_reason,
                    'notes' => $bi->notes,
                ];
            }
        } elseif ($hrRequest->contract) {
            $c = $hrRequest->contract;
            $items[] = [
                'contract_id' => $c->id,
                'full_name' => $c->full_name,
                'employee_id' => $c->employee_id,
                'national_id' => $c->national_id,
                'job_title' => $c->job_title,
                'contract_end' => $c->contract_end ? $c->contract_end->format('d M Y') : '—',
                'new_contract_end' => $hrRequest->contract_end ? $hrRequest->contract_end->format('d M Y') : null,
                'off_boarding_effective_date' => $hrRequest->off_boarding_date ? $hrRequest->off_boarding_date->format('d M Y') : null,
                'off_boarding_reason' => $hrRequest->off_boarding_reason,
                'notes' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $hrRequest->id,
                'request_number' => $hrRequest->request_number,
                'request_type' => $hrRequest->request_type,
                'request_type_label' => $hrRequest->request_type === 'extend_contract' ? 'Extend Contract' : ($hrRequest->request_type === 'off_boarding' ? 'Off-boarding' : 'Add Contract'),
                'status' => $hrRequest->status,
                'company_name' => $hrRequest->company?->company_name ?? '—',
                'submitted_by' => $hrRequest->submittedBy?->name ?? '—',
                'submitted_at' => $hrRequest->submitted_at ? $hrRequest->submitted_at->format('d M Y, H:i') : ($hrRequest->created_at ? $hrRequest->created_at->format('d M Y, H:i') : '—'),
                'reviewed_by' => $hrRequest->reviewedBy?->name ?? '—',
                'reviewed_at' => $hrRequest->reviewed_at ? $hrRequest->reviewed_at->format('d M Y, H:i') : null,
                'review_notes' => $hrRequest->review_notes,
                'items' => $items,
            ],
        ]);
    }
}
