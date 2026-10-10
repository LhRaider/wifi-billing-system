<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\Models\HotspotUser;
use App\Models\User;
use App\Services\MikrotikService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExpireAdminLicenses extends Command
{
    protected $signature = 'admin-licenses:expire';

    protected $description =
        'Expire ISP Admin platform licenses and disable customer router access';

    public function handle(MikrotikService $mikrotik): int
    {
        $expiredAdminCount = 0;
        $credentialCount = 0;
        $routerDisabledCount = 0;
        $routerFailedCount = 0;
        $invalidRecordCount = 0;

        /*
        |--------------------------------------------------------------------------
        | Find ISP Admins whose platform service is no longer valid
        |--------------------------------------------------------------------------
        |
        | We include Admins already marked expired so failed MikroTik shutdowns
        | are retried automatically on later scheduler runs.
        |
        */

        User::query()
            ->where('role', 'admin')
            ->where(function ($query) {
                $query
                    ->where('service_status', 'expired')
                    ->orWhere(function ($trialQuery) {
                        $trialQuery
                            ->where('service_status', 'trial')
                            ->where(function ($expiryQuery) {
                                $expiryQuery
                                    ->whereNull('trial_ends_at')
                                    ->orWhere(
                                        'trial_ends_at',
                                        '<=',
                                        now()
                                    );
                            });
                    })
                    ->orWhere(function ($activeQuery) {
                        $activeQuery
                            ->where('service_status', 'active')
                            ->where(function ($expiryQuery) {
                                $expiryQuery
                                    ->whereNull('service_expires_at')
                                    ->orWhere(
                                        'service_expires_at',
                                        '<=',
                                        now()
                                    );
                            });
                    });
            })
            ->orderBy('id')
            ->chunkById(
                50,
                function ($admins) use (
                    $mikrotik,
                    &$expiredAdminCount,
                    &$credentialCount,
                    &$routerDisabledCount,
                    &$routerFailedCount,
                    &$invalidRecordCount
                ) {
                    foreach ($admins as $admin) {
                        try {
                            /*
                            |--------------------------------------------------------------------------
                            | Re-check the Admin before taking action
                            |--------------------------------------------------------------------------
                            |
                            | A verified M-Pesa renewal may have happened between
                            | the original query and processing this Admin.
                            |
                            */

                            $admin->refresh();

                            if (
                                $admin->role !== 'admin'
                                || $this->licenseIsCurrentlyValid($admin)
                            ) {
                                continue;
                            }

                            $newlyExpired =
                                $admin->service_status !== 'expired';

                            /*
                            |--------------------------------------------------------------------------
                            | Mark the platform license expired
                            |--------------------------------------------------------------------------
                            |
                            | This does NOT alter customer subscription records.
                            | Customer billing history remains truthful.
                            |
                            */

                            if ($newlyExpired) {
                                $admin->update([
                                    'service_status' => 'expired',
                                ]);

                                $expiredAdminCount++;

                                NotificationHelper::create(
                                    'Admin Subscription Expired',
                                    'Admin '.$admin->name.
                                    ' subscription is over.',
                                    'warning',
                                    'super_admin'
                                );

                                NotificationHelper::create(
                                    'Subscription Required',
                                    'Your subscription is over. Please renew to continue providing services.',
                                    'warning',
                                    'admin',
                                    $admin->id
                                );

                                Log::warning(
                                    'ISP Admin platform license expired.',
                                    [
                                        'admin_id' => $admin->id,
                                    ]
                                );
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Disable this ISP's customer network credentials
                            |--------------------------------------------------------------------------
                            |
                            | We intentionally process only credentials that are
                            | locally active. Their customer subscription records
                            | remain unchanged because the ISP license expiration
                            | is separate from customer package expiration.
                            |
                            | Admins already marked expired are processed again
                            | every scheduler run. This means a temporary router
                            | outage does not permanently prevent shutdown:
                            | synchronization is retried automatically.
                            |
                            */

                            HotspotUser::query()
                                ->where('status', 'active')
                                ->whereHas(
                                    'user',
                                    function ($customerQuery) use ($admin) {
                                        $customerQuery
                                            ->where(
                                                'role',
                                                'customer'
                                            )
                                            ->where(
                                                'admin_id',
                                                $admin->id
                                            );
                                    }
                                )
                                ->with([
                                    'user',
                                    'subscription.package',
                                    'router',
                                ])
                                ->orderBy('id')
                                ->chunkById(
                                    100,
                                    function ($hotspotUsers) use (
                                        $admin,
                                        $mikrotik,
                                        &$credentialCount,
                                        &$routerDisabledCount,
                                        &$routerFailedCount,
                                        &$invalidRecordCount
                                    ) {
                                        foreach ($hotspotUsers as $hotspotUser) {
                                            $credentialCount++;

                                            try {
                                                $customer =
                                                    $hotspotUser->user;

                                                /*
                                                |--------------------------------------------------------------------------
                                                | Tenant verification
                                                |--------------------------------------------------------------------------
                                                */

                                                if (
                                                    !$customer
                                                    || $customer->role
                                                        !== 'customer'
                                                    || (int) $customer->admin_id
                                                        !== (int) $admin->id
                                                ) {
                                                    $invalidRecordCount++;

                                                    $hotspotUser->update([
                                                        'router_synced' => false,
                                                        'last_sync_status' =>
                                                            'Failed',
                                                        'last_sync_message' =>
                                                            'Tenant ownership verification failed during ISP license expiration.',
                                                    ]);

                                                    Log::error(
                                                        'ISP license expiration found invalid customer ownership.',
                                                        [
                                                            'admin_id' =>
                                                                $admin->id,
                                                            'hotspot_user_id' =>
                                                                $hotspotUser->id,
                                                            'customer_id' =>
                                                                $customer?->id,
                                                        ]
                                                    );

                                                    continue;
                                                }

                                                $subscription =
                                                    $hotspotUser->subscription;

                                                $package =
                                                    $subscription?->package;

                                                /*
                                                |--------------------------------------------------------------------------
                                                | Determine RouterOS access type
                                                |--------------------------------------------------------------------------
                                                |
                                                | We must know whether the
                                                | credential is Hotspot or PPPoE
                                                | before sending a router command.
                                                |
                                                */

                                                if (
                                                    !$subscription
                                                    || !$package
                                                    || (int) $subscription->user_id
                                                        !== (int) $customer->id
                                                    || (int) $package->admin_id
                                                        !== (int) $admin->id
                                                ) {
                                                    $invalidRecordCount++;

                                                    $hotspotUser->update([
                                                        'router_synced' => false,
                                                        'last_sync_status' =>
                                                            'Failed',
                                                        'last_sync_message' =>
                                                            'Subscription or package ownership verification failed during ISP license expiration.',
                                                    ]);

                                                    Log::error(
                                                        'ISP license expiration could not safely determine credential ownership or access type.',
                                                        [
                                                            'admin_id' =>
                                                                $admin->id,
                                                            'customer_id' =>
                                                                $customer->id,
                                                            'hotspot_user_id' =>
                                                                $hotspotUser->id,
                                                            'subscription_id' =>
                                                                $subscription?->id,
                                                            'package_id' =>
                                                                $package?->id,
                                                        ]
                                                    );

                                                    continue;
                                                }

                                                /*
                                                |--------------------------------------------------------------------------
                                                | Disable PPPoE or Hotspot access
                                                |--------------------------------------------------------------------------
                                                */

                                                if (
                                                    $package->package_type
                                                    === 'pppoe'
                                                ) {
                                                    $router =
                                                        $hotspotUser->router;

                                                    if (
                                                        !$router
                                                        || (int) $router->admin_id
                                                            !== (int) $admin->id
                                                    ) {
                                                        $result = [
                                                            'success' => false,
                                                            'message' =>
                                                                'No valid PPPoE router belonging to this ISP Admin was found.',
                                                        ];
                                                    } else {
                                                        $result =
                                                            $mikrotik
                                                                ->disablePppoeUser(
                                                                    $hotspotUser
                                                                        ->username,
                                                                    $router
                                                                );
                                                    }
                                                } else {
                                                    $result =
                                                        $mikrotik
                                                            ->disableHotspotUser(
                                                                $hotspotUser
                                                            );
                                                }

                                                $syncSuccessful =
                                                    (bool) (
                                                        $result['success']
                                                        ?? false
                                                    );

                                                /*
                                                |--------------------------------------------------------------------------
                                                | Preserve customer entitlement
                                                |--------------------------------------------------------------------------
                                                |
                                                | Do NOT set HotspotUser.status
                                                | inactive here.
                                                |
                                                | "active" continues to represent
                                                | the customer's own entitlement.
                                                | The ISP platform license is the
                                                | separate gate preventing service.
                                                |
                                                | This distinction allows a safe
                                                | renewal process later without
                                                | confusing ISP suspension with a
                                                | customer subscription expiration.
                                                |
                                                */

                                                $hotspotUser->update([
                                                    'router_synced' =>
                                                        $syncSuccessful,

                                                    'last_sync_status' =>
                                                        $syncSuccessful
                                                            ? 'Success'
                                                            : 'Failed',

                                                    'last_sync_message' =>
                                                        $result['message']
                                                        ?? (
                                                            $syncSuccessful
                                                                ? 'Router access disabled because the ISP platform license expired.'
                                                                : 'Router shutdown failed during ISP platform license expiration.'
                                                        ),
                                                ]);

                                                if ($syncSuccessful) {
                                                    $routerDisabledCount++;
                                                } else {
                                                    $routerFailedCount++;

                                                    Log::warning(
                                                        'Customer router access could not be disabled during ISP license expiration.',
                                                        [
                                                            'admin_id' =>
                                                                $admin->id,
                                                            'customer_id' =>
                                                                $customer->id,
                                                            'hotspot_user_id' =>
                                                                $hotspotUser->id,
                                                        ]
                                                    );
                                                }
                                            } catch (Throwable $e) {
                                                $routerFailedCount++;

                                                Log::error(
                                                    'Customer access shutdown failed during ISP license expiration.',
                                                    [
                                                        'admin_id' =>
                                                            $admin->id,
                                                        'hotspot_user_id' =>
                                                            $hotspotUser->id,
                                                        'exception' =>
                                                            $e::class,
                                                        'message' =>
                                                            $e->getMessage(),
                                                    ]
                                                );
                                            }
                                        }
                                    }
                                );
                        } catch (Throwable $e) {
                            Log::error(
                                'Automatic ISP Admin license expiration failed.',
                                [
                                    'admin_id' => $admin->id,
                                    'exception' => $e::class,
                                    'message' => $e->getMessage(),
                                ]
                            );
                        }
                    }
                }
            );

        $this->info(
            $expiredAdminCount.
            ' ISP Admin license(s) newly expired. '.
            $credentialCount.
            ' active customer credential(s) checked. '.
            $routerDisabledCount.
            ' router credential(s) disabled/disconnected successfully. '.
            $routerFailedCount.
            ' router synchronization/error(s) recorded. '.
            $invalidRecordCount.
            ' invalid tenant/access record(s) detected.'
        );

        return Command::SUCCESS;
    }

    /**
     * Determine whether this ISP Admin currently has a valid
     * platform trial or paid platform license.
     */
    private function licenseIsCurrentlyValid(User $admin): bool
    {
        if ($admin->role !== 'admin') {
            return false;
        }

        $trialActive =
            $admin->service_status === 'trial'
            && $admin->trial_ends_at
            && now()->lessThanOrEqualTo(
                $admin->trial_ends_at
            );

        $subscriptionActive =
            $admin->service_status === 'active'
            && $admin->service_expires_at
            && now()->lessThanOrEqualTo(
                $admin->service_expires_at
            );

        return $trialActive || $subscriptionActive;
    }
}