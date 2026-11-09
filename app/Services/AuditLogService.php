<?php

namespace App\Services;

use App\Models\AuditLog;
use Carbon\Carbon;

class AuditLogService
{
    public static function record(string $action, string $objectType, string $objectId, ?string $before = null, ?string $after = null, ?string $user = 'Admin'): void
    {
        try {
            AuditLog::create([
                'timestamp' => Carbon::now(),
                'user' => $user ?: 'Admin',
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
