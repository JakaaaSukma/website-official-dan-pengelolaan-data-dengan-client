<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\HrContract;
use App\Models\HrMessage;
use App\Models\HrRequest;
use App\Helpers\HrAuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class HrSuperAdminController extends Controller
{
    public function dashboard()
    {
        $prevMonth = Carbon::now()->subMonth()->startOfMonth();
        $currStart = Carbon::now()->startOfMonth();

        $stats = HrContract::withoutTenant()
            ->selectRaw("
                COUNT(CASE WHEN status = 'active' THEN 1 END) AS total_active,
                COUNT(CASE WHEN status = 'active' AND work_type = 'on_site' THEN 1 END) AS on_site,
                COUNT(CASE WHEN status = 'active' AND work_type IN ('remote','hybrid') THEN 1 END) AS remote_hybrid,
                COUNT(CASE WHEN status = 'active' AND contract_start <= ? AND (contract_end IS NULL OR contract_end >= ?) THEN 1 END) AS prev_month
            ", [$prevMonth->endOfMonth()->toDateString(), $prevMonth->copy()->startOfMonth()->toDateString()])
            ->first();

        $totalActive = (int) ($stats->total_active ?? 0);
        $onSite = (int) ($stats->on_site ?? 0);
        $remote = (int) ($stats->remote_hybrid ?? 0);
        $prevCount = (int) ($stats->prev_month ?? 0);
        $growthPct = $prevCount > 0 ? round((($totalActive - $prevCount) / $prevCount) * 100, 1) : 0;

        [$unreadCount, $newMessages] = [
            HrMessage::whereIn('status', ['unread'])->count(),
            HrMessage::with(['company', 'fromUser'])->orderByDesc('created_at')->limit(5)->get(),
        ];

        $clientAccounts = User::whereIn('role', ['hrd_company', 'company'])
            ->with('company')
            ->withCount(['hrRequestsSubmitted as pending_requests_count' => function ($sq) {
                $sq->whereIn('status', ['draft', 'submitted', 'in_review']);
            }])
            ->orderBy('name')
            ->paginate(10, pageName: 'clients_page');

        $allCompanies = Client::orderBy('company_name')->get();
        $perCompanyCounts = HrContract::withoutTenant()
            ->selectRaw('company_id, count(*) as cnt')
            ->where('status', 'active')
            ->groupBy('company_id')
            ->pluck('cnt', 'company_id');
        $companyStats = [];
        foreach ($allCompanies as $c) {
            $companyStats[$c->id] = [
                'name' => $c->company_name,
                'active' => (int) ($perCompanyCounts[$c->id] ?? 0),
            ];
        }

        $pendingRequests = HrRequest::withoutTenant()
            ->whereIn('status', ['submitted', 'in_review'])
            ->with(['company', 'submittedBy'])
            ->orderBy('submitted_at', 'desc')
            ->limit(8)
            ->get();

        HrAuditHelper::log('view_super_admin_dashboard', 'super_admin');

        return view('gik-superadmin.dashboard', [
            'totalActive' => $totalActive,
            'onSite' => $onSite,
            'remote' => $remote,
            'growthPct' => $growthPct,
            'unreadCount' => $unreadCount,
            'newMessages' => $newMessages,
            'clientAccounts' => $clientAccounts,
            'companyStats' => $companyStats,
            'pendingRequests' => $pendingRequests,
            'pageTitle' => 'Super Admin Control Center',
        ]);
    }

    public function clients(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = User::whereIn('role', ['hrd_company', 'company'])->with('company');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('company', function ($qq) use ($search) {
                        $qq->where('company_name', 'LIKE', "%{$search}%");
                    });
            });
        }
        if ($status && $status !== 'all') {
            $query->where('account_status', $status);
        }

        $clients = $query->orderByDesc('created_at')->paginate(15);
        $allCompanies = Client::where('is_active', true)->orderBy('company_name')->get();

        return view('gik-superadmin.clients', [
            'clients' => $clients,
            'allCompanies' => $allCompanies,
            'search' => $search,
            'status' => $status ?? 'all',
            'pageTitle' => 'Kelola Login Client',
        ]);
    }

    public function createClient()
    {
        $allCompanies = Client::where('is_active', true)->orderBy('company_name')->get();
        return redirect()->route('gik-superadmin.clients')->with([
            'showAddClientModal' => true,
            'allCompanies' => $allCompanies,
        ]);
    }

    public function editClient(User $client)
    {
        if (!in_array($client->role, ['hrd_company', 'company'])) abort(404);
        $allCompanies = Client::where('is_active', true)->orderBy('company_name')->get();
        return redirect()->route('gik-superadmin.clients')->with([
            'showEditClientModal' => true,
            'editClient' => $client,
            'allCompanies' => $allCompanies,
        ]);
    }

    public function storeClient(Request $request)
    {
        $mode = $request->input('company_mode', 'existing');
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:hrd_company,company',
            'account_status' => 'nullable|in:active,pending_review,suspended',
            'company_mode' => 'required|in:existing,new',
        ];
        if ($mode === 'existing') {
            $rules['company_id'] = 'required|exists:clients,id';
        } else {
            $rules['new_company_name'] = 'required|string|max:255';
            $rules['new_company_email'] = 'nullable|email|max:255';
            $rules['new_company_phone'] = 'nullable|string|max:30';
            $rules['new_company_about'] = 'nullable|string|max:1000';
        }
        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $companyId = null;
            if ($mode === 'new') {
                $company = \App\Models\Client::create([
                    'company_name' => $validated['new_company_name'],
                    'about_company' => trim(($validated['new_company_about'] ?? '') . (isset($validated['new_company_email']) ? "\nEmail: " . $validated['new_company_email'] : '') . (isset($validated['new_company_phone']) ? "\nTelp: " . $validated['new_company_phone'] : '')),
                    'is_active' => true,
                    'is_hidden' => false,
                ]);
                $companyId = $company->id;
                HrAuditHelper::log('create_client_company', 'clients', $company->id, null, $company->toArray(), 'warning');
            } else {
                $companyId = $validated['company_id'];
            }

            $user = new User([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
            ]);
            $user->role = $validated['role'];
            $user->company_id = $companyId;
            $user->account_status = $validated['account_status'] ?? 'active';
            $user->save();

            HrAuditHelper::log('create_client_account', 'users', $user->id, null, $user->toArray(), 'warning');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat akun: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-superadmin.clients')->with('success', 'Akun client berhasil dibuat' . ($mode === 'new' ? ' beserta perusahaan BARU.' : '.'));
    }

    public function updateClient(User $client, Request $request)
    {
        if (!in_array($client->role, ['hrd_company', 'company'])) abort(404);
        $validated = $request->validate([
            'company_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:hrd_company,company',
            'account_status' => 'required|in:active,pending_review,suspended',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $old = $client->toArray();
            $client->company_id = $validated['company_id'];
            $client->name = $validated['name'];
            $client->email = $validated['email'];
            $client->phone = $validated['phone'] ?? null;
            $client->role = $validated['role'];
            $client->account_status = $validated['account_status'];
            if (!empty($validated['password'])) {
                $client->password = Hash::make($validated['password']);
            }
            $client->save();
            HrAuditHelper::log('update_client_account', 'users', $client->id, $old, $client->toArray(), 'warning');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update akun: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('gik-superadmin.clients')->with('success', 'Akun client berhasil diupdate.');
    }

    public function destroyClient(User $client)
    {
        if (!in_array($client->role, ['hrd_company', 'company'])) abort(404);
        $old = $client->toArray();
        DB::beginTransaction();
        try {
            // Suspend bukan delete, sesuai bug report 7.2 action Suspend
            $client->account_status = 'suspended';
            $client->save();
            HrAuditHelper::log('suspend_client_account', 'users', $client->id, $old, $client->toArray(), 'critical');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal suspend akun: ' . $e->getMessage());
        }
        return redirect()->route('gik-superadmin.clients')->with('success', 'Akun client berhasil di-SUSPEND (nonaktif sementara).');
    }

    public function forceDeleteClient(User $client)
    {
        if (!in_array($client->role, ['hrd_company', 'company'])) abort(404);
        $old = $client->toArray();
        DB::beginTransaction();
        try {
            HrAuditHelper::log('force_delete_client_account', 'users', $client->id, $old, null, 'critical');
            // Nullify references soft way (avoid FK errors)
            \App\Models\HrMessage::where('from_user_id', $client->id)->orWhere('to_user_id', $client->id)->orWhere('replied_by', $client->id)->orWhere('assigned_to', $client->id)->delete();
            \App\Models\HrAuditLog::where('user_id', $client->id)->delete();
            $client->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal HAPUS akun permanen: ' . $e->getMessage());
        }
        return redirect()->route('gik-superadmin.clients')->with('success', 'Akun client berhasil DIHAPUS PERMANEN dari database.');
    }

    public function showChangePassword()
    {
        $pageTitle = 'Pengaturan Profil — Super Admin Recruitment';
        $sMenu = 'profile.change-password';
        return view('gik-superadmin.profile-change-password', compact('pageTitle', 'sMenu'));
    }

    public function updatePassword(Request $request)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);
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
            \App\Models\HrMessage::create([
                'company_id' => null,
                'from_user_id' => $u->id,
                'to_user_id' => $u->id,
                'message_type' => 'system_security',
                'subject' => 'Password Super Admin Berhasil Diubah',
                'body' => "Password akun Super Admin Anda ({$u->email}) berhasil diubah pada " . now()->format('d M Y H:i:s') . " dari IP " . request()->ip() . ". Jika bukan Anda, segera hubungi tim support.",
                'priority' => 'high',
                'status' => 'unread',
            ]);
        } catch (\Throwable $e) {}
        return redirect()->route('gik-superadmin.profile.change-password')->with('success', 'Password Super Admin berhasil diperbarui!');
    }

    public function updateEmail(Request $request)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $u->id,
            'current_password_email' => 'required|string',
        ]);
        if (!\Illuminate\Support\Facades\Hash::check($validated['current_password_email'], $u->password)) {
            return redirect()->back()->withErrors(['current_password_email' => 'Password saat ini yang Anda masukkan salah.'])->withInput();
        }
        $oldEmail = $u->email;
        $u->email = $validated['email'];
        $u->save();
        HrAuditHelper::log('change_email_self', 'users', $u->id, ['email' => $oldEmail], ['email' => $u->email], 'warning');
        return redirect()->route('gik-superadmin.profile.change-password')->with('success', 'Email berhasil diperbarui dari ' . $oldEmail . ' ke ' . $u->email);
    }

    public function activityLogs(Request $request)
    {
        $u = Auth::user();
        if (!$u->isSuperAdminRecruitment()) abort(403);
        $pageTitle = 'Log Aktivitas Seluruh Sistem — Super Admin';
        $sMenu = 'activity-logs';
        $q = \App\Models\HrAuditLog::with(['user', 'company']);
        if ($search = $request->query('search')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('action', 'LIKE', "%{$search}%")
                   ->orWhere('entity_type', 'LIKE', "%{$search}%")
                   ->orWhere('ip_address', 'LIKE', "%{$search}%")
                   ->orWhereHas('user', function ($uu) use ($search) { $uu->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%"); });
            });
        }
        if ($severity = $request->query('severity')) $q->where('severity', $severity);
        $logs = $q->orderByDesc('created_at')->paginate(25);
        $stats = [
            'totalToday' => \App\Models\HrAuditLog::whereDate('created_at', today())->count(),
            'warning' => \App\Models\HrAuditLog::whereDate('created_at', today())->where('severity', 'warning')->count(),
            'critical' => \App\Models\HrAuditLog::whereDate('created_at', today())->where('severity', 'critical')->count(),
            'uniqueUsers' => \App\Models\HrAuditLog::whereDate('created_at', today())->distinct()->count('user_id'),
        ];
        return view('gik-shared.activity-log', compact('logs', 'pageTitle', 'sMenu', 'search', 'severity', 'stats'));
    }

    public function inbox(Request $request)
    {
        $status = $request->query('status', 'all');
        $type = $request->query('type', 'all');
        $query = HrMessage::with(['company', 'fromUser', 'replies', 'replies.user']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($type !== 'all') {
            $query->where('message_type', $type);
        }

        $messages = $query->orderByDesc('created_at')->paginate(15);
        $messagesTotal = HrMessage::count();
        $unread = HrMessage::where('status', 'unread')->count();
        $replied = HrMessage::where('status', 'replied')->count();
        $resolved = HrMessage::where('status', 'resolved')->count();
        $unreadCount = $unread;

        return view('gik-superadmin.inbox', [
            'messages' => $messages,
            'messagesTotal' => $messagesTotal,
            'unread' => $unread,
            'replied' => $replied,
            'resolved' => $resolved,
            'unreadCount' => $unreadCount,
            'status' => $status,
            'type' => $type,
            'pageTitle' => 'Inbox Pesan Request',
        ]);
    }

    public function showMessage(HrMessage $message)
    {
        if ($message->status === 'unread') {
            $message->status = 'read';
            $message->save();
        }
        return view('gik-superadmin.message-show', [
            'message' => $message,
            'pageTitle' => $message->subject,
        ]);
    }

    public function replyMessage(HrMessage $message, Request $request)
    {
        $validated = $request->validate([
            'reply' => 'required|string',
            'status' => 'nullable|in:replied,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $old = $message->toArray();
        
        \App\Models\HrMessageReply::create([
            'hr_message_id' => $message->id,
            'user_id' => Auth::id(),
            'body' => $validated['reply'],
        ]);

        $message->replied_by = Auth::id();
        $message->replied_at = now();
        $message->status = $validated['status'] ?? 'replied';
        if (($validated['status'] ?? null) === 'resolved') {
            $message->resolved_at = now();
        }
        if (!empty($validated['assigned_to'] ?? null)) {
            $message->assigned_to = $validated['assigned_to'];
        }
        $message->save();
        HrAuditHelper::log('reply_message', 'hr_message', $message->id, $old, $message->toArray());
        return redirect()->back()->with('success', 'Balasan dikirim.');
    }

    public function assignMessage(HrMessage $message, Request $request)
    {
        $valid = $request->validate(['assigned_to' => 'nullable|exists:users,id']);
        $old = $message->toArray();
        $message->assigned_to = $valid['assigned_to'] ?? null;
        $message->save();
        HrAuditHelper::log('assign_message', 'hr_message', $message->id, $old, $message->toArray());
        return redirect()->back()->with('success', 'Pesan di-assign.');
    }

    public function markResolved(HrMessage $message)
    {
        $old = $message->toArray();
        $message->status = 'resolved';
        $message->resolved_at = now();
        $message->save();
        HrAuditHelper::log('resolve_message', 'hr_message', $message->id, $old, $message->toArray());
        return redirect()->back()->with('success', 'Pesan ditandai selesai.');
    }

    public function panduan()
    {
        $pageTitle = 'Panduan Penggunaan — Super Admin';
        $sMenu = 'panduan';
        return view('gik-superadmin.panduan', compact('pageTitle', 'sMenu'));
    }

    public function downloadPanduan()
    {
        $html = view('gik-shared.panduan-pdf', [
            'role' => 'superadmin',
            'roleTitle' => 'Super Admin Rekrutmen',
            'loginUrl' => url('/admin/login'),
        ])->render();

        $filename = 'Panduan_Gyokai_Connect_SuperAdmin_' . now()->format('Ymd') . '.html';
        $path = storage_path('app/' . $filename);
        file_put_contents($path, $html);

        return response()->download($path, $filename, [
            'Content-Type' => 'text/html',
        ])->deleteFileAfterSend(true);
    }
}
