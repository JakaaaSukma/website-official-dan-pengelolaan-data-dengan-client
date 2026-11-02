<?php

namespace App\Http\Controllers;

use App\Models\BpjsKetenagakerjaan;
use App\Models\BpjsRateSetting;
use App\Models\BpjsRateChangeLog;
use App\Models\BpjsAccidentReport;
use App\Models\BpjsPocketMoney;
use App\Models\HrContract;
use App\Models\Client;
use App\Models\HrMessage;
use App\Helpers\HrAuditHelper;
use App\Services\BpjsF2aService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Traits\ValidatesFileUpload;

class HrBpjsController extends Controller
{
    use ValidatesFileUpload;
    // ==================== HR COMPANY SIDE METHODS ====================

    /**
     * List peserta magang aktif + status BPJS untuk perusahaan HR.
     */
    public function indexHrd(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $query = HrContract::with('bpjsKetenagakerjaan')
            ->where('status', 'active')
            ->orderBy('first_name');

        if ($request->filled('search')) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s)
                  ->orWhere('national_id', 'like', $s)
                  ->orWhereHas('bpjsKetenagakerjaan', function ($q2) use ($s) {
                      $q2->where('bpjs_number', 'like', $s);
                  });
            });
        }

        if ($request->filled('bpjs_status') && in_array($request->bpjs_status, ['aktif', 'tidak_aktif', 'belum'])) {
            if ($request->bpjs_status === 'belum') {
                $query->doesntHave('bpjsKetenagakerjaan');
            } else {
                $query->whereHas('bpjsKetenagakerjaan', function ($q) use ($request) {
                    $q->where('bpjs_status', $request->bpjs_status);
                });
            }
        }

        $contracts = $query->paginate(20);

        $pageTitle = 'BPJS Ketenagakerjaan — Daftar Peserta';
        $sMenu = 'bpjs';
        return view('gik-hrd.bpjs-index', compact('contracts', 'pageTitle', 'sMenu'));
    }

    /**
     * Store manual pocket money input.
     */
    public function storePocketMoney(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $validated = $request->validate([
            'hr_contract_id' => 'required|exists:hr_contracts,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0',
        ]);

        $contract = HrContract::findOrFail($validated['hr_contract_id']);
        $this->authorizeAccessContract($contract);

        BpjsPocketMoney::updateOrCreate(
            [
                'hr_contract_id' => $contract->id,
                'month' => $validated['month'],
                'year' => $validated['year']
            ],
            [
                'company_id' => $contract->company_id,
                'amount' => $validated['amount'],
                'updated_by' => $u->id
            ]
        );

        return redirect()->back()->with('success', 'Uang saku berhasil disimpan.');
    }

    /**
     * Import pocket money from Excel.
     */
    public function importPocketMoneyExcel(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $successCount = 0;
        $errorCount = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Skip header

            $identifier = $row[0] ?? null; // Name, NIK, or Reference Number
            $amount = $row[1] ?? 0;

            if (!$identifier) continue;
            
            $amount = (float) preg_replace('/[^0-9.]/', '', $amount);

            $contract = HrContract::where('company_id', $u->company_id)
                ->where(function($q) use ($identifier) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$identifier}%")
                      ->orWhere('national_id', $identifier)
                      ->orWhereHas('bpjsKetenagakerjaan', function($sub) use ($identifier) {
                          $sub->where('nomor_referensi', $identifier);
                      });
                })->first();

            if ($contract) {
                BpjsPocketMoney::updateOrCreate(
                    [
                        'hr_contract_id' => $contract->id,
                        'month' => $request->month,
                        'year' => $request->year
                    ],
                    [
                        'company_id' => $contract->company_id,
                        'amount' => $amount,
                        'updated_by' => $u->id
                    ]
                );
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        return redirect()->back()->with('success', "Import selesai. Berhasil: {$successCount}, Tidak Ditemukan: {$errorCount}.");
    }

    /**
     * View rate settings (read-only untuk HRD).
     */
    public function viewRateSettings()
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $rate = BpjsRateSetting::getCurrentRate($u->company_id);
        $pageTitle = 'Rate Iuran BPJS Perusahaan Anda';
        $sMenu = 'bpjs';
        return view('gik-hrd.bpjs-rate-view', compact('rate', 'pageTitle', 'sMenu'));
    }

    /**
     * List laporan kecelakaan kerja per perusahaan.
     */
    public function accidentReportsHrd(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $query = BpjsAccidentReport::with(['hrContract', 'reportedByUser'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('incident_location', 'like', $s)
                  ->orWhere('chronology', 'like', $s)
                  ->orWhereHas('hrContract', function ($q2) use ($s) {
                      $q2->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s);
                  });
            });
        }
        if ($request->filled('status') && in_array($request->status, ['dilaporkan', 'dalam_proses', 'selesai', 'klaim_diajukan'])) {
            $query->where('handling_status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('incident_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('incident_date', '<=', $request->to_date);
        }
        if ($request->filled('contract_id')) {
            $query->where('hr_contract_id', $request->contract_id);
        }

        $reports = $query->paginate(15);
        $statusCounts = [
            'dilaporkan' => BpjsAccidentReport::where('handling_status', 'dilaporkan')->count(),
            'dalam_proses' => BpjsAccidentReport::where('handling_status', 'dalam_proses')->count(),
            'selesai' => BpjsAccidentReport::where('handling_status', 'selesai')->count(),
            'klaim_diajukan' => BpjsAccidentReport::where('handling_status', 'klaim_diajukan')->count(),
        ];

        $pageTitle = 'Laporan Kecelakaan Kerja';
        $sMenu = 'bpjs-accident';
        return view('gik-hrd.bpjs-accident-list', compact('reports', 'statusCounts', 'pageTitle', 'sMenu'));
    }

    /**

            // Kirim notifikasi ke Super Admin
            $companyName = $u->company?->company_name ?? 'Perusahaan';
            HrMessage::create([
                'company_id' => $u->company_id,
                'from_user_id' => $u->id,
                'to_user_id' => null,
                'message_type' => 'accident_report',
                'subject' => "🚨 Laporan Kecelakaan Kerja dari {$companyName} — {$contract->full_name}",
                'body' => "HR Company {$companyName} melaporkan kecelakaan kerja untuk peserta \"{$contract->full_name}\" ({$contract->job_title}). "
                    . "Kategori: " . ucfirst($validated['accident_category']) . ". "
                    . "Tanggal kejadian: " . date('d M Y', strtotime($validated['incident_date'])) . ". "
                    . "Lihat detail pada menu BPJS Ketenagakerjaan > Laporan Kecelakaan.",
                'priority' => $validated['accident_category'] === 'berat' ? 'urgent' : 'high',
                'status' => 'unread',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($documentPaths as $dp) {
                Storage::disk('public')->delete($dp);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan laporan kecelakaan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-hrd.bpjs.accidents.show', $report)->with(
            'success',
            'Laporan kecelakaan kerja berhasil dikirim. Notifikasi otomatis telah dikirim ke Tim Rekrutmen.'
        );
    }

    /**
     * Dashboard statistik kecelakaan kerja HR Company.
     */
    public function accidentDashboardHrd(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $year = (int) ($request->year ?? now()->year);

        // Monthly counts for bar chart
        $monthlyCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyCounts[$m] = BpjsAccidentReport::where('company_id', $u->company_id)
                ->whereYear('incident_date', $year)
                ->whereMonth('incident_date', $m)
                ->count();
        }

        // Category breakdown per month
        $categoryBreakdown = [];
        foreach (['ringan', 'sedang', 'berat'] as $cat) {
            for ($m = 1; $m <= 12; $m++) {
                $categoryBreakdown[$cat][$m] = BpjsAccidentReport::where('company_id', $u->company_id)
                    ->whereYear('incident_date', $year)
                    ->whereMonth('incident_date', $m)
                    ->where('accident_category', $cat)
                    ->count();
            }
        }

        $pageTitle = 'Dashboard Kecelakaan Kerja (HR)';
        $sMenu = 'bpjs-accident';
        $companySummary = []; // Not used in HR view
        return view('gik-hrd.bpjs-accident-dashboard', compact(
            'monthlyCounts', 'categoryBreakdown', 'companySummary', 'year', 'pageTitle', 'sMenu'
        ));
    }

    /**
     * Detail laporan kecelakaan.
     */
    public function showAccidentReport(BpjsAccidentReport $report)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);
        $this->authorizeAccess($report);

        HrAuditHelper::log('view_accident_report', 'bpjs_accident_reports', $report->id);

        $report->load(['hrContract', 'reportedByUser', 'company']);
        $pageTitle = "Detail Laporan #{$report->report_number_display}";
        $sMenu = 'bpjs-accident';
        return view('gik-hrd.bpjs-accident-show', compact('report', 'pageTitle', 'sMenu'));
    }

    /**
     * Preview laporan F2A (pilih periode, lihat tabel).
     */
    public function f2aReportHrd(Request $request, BpjsF2aService $f2aService)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $data = $f2aService->generateF2aData($u->company_id, (int) $month, (int) $year);
        $companyName = $u->company?->company_name ?? 'Perusahaan';

        $pageTitle = 'Laporan F2A — Rincian Iuran Tenaga Kerja';
        $sMenu = 'bpjs-f2a';
        return view('gik-hrd.bpjs-f2a', compact('data', 'companyName', 'month', 'year', 'pageTitle', 'sMenu'));
    }

    /**
     * Export F2A ke Excel.
     */
    public function exportF2aExcel(Request $request, BpjsF2aService $f2aService)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $companyName = $u->company?->company_name ?? 'Perusahaan';

        $data = $f2aService->generateF2aData($u->company_id, $month, $year);
        $path = $f2aService->exportToExcel($data, $companyName);

        HrAuditHelper::log('export_f2a_excel', 'bpjs_ketenagakerjaan', null, null, ['period' => "{$month}/{$year}"], 'info');

        while (ob_get_level() > 0) { ob_end_clean(); }
        return response()->download($path, basename($path))->deleteFileAfterSend(true);
    }

    /**
     * Export F2A ke Word (.docx).
     */
    public function exportF2aWord(Request $request, BpjsF2aService $f2aService)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $companyName = $u->company?->company_name ?? 'Perusahaan';

        $data = $f2aService->generateF2aData($u->company_id, $month, $year);
        $path = $f2aService->exportToWord($data, $companyName);

        HrAuditHelper::log('export_f2a_word', 'bpjs_ketenagakerjaan', null, null, ['period' => "{$month}/{$year}"], 'info');

        while (ob_get_level() > 0) { ob_end_clean(); }
        return response()->download($path, basename($path))->deleteFileAfterSend(true);
    }

    // ==================== SUPER ADMIN SIDE METHODS ====================

    /**
     * List peserta + status BPJS semua perusahaan.
     */
    public function indexSuperAdmin(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $query = HrContract::withoutTenant()
            ->with(['bpjsKetenagakerjaan', 'company'])
            ->where('status', 'active')
            ->orderBy('first_name');

        if ($request->filled('search')) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s)
                  ->orWhere('national_id', 'like', $s)
                  ->orWhereHas('bpjsKetenagakerjaan', function ($q2) use ($s) {
                      $q2->where('bpjs_number', 'like', $s);
                  });
            });
        }
        if ($request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('bpjs_status') && in_array($request->bpjs_status, ['aktif', 'tidak_aktif', 'belum'])) {
            if ($request->bpjs_status === 'belum') {
                $query->doesntHave('bpjsKetenagakerjaan');
            } else {
                $query->whereHas('bpjsKetenagakerjaan', function ($q) use ($request) {
                    $q->where('bpjs_status', $request->bpjs_status);
                });
            }
        }

        $contracts = $query->paginate(20);
        $companies = Client::orderBy('company_name')->get();

        $pageTitle = 'BPJS Ketenagakerjaan — Semua Perusahaan';
        $sMenu = 'bpjs';
        return view('gik-superadmin.bpjs-index', compact('contracts', 'companies', 'pageTitle', 'sMenu'));
    }

    /**
     * Update status BPJS & nomor BPJS per peserta (Super Admin).
     */
    public function updateBpjsStatusSuperAdmin(Request $request, $id)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);

        $contract = HrContract::withoutTenant()->findOrFail($id);

        $validated = $request->validate([
            'bpjs_status' => 'required|in:aktif,tidak_aktif',
            'bpjs_number' => 'nullable|string|digits_between:10,16',
            'bpjs_join_date' => 'nullable|date',
            'nomor_referensi' => 'nullable|string|max:50',
            'nomor_pegawai' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $bpjs = BpjsKetenagakerjaan::withoutTenant()->firstOrNew(['hr_contract_id' => $contract->id]);
            $old = $bpjs->exists ? $bpjs->toArray() : null;

            $bpjs->hr_contract_id = $contract->id;
            $bpjs->company_id = $contract->company_id;
            $bpjs->bpjs_status = $validated['bpjs_status'];
            $bpjs->bpjs_number = $validated['bpjs_number'] ?? $bpjs->bpjs_number;
            $bpjs->bpjs_join_date = $validated['bpjs_join_date'] ?? $bpjs->bpjs_join_date;
            $bpjs->nomor_referensi = $validated['nomor_referensi'] ?? $bpjs->nomor_referensi;
            $bpjs->nomor_pegawai = $validated['nomor_pegawai'] ?? $bpjs->nomor_pegawai;
            $bpjs->updated_by = $u->id;
            $bpjs->save();

            HrAuditHelper::log('update_bpjs_status', 'bpjs_ketenagakerjaan', $bpjs->id, $old, $bpjs->toArray(), 'warning');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update status BPJS: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "Status BPJS {$contract->full_name} berhasil diperbarui.");
    }

    /**
     * Halaman pengaturan rate BPJS per perusahaan (Super Admin only).
     */
    public function rateSettingsIndex()
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $companies = Client::orderBy('company_name')->get();
        $rates = [];
        foreach ($companies as $company) {
            $rates[$company->id] = BpjsRateSetting::getCurrentRate($company->id);
        }

        $pageTitle = 'Pengaturan Rate Iuran BPJS per Perusahaan';
        $sMenu = 'bpjs-rate';
        return view('gik-superadmin.bpjs-rate-settings', compact('companies', 'rates', 'pageTitle', 'sMenu'));
    }

    /**
     * Update rate JKK/JKM per perusahaan.
     */
    public function updateRateSettings(Request $request, $companyId)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $company = Client::findOrFail($companyId);

        $validated = $request->validate([
            'jkk_rate' => 'required|numeric|min:0|max:10',
            'jkm_rate' => 'required|numeric|min:0|max:10',
            'jht_employer_rate' => 'nullable|numeric|min:0|max:10',
            'jht_employee_rate' => 'nullable|numeric|min:0|max:10',
            'jp_employer_rate' => 'nullable|numeric|min:0|max:10',
            'jp_employee_rate' => 'nullable|numeric|min:0|max:10',
            'jkp_employer_rate' => 'nullable|numeric|min:0|max:10',
            'jkp_government_rate' => 'nullable|numeric|min:0|max:10',
            'effective_from' => 'required|date',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Close previous rate (set effective_until)
            $prevRate = BpjsRateSetting::getCurrentRate($companyId);
            if ($prevRate) {
                $effectiveFrom = Carbon::parse($validated['effective_from']);
                $prevRate->effective_until = $effectiveFrom->copy()->subDay()->toDateString();
                $prevRate->save();

                // Log changes for each changed field
                foreach (BpjsRateSetting::rateFields() as $field => $label) {
                    $oldVal = (float) ($prevRate->$field ?? 0);
                    $newVal = (float) ($validated[$field] ?? 0);
                    if (abs($oldVal - $newVal) > 0.0001) {
                        BpjsRateChangeLog::create([
                            'company_id' => $companyId,
                            'field_name' => $field,
                            'old_value' => $oldVal,
                            'new_value' => $newVal,
                            'effective_from' => $validated['effective_from'],
                            'changed_by' => $user->id,
                            'reason' => $validated['reason'] ?? null,
                        ]);
                    }
                }
            }

            // Create new rate setting
            $newRate = BpjsRateSetting::create([
                'company_id' => $companyId,
                'jkk_rate' => $validated['jkk_rate'],
                'jkm_rate' => $validated['jkm_rate'],
                'jht_employer_rate' => $validated['jht_employer_rate'] ?? 0,
                'jht_employee_rate' => $validated['jht_employee_rate'] ?? 0,
                'jp_employer_rate' => $validated['jp_employer_rate'] ?? 0,
                'jp_employee_rate' => $validated['jp_employee_rate'] ?? 0,
                'jkp_employer_rate' => $validated['jkp_employer_rate'] ?? 0,
                'jkp_government_rate' => $validated['jkp_government_rate'] ?? 0,
                'effective_from' => $validated['effective_from'],
                'effective_until' => null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            HrAuditHelper::log(
                'update_bpjs_rate_settings',
                'bpjs_rate_settings',
                $newRate->id,
                $prevRate ? $prevRate->toArray() : null,
                $newRate->toArray(),
                'critical'
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update rate: ' . $e->getMessage())->withInput();
        }

        return redirect()->back()->with('success', "Rate BPJS untuk {$company->company_name} berhasil diperbarui.");
    }

    /**
     * Histori perubahan rate per perusahaan.
     */
    public function rateChangeHistory($companyId)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $company = Client::findOrFail($companyId);
        $logs = BpjsRateChangeLog::where('company_id', $companyId)
            ->with('changedByUser')
            ->orderByDesc('created_at')
            ->paginate(25);

        $pageTitle = "Histori Rate BPJS — {$company->company_name}";
        $sMenu = 'bpjs-rate';
        return view('gik-superadmin.bpjs-rate-history', compact('company', 'logs', 'pageTitle', 'sMenu'));
    }

    /**
     * Preview laporan F2A (Super Admin).
     */
    public function f2aReportSuperAdmin(Request $request, BpjsF2aService $f2aService)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $companyId = $request->input('company_id');

        $companies = Client::orderBy('company_name')->get();
        $data = null;
        $companyName = null;

        if ($companyId) {
            $data = $f2aService->generateF2aData($companyId, (int) $month, (int) $year);
            $companyName = Client::find($companyId)->company_name ?? 'Perusahaan';
        }

        $pageTitle = 'Laporan F2A (Super Admin)';
        $sMenu = 'bpjs-f2a';
        return view('gik-superadmin.bpjs-f2a', compact('data', 'companies', 'companyId', 'companyName', 'month', 'year', 'pageTitle', 'sMenu'));
    }

    public function exportF2aExcelSuperAdmin(Request $request, BpjsF2aService $f2aService)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $companyId = $request->input('company_id');
        
        if (!$companyId) return redirect()->back()->with('error', 'Pilih perusahaan terlebih dahulu.');

        $companyName = Client::find($companyId)->company_name ?? 'Perusahaan';
        $data = $f2aService->generateF2aData($companyId, $month, $year);
        $path = $f2aService->exportToExcel($data, $companyName);

        while (ob_get_level() > 0) { ob_end_clean(); }
        return response()->download($path, basename($path))->deleteFileAfterSend(true);
    }

    public function exportF2aWordSuperAdmin(Request $request, BpjsF2aService $f2aService)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $companyId = $request->input('company_id');
        
        if (!$companyId) return redirect()->back()->with('error', 'Pilih perusahaan terlebih dahulu.');

        $companyName = Client::find($companyId)->company_name ?? 'Perusahaan';
        $data = $f2aService->generateF2aData($companyId, $month, $year);
        $path = $f2aService->exportToWord($data, $companyName);

        while (ob_get_level() > 0) { ob_end_clean(); }
        return response()->download($path, basename($path))->deleteFileAfterSend(true);
    }

    /**
     * Dashboard grafik kecelakaan kerja (Super Admin).
     */
    public function accidentDashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $year = (int) ($request->year ?? now()->year);

        // Monthly counts for bar chart
        $monthlyCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyCounts[$m] = BpjsAccidentReport::withoutTenant()
                ->whereYear('incident_date', $year)
                ->whereMonth('incident_date', $m)
                ->count();
        }

        // Category breakdown per month
        $categoryBreakdown = [];
        foreach (['ringan', 'sedang', 'berat'] as $cat) {
            for ($m = 1; $m <= 12; $m++) {
                $categoryBreakdown[$cat][$m] = BpjsAccidentReport::withoutTenant()
                    ->whereYear('incident_date', $year)
                    ->whereMonth('incident_date', $m)
                    ->where('accident_category', $cat)
                    ->count();
            }
        }

        // Per-company summary
        $companySummary = DB::table('bpjs_accident_reports')
            ->join('clients', 'bpjs_accident_reports.company_id', '=', 'clients.id')
            ->whereNull('bpjs_accident_reports.deleted_at')
            ->whereYear('incident_date', $year)
            ->select('clients.company_name', 'bpjs_accident_reports.company_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN accident_category = 'ringan' THEN 1 ELSE 0 END) as ringan"),
                DB::raw("SUM(CASE WHEN accident_category = 'sedang' THEN 1 ELSE 0 END) as sedang"),
                DB::raw("SUM(CASE WHEN accident_category = 'berat' THEN 1 ELSE 0 END) as berat"))
            ->groupBy('clients.company_name', 'bpjs_accident_reports.company_id')
            ->orderByDesc('total')
            ->get();

        $pageTitle = 'Dashboard Kecelakaan Kerja';
        $sMenu = 'bpjs-accident';
        return view('gik-superadmin.bpjs-accident-dashboard', compact(
            'monthlyCounts', 'categoryBreakdown', 'companySummary', 'year', 'pageTitle', 'sMenu'
        ));
    }

    /**
     * List semua laporan kecelakaan lintas perusahaan (Super Admin).
     */
    public function accidentReportsSuperAdmin(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $query = BpjsAccidentReport::withoutTenant()
            ->with(['hrContract', 'company', 'reportedByUser'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('incident_location', 'like', $s)
                  ->orWhere('chronology', 'like', $s)
                  ->orWhereHas('hrContract', function ($q2) use ($s) {
                      $q2->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s);
                  })
                  ->orWhereHas('company', function ($q2) use ($s) {
                      $q2->where('company_name', 'like', $s);
                  });
            });
        }
        if ($request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('status') && in_array($request->status, ['dilaporkan', 'dalam_proses', 'selesai', 'klaim_diajukan'])) {
            $query->where('handling_status', $request->status);
        }
        if ($request->filled('category') && in_array($request->category, ['ringan', 'sedang', 'berat'])) {
            $query->where('accident_category', $request->category);
        }
        if ($request->filled('from_date')) $query->whereDate('incident_date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('incident_date', '<=', $request->to_date);

        $reports = $query->paginate(20);
        $companies = Client::orderBy('company_name')->get();

        $pageTitle = 'Laporan Kecelakaan Kerja — Semua Perusahaan';
        $sMenu = 'bpjs-accident';
        return view('gik-superadmin.bpjs-accidents', compact('reports', 'companies', 'pageTitle', 'sMenu'));
    }

    /**
     * Form input laporan kecelakaan kerja baru (Super Admin).
     */
    public function createAccidentReportSuperAdmin(Request $request)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);

        $companies = Client::orderBy('company_name')->get();
        $selectedCompanyId = $request->company_id;
        $contracts = [];

        if ($selectedCompanyId) {
            $contracts = HrContract::withoutTenant()
                ->where('company_id', $selectedCompanyId)
                ->where('status', 'active')
                ->orderBy('first_name')->get();
        }

        $pageTitle = "Tambah Laporan Kecelakaan Kerja";
        $sMenu = 'bpjs-accident';
        return view('gik-superadmin.bpjs-accident-create', compact('companies', 'selectedCompanyId', 'contracts', 'pageTitle', 'sMenu'));
    }

    /**
     * Simpan laporan kecelakaan kerja (Super Admin).
     */
    public function storeAccidentReportSuperAdmin(Request $request)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);

        $validated = $request->validate([
            'hr_contract_id' => 'required|exists:hr_contracts,id',
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_time' => 'nullable|date_format:H:i',
            'incident_location' => 'required|string|max:255',
            'chronology' => 'required|string|min:10|max:5000',
            'accident_category' => 'required|in:ringan,sedang,berat',
            'action_taken' => 'nullable|string|max:3000',
            'documents.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $contract = HrContract::withoutTenant()->findOrFail($validated['hr_contract_id']);

        $documentPaths = [];
        DB::beginTransaction();
        try {
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $documentPaths[] = $file->store('bpjs-accident-docs', 'public');
                }
            }

            $report = BpjsAccidentReport::create([
                'report_number' => BpjsAccidentReport::generateReportNumber(),
                'company_id' => $contract->company_id,
                'hr_contract_id' => $contract->id,
                'incident_date' => $validated['incident_date'],
                'incident_time' => $validated['incident_time'] ?? null,
                'incident_location' => $validated['incident_location'],
                'chronology' => $validated['chronology'],
                'accident_category' => $validated['accident_category'],
                'action_taken' => $validated['action_taken'] ?? null,
                'handling_status' => 'dilaporkan',
                'documents' => !empty($documentPaths) ? $documentPaths : null,
                'reported_by' => $u->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($documentPaths as $dp) {
                Storage::disk('public')->delete($dp);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan laporan kecelakaan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-superadmin.bpjs.accidents')->with('success', 'Laporan kecelakaan kerja berhasil dibuat.');
    }

    /**
     * Update status penanganan kecelakaan (Super Admin).
     */
    public function updateAccidentStatus(BpjsAccidentReport $report, Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $validated = $request->validate([
            'handling_status' => 'required|in:dilaporkan,dalam_proses,selesai,klaim_diajukan',
        ]);

        $oldStatus = $report->handling_status;
        $report->handling_status = $validated['handling_status'];
        $report->save();

        HrAuditHelper::log(
            'update_accident_status',
            'bpjs_accident_reports',
            $report->id,
            ['handling_status' => $oldStatus],
            ['handling_status' => $validated['handling_status']],
            'warning'
        );

        return redirect()->back()->with('success', 'Status laporan kecelakaan berhasil diperbarui.');
    }

    /**
     * Export rekap kecelakaan ke Excel.
     */
    public function exportAccidentRecap(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) abort(403);

        $year = (int) ($request->year ?? now()->year);

        $reports = BpjsAccidentReport::withoutTenant()
            ->with(['hrContract', 'company', 'reportedByUser'])
            ->whereYear('incident_date', $year)
            ->orderBy('incident_date')
            ->get();

        $filename = "Rekap_Kecelakaan_Kerja_{$year}.xls";
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function() use ($reports, $year) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            echo "<html><head><meta charset='UTF-8'></head><body>";
            echo "<table border='1' style='border-collapse:collapse;'>";
            echo "<tr><td colspan='9' style='text-align:center;font-weight:bold;font-size:14px;padding:10px;'>Rekap Kecelakaan Kerja Tahun {$year}</td></tr>";
            echo "<tr style='background-color:#E53E3E;color:white;font-weight:bold;text-align:center;'>";
            echo "<th style='padding:8px;'>No</th><th style='padding:8px;'>Tanggal</th><th style='padding:8px;'>Perusahaan</th>";
            echo "<th style='padding:8px;'>Nama Peserta</th><th style='padding:8px;'>Lokasi</th><th style='padding:8px;'>Kategori</th>";
            echo "<th style='padding:8px;'>Status</th><th style='padding:8px;'>Pelapor</th><th style='padding:8px;'>Nomor Laporan</th></tr>";

            $no = 1;
            foreach ($reports as $r) {
                $bg = $no % 2 == 0 ? '#f9f9f9' : '#ffffff';
                echo "<tr style='background-color:{$bg};'>";
                echo "<td style='padding:6px;text-align:center;'>{$no}</td>";
                echo "<td style='padding:6px;'>" . optional($r->incident_date)->format('d/m/Y') . "</td>";
                echo "<td style='padding:6px;'>" . htmlspecialchars($r->company?->company_name ?? '-') . "</td>";
                echo "<td style='padding:6px;'>" . htmlspecialchars($r->hrContract?->full_name ?? '-') . "</td>";
                echo "<td style='padding:6px;'>" . htmlspecialchars($r->incident_location) . "</td>";
                echo "<td style='padding:6px;text-align:center;'>" . ucfirst($r->accident_category) . "</td>";
                echo "<td style='padding:6px;text-align:center;'>{$r->status_label}</td>";
                echo "<td style='padding:6px;'>" . htmlspecialchars($r->reportedByUser?->name ?? '-') . "</td>";
                echo "<td style='padding:6px;'>{$r->report_number}</td>";
                echo "</tr>";
                $no++;
            }
            echo "</table></body></html>";
        }, 200, $headers);
    }

    // ==================== AUTHORIZATION HELPERS ====================

    private function authorizeAccess(BpjsAccidentReport $report): void
    {
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) return;
        if (($user->role === 'hrd_company' || $user->role === 'company') && $report->company_id === $user->company_id) return;
        abort(403, 'Akses ditolak.');
    }

    private function authorizeAccessContract(HrContract $contract): void
    {
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) return;
        if (($user->role === 'hrd_company' || $user->role === 'company') && $contract->company_id === $user->company_id) return;
        abort(403, 'Akses ditolak.');
    }
}
