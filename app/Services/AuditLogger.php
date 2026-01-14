<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Log an action in the audit logs.
     *
     * @param string $action The action performed (e.g., LOGIN, CREATE, UPDATE, DELETE, VOTE_CAST)
     * @param string $module The system part affected (e.g., Candidates, Settings, Elections)
     * @param string|null $description A human-readable summary
     * @param array|null $oldValues Data before change
     * @param array|null $newValues Data after change
     * @return AuditLog
     */
    public static function log(string $action, string $module, ?string $description = null, ?array $oldValues = null, ?array $newValues = null)
    {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
