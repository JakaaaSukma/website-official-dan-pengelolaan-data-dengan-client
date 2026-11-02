<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdminRecruitment
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        $userRole = $user->role ?? null;

        if (!in_array($userRole, ['super_admin_recruitment', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya Tim Rekrutmen (Super Admin) yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
