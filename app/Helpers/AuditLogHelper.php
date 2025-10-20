<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogHelper
{
    public static function log($action, $description = null)
    {
        $user = Auth::user();
        $ip = Request::ip();
        $userAgent = Request::header('User-Agent');

        AuditLog::create([
            // user_id can be null for unauthenticated events (e.g. failed login)
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }
}
