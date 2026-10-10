<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditHelper
{
    public static function log(
        string $action,
        string $module,
        string $description
    ): void {
        $user = Auth::user();

        $adminId = null;
        $scope = 'system';
        $actorRole = null;
        $actorName = null;
        $actorEmail = null;

        if ($user) {
            $actorRole = $user->role;
            $actorName = $user->name;
            $actorEmail = $user->email;

            if ($user->role === 'admin') {
                $adminId = $user->id;
                $scope = 'tenant';
            } elseif ($user->role === 'customer') {
                $adminId = $user->admin_id;
                $scope = $user->admin_id
                    ? 'tenant'
                    : 'system';
            } elseif ($user->role === 'super_admin') {
                $scope = 'platform';
            }
        }

        AuditLog::create([
            'user_id' => $user?->id,
            'admin_id' => $adminId,
            'actor_role' => $actorRole,
            'actor_name' => $actorName,
            'actor_email' => $actorEmail,
            'scope' => $scope,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}