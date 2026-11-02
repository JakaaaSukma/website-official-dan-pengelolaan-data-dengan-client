<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureHrdCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        $userRole = $user->role ?? null;

        if (!in_array($userRole, ['hrd_company', 'super_admin_recruitment', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya HRD Perusahaan dan Super Admin yang dapat mengakses halaman ini.');
        }

        if ($userRole === 'hrd_company') {
            if (!$user->company_id) {
                abort(403, 'Akun HRD Anda tidak terhubung dengan perusahaan manapun. Silakan hubungi Super Admin.');
            }
            if ($user->account_status !== 'active') {
                $statusText = [
                    'pending_review' => 'Akun Anda sedang dalam proses review.',
                    'suspended' => 'Akun Anda telah disuspend.',
                ][$user->account_status] ?? 'Akun Anda tidak aktif.';
                abort(403, $statusText . ' Silakan hubungi Super Admin.');
            }
        }

        return $next($request);
    }
}
