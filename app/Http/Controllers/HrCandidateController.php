<?php

namespace App\Http\Controllers;

use App\Models\HrCandidate;
use App\Helpers\HrAuditHelper;
use App\Services\HrImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\ValidatesFileUpload;

class HrCandidateController extends Controller
{
    use ValidatesFileUpload;
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        $viewingCompanyId = $request->query('company_id');

        if ($isSuperAdmin && $viewingCompanyId) {
            $query = HrCandidate::withoutTenant()->where('company_id', $viewingCompanyId);
        } else {
            $query = HrCandidate::query();
        }

        $roleFilter = $request->query('role');
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('job_title', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        if ($roleFilter && $roleFilter !== 'all') {
            $query->where('job_title', 'LIKE', "%{$roleFilter}%");
        }
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(12);

        $roles = HrCandidate::query()
            ->when($isSuperAdmin && $viewingCompanyId, fn($q) => $q->withoutTenant()->where('company_id', $viewingCompanyId))
            ->distinct()->pluck('job_title')->filter()->values()->toArray();

        return view('gik-hrd.candidates', [
            'candidates' => $candidates,
            'roles' => $roles,
            'search' => $search,
            'roleFilter' => $roleFilter ?? 'all',
            'statusFilter' => $statusFilter ?? 'all',
            'pageTitle' => 'Candidate Database',
            'isSuperAdmin' => $isSuperAdmin,
            'viewingCompanyId' => $viewingCompanyId,
        ]);
    }

