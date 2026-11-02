<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrSuspensionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuspensionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $q = HrSuspensionRequest::withoutTenant()
            ->with(['company', 'intern', 'requestedBy']);

        if ($request->filled('company_id')) {
            $q->where('company_id', $request->input('company_id'));
        }

        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = "%{$request->input('search')}%";
            $q->whereHas('intern', function ($sq) use ($search) {
                $sq->whereRaw("CONCAT_WS(' ',first_name,last_name) like ?", [$search]);
            });
        }

        $reports = $q->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $reports
        ]);
    }
}
