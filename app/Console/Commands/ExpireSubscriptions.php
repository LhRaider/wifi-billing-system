<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\HotspotUser;
use App\Helpers\NotificationHelper;
use App\Services\MikrotikService;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire customer subscriptions and disable router access';

    public function handle(MikrotikService $mikrotik): int
    {
        $expiredSubscriptions = Subscription::with(['user', 'package'])
            ->where('status', 'active')
            ->where('end_time', '<=', now())
            ->get();

        $expiredCount = 0;
        $routerDisabledCount = 0;

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update([
                'status' => 'expired',
            ]);

            $expiredCount++;

            $customer = $subscription->user;
            $package = $subscription->package;
            $adminId = $customer->admin_id ?? null;

            NotificationHelper::create(
                'Subscription Expired',
                'Subscription for '.($customer->name ?? 'customer').' has expired.',
                'warning',
                'admin',
                $adminId
            );

            $hotspotUsers = HotspotUser::where('subscription_id', $subscription->id)->get();

            foreach ($hotspotUsers as $hotspotUser) {
                if ($package && $package->package_type === 'pppoe' && $hotspotUser->router) {
                    $result = $mikrotik->disablePppoeUser($hotspotUser->username, $hotspotUser->router);
                } else {
                    $result = $mikrotik->disableHotspotUser($hotspotUser);
                }

                $hotspotUser->update([
                    'status' => 'inactive',
                    'router_synced' => $result['success'],
                    'last_sync_status' => $result['success'] ? 'Success' : 'Failed',
                    'last_sync_message' => $result['message'],
                ]);

                $routerDisabledCount++;
            }
        }

        $this->info($expiredCount.' subscription(s) expired. '.$routerDisabledCount.' router user(s) disabled.');

        return Command::SUCCESS;
    }
}