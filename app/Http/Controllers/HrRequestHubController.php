<?php

namespace App\Http\Controllers;

use App\Models\HrRequest;
use App\Models\HrContract;
use App\Models\HrRequestDocument;
use App\Helpers\HrAuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Traits\ValidatesFileUpload;

class HrRequestHubController extends Controller
{
    use ValidatesFileUpload;
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        $viewingCompanyId = $request->query('company_id');

        if ($isSuperAdmin && $viewingCompanyId) {
            $requests = HrRequest::withoutTenant()->where('company_id', $viewingCompanyId);
        } else {
            $requests = HrRequest::query();
        }

        $stats = [
            'total' => (clone $requests)->count(),
            'add' => (clone $requests)->where('request_type', 'add_contract')->count(),
            'extend' => (clone $requests)->where('request_type', 'extend_contract')->count(),
            'off' => (clone $requests)->where('request_type', 'off_boarding')->count(),
            'pending' => (clone $requests)->whereIn('status', ['draft', 'submitted', 'in_review'])->count(),
        ];

        return view('gik-hrd.request-hub', [
            'stats' => $stats,
            'pageTitle' => 'Request Hub',
        ]);
    }

    public function create(string $type, Request $request)
    {
        if (!in_array($type, ['add_contract', 'extend_contract', 'off_boarding'])) {
            abort(404);
        }

        $contractId = $request->query('contract_id');
        $existingContract = null;
        if ($contractId) {
            $existingContract = HrContract::find($contractId);
            if ($existingContract && !Auth::user()->isSuperAdminRecruitment()) {
                if ($existingContract->company_id !== Auth::user()->company_id) {
                    abort(403);
                }
            }
        }

        $req = HrRequest::create([
            'company_id' => Auth::user()->company_id,
            'contract_id' => $existingContract?->id,
            'request_number' => HrAuditHelper::generateRequestNumber(match($type) {
                'add_contract' => 'ADD',
                'extend_contract' => 'EXT',
                'off_boarding' => 'OFF',
            }),
            'request_type' => $type,
            'status' => 'draft',
            'current_step' => 1,
            'total_steps' => 3,
            'first_name' => $existingContract?->first_name,
            'last_name' => $existingContract?->last_name,
            'email' => $existingContract?->email,
            'phone' => $existingContract?->phone,
            'national_id' => $existingContract?->national_id,
            'contract_type' => match ($type) {
                'add_contract' => null,
                'extend_contract' => $existingContract?->contract_type,
                'off_boarding' => null,
            },
            'department' => $existingContract?->department,
            'job_title' => $existingContract?->job_title,
            'contract_start' => match ($type) {
                'extend_contract' => $existingContract?->contract_end?->copy()->addDay(),
                default => null,
            },
            'contract_end' => null,
            'created_by' => Auth::id(),
        ]);

        HrAuditHelper::log('create_request', 'hr_request', $req->id, null, $req->toArray());

        return redirect()->route('gik-hrd.requests.edit', ['hrRequest' => $req->id, 'step' => 1]);
    }

    public function edit(HrRequest $hrRequest, Request $request)
    {
        $this->authorizeAccess($hrRequest);
        $currentStep = max(1, min(3, (int) ($request->query('step', $hrRequest->current_step))));
        return view('gik-hrd.request-form', [
            'request' => $hrRequest,
            'currentStep' => $currentStep,
            'pageTitle' => "{$hrRequest->request_type_label} - Step {$currentStep}",
        ]);
    }

    public function saveStep(HrRequest $hrRequest, Request $request)
    {
        $this->authorizeAccess($hrRequest);
        $step = (int) $request->input('step', 1);
        $nextStep = (int) $request->input('next_step', $step);
        $submit = (bool) $request->input('submit', false);

        DB::beginTransaction();
        try {
            $old = $hrRequest->toArray();

            if ($step === 1) {
                $rules = [
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'email' => 'required|email|max:150',
                    'phone' => 'nullable|string|max:20',
                    'national_id' => 'nullable|string|max:50',
                ];
                if ($hrRequest->request_type === 'off_boarding') {
                    $rules['off_boarding_date'] = 'required|date';
                    $rules['off_boarding_reason'] = 'required|string|max:1000';
                }
                $data = $request->validate($rules);
                $hrRequest->fill($data);
            }

            if ($step === 2) {
                if ($hrRequest->request_type === 'off_boarding') {
                    $data = $request->validate([
                        'off_boarding_date' => 'required|date',
                        'off_boarding_reason' => 'required|string|max:1000',
                    ]);
                } else {
                    $data = $request->validate([
                        'contract_type' => 'required|in:internship,full_time,part_time,contract,probation',
                        'department' => 'required|string|max:100',
                        'job_title' => 'required|string|max:150',
                        'contract_start' => 'required|date',
                        'contract_end' => 'required|date|after:contract_start',
                    ]);
                }
                $hrRequest->fill($data);
            }

            if ($step === 3) {
                if ($request->hasFile('documents')) {
                    foreach ($request->file('documents') as $idx => $file) {
                        if (!$file) continue;
                        $docName = $request->input("document_names.{$idx}", 'Document ' . ($idx + 1));
                        $docType = $request->input("document_types.{$idx}", 'other');
                        $path = $file->store('hr-documents/' . $hrRequest->id, 'public');
                        HrRequestDocument::create([
                            'request_id' => $hrRequest->id,
                            'document_name' => $docName,
                            'document_type' => $docType,
                            'file_path' => $path,
                            'uploaded_by' => Auth::id(),
                        ]);
                    }
                }
            }

            $hrRequest->current_step = max($hrRequest->current_step, min($nextStep, $hrRequest->total_steps));

            if ($submit) {
                if ($hrRequest->current_step < $hrRequest->total_steps) {
                    return redirect()->back()
                        ->with('error', 'Harap selesaikan semua step sebelum submit.')
                        ->withInput();
                }
                $hrRequest->status = 'submitted';
                $hrRequest->submitted_by = Auth::id();
                $hrRequest->submitted_at = now();
            }

            $hrRequest->save();
            HrAuditHelper::log(
                $submit ? 'submit_request' : 'update_request_step_' . $step,
                'hr_request',
                $hrRequest->id,
                $old,
                $hrRequest->toArray(),
                $submit ? 'warning' : 'info'
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        if ($submit) {
            return redirect()->route('gik-hrd.operations')->with('success', 'Request berhasil disubmit dan menunggu review.');
        }

        $redirect = redirect()->route('gik-hrd.requests.edit', [
            'hrRequest' => $hrRequest->id,
            'step' => $hrRequest->current_step,
        ]);

        return $redirect->with('success', 'Data tersimpan sebagai draft.');
    }

    public function saveDraft(HrRequest $hrRequest, Request $request)
    {
        $this->authorizeAccess($hrRequest);
        $old = $hrRequest->toArray();
        $fields = $request->only([
            'first_name', 'last_name', 'email', 'phone', 'national_id',
            'contract_type', 'department', 'job_title',
            'contract_start', 'contract_end', 'off_boarding_date', 'off_boarding_reason'
        ]);
        $hrRequest->fill($fields);
        $hrRequest->status = 'draft';
        $hrRequest->save();
        HrAuditHelper::log('save_draft', 'hr_request', $hrRequest->id, $old, $hrRequest->toArray());
        return response()->json(['saved' => true, 'updated_at' => $hrRequest->updated_at->format('H:i:s')]);
    }

    public function submit(HrRequest $hrRequest)
    {
        $this->authorizeAccess($hrRequest);
        if ($hrRequest->current_step < $hrRequest->total_steps) {
            return redirect()->back()->with('error', 'Harap selesaikan semua step.');
        }
        $old = $hrRequest->toArray();
        $hrRequest->status = 'submitted';
        $hrRequest->submitted_by = Auth::id();
        $hrRequest->submitted_at = now();
        $hrRequest->save();
        HrAuditHelper::log('submit_request', 'hr_request', $hrRequest->id, $old, $hrRequest->toArray(), 'warning');
        return redirect()->route('gik-hrd.operations')->with('success', 'Request disubmit.');
    }

    public function destroy(HrRequest $hrRequest)
    {
        $this->authorizeAccess($hrRequest);
        if (!in_array($hrRequest->status, ['draft', 'rejected'])) {
            return redirect()->back()->with('error', 'Request yang sudah disubmit tidak bisa dihapus.');
        }
        $old = $hrRequest->toArray();
        $hrRequest->documents()->each(function ($doc) {
            Storage::disk('public')->delete($doc->file_path);
            $doc->delete();
        });
        $hrRequest->delete();
        HrAuditHelper::log('delete_request', 'hr_request', $hrRequest->id, $old, null, 'warning');
        return redirect()->route('gik-hrd.operations')->with('success', 'Request dihapus.');
    }

    public function review(HrRequest $hrRequest, Request $request)
    {
        if (!Auth::user()->isSuperAdminRecruitment()) abort(403);
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'review_notes' => 'nullable|string',
        ]);
        $old = $hrRequest->toArray();
        $hrRequest->status = $validated['action'] === 'approve' ? 'approved' : 'rejected';
        $hrRequest->reviewed_by = Auth::id();
        $hrRequest->reviewed_at = now();
        $hrRequest->review_notes = $validated['review_notes'] ?? null;
        $hrRequest->save();

        if ($validated['action'] === 'approve') {
            $this->processApprovedRequest($hrRequest);
        }

        HrAuditHelper::log('review_request_' . $validated['action'], 'hr_request', $hrRequest->id, $old, $hrRequest->toArray(), 'warning');
        return redirect()->back()->with('success', 'Request telah ' . ($validated['action'] === 'approve' ? 'disetujui' : 'ditolak'));
    }

    private function processApprovedRequest(HrRequest $req): void
    {
        DB::beginTransaction();
        try {
            switch ($req->request_type) {
                case 'add_contract':
                    HrContract::create([
                        'company_id' => $req->company_id,
                        'employee_id' => $req->request_number,
                        'first_name' => $req->first_name,
                        'last_name' => $req->last_name,
                        'email' => $req->email,
                        'phone' => $req->phone,
                        'national_id' => $req->national_id,
                        'department' => $req->department,
                        'job_title' => $req->job_title,
                        'contract_type' => $req->contract_type,
                        'contract_start' => $req->contract_start,
                        'contract_end' => $req->contract_end,
                        'status' => 'active',
                        'created_by' => Auth::id(),
                    ]);
                    $req->status = 'completed';
                    $req->save();
                    break;
                case 'extend_contract':
                    if ($req->contract) {
                        $oldC = $req->contract->toArray();
                        $req->contract->contract_end = $req->contract_end ?? $req->contract->contract_end;
                        $req->contract->status = 'extended';
                        $req->contract->updated_by = Auth::id();
                        $req->contract->save();
                        HrAuditHelper::log('extend_contract', 'hr_contract', $req->contract->id, $oldC, $req->contract->toArray(), 'warning');
                    }
                    HrContract::create([
                        'company_id' => $req->company_id,
                        'employee_id' => ($req->contract?->employee_id ?? $req->request_number) . '-EXT',
                        'first_name' => $req->first_name,
                        'last_name' => $req->last_name,
                        'email' => $req->email,
                        'phone' => $req->phone,
                        'national_id' => $req->national_id,
                        'department' => $req->contract?->department ?? $req->department,
                        'job_title' => $req->contract?->job_title ?? $req->job_title,
                        'contract_type' => $req->contract_type ?? $req->contract?->contract_type,
                        'contract_start' => $req->contract_start,
                        'contract_end' => $req->contract_end,
                        'status' => 'active',
                        'created_by' => Auth::id(),
                    ]);
                    $req->status = 'completed';
                    $req->save();
                    break;
                case 'off_boarding':
                    if ($req->contract) {
                        $oldC = $req->contract->toArray();
                        $req->contract->status = 'offboarded';
                        $req->contract->updated_by = Auth::id();
                        $req->contract->save();
                        HrAuditHelper::log('offboard_contract', 'hr_contract', $req->contract->id, $oldC, $req->contract->toArray(), 'critical');
                    }
                    $req->status = 'completed';
                    $req->save();
                    break;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function authorizeAccess(HrRequest $hrRequest): void
    {
        $user = Auth::user();
        if ($user->isSuperAdminRecruitment()) return;
        if ($user->role === 'hrd_company' && $hrRequest->company_id === $user->company_id) return;
        abort(403, 'Akses ditolak.');
    }
}
