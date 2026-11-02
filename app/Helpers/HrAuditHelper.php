<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class HrAuditHelper
{
    public static function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $severity = 'info'
    ): void {
        try {
            $user = Auth::user();
            DB::table('hr_audit_logs')->insert([
                'user_id' => $user?->id,
                'company_id' => $user?->company_id,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'severity' => $severity,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Audit log failed: ' . $e->getMessage());
        }
    }

    public static function maskNationalId(?string $nationalId): ?string
    {
        if (empty($nationalId)) {
            return null;
        }
        $length = strlen($nationalId);
        if ($length <= 4) {
            return str_repeat('X', $length);
        }
        $prefix = substr($nationalId, 0, 2);
        $suffix = substr($nationalId, -2);
        $middle = str_repeat('X', $length - 4);
        return $prefix . $middle . $suffix;
    }

    public static function maskPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        $length = strlen(preg_replace('/\D/', '', $phone));
        if ($length <= 4) {
            return $phone;
        }
        $digits = preg_replace('/\D/', '', $phone);
        $visibleStart = 3;
        $visibleEnd = 2;
        return substr($digits, 0, $visibleStart)
            . str_repeat('*', max(0, $length - $visibleStart - $visibleEnd))
            . substr($digits, -$visibleEnd);
    }

    public static function generateRequestNumber(string $prefix = 'REQ'): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid('', true)), 0, 6));
        return "{$prefix}-{$date}-{$random}";
    }
}