    public function create(Request $request)
    {
        return view('gik-hrd.candidate-create', [
            'companyId' => Auth::user()->company_id,
            'pageTitle' => 'Add Candidate',
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'job_title' => 'required|string|max:150',
            'department' => 'nullable|string|max:100',
            'years_experience' => 'nullable|numeric|min:0|max:99',
            'education' => 'nullable|string',
            'skills' => 'nullable|string',
            'status' => 'required|in:new,interviewing,offer_sent,hired,rejected,passive',
            'notes' => 'nullable|string',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
        }

        $companyId = $user->isSuperAdminRecruitment()
            ? ($request->input('company_id') ?? $user->company_id)
            : $user->company_id;

        DB::beginTransaction();
        try {
            $candidate = HrCandidate::create([
                'company_id' => $companyId,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'national_id' => $validated['national_id'],
                'job_title' => $validated['job_title'],
                'department' => $validated['department'],
                'years_experience' => $validated['years_experience'] ?? 0,
                'education' => $validated['education'],
                'skills' => $validated['skills'],
                'status' => $validated['status'],
                'cv_path' => $cvPath,
                'notes' => $validated['notes'],
                'created_by' => $user->id,
            ]);

            HrAuditHelper::log('create_candidate', 'hr_candidate', $candidate->id, null, $candidate->toArray());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($cvPath) Storage::disk('public')->delete($cvPath);
            return redirect()->back()->with('error', 'Gagal menyimpan kandidat: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-hrd.candidates')->with('success', 'Kandidat berhasil ditambahkan.');
    }

    public function show(HrCandidate $candidate)
    {
        $this->authorizeAccess($candidate);
        HrAuditHelper::log('view_candidate_cv', 'hr_candidate', $candidate->id, null, null, 'warning');
        return view('gik-hrd.candidate-show', [
            'candidate' => $candidate,
            'pageTitle' => 'View Candidate: ' . $candidate->full_name,
        ]);
    }

    public function destroy(HrCandidate $candidate)
    {
        $this->authorizeAccess($candidate);
        $old = $candidate->toArray();
        $candidate->delete();
        HrAuditHelper::log('delete_candidate', 'hr_candidate', $candidate->id, $old, null, 'warning');
        return redirect()->route('gik-hrd.candidates')->with('success', 'Kandidat berhasil dihapus.');
    }

    public function showImportForm(Request $request)
    {
        $recent = \App\Models\HrImportLog::where('import_type', 'candidates')
            ->orderByDesc('created_at')->limit(10)->get();
        $isSuperAdmin = Auth::user()?->isSuperAdminRecruitment();
        if ($isSuperAdmin && view()->exists('gik-superadmin.import')) {
            return view('gik-superadmin.import', [
                'recentLogs' => $recent,
                'mode' => 'candidates',
                'pageTitle' => 'Import Candidates Excel',
            ]);
        }
        return view('gik-hrd.candidates-import', [
            'recentLogs' => $recent,
            'pageTitle' => 'Import Candidates',
        ]);
    }

    public function downloadTemplate()
    {
        $service = new HrImportService();
        $path = $service->generateCandidateTemplate();
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

    public function import(Request $request, HrImportService $importService)
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
            $storedPath = $file->storeAs('imports/candidates', uniqid() . '_' . $originalName, 'local');
            $fullPath = Storage::disk('local')->path($storedPath);
        }

        $companyId = $user->company_id;
        if ($user->isSuperAdminRecruitment() && $request->input('company_id')) {
            $companyId = $request->input('company_id');
        }

        try {
            $excel = $importService->readExcel($fullPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage())->withInput();
        }

        $fieldMapping = [
            'First Name' => 'first_name',
            'Last Name' => 'last_name',
            'Email' => 'email',
            'Phone Number' => 'phone',
            'National ID/SSN' => 'national_id',
            'Job Title' => 'job_title',
            'Department' => 'department',
            'Years Experience' => 'years_experience',
            'Education' => 'education',
            'Skills (comma separated)' => 'skills',
            'Status (new/interviewing/offer_sent)' => 'status',
        ];

        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'required|string|max:150',
            'department' => 'nullable|string|max:100',
            'years_experience' => 'nullable|numeric|min:0|max:99',
            'status' => 'nullable|in:new,interviewing,offer_sent,hired,rejected,passive',
            'national_id' => 'nullable|string|max:50',
            'education' => 'nullable|string',
            'skills' => 'nullable|string',
        ];

        $validated = $importService->validateRows($excel['rows'], $rules, $fieldMapping);
        $logId = $importService->createImportLog(
            'candidates',
            $originalName,
            $storedPath,
            count($excel['rows']),
            'validating'
        );

        if ($request->has('confirm_import') && $request->boolean('confirm_import')) {
            $successCount = 0;
            $failedRows = [];
            DB::beginTransaction();
            try {
                $importService->updateImportLog($logId, ['status' => 'processing']);
                foreach ($validated['valid'] as $row) {
                    try {
                        $existing = null;
                        if (!empty($row['data']['email'])) {
                            $existing = HrCandidate::where('company_id', $companyId)
                                ->where('email', $row['data']['email'])
                                ->first();
                        } elseif (!empty($row['data']['national_id'])) {
                            $existing = HrCandidate::where('company_id', $companyId)
                                ->where('national_id', $row['data']['national_id'])
                                ->first();
                        }

                        if ($existing) {
                            $existing->update($row['data']);
                        } else {
                            HrCandidate::create(array_merge($row['data'], [
                                'company_id' => $companyId,
                                'status' => $row['data']['status'] ?? 'new',
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
                HrAuditHelper::log('import_candidates_complete', 'hr_import_log', $logId, null, [
                    'total' => count($excel['rows']),
                    'success' => $successCount,
                    'failed' => count($validated['invalid']) + count($failedRows),
                ]);
                return redirect()->route('gik-hrd.candidates')->with('success',
                    "Import selesai. Berhasil: {$successCount}, Gagal: " . (count($validated['invalid']) + count($failedRows)));
            } catch (\Exception $e) {
                DB::rollBack();
                $importService->updateImportLog($logId, [
                    'status' => 'rollback',
                    'error_details' => ['fatal' => $e->getMessage()],
                    'completed_at' => now(),
                ]);
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
            'preview_data' => [
                'valid' => array_slice($validated['valid'], 0, 10),
                'invalid' => $validated['invalid'],
                'valid_count' => count($validated['valid']),
                'invalid_count' => count($validated['invalid']),
                'total_rows' => count($excel['rows']),
                'stored_path' => $storedPath,
            ],
        ])->withInput();
    }

    private function authorizeAccess(HrCandidate $candidate): void
    {
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) return;
        if ($user->role === 'hrd_company' && $candidate->company_id === $user->company_id) return;
        abort(403, 'Akses ditolak.');
    }
}
