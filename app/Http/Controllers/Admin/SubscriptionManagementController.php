<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\HotspotUser;
use App\Models\Subscription;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    /**
     * Display subscriptions belonging only to customers
     * of the authenticated ISP Admin.
     */
    public function index(Request $request)
    {
        $admin = $this->currentAdmin();

        $search = trim((string) $request->query('search', ''));

        $subscriptions = Subscription::with([
            'user',
            'package',
        ])
            ->whereHas('user', function ($query) use ($admin) {
                $query
                    ->where('role', 'customer')
                    ->where('admin_id', $admin->id);
            })
            ->whereHas('package', function ($query) use ($admin) {
                $query->where('admin_id', $admin->id);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('package', function ($packageQuery) use ($search) {
                            $packageQuery->where(
                                'name',
                                'like',
                                '%'.$search.'%'
                            );
                        });
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.subscriptions.index', compact(
            'subscriptions',
            'search'
        ));
    }

    /**
     * Manually activate an existing valid subscription.
     *
     * A subscription is never marked active unless its customer's
     * MikroTik access credential can also be enabled successfully.
     */
    public function activate(
        Subscription $subscription,
        MikrotikService $mikrotik
    ) {
        $admin = $this->currentAdmin();

        $this->ensureSubscriptionBelongsToAdmin(
            $subscription,
            $admin->id
        );

        $subscription->loadMissing([
            'user',
            'package',
        ]);

        $customer = $subscription->user;
        $package = $subscription->package;

        if ($subscription->status === 'active') {
            return back()->with(
                'success',
                'This subscription is already active.'
            );
        }

        /*
         * Historical subscriptions whose service period has already ended
         * must not be reactivated. A renewal must use a new subscription.
         */
        if (!$subscription->end_time || $subscription->end_time->lte(now())) {
            return back()->with(
                'error',
                'This subscription period has already ended and cannot be reactivated. Create a new paid subscription for renewal.'
            );
        }

        /*
         * Do not activate a subscription before its scheduled start time.
         */
        if (
            $subscription->start_time &&
            $subscription->start_time->gt(now())
        ) {
            return back()->with(
                'error',
                'This subscription has not reached its start time yet.'
            );
        }

        /*
         * New access must not be activated using a package that the
         * ISP Admin has deliberately deactivated.
         */
        if (!$package->is_active) {
            return back()->with(
                'error',
                'This package is inactive. Activate the package before activating this subscription.'
            );
        }

        /*
         * A customer must not have multiple active subscriptions at once.
         *
         * The existing active subscription should first be expired before
         * another subscription is activated.
         */
        $anotherActiveSubscriptionExists = Subscription::query()
            ->where('user_id', $customer->id)
            ->where('status', 'active')
            ->where('id', '!=', $subscription->id)
            ->exists();

        if ($anotherActiveSubscriptionExists) {
            return back()->with(
                'error',
                'This customer already has another active subscription. Expire the current subscription before activating a new one.'
            );
        }

        /*
         * Each customer has one reusable MikroTik access credential.
         *
         * We intentionally locate it by user_id rather than subscription_id
         * because a customer's credential may be reused for a later renewal.
         */
        $hotspotUser = HotspotUser::with([
            'user',
            'router',
        ])
            ->where('user_id', $customer->id)
            ->first();

        if (!$hotspotUser) {
            return back()->with(
                'error',
                'This customer does not have a MikroTik access credential. Subscription activation has been blocked.'
            );
        }

        /*
         * Defense-in-depth tenant verification.
         */
        if (
            !$hotspotUser->user ||
            $hotspotUser->user->role !== 'customer' ||
            (int) $hotspotUser->user->admin_id !== (int) $admin->id
        ) {
            abort(403);
        }

        /*
         * Remember the previous subscription association so that a failed
         * synchronization cannot leave the credential attached incorrectly.
         */
        $previousSubscriptionId = $hotspotUser->subscription_id;

        /*
         * Hotspot synchronization may resolve the package through the
         * HotspotUser subscription relationship, so associate the credential
         * with the subscription being activated before synchronizing.
         */
        $hotspotUser->subscription_id = $subscription->id;
        $hotspotUser->save();

        $hotspotUser->unsetRelation('subscription');
        $hotspotUser->loadMissing([
            'subscription.package',
            'user',
            'router',
        ]);

        if ($package->package_type === 'pppoe') {
            $router = $hotspotUser->router;

            if (
                !$router ||
                (int) $router->admin_id !== (int) $admin->id ||
                !$router->is_active
            ) {
                $result = [
                    'success' => false,
                    'message' => 'No valid active PPPoE router belonging to this ISP Admin is assigned to this customer.',
                ];
            } else {
                $result = $mikrotik->enablePppoeUser(
                    $hotspotUser->username,
                    $router,
                    $package,
                    $hotspotUser->password
                );
            }
        } else {
            $result = $mikrotik->enableHotspotUser(
                $hotspotUser
            );
        }

        if (!($result['success'] ?? false)) {
            /*
             * Restore the previous credential relationship because the
             * activation never completed.
             */
            $hotspotUser->subscription_id = $previousSubscriptionId;

            $hotspotUser->router_synced = false;
            $hotspotUser->last_sync_status = 'Failed';
            $hotspotUser->last_sync_message =
                $result['message'] ?? 'Router synchronization failed.';

            $hotspotUser->save();

            return back()->with(
                'error',
                'Subscription could not be activated because MikroTik access could not be enabled.'
            );
        }

        /*
         * Only now, after successful router synchronization, do we mark
         * both the credential and subscription active.
         */
        $hotspotUser->update([
            'status' => 'active',
            'router_synced' => true,
            'last_sync_status' => 'Success',
            'last_sync_message' =>
                $result['message'] ?? 'Router synchronization completed successfully.',
        ]);

        $subscription->update([
            'status' => 'active',
        ]);

        /*
         * Subscription activation restores the customer's account if it
         * had previously been suspended.
         */
        if ($customer->is_suspended) {
            $customer->update([
                'is_suspended' => false,
            ]);
        }

        AuditHelper::log(
            'Activate Subscription',
            'Subscriptions',
            'Subscription '.$subscription->id
                .' for '.$customer->name
                .' was manually activated.'
        );

        NotificationHelper::create(
            'Subscription Activated',
            'Subscription for '.$customer->name
                .' was activated.',
            'success',
            'admin',
            $admin->id
        );

        return back()->with(
            'success',
            'Subscription activated and MikroTik access enabled successfully.'
        );
    }

    /**
     * Manually expire a subscription and disable its MikroTik access.
     */
    public function expire(
        Subscription $subscription,
        MikrotikService $mikrotik
    ) {
        $admin = $this->currentAdmin();

        $this->ensureSubscriptionBelongsToAdmin(
            $subscription,
            $admin->id
        );

        $subscription->loadMissing([
            'user',
            'package',
        ]);

        $customer = $subscription->user;
        $package = $subscription->package;

        if ($subscription->status === 'expired') {
            return back()->with(
                'success',
                'This subscription is already expired.'
            );
        }

        /*
         * Locate the customer's reusable credential.
         */
        $hotspotUser = HotspotUser::with([
            'user',
            'router',
        ])
            ->where('user_id', $customer->id)
            ->first();

        $syncFailed = false;

        /*
         * Only disable the MikroTik credential when it still belongs to
         * THIS subscription.
         *
         * This prevents an old historical subscription from disabling a
         * credential that has already been reassigned to a newer renewal.
         */
        if (
            $hotspotUser &&
            (int) $hotspotUser->subscription_id === (int) $subscription->id
        ) {
            if (
                !$hotspotUser->user ||
                $hotspotUser->user->role !== 'customer' ||
                (int) $hotspotUser->user->admin_id !== (int) $admin->id
            ) {
                abort(403);
            }

            if ($package->package_type === 'pppoe') {
                $router = $hotspotUser->router;

                if (
                    !$router ||
                    (int) $router->admin_id !== (int) $admin->id
                ) {
                    $result = [
                        'success' => false,
                        'message' => 'No valid PPPoE router belonging to this ISP Admin was found.',
                    ];
                } else {
                    $result = $mikrotik->disablePppoeUser(
                        $hotspotUser->username,
                        $router
                    );
                }
            } else {
                $result = $mikrotik->disableHotspotUser(
                    $hotspotUser
                );
            }

            $syncFailed = !($result['success'] ?? false);

            /*
             * Local access must always become inactive even if the physical
             * router is temporarily unreachable.
             */
            $hotspotUser->update([
                'status' => 'inactive',
                'router_synced' => !$syncFailed,
                'last_sync_status' => $syncFailed
                    ? 'Failed'
                    : 'Success',
                'last_sync_message' =>
                    $result['message'] ?? (
                        $syncFailed
                            ? 'Router synchronization failed.'
                            : 'Router synchronization completed successfully.'
                    ),
            ]);
        }

        /*
         * Database entitlement must expire regardless of router availability.
         */
        $subscription->update([
            'status' => 'expired',
        ]);

        AuditHelper::log(
            'Expire Subscription',
            'Subscriptions',
            'Subscription '.$subscription->id
                .' for '.$customer->name
                .' was manually expired.'
        );

        NotificationHelper::create(
            'Subscription Expired',
            'Subscription for '.$customer->name
                .' was expired.',
            'warning',
            'admin',
            $admin->id
        );

        if ($syncFailed) {
            return back()->with(
                'warning',
                'Subscription expired locally, but MikroTik access could not be confirmed as disabled. Check router synchronization.'
            );
        }

        return back()->with(
            'success',
            'Subscription expired and associated MikroTik access disabled successfully.'
        );
    }

    /**
     * Return the authenticated ISP Admin.
     */
    private function currentAdmin()
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        if ($user->role !== 'admin') {
            abort(403);
        }

        return $user;
    }

    /**
     * Confirm that both the customer and package belonging to a
     * subscription are owned by the authenticated ISP Admin.
     */
    private function ensureSubscriptionBelongsToAdmin(
        Subscription $subscription,
        int $adminId
    ): void {
        $subscription->loadMissing([
            'user',
            'package',
        ]);

        $customer = $subscription->user;
        $package = $subscription->package;

        if (
            !$customer ||
            !$package ||
            $customer->role !== 'customer' ||
            (int) $customer->admin_id !== $adminId ||
            (int) $package->admin_id !== $adminId
        ) {
            abort(403);
        }
    }
}