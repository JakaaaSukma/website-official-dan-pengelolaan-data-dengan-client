<?php

namespace App\Http\Controllers;

use App\Models\HrContract;
use App\Models\HrCandidate;
use App\Models\HrRequest;
use App\Models\HrMessage;
use App\Helpers\HrAuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HrDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        $viewingCompanyId = $request->query('company_id');
        if ($isSuperAdmin && $viewingCompanyId) {
            $query = HrContract::withoutTenant()->where('company_id', $viewingCompanyId);
        } else {
            $query = HrContract::query();
        }

        $search = $request->query('search');
        $deptFilter = $request->query('department');
        $perPage = 10;

        $allContractsQuery = (clone $query)->where('status', 'active');

        if ($search) {
            $allContractsQuery->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('employee_id', 'LIKE', "%{$search}%")
                    ->orWhere('job_title', 'LIKE', "%{$search}%");
            });
        }
        if ($deptFilter && $deptFilter !== 'all') {
            $allContractsQuery->where('department', $deptFilter);
        }

        $contracts = (clone $allContractsQuery)->orderBy('contract_end', 'asc')->paginate($perPage);

        $departments = (clone $query)->where('status', 'active')
            ->distinct()->pluck('department')->filter()->values()->toArray();

        $activeCount = (clone $query)->where('status', 'active')->count();
        $expiringSoon = (clone $query)->where('status', 'active')
            ->whereBetween('contract_end', [Carbon::now(), Carbon::now()->addDays(30)])
            ->orderBy('contract_end', 'asc')
            ->limit(5)
            ->get();

        $monthStart = Carbon::now()->startOfMonth();
        $newThisMonth = (clone $query)->where('status', 'active')
            ->where('contract_start', '>=', $monthStart)
            ->count();

        $completedContracts = (clone $query)
            ->where(function ($q) {
                $q->where('status', 'expired')
                    ->orWhere('status', 'offboarded');
            })
            ->whereMonth('contract_end', Carbon::now()->month)
            ->whereYear('contract_end', Carbon::now()->year)
            ->count();

        $data = [
            'company' => $company,
            'contracts' => $contracts,
            'departments' => $departments,
            'activeCount' => $activeCount,
            'expiringSoon' => $expiringSoon,
            'newThisMonth' => $newThisMonth,
            'completedContracts' => $completedContracts,
            'search' => $search,
            'deptFilter' => $deptFilter ?? 'all',
            'isSuperAdmin' => $isSuperAdmin,
            'viewingCompanyId' => $viewingCompanyId,
            'pageTitle' => $company->company_name ?? 'HR Dashboard',
        ];

        HrAuditHelper::log('view_dashboard', 'hr_contract');

        return view('gik-hrd.dashboard', $data);
    }

    public function operations(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        $viewingCompanyId = $request->query('company_id');

        if ($isSuperAdmin && $viewingCompanyId) {
            $requests = HrRequest::withoutTenant()->where('company_id', $viewingCompanyId);
        } else {
            $requests = HrRequest::query();
        }

        $requests = $requests->orderBy('created_at', 'desc')->paginate(15);

        return view('gik-hrd.operations', [
            'requests' => $requests,
            'pageTitle' => 'Operations',
        ]);
    }

    public function messages()
    {
        $user = Auth::user();
        $messages = HrMessage::where('company_id', $user->company_id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('gik-hrd.messages', [
            'messages' => $messages,
            'pageTitle' => 'Messages',
        ]);
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'message_type' => 'required|in:request_access,quota_request,account_issue,performance_report,general',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ]);

        $user = Auth::user();

        $msg = HrMessage::create([
            'company_id' => $user->company_id,
            'from_user_id' => $user->id,
            'message_type' => $validated['message_type'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'priority' => $validated['priority'] ?? 'normal',
            'status' => 'unread',
        ]);

        HrAuditHelper::log('send_message', 'hr_message', $msg->id, null, $msg->toArray());

        return redirect()->back()->with('success', 'Pesan berhasil dikirim ke Tim Rekrutmen.');
    }

    public function showMessage(HrMessage $message)
    {
        if ($message->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $message->load(['replies', 'replies.user']);

        return view('gik-hrd.message-show', [
            'message' => $message,
            'pageTitle' => 'Chat: ' . $message->subject,
        ]);
    }

    public function replyMessage(Request $request, HrMessage $message)
    {
        if ($message->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'reply' => 'required|string',
        ]);

        \App\Models\HrMessageReply::create([
            'hr_message_id' => $message->id,
            'user_id' => Auth::id(),
            'body' => $validated['reply'],
        ]);

        $message->status = 'unread'; // Mark as unread so SuperAdmin sees it
        $message->save();

        return redirect()->back()->with('success', 'Balasan berhasil dikirim.');
    }

    public function showChangePassword()
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);
        $pageTitle = 'Ubah Password — HR Company';
        $sMenu = 'profile.change-password';
        return view('gik-hrd.profile-change-password', compact('pageTitle', 'sMenu'));
    }

    public function updatePassword(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if (!\Illuminate\Support\Facades\Hash::check($validated['current_password'], $u->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.'])->withInput();
        }
        $old = ['password_changed_at' => $u->updated_at?->toDateTimeString()];
        $u->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $u->save();
        HrAuditHelper::log('change_password_self', 'users', $u->id, $old, ['password_changed_at' => now()->toDateTimeString()], 'warning');
        try {
            HrMessage::create([
                'company_id' => $u->company_id,
                'from_user_id' => $u->id,
                'to_user_id' => $u->id,
                'message_type' => 'system_security',
                'subject' => 'Password HR Company Berhasil Diubah',
                'body' => "Password akun HR Company Anda ({$u->email}) berhasil diubah pada " . now()->format('d M Y H:i:s') . " dari IP " . request()->ip() . ". Jika bukan Anda, segera laporkan ke Tim Rekrutmen.",
                'priority' => 'high',
                'status' => 'unread',
            ]);
        } catch (\Throwable $e) {}
        return redirect()->route('gik-hrd.profile.change-password')->with('success', 'Password Anda berhasil diperbarui!');
    }

    public function activityLogs(Request $request)
    {
        $u = Auth::user();
        if (!($u->isHrdCompany() || $u->role === 'company')) abort(403);
        $pageTitle = 'Log Aktivitas Saya — HR Company';
        $sMenu = 'activity-logs';
        $q = \App\Models\HrAuditLog::with(['user', 'company'])->where(function ($qq) use ($u) {
            $qq->where('user_id', $u->id)->orWhere('company_id', $u->company_id);
        });
        if ($search = $request->query('search')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('action', 'LIKE', "%{$search}%")
                   ->orWhere('entity_type', 'LIKE', "%{$search}%")
                   ->orWhere('ip_address', 'LIKE', "%{$search}%");
            });
        }
        if ($severity = $request->query('severity')) $q->where('severity', $severity);
        $logs = $q->orderByDesc('created_at')->paginate(25);
        $stats = [
            'totalToday' => \App\Models\HrAuditLog::whereDate('created_at', today())->where(fn($qq2) => $qq2->where('user_id', $u->id)->orWhere('company_id', $u->company_id))->count(),
            'warning' => \App\Models\HrAuditLog::whereDate('created_at', today())->where(fn($qq2) => $qq2->where('user_id', $u->id)->orWhere('company_id', $u->company_id))->where('severity', 'warning')->count(),
            'critical' => \App\Models\HrAuditLog::whereDate('created_at', today())->where(fn($qq2) => $qq2->where('user_id', $u->id)->orWhere('company_id', $u->company_id))->where('severity', 'critical')->count(),
            'uniqueUsers' => 1,
        ];
        return view('gik-shared.activity-log', compact('logs', 'pageTitle', 'sMenu', 'search', 'severity', 'stats'));
    }

    public function panduan()
    {
        $pageTitle = 'Panduan Penggunaan — HR Perusahaan';
        $sMenu = 'panduan';
        return view('gik-hrd.panduan', compact('pageTitle', 'sMenu'));
    }

    public function downloadPanduan()
    {
        $html = view('gik-shared.panduan-pdf', [
            'role' => 'hrd',
            'roleTitle' => 'HR Perusahaan',
            'loginUrl' => url('/admin/login'),
        ])->render();

        $filename = 'Panduan_Gyokai_Connect_HR_Perusahaan_' . now()->format('Ymd') . '.html';
        $path = storage_path('app/' . $filename);
        file_put_contents($path, $html);

        return response()->download($path, $filename, [
            'Content-Type' => 'text/html',
        ])->deleteFileAfterSend(true);
    }
}
