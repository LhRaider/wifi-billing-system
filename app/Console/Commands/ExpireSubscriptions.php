<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\Models\HotspotUser;
use App\Models\Subscription;
use App\Services\MikrotikService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire ended customer subscriptions and disable associated router access';

    public function handle(MikrotikService $mikrotik): int
    {
        $expiredCount = 0;
        $routerDisabledCount = 0;
        $routerFailedCount = 0;
        $invalidRecordCount = 0;

        /*
         * Process subscriptions in batches so the command remains safe
         * when the database grows.
         */
        Subscription::query()
            ->where('status', 'active')
            ->where('end_time', '<=', now())
            ->orderBy('id')
            ->chunkById(100, function ($subscriptions) use (
                $mikrotik,
                &$expiredCount,
                &$routerDisabledCount,
                &$routerFailedCount,
                &$invalidRecordCount
            ) {
                foreach ($subscriptions as $subscription) {
                    try {
                        /*
                         * Atomically claim this subscription for expiration.
                         *
                         * If another scheduler process already expired it,
                         * affected rows will be zero and we do nothing else.
                         */
                        $updated = Subscription::query()
                            ->where('id', $subscription->id)
                            ->where('status', 'active')
                            ->where('end_time', '<=', now())
                            ->update([
                                'status' => 'expired',
                            ]);

                        if ($updated !== 1) {
                            continue;
                        }

                        $expiredCount++;

                        /*
                         * Reload relationships after successfully claiming
                         * the subscription.
                         */
                        $subscription->refresh();

                        $subscription->loadMissing([
                            'user',
                            'package',
                        ]);

                        $customer = $subscription->user;
                        $package = $subscription->package;

                        /*
                         * The billing entitlement stays expired even if the
                         * associated data is incomplete or corrupted.
                         *
                         * Router commands, however, must never be sent until
                         * complete tenant ownership has been verified.
                         */
                        if (
                            !$customer ||
                            !$package ||
                            $customer->role !== 'customer' ||
                            !$customer->admin_id ||
                            !$package->admin_id ||
                            (int) $package->admin_id !== (int) $customer->admin_id
                        ) {
                            $invalidRecordCount++;

                            Log::error(
                                'Expired subscription has invalid tenant ownership.',
                                [
                                    'subscription_id' => $subscription->id,
                                    'customer_id' => $customer?->id,
                                    'customer_admin_id' => $customer?->admin_id,
                                    'package_id' => $package?->id,
                                    'package_admin_id' => $package?->admin_id,
                                ]
                            );

                            continue;
                        }

                        /*
                         * Notify only after tenant ownership has been safely
                         * established.
                         */
                        NotificationHelper::create(
                            'Subscription Expired',
                            'Subscription for '.$customer->name.' has expired.',
                            'warning',
                            'admin',
                            $customer->admin_id
                        );

                        /*
                         * Each customer has at most one reusable access
                         * credential.
                         *
                         * Only disable it when it is still associated with
                         * THIS subscription. If it has already been moved to
                         * a newer subscription, the newer service must not be
                         * disconnected by this historical expiration.
                         */
                        $hotspotUser = HotspotUser::with([
                            'user',
                            'router',
                        ])
                            ->where(
                                'subscription_id',
                                $subscription->id
                            )
                            ->first();

                        if (!$hotspotUser) {
                            continue;
                        }

                        /*
                         * Verify that the credential actually belongs to the
                         * customer and ISP Admin whose subscription expired.
                         */
                        if (
                            !$hotspotUser->user ||
                            $hotspotUser->user->role !== 'customer' ||
                            (int) $hotspotUser->user->id !== (int) $customer->id ||
                            (int) $hotspotUser->user->admin_id !== (int) $customer->admin_id
                        ) {
                            $hotspotUser->update([
                                'status' => 'inactive',
                                'router_synced' => false,
                                'last_sync_status' => 'Failed',
                                'last_sync_message' =>
                                    'Tenant ownership verification failed during automatic expiration.',
                            ]);

                            $routerFailedCount++;

                            Log::error(
                                'Router credential tenant verification failed during subscription expiration.',
                                [
                                    'subscription_id' => $subscription->id,
                                    'hotspot_user_id' => $hotspotUser->id,
                                    'customer_id' => $customer->id,
                                ]
                            );

                            continue;
                        }

                        /*
                         * PPPoE requires an explicitly assigned router.
                         */
                        if ($package->package_type === 'pppoe') {
                            $router = $hotspotUser->router;

                            if (
                                !$router ||
                                (int) $router->admin_id !== (int) $customer->admin_id
                            ) {
                                $result = [
                                    'success' => false,
                                    'message' =>
                                        'No valid PPPoE router belonging to this ISP Admin was found.',
                                ];
                            } else {
                                $result = $mikrotik->disablePppoeUser(
                                    $hotspotUser->username,
                                    $router
                                );
                            }
                        } else {
                            /*
                             * Hotspot disable uses the tenant-aware router
                             * selection inside MikrotikService.
                             */
                            $result = $mikrotik->disableHotspotUser(
                                $hotspotUser
                            );
                        }

                        $syncSuccessful = (bool) (
                            $result['success'] ?? false
                        );

                        /*
                         * Local access must always become inactive once the
                         * billing entitlement has expired, even if the router
                         * is temporarily unreachable.
                         */
                        $hotspotUser->update([
                            'status' => 'inactive',

                            'router_synced' => $syncSuccessful,

                            'last_sync_status' => $syncSuccessful
                                ? 'Success'
                                : 'Failed',

                            'last_sync_message' =>
                                $result['message']
                                ?? (
                                    $syncSuccessful
                                        ? 'Router access disabled successfully.'
                                        : 'Router synchronization failed during automatic expiration.'
                                ),
                        ]);

                        if ($syncSuccessful) {
                            $routerDisabledCount++;
                        } else {
                            $routerFailedCount++;
                        }
                    } catch (Throwable $e) {
                        /*
                         * A problem with one record must never prevent other
                         * expired subscriptions from being processed.
                         *
                         * Detailed exception information stays in the server
                         * log and is never exposed to customers or Admins.
                         */
                        Log::error(
                            'Automatic subscription expiration failed.',
                            [
                                'subscription_id' => $subscription->id,
                                'exception' => $e::class,
                                'message' => $e->getMessage(),
                            ]
                        );

                        $routerFailedCount++;
                    }
                }
            });

        $this->info(
            $expiredCount.' subscription(s) expired. '
            .$routerDisabledCount.' router user(s) disabled successfully. '
            .$routerFailedCount.' router synchronization/error(s) recorded. '
            .$invalidRecordCount.' invalid tenant record(s) detected.'
        );

        return Command::SUCCESS;
    }
}