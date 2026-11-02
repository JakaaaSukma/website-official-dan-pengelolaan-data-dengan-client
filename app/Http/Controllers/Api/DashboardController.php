<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrContract;
use App\Models\HrMessage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for the mobile app
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        
        // Base query for contracts (Active Participants)
        if ($isSuperAdmin) {
            $query = HrContract::withoutTenant();
            $unreadMessages = HrMessage::where('status', 'unread')->count();
        } else {
            // Global scope automatically filters by company_id for HR Company
            $query = HrContract::query();
            // HR Company unread messages (assuming they receive messages from super admin)
            // But usually messages in Gyokai are sent TO Super Admin. Let's just return 0 for HR.
            $unreadMessages = 0;
        }

        // Active participants
        $activeCount = (clone $query)->where('status', 'active')->count();

        // Expiring within 30 days
        $expiringSoonCount = (clone $query)->where('status', 'active')
            ->whereBetween('contract_end', [Carbon::now(), Carbon::now()->addDays(30)])
            ->count();

        // New this month
        $monthStart = Carbon::now()->startOfMonth();
        $newThisMonthCount = (clone $query)->where('status', 'active')
            ->where('contract_start', '>=', $monthStart)
            ->count();

        // Completed this month (expired or offboarded)
        $completedContractsCount = (clone $query)
            ->whereIn('status', ['expired', 'offboarded'])
            ->whereMonth('contract_end', Carbon::now()->month)
            ->whereYear('contract_end', Carbon::now()->year)
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'statistics' => [
                    'active_participants' => $activeCount,
                    'expiring_soon' => $expiringSoonCount,
                    'new_this_month' => $newThisMonthCount,
                    'completed_this_month' => $completedContractsCount,
                    'unread_messages' => $unreadMessages,
                ]
            ]
        ]);
    }
}
