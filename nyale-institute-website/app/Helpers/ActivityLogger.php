<?php

namespace App\Helpers;

use App\Models\UserActivityLog;

class ActivityLogger
{
    public static function log($action, $module = null, $description = null, $data = null)
    {
        if (!auth()->check()) {
            return;
        }

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
