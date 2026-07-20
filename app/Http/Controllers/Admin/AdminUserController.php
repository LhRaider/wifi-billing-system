<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;

class AdminUserController extends Controller
{
    public function index()
{
    $admins = User::where('role', 'admin')
        ->latest()
        ->paginate(10);

    return view('admin.admin-users.index', compact('admins'));
}

    public function activate(User $admin)
    {
        if ($admin->role === 'super_admin') {
            return back()->with('error', 'Super admin cannot be deactivated or activated here.');
        }

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

        return back()->with('success', 'Admin activated successfully.');
    }

    public function deactivate(User $admin)
    {
        if ($admin->role === 'super_admin') {
            return back()->with('error', 'Super admin cannot be deactivated.');
        }

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

        return back()->with('success', 'Admin deactivated successfully.');
    }
}