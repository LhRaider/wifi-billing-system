<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display audit records available to the authenticated administrator.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            abort(403);
        }

        $search = trim((string) $request->query('search', ''));

        /*
         * Load related accounts for display only.
         *
         * Authorization and tenant isolation are enforced by the audit_logs
         * scope/admin_id conditions below and never by these relationships.
         */
        $logs = AuditLog::query()
            ->with([
                'user:id,name,email',
                'admin:id,name,email',
            ]);

        /*
         * Super Admin can see platform-level audit events only.
         *
         * Tenant audit activity is deliberately excluded so the platform
         * administrator cannot browse an individual ISP's customer activity
         * through the audit-log interface.
         */
        if ($user->role === 'super_admin') {
            $logs->where('scope', 'platform');
        } else {
            /*
             * An ISP Admin can see only events belonging to their own tenant.
             *
             * This includes events performed by that Admin and events
             * performed by customers belonging to that ISP.
             */
            $logs
                ->where('scope', 'tenant')
                ->where('admin_id', $user->id);
        }

        /*
         * Search is deliberately applied only after the role/tenant scope
         * has been established. The nested OR conditions therefore cannot
         * escape the tenant boundary.
         */
        if ($search !== '') {
            $logs->where(function ($query) use ($search) {
                $query
                    ->where('action', 'like', '%' . $search . '%')
                    ->orWhere('module', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('actor_name', 'like', '%' . $search . '%')
                    ->orWhere('actor_email', 'like', '%' . $search . '%')
                    ->orWhere('actor_role', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%');
            });
        }

        $logs = $logs
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'search' => $search,
        ]);
    }
}