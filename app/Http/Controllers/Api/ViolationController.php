<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrViolationReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $search = $request->query('search');
        $status = $request->query('status');
        $companyId = $request->query('company_id');

        $query = HrViolationReport::withoutTenant()
            ->with(['intern', 'company', 'reportedBy'])
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

        $reports = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $reports
        ]);
    }
}
