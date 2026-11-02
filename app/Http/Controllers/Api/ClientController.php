<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class ClientController extends Controller
{
    public function index(Request $request)
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

        $clients = $query->orderByDesc('created_at')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $clients
        ]);
    }
}
