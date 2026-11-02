<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BpjsAccidentReport;
use App\Services\BpjsF2aService;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BpjsController extends Controller
{
    public function accidents(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $search = $request->query('search');

        $query = BpjsAccidentReport::withoutTenant()
            ->with(['hrContract', 'company', 'reportedByUser'])
            ->orderByDesc('created_at');

        if ($search) {
            $s = '%' . trim($search) . '%';
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
        
        $reports = $query->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $reports
        ]);
    }

    public function f2a(Request $request, BpjsF2aService $f2aService)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Default to current month/year or fetch from request
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $companyId = $request->input('company_id');

        $companies = Client::where('is_active', true)->orderBy('company_name')->get();

        $data = null;
        if ($companyId) {
            $data = $f2aService->generateF2aData($companyId, (int) $month, (int) $year);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'companies' => $companies,
                'f2a_data' => $data,
                'params' => [
                    'month' => $month,
                    'year' => $year,
                    'company_id' => $companyId
                ]
            ]
        ]);
    }
}
