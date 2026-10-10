<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Display ISP Admin accounts.
     *
     * Only the platform Super Admin may access
     * this controller.
     */
    public function index()
    {
        $this->ensureSuperAdmin();

        $admins = User::where('role', 'admin')
            ->latest()
            ->paginate(10);

        return view(
            'admin.admin-users.index',
            compact('admins')
        );
    }

    /**
     * Activate an ISP Admin account.
     */
    public function activate(User $admin)
    {
        $this->ensureSuperAdmin();

        $this->ensureTargetIsAdmin($admin);

        $admin->update([
            'is_suspended' => false,
        ]);

        AuditHelper::log(
            'Activate Admin',
            'Admin Users',
            'Admin '.$admin->name.' was activated.'
        );

        NotificationHelper::create(
            'Admin Activated',
            'Admin '.$admin->name.' was activated by super admin.',
            'success',
            'super_admin'
        );

        return back()->with(
            'success',
            'Admin activated successfully.'
        );
    }

    /**
     * Suspend an ISP Admin account.
     */
    public function deactivate(User $admin)
    {
        $this->ensureSuperAdmin();

        $this->ensureTargetIsAdmin($admin);

        $admin->update([
            'is_suspended' => true,
        ]);

        AuditHelper::log(
            'Deactivate Admin',
            'Admin Users',
            'Admin '.$admin->name.' was deactivated.'
        );

        NotificationHelper::create(
            'Admin Deactivated',
            'Admin '.$admin->name.' was deactivated by super admin.',
            'warning',
            'super_admin'
        );

        return back()->with(
            'success',
            'Admin deactivated successfully.'
        );
    }

    /**
     * Ensure the currently logged-in user
     * is the platform Super Admin.
     */
    private function ensureSuperAdmin(): void
    {
        if (
            !auth()->check() ||
            auth()->user()->role !== 'super_admin'
        ) {
            abort(
                403,
                'Only the Super Admin can manage ISP Admin accounts.'
            );
        }
    }

    /**
     * Ensure this controller can only operate
     * on ISP Admin accounts.
     *
     * Customers and Super Admin accounts must
     * never be managed through these methods.
     */
    private function ensureTargetIsAdmin(User $admin): void
    {
        if ($admin->role !== 'admin') {
            abort(
                403,
                'Only ISP Admin accounts can be managed here.'
            );
        }
    }
}