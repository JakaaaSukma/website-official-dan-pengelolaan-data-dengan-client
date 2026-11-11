<?php

namespace App\Http\Controllers;

use App\Models\HrContract;
use App\Helpers\HrAuditHelper;
use App\Services\HrImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\ValidatesFileUpload;

class HrEmployeeController extends Controller
{
    use ValidatesFileUpload;
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();

        $query = HrContract::with('company')->orderByDesc('contract_end');
        if ($request->filled('search')) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', $s)
                    ->orWhere('last_name', 'like', $s)
                    ->orWhere(DB::raw("CONCAT_WS(' ', first_name, last_name)"), 'like', $s)
                    ->orWhere('employee_id', 'like', $s)
                    ->orWhere('job_title', 'like', $s);
            });
        }
        if ($request->filled('department') && $request->department !== 'all') {
            $query->where('department', $request->department);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($isSuperAdmin && $request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }

        $employees = $query->paginate(20)->withQueryString();
        $departments = HrContract::withoutTenant()->distinct()->pluck('department')->sort()->values();
        $companies = $isSuperAdmin ? \App\Models\Client::orderBy('company_name')->get() : collect();
        $selectedDept = $request->department;
        $selectedCompany = $request->company_id;
        $selectedStatus = $request->status ?? 'all';
        $searchKeyword = $request->search;

        if ($isSuperAdmin) {
            return view('gik-superadmin.employees', compact(
                'employees', 'departments', 'companies', 'isSuperAdmin',
                'selectedDept', 'selectedCompany', 'selectedStatus', 'searchKeyword'
            ));
        }

        return redirect()->route('gik-hrd.dashboard')->with('info', 'Please use Dashboard to view your interns/employees list.');
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        // 🔒 STRICT ACCESS: HANYA SUPER ADMIN REKRUTMEN YANG BOLEH MENAMBAH PESERTA BARU (MANUAL).
        if (!$user->isSuperAdminRecruitment()) {
            abort(403, 'Akses ditolak! Penambahan data Peserta Aktif hanya dapat dilakukan oleh Tim Rekrutmen Gyokai Connect (Super Admin). HR Company mohon menghubungi Super Admin.');
        }
        $isSuperAdmin = true;

        $companyId = $user->company_id;
        if ($request->query('company_id')) {
            $companyId = $request->query('company_id');
        }

        $companies = \App\Models\Client::orderBy('company_name')->get();

        return view('gik-shared.employee-create', [
            'companyId' => $companyId,
            'pageTitle' => 'Tambah Intern / Employee Baru',
            'viewingCompanyId' => $request->query('company_id'),
            'companies' => $companies,
            'isSuperAdmin' => true,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isSuper = $user->isSuperAdminRecruitment();
        // 🔒 STRICT ACCESS: HANYA SA BISA INSERT BARU. HRD route disable + controller check double lock.
        if (!$isSuper) {
            abort(403, 'Akses ditolak! Penyimpanan data Peserta Aktif hanya dapat dilakukan Super Admin Rekrutmen.');
        }
        $validated = $request->validate([
            'company_id' => $isSuper ? 'required|exists:clients,id' : 'prohibited',
            'employee_id' => 'nullable|string|max:50',
            'full_name' => 'required|string|max:200',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'department' => 'required|string|max:100',
            'job_title' => 'required|string|max:150',
            'work_type' => 'required|in:on_site,remote,hybrid',
            'contract_type' => 'required|in:internship,full_time,part_time,contract,probation',
            'contract_start' => 'required|date',
            'contract_end' => 'required|date|after:contract_start',
            'salary' => 'nullable|numeric',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('employee-avatars', 'public');
        }

        $companyId = $isSuper ? $validated['company_id'] : $user->company_id;

        DB::beginTransaction();
        try {
            // Pecah nama lengkap menjadi first_name + last_name
            $namaParts = explode(' ', trim($validated['full_name']), 2);

            $contract = HrContract::create([
                'company_id' => $companyId,
                'employee_id' => $validated['employee_id'] ?? null,
                'first_name' => $namaParts[0],
                'last_name' => $namaParts[1] ?? '',
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'national_id' => $validated['national_id'],
                'department' => $validated['department'],
                'job_title' => $validated['job_title'],
                'work_type' => $validated['work_type'],
                'contract_type' => $validated['contract_type'],
                'contract_start' => $validated['contract_start'],
                'contract_end' => $validated['contract_end'],
                'salary' => $validated['salary'] ?? null,
                'address' => $validated['address'] ?? null,
                'avatar_path' => $avatarPath,
                'status' => 'active',
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
            HrAuditHelper::log('create_employee_contract', 'hr_contract', $contract->id, null, $contract->toArray(), 'warning');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($avatarPath) Storage::disk('public')->delete($avatarPath);
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }

        $route = $isSuper ? 'gik-superadmin.dashboard' : 'gik-hrd.dashboard';
        $params = $isSuper ? [] : [];
        if ($isSuper && $companyId) {
            $params = ['company_id' => $companyId];
        }
        return redirect()->route($route, $params)->with('success', 'Karyawan/Peserta baru berhasil ditambahkan.');
    }

    public function edit(HrContract $hrContract)
    {
        $this->authorizeAccess($hrContract);
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) {
            return view('gik-superadmin.employees-edit', [
                'contract' => $hrContract,
                'pageTitle' => 'Edit ' . $hrContract->full_name,
            ]);
        }
        return view('gik-hrd.employee-edit', [
            'contract' => $hrContract,
            'pageTitle' => 'Edit ' . $hrContract->full_name,
        ]);
    }

    public function update(HrContract $hrContract, Request $request)
    {
        $this->authorizeAccess($hrContract);
        $user = Auth::user();
        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:50',
            'full_name' => 'required|string|max:200',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'department' => 'required|string|max:100',
            'job_title' => 'required|string|max:150',
            'work_type' => 'required|in:on_site,remote,hybrid',
            'contract_type' => 'required|in:internship,full_time,part_time,contract,probation,permanent',
            'contract_start' => 'required|date',
            'contract_end' => 'nullable|date',
            'salary' => 'nullable|numeric',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,expired,extended,offboarded,terminated,suspended',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $avatarPath = $hrContract->avatar_path;
        if ($request->hasFile('avatar')) {
            if ($avatarPath) Storage::disk('public')->delete($avatarPath);
            $avatarPath = $request->file('avatar')->store('employee-avatars', 'public');
        }

        DB::beginTransaction();
        try {
            $old = $hrContract->toArray();

            // Pecah nama lengkap menjadi first_name + last_name
            $namaParts = explode(' ', trim($validated['full_name']), 2);
            $validated['first_name'] = $namaParts[0];
            $validated['last_name'] = $namaParts[1] ?? '';
            unset($validated['full_name']);

            $hrContract->fill($validated);
            $hrContract->avatar_path = $avatarPath;
            $hrContract->updated_by = $user->id;
            $hrContract->save();
            HrAuditHelper::log('update_employee_contract', 'hr_contract', $hrContract->id, $old, $hrContract->toArray(), 'warning');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }

        return redirect()->back()->with('success', 'Data karyawan berhasil diupdate.');
    }

    public function show(HrContract $hrContract)
    {
        $this->authorizeAccess($hrContract);
        HrAuditHelper::log('view_employee_detail', 'hr_contract', $hrContract->id);

        $user = Auth::user();
        $viewName = $user->isSuperAdminRecruitment() ? 'gik-superadmin.employees-show' : 'gik-hrd.employee-show';

        return view($viewName, [
            'contract' => $hrContract,
            'pageTitle' => $hrContract->full_name,
        ]);
    }

    public function destroy(HrContract $hrContract)
    {
        // 🔒 STRICT: HANYA SA yang bisa DELETE peserta aktif.
        if (!Auth::user()->isSuperAdminRecruitment()) {
            abort(403, 'Akses ditolak! Hapus data Peserta Aktif hanya diperbolehkan Tim Rekrutmen Gyokai Connect (Super Admin).');
        }
        $this->authorizeAccess($hrContract);
        $old = $hrContract->toArray();
        $hrContract->delete();
        HrAuditHelper::log('delete_employee_contract', 'hr_contract', $hrContract->id, $old, null, 'critical');
        return redirect()->route('gik-superadmin.employees')->with('success', 'Kontrak dihapus oleh Super Admin Rekrutmen.');
    }

    public function reviewExpiring(HrContract $contract)
    {
        $this->authorizeAccess($contract);
        return redirect()->route('gik-hrd.quick.action', [
            'type' => 'extend_contract',
            'ids' => $contract->id,
        ]);
    }

    public function showImportForm(Request $request)
    {
        $recent = \App\Models\HrImportLog::where('import_type', 'employees')
            ->orderByDesc('created_at')->limit(10)->get();
        $isSuperAdmin = Auth::user()?->isSuperAdminRecruitment();
        if ($isSuperAdmin && view()->exists('gik-superadmin.import')) {
            return view('gik-superadmin.import', [
                'recentLogs' => $recent,
                'mode' => 'employees',
                'pageTitle' => 'Import Employees Excel',
            ]);
        }
        return view('gik-hrd.employees-import', [
            'recentLogs' => $recent,
            'pageTitle' => 'Import Employees',
            'viewingCompanyId' => $request->query('company_id'),
        ]);
    }

    public function downloadEmployeeTemplate()
    {
        $service = new HrImportService();
        $path = $service->generateEmployeeTemplate();
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
        // 🔒 STRICT: HANYA SA yang bisa EXECUTE import method (process data Excel).
        if (!$user->isSuperAdminRecruitment()) {
            abort(403, 'Akses ditolak! Proses import data Peserta Aktif hanya dapat dijalankan Super Admin Rekrutmen.');
        }
        $isSuper = true;
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
            $storedPath = $file->storeAs('imports/employees', uniqid() . '_' . $originalName, 'local');
            $fullPath = Storage::disk('local')->path($storedPath);
        }

        try {
            $excel = $importService->readExcel($fullPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal baca Excel: ' . $e->getMessage())->withInput();
        }

        $fieldMapping = [
            'Company ID *' => 'company_id',
            'Company ID' => 'company_id',
            'Company Name (Reference only, auto lookup by ID)' => '_company_name_ignore',
            'Employee ID' => 'employee_id',
            'Nama Lengkap' => 'nama_lengkap',
            // Backward compatibility: jika user upload template lama dengan First/Last Name
            'First Name' => 'first_name',
            'Last Name' => 'last_name',
            'Email' => 'email',
            'Phone Number' => 'phone',
            'National ID/SSN' => 'national_id',
            'Department' => 'department',
            'Job Title' => 'job_title',
            'Work Type (on_site/remote/hybrid)' => 'work_type',
            'Contract Type (internship/full_time/part_time/contract)' => 'contract_type',
            'Contract Type (internship/full_time/part_time/contract/probation)' => 'contract_type',
            'Contract Start (YYYY-MM-DD)' => 'contract_start',
            'Contract End (YYYY-MM-DD)' => 'contract_end',
            'Salary (IDR, optional)' => 'salary',
            'Salary' => 'salary',
            'Address' => 'address',
            'Notes' => 'notes',
        ];

        // Detect which name format is used in the Excel header
        $hasNamaLengkap = in_array('Nama Lengkap', $excel['header'] ?? []);

        $rules = [
            'department' => 'required|string|max:100',
            'job_title' => 'required|string|max:150',
            'work_type' => 'required|in:on_site,remote,hybrid',
            'contract_type' => 'required|in:internship,full_time,part_time,contract,probation',
            'contract_start' => 'required|date',
            'contract_end' => 'required|date|after:contract_start',
            'email' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:20',
            'salary' => 'nullable|numeric|min:0',
            'employee_id' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        // Validasi nama: tergantung format Excel
        if ($hasNamaLengkap) {
            $rules['nama_lengkap'] = 'required|string|max:200';
        } else {
            // Template lama: First Name + Last Name
            $rules['first_name'] = 'required|string|max:100';
            $rules['last_name'] = 'required|string|max:100';
        }
        // Rule khusus SA: Company ID WAJIB & exist di table clients
        if ($isSuper) {
            $rules['company_id'] = 'required|integer|exists:clients,id';
        }
        $validated = $importService->validateRows($excel['rows'], $rules, $fieldMapping);

        // Default company: auth user company. Jika SA: pakai dari tiap row kolom company ID (lebih fleksibel per row beda company).
        $globalCompanyId = $user->company_id;
        if ($isSuper && $request->input('company_id')) {
            $globalCompanyId = $request->input('company_id');
        }

        $logId = $importService->createImportLog('employees', $originalName, $storedPath, count($excel['rows']), 'validating');

        if ($request->boolean('confirm_import')) {
            $successCount = 0;
            $failedRows = [];
            DB::beginTransaction();
            try {
                $importService->updateImportLog($logId, ['status' => 'processing']);
                foreach ($validated['valid'] as $row) {
                    try {
                        // SA bisa isi company_id PER ROW di Excel. Jika kosong (format template lawas), fallback ke global company.
                        $rowCompany = $globalCompanyId;
                        if ($isSuper && !empty($row['data']['company_id'])) {
                            $rowCompany = (int) $row['data']['company_id'];
                        }
                        // Bersihkan field ignore internal
                        $cleanData = $row['data'];
                        unset($cleanData['_company_name_ignore'], $cleanData['company_id']);

                        // Jika menggunakan format Nama Lengkap, pecah menjadi first_name + last_name
                        if (!empty($cleanData['nama_lengkap'])) {
                            $namaParts = explode(' ', trim($cleanData['nama_lengkap']), 2);
                            $cleanData['first_name'] = $namaParts[0];
                            $cleanData['last_name'] = $namaParts[1] ?? '';
                            unset($cleanData['nama_lengkap']);
                        }

                        $existing = null;
                        if (!empty($cleanData['employee_id'])) {
                            $existing = HrContract::where('company_id', $rowCompany)
                                ->where('employee_id', $cleanData['employee_id'])
                                ->first();
                        } elseif (!empty($cleanData['email'])) {
                            $existing = HrContract::where('company_id', $rowCompany)
                                ->where('email', $cleanData['email'])
                                ->first();
                        } elseif (!empty($cleanData['national_id'])) {
                            $existing = HrContract::where('company_id', $rowCompany)
                                ->where('national_id', $cleanData['national_id'])
                                ->first();
                        }

                        if ($existing) {
                            $existing->update(array_merge($cleanData, [
                                'updated_by' => $user->id,
                            ]));
                        } else {
                            HrContract::create(array_merge($cleanData, [
                                'company_id' => $rowCompany,
                                'work_type' => $row['data']['work_type'] ?? 'on_site',
                                'status' => 'active',
                                'created_by' => $user->id,
                                'updated_by' => $user->id,
                            ]));
                        }
                        $successCount++;
                    } catch (\Exception $e) {
                        $failedRows[] = ['row' => $row['row_number'], 'error' => $e->getMessage()];
                    }
                }
                $errorDetails = array_merge($validated['invalid'], $failedRows);
                // Slice to max 50 errors to prevent Data Too Long error
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
                HrAuditHelper::log('import_employees_complete', 'hr_import_log', $logId, null, ['success' => $successCount]);
                $route = $isSuper ? 'gik-superadmin.dashboard' : 'gik-hrd.dashboard';
                return redirect()->route($route)->with('success', "Import selesai. Berhasil: {$successCount}");
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
            'employee_preview' => [
                'valid' => array_slice($validated['valid'], 0, 10),
                'invalid' => $validated['invalid'],
                'valid_count' => count($validated['valid']),
                'invalid_count' => count($validated['invalid']),
                'total_rows' => count($excel['rows']),
                'stored_path' => $storedPath,
            ],
        ])->withInput();
    }

    private function authorizeAccess(HrContract $contract): void
    {
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) return;
        if ($user->role === 'hrd_company' && $contract->company_id === $user->company_id) return;
        abort(403, 'Akses ditolak.');
    }
}
