<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrTalent;
use Illuminate\Support\Facades\Auth;

class TalentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['super_admin_recruitment', 'hrd_company'])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $tab = $request->query('tab', 'ready_to_hire');
        $allowedTabs = ['ready_to_hire', 'in_process', 'passive'];
        if (!in_array($tab, $allowedTabs)) $tab = 'ready_to_hire';
        $search = $request->query('search');

        $counts = [
            'ready_to_hire' => HrTalent::where('status', 'ready_to_hire')->count(),
            'in_process' => HrTalent::where('status', 'in_process')->count(),
            'passive' => HrTalent::where('status', 'passive')->count(),
        ];

        $query = HrTalent::where('status', $tab);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('target_role', 'LIKE', "%{$search}%")
                    ->orWhere('current_job_title', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $talents = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => [
                'counts' => $counts,
                'talents' => $talents
            ]
        ]);
    }
}
