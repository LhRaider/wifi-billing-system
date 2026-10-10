<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\AdminLicenseNetworkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RestoreAdminLicenseNetworkAccess extends Command
{
    protected $signature = 'admin-licenses:restore-network';

    protected $description =
        'Restore eligible customer router access for ISP Admins with valid platform licenses';

    public function handle(
        AdminLicenseNetworkService $networkService
    ): int {
        $adminCount = 0;

        $totals = [
            'checked' => 0,
            'restored' => 0,
            'failed' => 0,
            'skipped' => 0,
            'invalid' => 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | Find ISP Admins with a currently valid platform entitlement
        |--------------------------------------------------------------------------
        |
        | Both paid licenses and valid trials are supported.
        |
        | The network service performs another license validation immediately
        | before restoration. This protects against the license expiring between
        | this query and the actual processing of an ISP Admin.
        |
        */

        User::query()
            ->where('role', 'admin')
            ->where(function ($query) {
                $query
                    ->where(function ($trialQuery) {
                        $trialQuery
                            ->where('service_status', 'trial')
                            ->whereNotNull('trial_ends_at')
                            ->where(
                                'trial_ends_at',
                                '>=',
                                now()
                            );
                    })
                    ->orWhere(function ($activeQuery) {
                        $activeQuery
                            ->where('service_status', 'active')
                            ->whereNotNull('service_expires_at')
                            ->where(
                                'service_expires_at',
                                '>=',
                                now()
                            );
                    });
            })
            ->orderBy('id')
            ->chunkById(
                50,
                function ($admins) use (
                    $networkService,
                    &$adminCount,
                    &$totals
                ) {
                    foreach ($admins as $admin) {
                        try {
                            /*
                            |--------------------------------------------------------------------------
                            | Re-check current Admin state
                            |--------------------------------------------------------------------------
                            */

                            $admin->refresh();

                            if (!$this->licenseIsCurrentlyValid($admin)) {
                                continue;
                            }

                            $adminCount++;

                            $result =
                                $networkService
                                    ->restoreEligibleCustomerAccess(
                                        $admin
                                    );

                            foreach (array_keys($totals) as $key) {
                                $totals[$key] +=
                                    (int) ($result[$key] ?? 0);
                            }
                        } catch (Throwable $e) {
                            $totals['failed']++;

                            Log::error(
                                'Automatic ISP Admin network restoration failed.',
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
            $adminCount.
            ' valid ISP Admin license(s) checked. '.
            $totals['checked'].
            ' active customer credential(s) checked. '.
            $totals['restored'].
            ' credential(s) restored successfully. '.
            $totals['failed'].
            ' restoration failure(s). '.
            $totals['skipped'].
            ' credential(s) skipped. '.
            $totals['invalid'].
            ' invalid tenant/access record(s) detected.'
        );

        return Command::SUCCESS;
    }

    /**
     * Confirm that the ISP Admin currently has a valid trial
     * or paid platform license.
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