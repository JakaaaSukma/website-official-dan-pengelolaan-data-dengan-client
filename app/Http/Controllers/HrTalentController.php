<?php

namespace App\Http\Controllers;

use App\Models\HrTalent;
use App\Models\HrClientNeed;
use App\Models\HrTalentCallRequest;
use App\Models\HrMessage;
use App\Services\HrMatchingService;
use App\Services\HrImportService;
use App\Helpers\HrAuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\ValidatesFileUpload;

class HrTalentController extends Controller
{
    use ValidatesFileUpload;
    // ==================== HR COMPANY SIDE METHODS ====================

    public function indexTalentsHrd(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $search = $request->query('search');
        $role = $request->query('role');
        $availability = $request->query('availability');

        $query = HrTalent::whereIn('status', ['ready_to_hire', 'in_process']);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('target_role', 'LIKE', "%{$search}%")
                    ->orWhere('current_job_title', 'LIKE', "%{$search}%")
                    ->orWhere('skill_tags', 'LIKE', "%{$search}%")
                    ->orWhere('skills', 'LIKE', "%{$search}%");
            });
        }
        if ($role) $query->where('target_role', 'LIKE', "%{$role}%");
        if ($availability) $query->where('availability_status', $availability);

        $talents = $query->orderByDesc('match_score')->orderByDesc('created_at')->paginate(12);
        $roles = HrTalent::distinct()->pluck('target_role')->filter()->values();

        $pageTitle = 'Candidate Database — Talent Berpengalaman';
        $sMenu = 'candidate-database';
        return view('gik-hrd.talents', compact('talents', 'roles', 'search', 'role', 'availability', 'pageTitle', 'sMenu'));
    }

    public function showTalentHrd(HrTalent $talent)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        HrAuditHelper::log('hrd_view_talent', 'hr_talent', $talent->id);
        $existingCalls = HrTalentCallRequest::where('talent_id', $talent->id)
            ->where('company_id', $u->company_id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $pageTitle = "Detail Kandidat: {$talent->full_name}";
        $sMenu = 'candidate-database';
        return view('gik-hrd.talent-show', compact('talent', 'existingCalls', 'pageTitle', 'sMenu'));
    }

    public function submitTalentCallRequestHrd(HrTalent $talent, Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $validated = $request->validate([
            'call_type' => 'required|in:test,interview,other',
            'notes' => 'nullable|string|min:3|max:2000',
        ]);

        DB::beginTransaction();
        try {
            $call = HrTalentCallRequest::create([
                'company_id' => $u->company_id,
                'talent_id' => $talent->id,
                'requested_by' => $u->id,
                'call_type' => $validated['call_type'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            HrAuditHelper::log('hrd_submit_talent_call_request', 'hr_talent_call_requests', $call->id, null, $call->toArray(), 'warning');

            $companyName = $u->company?->company_name ?? 'Perusahaan';
            HrMessage::create([
                'company_id' => $u->company_id,
                'from_user_id' => $u->id,
                'to_user_id' => null,
                'message_type' => 'talent_call_request',
                'subject' => "📞 HR {$companyName} meminta {$call->call_type_label} kandidat {$talent->full_name}",
                'body' => "Permintaan pemanggilan kandidat ({$call->call_type_label}) untuk \"{$talent->full_name}\" ({$talent->target_role}). " . ($call->notes ? "Catatan: {$call->notes}" : "Segera jadwalkan pemanggilan dan konfirmasi ke HR Company."),
                'priority' => 'high',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengirim permintaan pemanggilan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-hrd.talents.show', $talent)->with('success', 'Permintaan pemanggilan kandidat berhasil dikirim ke Super Admin Rekrutmen.');
    }

    public function createClientNeedHrd()
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $myNeeds = HrClientNeed::where('company_id', $u->company_id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $pageTitle = 'Pengajuan Permintaan Tenaga Ahli / Berpengalaman';
        $sMenu = 'client-needs-create';
        return view('gik-hrd.client-need-create', compact('myNeeds', 'pageTitle', 'sMenu'));
    }

    public function storeClientNeedHrd(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $validated = $request->validate([
            'job_title' => 'required|string|max:150',
            'quota' => 'required|integer|min:1|max:100',
            'skill_tags' => 'required|string|min:2',
            'urgency' => 'required|in:urgent,open',
            'deadline' => 'nullable|date|after_or_equal:today',
            'department' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:3000',
            'valid_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('valid_proof')) {
            $proofPath = $request->file('valid_proof')->store('client-need-proofs', 'public');
        }

        DB::beginTransaction();
        try {
            $need = HrClientNeed::create([
                'company_id' => $u->company_id,
                'job_title' => $validated['job_title'],
                'department' => $validated['department'] ?? null,
                'skill_tags' => $validated['skill_tags'],
                'required_skills' => $validated['skill_tags'],
                'quota' => $validated['quota'],
                'urgency' => $validated['urgency'],
                'description' => $validated['description'] ?? null,
                'deadline' => $validated['deadline'] ?? null,
                'valid_proof_path' => $proofPath,
                'status' => 'open',
                'created_by' => $u->id,
            ]);

            HrAuditHelper::log('hrd_create_client_need', 'hr_client_need', $need->id, null, $need->toArray(), 'warning');

            $companyName = $u->company?->company_name ?? 'Perusahaan';
            HrMessage::create([
                'company_id' => $u->company_id,
                'from_user_id' => $u->id,
                'to_user_id' => null,
                'message_type' => 'client_need_request',
                'subject' => "💼 HR {$companyName} butuh Tenaga Ahli: {$need->job_title}",
                'body' => "Permintaan tenaga ahli/berpengalaman baru: \"{$need->job_title}\" ({$need->quota} posisi). Urgensi: " . strtoupper($need->urgency) . ($need->deadline ? " • Due: {$need->deadline->format('d M Y')}" : '') . ". Segera jalankan ⚡ Match Engine di Talent DB.",
                'priority' => $need->urgency === 'urgent' ? 'urgent' : 'high',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengirim permintaan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-hrd.client-needs.create')->with('success', "Permintaan tenaga ahli \"{$need->job_title}\" berhasil dikirim ke Super Admin Rekrutmen.");
    }

    // ==================== SUPER ADMIN SIDE (EXISTING) ====================

    public function index(Request $request, HrMatchingService $matchingService)
    {
        $tab = $request->query('tab', 'ready_to_hire');
        $allowedTabs = ['ready_to_hire', 'in_process', 'passive'];
        if (!in_array($tab, $allowedTabs)) $tab = 'ready_to_hire';
        $search = $request->query('search');
        $role = $request->query('role');

        $counts = [
            'ready_to_hire' => HrTalent::where('status', 'ready_to_hire')->count(),
            'in_process' => HrTalent::where('status', 'in_process')->count(),
            'passive' => HrTalent::where('status', 'passive')->count(),
        ];

        $query = HrTalent::where('status', $tab);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('target_role', 'LIKE', "%{$search}%")
                    ->orWhere('current_job_title', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        if ($role) {
            $query->where('target_role', 'LIKE', "%{$role}%");
        }

        $talents = $query->orderByDesc('created_at')->paginate(15);

        $needs = HrClientNeed::withoutTenant()
            ->with('company', 'createdBy')
            ->whereIn('status', ['open', 'in_progress'])
            ->orderByDesc('urgency')
            ->orderByDesc('created_at')
            ->get();

        $callRequests = HrTalentCallRequest::withoutTenant()
            ->with(['talent', 'company', 'requestedBy'])
            ->orderByRaw("FIELD(status, 'pending', 'scheduled', 'completed', 'cancelled')")
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $needMatches = [];
        foreach ($needs as $need) {
            $needMatches[$need->id] = $matchingService->getExistingMatches($need);
        }

        return view('gik-superadmin.talents', [
            'talents' => $talents,
            'counts' => $counts,
            'tab' => $tab,
            'search' => $search,
            'role' => $role,
            'clientNeeds' => $needs,
            'needMatches' => $needMatches,
            'talentCallRequests' => $callRequests,
            'pageTitle' => 'Talent Database & Matching',
        ]);
    }

    public function createTalent()
    {
        return view('gik-superadmin.talent-create', ['pageTitle' => 'Add Candidate']);
    }

    public function storeTalent(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'current_job_title' => 'nullable|string|max:150',
            'target_role' => 'required|string|max:150',
            'years_experience' => 'nullable|numeric|min:0|max:99',
            'education' => 'nullable|string',
            'skills' => 'nullable|string',
            'skill_tags' => 'nullable|string',
            'status' => 'required|in:ready_to_hire,in_process,passive,hired,not_available',
            'availability_status' => 'nullable|in:available,notice_period,employed',
            'location' => 'nullable|string|max:150',
            'expected_salary' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('talent-cvs', 'public');
        }

        DB::beginTransaction();
        try {
            $talent = HrTalent::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'national_id' => $validated['national_id'],
                'current_job_title' => $validated['current_job_title'],
                'target_role' => $validated['target_role'],
                'years_experience' => $validated['years_experience'] ?? 0,
                'education' => $validated['education'],
                'skills' => $validated['skills'],
                'skill_tags' => $validated['skill_tags'],
                'status' => $validated['status'],
                'availability_status' => $validated['availability_status'] ?? 'available',
                'cv_path' => $cvPath,
                'location' => $validated['location'],
                'expected_salary' => $validated['expected_salary'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);
            HrAuditHelper::log('create_talent', 'hr_talent', $talent->id, null, $talent->toArray());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($cvPath) Storage::disk('public')->delete($cvPath);
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-superadmin.talents')->with('success', 'Talent berhasil ditambahkan.');
    }

    public function showTalent(HrTalent $talent)
    {
        HrAuditHelper::log('view_talent', 'hr_talent', $talent->id);
        return view('gik-superadmin.talent-show', [
            'talent' => $talent,
            'pageTitle' => "View Talent: {$talent->full_name}",
        ]);
    }

    public function destroyTalent(HrTalent $talent)
    {
        $old = $talent->toArray();
        $talent->delete();
        HrAuditHelper::log('delete_talent', 'hr_talent', $talent->id, $old, null, 'warning');
        return redirect()->route('gik-superadmin.talents')->with('success', 'Talent dihapus.');
    }

    public function storeClientNeed(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:clients,id',
            'job_title' => 'required|string|max:150',
            'department' => 'nullable|string|max:100',
            'required_skills' => 'nullable|string',
            'skill_tags' => 'nullable|string',
            'quota' => 'nullable|integer|min:1',
            'urgency' => 'required|in:urgent,open,low',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $need = HrClientNeed::create(array_merge($validated, [
            'status' => 'open',
            'created_by' => Auth::id(),
        ]));
        HrAuditHelper::log('create_client_need', 'hr_client_need', $need->id, null, $need->toArray(), 'warning');
        return redirect()->back()->with('success', 'Client need berhasil ditambahkan.');
    }

    public function runMatchEngine(HrClientNeed $need, HrMatchingService $matchingService)
    {
        DB::beginTransaction();
        try {
            $results = $matchingService->runMatchEngine($need);
            HrAuditHelper::log('run_match_engine', 'hr_client_need', $need->id, null, [
                'matches_count' => count($results),
                'top_score' => $results[0]['score'] ?? 0,
            ], 'warning');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Match engine gagal: ' . $e->getMessage());
        }
        $top = $results[0]['score'] ?? 0;
        return redirect()->route('gik-superadmin.talents')->with('success',
            "Match Engine selesai. " . count($results) . " kandidat ditemukan. Top score: {$top}%");
    }

    public function showImportForm(Request $request)
    {
        $recent = \App\Models\HrImportLog::where('import_type', 'talents')
            ->orderByDesc('created_at')->limit(10)->get();
        $view = 'gik-hrd.import';
        if (view()->exists('gik-superadmin.import')) {
            $view = 'gik-superadmin.import';
        }
        return view($view, [
            'recentLogs' => $recent,
            'mode' => 'talents',
            'pageTitle' => 'Import Talents Excel',
        ]);
    }

    public function downloadTalentTemplate()
    {
        $service = new HrImportService();
        $path = $service->generateTalentTemplate();
        while (ob_get_level() > 0) { ob_end_clean(); }
        return response()->download($path, basename($path), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Description' => 'File Transfer',
            'Content-Transfer-Encoding' => 'binary',
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Cache-Control' => 'private, no-transform, no-store, must-revalidate, max-age=0, post-check=0, pre-check=0',
            'Pragma' => 'public',
        ])->deleteFileAfterSend(true);
    }

    public function importTalents(Request $request, HrImportService $importService)
    {
        $user = Auth::user();
        if ($request->boolean('confirm_import') && $request->has('stored_path')) {
            $storedPath = $request->input('stored_path');
            $fullPath = Storage::disk('local')->path($storedPath);
            $originalName = basename($storedPath);
        } else {
            $file = $request->file('excel_file');
            $validation = $importService->validateFile($file);
            if (!$validation['valid']) {
                return redirect()->back()->withErrors($validation['errors'])->withInput();
            }
            $originalName = $file->getClientOriginalName();
            $storedPath = $file->storeAs('imports/talents', uniqid() . '_' . $originalName, 'local');
            $fullPath = Storage::disk('local')->path($storedPath);
        }

        try {
            $excel = $importService->readExcel($fullPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal baca Excel: ' . $e->getMessage())->withInput();
        }

        $fieldMapping = [
            'First Name' => 'first_name',
            'Last Name' => 'last_name',
            'Email' => 'email',
            'Phone Number' => 'phone',
            'National ID/SSN' => 'national_id',
            'Target Role' => 'target_role',
            'Years Experience' => 'years_experience',
            'Education' => 'education',
            'Skills (comma separated)' => 'skills',
            'Skill Tags (comma separated)' => 'skill_tags',
            'Status (ready_to_hire/in_process/passive)' => 'status',
        ];
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'target_role' => 'required|string|max:150',
            'email' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:ready_to_hire,in_process,passive,hired,not_available',
            'years_experience' => 'nullable|numeric|min:0|max:99',
            'national_id' => 'nullable|string|max:50',
            'education' => 'nullable|string',
            'skills' => 'nullable|string',
            'skill_tags' => 'nullable|string',
        ];
        $validated = $importService->validateRows($excel['rows'], $rules, $fieldMapping);
        $logId = $importService->createImportLog('talents', $originalName, $storedPath, count($excel['rows']), 'validating');

        if ($request->boolean('confirm_import')) {
            $successCount = 0;
            $failedRows = [];
            DB::beginTransaction();
            try {
                $importService->updateImportLog($logId, ['status' => 'processing']);
                foreach ($validated['valid'] as $row) {
                    try {
                        $existing = null;
                        if (!empty($row['data']['email'])) {
                            $existing = HrTalent::where('email', $row['data']['email'])
                                ->first();
                        } elseif (!empty($row['data']['national_id'])) {
                            $existing = HrTalent::where('national_id', $row['data']['national_id'])
                                ->first();
                        }

                        if ($existing) {
                            $existing->update($row['data']);
                        } else {
                            HrTalent::create(array_merge($row['data'], [
                                'status' => $row['data']['status'] ?? 'ready_to_hire',
                                'availability_status' => 'available',
                                'years_experience' => $row['data']['years_experience'] ?? 0,
                                'created_by' => $user->id,
                            ]));
                        }
                        $successCount++;
                    } catch (\Exception $e) {
                        $failedRows[] = ['row' => $row['row_number'], 'error' => $e->getMessage()];
                    }
                }
                $errorDetails = array_merge($validated['invalid'], $failedRows);
                if (count($errorDetails) > 50) {
                    $errorDetails = array_slice($errorDetails, 0, 50);
                    $errorDetails[] = ['row' => '-', 'error' => '... and more errors omitted.'];
                }

                $importService->updateImportLog($logId, [
                    'status' => 'completed',
                    'success_rows' => $successCount,
                    'failed_rows' => count($validated['invalid']) + count($failedRows),
                    'error_details' => $errorDetails,
                    'completed_at' => now(),
                ]);
                DB::commit();
                HrAuditHelper::log('import_talents_complete', 'hr_import_log', $logId, null, ['success' => $successCount]);
                return redirect()->route('gik-superadmin.talents')->with('success', "Import selesai. Berhasil: {$successCount}");
            } catch (\Exception $e) {
                DB::rollBack();
                $importService->updateImportLog($logId, ['status' => 'rollback', 'completed_at' => now()]);
                return redirect()->back()->with('error', 'Import digagalkan: ' . $e->getMessage());
            }
        }

        $previewValidRows = [];
        foreach (array_slice($validated['valid'], 0, 50) as $v) {
            $previewValidRows[] = $v['data'];
        }

        $importErrors = [];
        foreach ($validated['invalid'] as $inv) {
            $importErrors[] = [
                'row' => $inv['row_number'],
                'error' => implode(', ', $inv['errors'])
            ];
        }

        return redirect()->back()->with([
            'preview_valid_rows' => $previewValidRows,
            'import_errors' => empty($importErrors) ? null : $importErrors,
            'talent_preview' => [
                'valid' => array_slice($validated['valid'], 0, 10),
                'invalid' => $validated['invalid'],
                'valid_count' => count($validated['valid']),
                'invalid_count' => count($validated['invalid']),
                'total_rows' => count($excel['rows']),
                'stored_path' => $storedPath,
            ],
        ])->withInput();
    }
}
