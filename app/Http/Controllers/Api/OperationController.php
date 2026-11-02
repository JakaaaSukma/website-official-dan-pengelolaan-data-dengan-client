<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        $viewingCompanyId = $request->query('company_id');

        if ($isSuperAdmin) {
            $query = HrRequest::withoutTenant();
            if ($viewingCompanyId) {
                $query->where('company_id', $viewingCompanyId);
            }
        } else {
            $query = HrRequest::query();
        }

        $search = $request->query('search');
        if ($search) {
            $s = '%' . trim($search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('request_number', 'like', $s)
                  ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $s)
                  ->orWhere('job_title', 'like', $s)
                  ->orWhereHas('company', function ($q2) use ($s) {
                      $q2->where('company_name', 'like', $s);
                  });
            });
        }
        
        $status = $request->query('status');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $type = $request->query('type');
        if ($type && $type !== 'all') {
            $query->where('request_type', $type);
        }

        $requests = $query->with('company')->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $requests
        ]);
    }
}
