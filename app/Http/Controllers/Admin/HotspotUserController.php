<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\HotspotUser;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotspotUserController extends Controller
{
    /**
     * Display Hotspot users belonging only to
     * the logged-in ISP Admin's customers.
     */
    public function index(Request $request)
    {
        $adminId = $this->currentAdminId();

        $search = trim((string) $request->input('search'));

        $hotspotUsers = HotspotUser::with([
            'user',
            'router',
            'subscription.package',
        ])
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('role', 'customer')
                    ->where('admin_id', $adminId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('profile', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('router', function ($routerQuery) use ($search) {
                            $routerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('ip_address', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.hotspot-users.index',
            compact('hotspotUsers', 'search')
        );
    }

    /**
     * Enable a customer's Hotspot account.
     */
    public function activate(
        int $id,
        MikrotikService $mikrotik
    ) {
        $adminId = $this->currentAdminId();

        $hotspotUser = $this->findAllowedHotspotUser(
            $id,
            $adminId
        );

        if (!$this->hasValidSubscription($hotspotUser, $adminId)) {
            return back()->with(
                'error',
                'This customer does not have a valid active subscription. Renew or activate the subscription first.'
            );
        }

        $result = $mikrotik->enableHotspotUser($hotspotUser);

        $hotspotUser->update([
            'status' => $result['success']
                ? 'active'
                : 'inactive',

            'router_synced' => $result['success'],

            'last_sync_status' => $result['success']
                ? 'Success'
                : 'Failed',

            'last_sync_message' => $result['message'],
        ]);

        if (!$result['success']) {
            AuditHelper::log(
                'Activate Hotspot User Failed',
                'Hotspot Users',
                $hotspotUser->username . ' activation failed'
            );

            return back()->with(
                'error',
                $result['message']
            );
        }

        AuditHelper::log(
            'Activate Hotspot User',
            'Hotspot Users',
            $hotspotUser->username . ' activated'
        );

        NotificationHelper::create(
            'Hotspot User Activated',
            $hotspotUser->username . ' activated successfully.',
            'success',
            'admin',
            $adminId
        );

        return back()->with(
            'success',
            $result['message']
        );
    }

    /**
     * Disable a customer's Hotspot account.
     */
    public function deactivate(
        int $id,
        MikrotikService $mikrotik
    ) {
        $adminId = $this->currentAdminId();

        $hotspotUser = $this->findAllowedHotspotUser(
            $id,
            $adminId
        );

        $result = $mikrotik->disableHotspotUser($hotspotUser);

        /*
         * Local authorization is always removed even if
         * the router cannot currently be reached.
         */
        $hotspotUser->update([
            'status' => 'inactive',

            'router_synced' => $result['success'],

            'last_sync_status' => $result['success']
                ? 'Success'
                : 'Failed',

            'last_sync_message' => $result['message'],
        ]);

        if ($result['success']) {
            AuditHelper::log(
                'Deactivate Hotspot User',
                'Hotspot Users',
                $hotspotUser->username . ' deactivated'
            );
        } else {
            AuditHelper::log(
                'Deactivate Hotspot User Sync Failed',
                'Hotspot Users',
                $hotspotUser->username . ' marked inactive but router synchronization failed'
            );
        }

        NotificationHelper::create(
            'Hotspot User Deactivated',
            $hotspotUser->username . ' deactivated.',
            'warning',
            'admin',
            $adminId
        );

        if (!$result['success']) {
            return back()->with(
                'error',
                'The customer was marked inactive locally, but the router could not be synchronized. '
                . $result['message']
            );
        }

        return back()->with(
            'success',
            $result['message']
        );
    }

    /**
     * Generate and synchronize a new Hotspot password.
     *
     * The new password is displayed to the Admin only
     * on the next page load and is never written to logs.
     */
    public function resetPassword(
        int $id,
        MikrotikService $mikrotik
    ) {
        $adminId = $this->currentAdminId();

        $hotspotUser = $this->findAllowedHotspotUser(
            $id,
            $adminId
        );

        $newPassword = (string) random_int(
            100000,
            999999
        );

        /*
         * Keep the current password in memory until the
         * router confirms that the new password was accepted.
         */
        $oldPassword = $hotspotUser->password;

        $hotspotUser->password = $newPassword;

        $result = $mikrotik->resetPassword($hotspotUser);

        if (!$result['success']) {
            $hotspotUser->password = $oldPassword;

            $hotspotUser->router_synced = false;
            $hotspotUser->last_sync_status = 'Failed';
            $hotspotUser->last_sync_message = $result['message'];
            $hotspotUser->save();

            AuditHelper::log(
                'Reset Hotspot Password Failed',
                'Hotspot Users',
                $hotspotUser->username . ' password reset failed'
            );

            return back()->with(
                'error',
                $result['message']
            );
        }

        /*
         * Router synchronization succeeded, so save the
         * encrypted password locally.
         */
        $hotspotUser->password = $newPassword;
        $hotspotUser->router_synced = true;
        $hotspotUser->last_sync_status = 'Success';
        $hotspotUser->last_sync_message = $result['message'];
        $hotspotUser->save();

        AuditHelper::log(
            'Reset Hotspot Password',
            'Hotspot Users',
            $hotspotUser->username . ' password reset successfully'
        );

        NotificationHelper::create(
            'Hotspot Password Reset',
            'Password reset completed for '
                . $hotspotUser->username
                . '.',
            'info',
            'admin',
            $adminId
        );

        /*
         * Flash the credential once so the Admin can give
         * it to the customer. It is not included in logs
         * or persistent notification messages.
         */
        return back()->with([
            'success' => $result['message'],
            'temporary_hotspot_username' => $hotspotUser->username,
            'temporary_hotspot_password' => $newPassword,
        ]);
    }

    /**
     * Synchronize the local Hotspot state with the router.
     *
     * Synchronization must never change an intentionally
     * inactive user into an active user.
     */
    public function syncRouter(
        int $id,
        MikrotikService $mikrotik
    ) {
        $adminId = $this->currentAdminId();

        $hotspotUser = $this->findAllowedHotspotUser(
            $id,
            $adminId
        );

        if ($hotspotUser->status === 'active') {
            if (!$this->hasValidSubscription($hotspotUser, $adminId)) {
                return back()->with(
                    'error',
                    'This Hotspot user cannot be synchronized as active because the customer does not have a valid active subscription.'
                );
            }

            $result = $mikrotik->createHotspotUser(
                $hotspotUser
            );
        } else {
            /*
             * Preserve the local inactive state rather than
             * accidentally enabling the customer during sync.
             */
            $result = $mikrotik->disableHotspotUser(
                $hotspotUser
            );
        }

        $hotspotUser->update([
            'router_synced' => $result['success'],

            'last_sync_status' => $result['success']
                ? 'Success'
                : 'Failed',

            'last_sync_message' => $result['message'],
        ]);

        AuditHelper::log(
            $result['success']
                ? 'Sync Hotspot User'
                : 'Sync Hotspot User Failed',
            'Hotspot Users',
            $hotspotUser->username
                . ($result['success']
                    ? ' synchronized successfully'
                    : ' synchronization failed')
        );

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Return the current ISP Admin ID.
     */
    private function currentAdminId(): int
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        if ($user->role !== 'admin') {
            abort(
                403,
                'Only ISP Admins can manage Hotspot users.'
            );
        }

        return (int) $user->id;
    }

    /**
     * Retrieve a Hotspot user only when the current ISP
     * Admin owns the corresponding customer.
     */
    private function findAllowedHotspotUser(
        int $id,
        int $adminId
    ): HotspotUser {
        $hotspotUser = HotspotUser::with([
            'user',
            'router',
            'subscription.package',
        ])
            ->whereKey($id)
            ->whereHas('user', function ($query) use ($adminId) {
                $query->where('role', 'customer')
                    ->where('admin_id', $adminId);
            })
            ->firstOrFail();

        $customer = $hotspotUser->user;

        if (
            !$customer ||
            (int) $customer->admin_id !== $adminId
        ) {
            abort(403);
        }

        /*
         * An assigned router must belong to the same ISP Admin.
         */
        if (
            $hotspotUser->router &&
            (int) $hotspotUser->router->admin_id !== $adminId
        ) {
            abort(403);
        }

        /*
         * An attached subscription package must also belong
         * to the same ISP Admin.
         */
        if (
            $hotspotUser->subscription &&
            $hotspotUser->subscription->package &&
            (int) $hotspotUser->subscription->package->admin_id !== $adminId
        ) {
            abort(403);
        }

        return $hotspotUser;
    }

    /**
     * Confirm that the Hotspot user has an active,
     * currently valid subscription belonging to the
     * current ISP Admin.
     *
     * Every package has a finite duration. Unlimited speed
     * never removes or bypasses the subscription expiry.
     */
    private function hasValidSubscription(
        HotspotUser $hotspotUser,
        int $adminId
    ): bool {
        $subscription = $hotspotUser->subscription;

        if (!$subscription) {
            return false;
        }

        $package = $subscription->package;

        if (!$package) {
            return false;
        }

        /*
         * A suspended customer must never regain RouterOS access,
         * even if inconsistent data leaves the subscription marked active.
         */
        if (
            !$hotspotUser->user ||
            $hotspotUser->user->is_suspended
        ) {
            return false;
        }

        if ($subscription->status !== 'active') {
            return false;
        }

        if (
            (int) $hotspotUser->user->admin_id !== $adminId ||
            (int) $package->admin_id !== $adminId
        ) {
            return false;
        }

        /*
         * An active subscription must not begin in the future.
         */
        if (
            $subscription->start_time &&
            $subscription->start_time->isFuture()
        ) {
            return false;
        }

        /*
         * Every package has a finite duration, so every valid
         * active subscription must have a future end time.
         */
        if (!$subscription->end_time) {
            return false;
        }

        return $subscription->end_time->isFuture();
    }
}