<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrMessage;

class MessageController extends Controller
{
    /**
     * Get messages (inbox) for the mobile app
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = $user->isSuperAdminRecruitment();
        
        $query = HrMessage::with(['company', 'fromUser'])->orderByDesc('created_at');

        if (!$isSuperAdmin) {
            // If HR Company, only show their messages
            $query->where('company_id', $user->company_id);
        }

        // Apply filters
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 20);
        $messages = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Mark a message as read
     */
    public function markAsRead(Request $request, $id)
    {
        $message = HrMessage::findOrFail($id);
        
        // Basic authorization check
        $user = $request->user();
        if (!$user->isSuperAdminRecruitment() && $message->company_id !== $user->company_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if ($message->status === 'unread') {
            $message->status = 'read';
            $message->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Message marked as read'
        ]);
    }
}
