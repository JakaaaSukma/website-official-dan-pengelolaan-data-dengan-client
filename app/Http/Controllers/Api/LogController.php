<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSuperAdminRecruitment()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $q = HrAuditLog::with(['user', 'company']);
        
        if ($search = $request->query('search')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('action', 'LIKE', "%{$search}%")
                   ->orWhere('entity_type', 'LIKE', "%{$search}%")
                   ->orWhere('ip_address', 'LIKE', "%{$search}%")
                   ->orWhereHas('user', function ($uu) use ($search) { 
                       $uu->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%"); 
                   });
            });
        }
        if ($severity = $request->query('severity')) {
            if ($severity !== 'all') {
                $q->where('severity', $severity);
            }
        }

        $logs = $q->orderByDesc('created_at')->paginate(25);

        $stats = [
            'totalToday' => HrAuditLog::whereDate('created_at', today())->count(),
            'warning' => HrAuditLog::whereDate('created_at', today())->where('severity', 'warning')->count(),
            'critical' => HrAuditLog::whereDate('created_at', today())->where('severity', 'critical')->count(),
            'uniqueUsers' => HrAuditLog::whereDate('created_at', today())->distinct()->count('user_id'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => [
                'logs' => $logs,
                'stats' => $stats
            ]
        ]);
    }
}
