<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrContract;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Get active participants (employees) for the mobile app
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        
        $query = HrContract::with('company')->orderByDesc('contract_end');

        if ($isSuperAdmin) {
            $query = HrContract::withoutTenant()->with('company')->orderByDesc('contract_end');
            
            if ($request->filled('company_id') && $request->company_id !== 'all') {
                $query->where('company_id', $request->company_id);
            }
        }

        // Apply filters
        if ($request->filled('search')) {
            $s = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s)
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

        $perPage = $request->input('per_page', 20);
        $employees = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $employees
        ]);
    }
}
