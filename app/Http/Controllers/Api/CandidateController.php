<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrCandidate;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        
        $query = HrCandidate::query();
        
        // If it's a superadmin, they might need to view all regardless of tenant
        if ($isSuperAdmin) {
            $query = HrCandidate::withoutTenant();
        }

        $search = $request->query('search');
        $status = $request->query('status');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('job_title', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $candidates
        ]);
    }
}
