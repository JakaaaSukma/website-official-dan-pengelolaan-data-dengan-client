<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrAddContractBatch;
use App\Models\HrCandidateItem;
use App\Models\Client;
use App\Helpers\HrAuditHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['super_admin_recruitment', 'hrd_company'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $query = HrAddContractBatch::with('company')->withCount('candidates');

        if ($user->role === 'hrd_company') {
            $query->where('company_id', $user->company_id);
        }

        $search = $request->query('search');
        if ($search) {
            $query->where('batch_number', 'LIKE', "%{$search}%")
                ->orWhereHas('company', function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', "%{$search}%");
                });
        }

        $batches = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $batches
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['super_admin_recruitment', 'hrd_company'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if ($user->role === 'hrd_company') {
            $companies = Client::where('id', $user->company_id)->get();
        } else {
            $companies = Client::where('is_active', true)->orderBy('company_name')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'companies' => $companies
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['super_admin_recruitment', 'hrd_company'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'company_id' => 'required|exists:clients,id',
            'notes' => 'nullable|string|max:2000',
            'candidates' => 'required|array|min:1',
            'candidates.*.first_name' => 'required|string|max:100',
            'candidates.*.last_name' => 'nullable|string|max:100',
            'candidates.*.national_id' => 'required|string|max:50',
            'candidates.*.birth_date' => 'nullable|date',
            'candidates.*.job_title' => 'required|string|max:150',
            'candidates.*.address' => 'nullable|string|max:1000',
            'candidates.*.contract_start' => 'required|date',
            'candidates.*.contract_duration' => 'required|integer|in:3,6,12,18,24,36',
        ]);

        if ($user->role === 'hrd_company' && $validated['company_id'] != $user->company_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized company access'], 403);
        }

        DB::beginTransaction();
        try {
            $batch = HrAddContractBatch::create([
                'batch_number' => HrAuditHelper::generateRequestNumber('CAND'),
                'company_id' => $validated['company_id'],
                'created_by' => $user->id,
                'status' => 'submitted',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['candidates'] as $cand) {
                HrCandidateItem::create([
                    'hr_add_contract_batch_id' => $batch->id,
                    'national_id' => trim($cand['national_id']),
                    'first_name' => trim($cand['first_name']),
                    'last_name' => isset($cand['last_name']) ? trim($cand['last_name']) : null,
                    'date_of_birth' => $cand['birth_date'] ?? null,
                    'address' => $cand['address'] ?? null,
                    'job_title' => trim($cand['job_title']),
                    'contract_start' => $cand['contract_start'],
                    'contract_duration_months' => $cand['contract_duration'],
                ]);
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Contract request submitted successfully.',
                'data' => $batch
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save data. ' . $e->getMessage()
            ], 500);
        }
    }
}
