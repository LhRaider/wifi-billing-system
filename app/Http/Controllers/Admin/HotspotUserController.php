<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Models\HotspotUser;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class HotspotUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        if (auth()->user()->role === 'super_admin') {
            $hotspotUsers = HotspotUser::whereRaw('1 = 0')->paginate(10);

            return view('admin.hotspot-users.index', compact('hotspotUsers', 'search'));
        }

        $hotspotUsers = HotspotUser::with(['user', 'router'])
            ->whereHas('user', function ($query) {
                $query->where('admin_id', auth()->id());
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('profile', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.hotspot-users.index', compact('hotspotUsers', 'search'));
    }

    public function activate($id, MikrotikService $mikrotik)
    {
        $hotspotUser = $this->findAllowedHotspotUser($id);

        $result = $mikrotik->enableHotspotUser($hotspotUser);

        $hotspotUser->update([
            'status' => 'active',
            'router_synced' => $result['success'],
            'last_sync_status' => $result['success'] ? 'Success' : 'Failed',
            'last_sync_message' => $result['message'],
        ]);

        AuditHelper::log('Activate Hotspot User', 'Hotspot Users', $hotspotUser->username . ' activated');

        NotificationHelper::create(
            'Hotspot User Activated',
            $hotspotUser->username . ' activated successfully.',
            'success'
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function deactivate($id, MikrotikService $mikrotik)
    {
        $hotspotUser = $this->findAllowedHotspotUser($id);

        $result = $mikrotik->disableHotspotUser($hotspotUser);

        $hotspotUser->update([
            'status' => 'inactive',
            'router_synced' => $result['success'],
            'last_sync_status' => $result['success'] ? 'Success' : 'Failed',
            'last_sync_message' => $result['message'],
        ]);

        AuditHelper::log('Deactivate Hotspot User', 'Hotspot Users', $hotspotUser->username . ' deactivated');

        NotificationHelper::create(
            'Hotspot User Deactivated',
            $hotspotUser->username . ' deactivated.',
            'warning'
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function resetPassword($id, MikrotikService $mikrotik)
    {
        $hotspotUser = $this->findAllowedHotspotUser($id);

        $hotspotUser->update([
            'password' => rand(100000, 999999),
        ]);

        $result = $mikrotik->resetPassword($hotspotUser);

        $hotspotUser->update([
            'router_synced' => $result['success'],
            'last_sync_status' => $result['success'] ? 'Success' : 'Failed',
            'last_sync_message' => $result['message'],
        ]);

        AuditHelper::log('Reset Hotspot Password', 'Hotspot Users', $hotspotUser->username . ' password reset');

        NotificationHelper::create(
            'Password Reset',
            'Password reset for ' . $hotspotUser->username,
            'info'
        );

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function syncRouter($id, MikrotikService $mikrotik)
    {
        $hotspotUser = $this->findAllowedHotspotUser($id);

        $result = $mikrotik->createHotspotUser($hotspotUser);

        $hotspotUser->update([
            'router_synced' => $result['success'],
            'last_sync_status' => $result['success'] ? 'Success' : 'Failed',
            'last_sync_message' => $result['message'],
        ]);

        AuditHelper::log('Sync Router', 'Hotspot Users', $hotspotUser->username . ' router sync attempted');

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function findAllowedHotspotUser($id)
    {
        if (auth()->user()->role === 'super_admin') {
            abort(403, 'Super admin cannot manage ISP hotspot users directly.');
        }

        return HotspotUser::where('id', $id)
            ->whereHas('user', function ($query) {
                $query->where('admin_id', auth()->id());
            })
            ->firstOrFail();
    }
}