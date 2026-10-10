<?php

namespace App\Services;

use App\Models\HotspotUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminLicenseNetworkService
{
    public function __construct(
        private readonly MikrotikService $mikrotik
    ) {
    }

    /**
     * Restore network access after an ISP Admin obtains a valid
     * platform license.
     *
     * Customer access is restored only when the customer's own
     * entitlement is independently valid.
     */
    public function restoreEligibleCustomerAccess(User $admin): array
    {
        $summary = [
            'checked' => 0,
            'restored' => 0,
            'failed' => 0,
            'skipped' => 0,
            'invalid' => 0,
        ];

        $admin->refresh();

        /*
        |--------------------------------------------------------------------------
        | ISP Admin validation
        |--------------------------------------------------------------------------
        */

        if (
            $admin->role !== 'admin'
            || !$this->licenseIsCurrentlyValid($admin)
        ) {
            Log::warning(
                'Customer network restoration refused because ISP Admin license is not valid.',
                [
                    'admin_id' => $admin->id,
                ]
            );

            return $summary;
        }

        /*
        |--------------------------------------------------------------------------
        | Find locally active customer credentials
        |--------------------------------------------------------------------------
        |
        | HotspotUser.status remains tied to the customer's own entitlement.
        | ISP license expiration does not change it.
        |
        | Therefore only locally active credentials are candidates for
        | restoration after the ISP Admin renews.
        |
        */

        HotspotUser::query()
            ->where('status', 'active')
            ->whereHas(
                'user',
                function ($query) use ($admin) {
                    $query
                        ->where('role', 'customer')
                        ->where('admin_id', $admin->id);
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
                    &$summary
                ) {
                    foreach ($hotspotUsers as $hotspotUser) {
                        $summary['checked']++;

                        try {
                            $customer = $hotspotUser->user;
                            $subscription = $hotspotUser->subscription;
                            $package = $subscription?->package;

                            /*
                            |--------------------------------------------------------------------------
                            | Customer and tenant validation
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !$customer
                                || $customer->role !== 'customer'
                                || (int) $customer->admin_id
                                    !== (int) $admin->id
                            ) {
                                $summary['invalid']++;

                                $this->recordFailure(
                                    $hotspotUser,
                                    'Customer ownership verification failed during ISP license restoration.'
                                );

                                Log::error(
                                    'ISP license restoration found invalid customer ownership.',
                                    [
                                        'admin_id' => $admin->id,
                                        'hotspot_user_id' => $hotspotUser->id,
                                        'customer_id' => $customer?->id,
                                    ]
                                );

                                continue;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Suspended customers must never regain access
                            |--------------------------------------------------------------------------
                            */

                            if ($customer->is_suspended) {
                                $summary['skipped']++;

                                $this->recordSkipped(
                                    $hotspotUser,
                                    'Customer remains suspended. Network access was not restored.'
                                );

                                continue;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Subscription and package ownership validation
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !$subscription
                                || !$package
                                || (int) $subscription->user_id
                                    !== (int) $customer->id
                                || (int) $package->admin_id
                                    !== (int) $admin->id
                            ) {
                                $summary['invalid']++;

                                $this->recordFailure(
                                    $hotspotUser,
                                    'Subscription or package ownership verification failed during ISP license restoration.'
                                );

                                Log::error(
                                    'ISP license restoration found invalid subscription or package ownership.',
                                    [
                                        'admin_id' => $admin->id,
                                        'customer_id' => $customer->id,
                                        'hotspot_user_id' => $hotspotUser->id,
                                        'subscription_id' => $subscription?->id,
                                        'package_id' => $package?->id,
                                    ]
                                );

                                continue;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Customer entitlement validation
                            |--------------------------------------------------------------------------
                            |
                            | Every package has a finite duration.
                            | Restoration therefore requires an active subscription
                            | with both start_time and end_time and with the current
                            | time inside that entitlement window.
                            |
                            */

                            if (
                                $subscription->status !== 'active'
                                || !$subscription->start_time
                                || !$subscription->end_time
                                || now()->lessThan(
                                    $subscription->start_time
                                )
                                || now()->greaterThanOrEqualTo(
                                    $subscription->end_time
                                )
                            ) {
                                $summary['skipped']++;

                                $this->recordSkipped(
                                    $hotspotUser,
                                    'Customer subscription is not currently valid. Network access was not restored.'
                                );

                                continue;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Package validation
                            |--------------------------------------------------------------------------
                            */

                            if (!$package->is_active) {
                                $summary['skipped']++;

                                $this->recordSkipped(
                                    $hotspotUser,
                                    'Customer package is inactive. Network access was not restored.'
                                );

                                continue;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Restore PPPoE or Hotspot access
                            |--------------------------------------------------------------------------
                            */

                            if ($package->package_type === 'pppoe') {
                                $router = $hotspotUser->router;

                                if (
                                    !$router
                                    || !$router->is_active
                                    || (int) $router->admin_id
                                        !== (int) $admin->id
                                ) {
                                    $summary['failed']++;

                                    $this->recordFailure(
                                        $hotspotUser,
                                        'No valid active PPPoE router belonging to this ISP Admin was found.'
                                    );

                                    continue;
                                }

                                $result = $this->mikrotik
                                    ->enablePppoeUser(
                                        $hotspotUser->username,
                                        $router,
                                        $package,
                                        $hotspotUser->password
                                    );
                            } else {
                                $result = $this->mikrotik
                                    ->enableHotspotUser(
                                        $hotspotUser
                                    );
                            }

                            $successful = (bool) (
                                $result['success'] ?? false
                            );

                            $hotspotUser->update([
                                'router_synced' => $successful,
                                'last_sync_status' => $successful
                                    ? 'Success'
                                    : 'Failed',
                                'last_sync_message' =>
                                    $result['message']
                                    ?? (
                                        $successful
                                            ? 'Customer network access restored after ISP license renewal.'
                                            : 'Customer network access could not be restored after ISP license renewal.'
                                    ),
                            ]);

                            if ($successful) {
                                $summary['restored']++;
                            } else {
                                $summary['failed']++;

                                Log::warning(
                                    'Customer network access could not be restored after ISP license renewal.',
                                    [
                                        'admin_id' => $admin->id,
                                        'customer_id' => $customer->id,
                                        'hotspot_user_id' => $hotspotUser->id,
                                    ]
                                );
                            }
                        } catch (Throwable $e) {
                            $summary['failed']++;

                            try {
                                $this->recordFailure(
                                    $hotspotUser,
                                    'Unexpected error occurred while restoring network access after ISP license renewal.'
                                );
                            } catch (Throwable) {
                                // Preserve the original failure and continue.
                            }

                            Log::error(
                                'Customer network restoration failed after ISP license renewal.',
                                [
                                    'admin_id' => $admin->id,
                                    'hotspot_user_id' => $hotspotUser->id,
                                    'exception' => $e::class,
                                    'message' => $e->getMessage(),
                                ]
                            );
                        }
                    }
                }
            );

        Log::info(
            'ISP Admin customer network restoration completed.',
            [
                'admin_id' => $admin->id,
                'checked' => $summary['checked'],
                'restored' => $summary['restored'],
                'failed' => $summary['failed'],
                'skipped' => $summary['skipped'],
                'invalid' => $summary['invalid'],
            ]
        );

        return $summary;
    }

    /**
     * Confirm that the ISP Admin currently has a valid platform
     * trial or paid platform license.
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

    /**
     * Record a synchronization failure without changing the
     * customer's entitlement status.
     */
    private function recordFailure(
        HotspotUser $hotspotUser,
        string $message
    ): void {
        $hotspotUser->update([
            'router_synced' => false,
            'last_sync_status' => 'Failed',
            'last_sync_message' => $message,
        ]);
    }

    /**
     * Record an intentional non-restoration without changing
     * the customer's entitlement status.
     */
    private function recordSkipped(
        HotspotUser $hotspotUser,
        string $message
    ): void {
        $hotspotUser->update([
            'router_synced' => false,
            'last_sync_status' => 'Skipped',
            'last_sync_message' => $message,
        ]);
    }
}