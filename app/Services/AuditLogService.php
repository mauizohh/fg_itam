<?php

namespace App\Services;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public static function record(string $action, string $objectType, string $objectId, ?string $before = null, ?string $after = null, ?string $user = null): void
    {
        $resolvedUser = $user;
        if ($resolvedUser === null || trim($resolvedUser) === '') {
            $resolvedUser = Auth::user()?->name ?? 'System';
        }

        try {
            AuditLog::create([
                'timestamp' => Carbon::now(),
                'user' => $resolvedUser,
                'action' => $action,
                'object_type' => $objectType,
                'object_id' => $objectId,
                'before_state' => $before,
                'after_state' => $after,
            ]);
        } catch (\Throwable $e) {
            // Keep going if logging fails
        }
    }
}
