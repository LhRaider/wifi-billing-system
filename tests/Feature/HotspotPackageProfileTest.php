<?php

namespace Tests\Feature;

use App\Models\HotspotUser;
use App\Models\Package;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use App\Services\MikrotikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotspotPackageProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Activating a Hotspot credential must synchronize its
     * local profile with the package on its current active
     * subscription.
     *
     * This protects customers who renew or move to a different
     * package from retaining a stale RouterOS profile.
     */
    public function test_hotspot_activation_uses_current_subscription_package_profile(): void
    {
        $admin = $this->createAdmin();

        $customer = User::factory()
            ->customer()
            ->create([
                'admin_id' => $admin->id,
                'is_suspended' => false,
            ]);

        $router = $this->createRouter($admin);

        $oldPackage = $this->createPackage(
            $admin,
            'Old Package',
            'old-hotspot-profile'
        );

        $currentPackage = $this->createPackage(
            $admin,
            'Current Package',
            'current-hotspot-profile'
        );

        $subscription = Subscription::create([
            'user_id' => $customer->id,
            'package_id' => $currentPackage->id,
            'start_time' => now()->subMinute(),
            'end_time' => now()->addHour(),
            'status' => 'active',
        ]);

        $hotspotUser = HotspotUser::create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'router_id' => $router->id,
            'username' => 'profile-test-user',
            'password' => 'profile-test-password',
            'profile' => $oldPackage->router_profile,
            'status' => 'inactive',
            'router_synced' => false,
        ]);

        $this->assertSame(
            'old-hotspot-profile',
            $hotspotUser->profile
        );

        $result = app(MikrotikService::class)
            ->enableHotspotUser($hotspotUser);

        $this->assertTrue(
            $result['success'],
            $result['message']
        );

        $hotspotUser->refresh();

        $this->assertSame(
            'current-hotspot-profile',
            $hotspotUser->profile
        );
    }

    /**
     * Creating/synchronizing an active Hotspot credential must
     * also repair a stale local profile from its current
     * subscription package.
     */
    public function test_hotspot_creation_uses_current_subscription_package_profile(): void
    {
        $admin = $this->createAdmin();

        $customer = User::factory()
            ->customer()
            ->create([
                'admin_id' => $admin->id,
                'is_suspended' => false,
            ]);

        $router = $this->createRouter($admin);

        $currentPackage = $this->createPackage(
            $admin,
            'Replacement Package',
            'replacement-hotspot-profile'
        );

        $subscription = Subscription::create([
            'user_id' => $customer->id,
            'package_id' => $currentPackage->id,
            'start_time' => now()->subMinute(),
            'end_time' => now()->addHour(),
            'status' => 'active',
        ]);

        $hotspotUser = HotspotUser::create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'router_id' => $router->id,
            'username' => 'creation-profile-test-user',
            'password' => 'creation-profile-test-password',
            'profile' => 'stale-hotspot-profile',
            'status' => 'active',
            'router_synced' => false,
        ]);

        $result = app(MikrotikService::class)
            ->createHotspotUser($hotspotUser);

        $this->assertTrue(
            $result['success'],
            $result['message']
        );

        $hotspotUser->refresh();

        $this->assertSame(
            'replacement-hotspot-profile',
            $hotspotUser->profile
        );
    }

    /**
     * A package from another ISP Admin must never be accepted
     * as the profile source for a Hotspot credential.
     */
    public function test_hotspot_profile_cannot_be_derived_from_another_admin_package(): void
    {
        $adminA = $this->createAdmin();
        $adminB = $this->createAdmin();

        $customer = User::factory()
            ->customer()
            ->create([
                'admin_id' => $adminA->id,
                'is_suspended' => false,
            ]);

        $router = $this->createRouter($adminA);

        $foreignPackage = $this->createPackage(
            $adminB,
            'Foreign Package',
            'foreign-hotspot-profile'
        );

        $subscription = Subscription::create([
            'user_id' => $customer->id,
            'package_id' => $foreignPackage->id,
            'start_time' => now()->subMinute(),
            'end_time' => now()->addHour(),
            'status' => 'active',
        ]);

        $hotspotUser = HotspotUser::create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'router_id' => $router->id,
            'username' => 'tenant-profile-test-user',
            'password' => 'tenant-profile-test-password',
            'profile' => 'safe-existing-profile',
            'status' => 'inactive',
            'router_synced' => false,
        ]);

        $result = app(MikrotikService::class)
            ->enableHotspotUser($hotspotUser);

        $this->assertFalse($result['success']);

        $hotspotUser->refresh();

        $this->assertSame(
            'safe-existing-profile',
            $hotspotUser->profile
        );

        $this->assertStringContainsString(
            'subscription package',
            strtolower($result['message'])
        );
    }

    private function createAdmin(): User
    {
        return User::factory()
            ->admin()
            ->create([
                'service_status' => 'active',
                'trial_ends_at' => null,
                'service_expires_at' => now()->addDay(),
            ]);
    }

    private function createRouter(User $admin): Router
    {
        return Router::create([
            'admin_id' => $admin->id,
            'name' => 'Profile Test Router',
            'ip_address' => '192.0.2.'.random_int(1, 200),
            'username' => 'test-admin',
            'password' => 'test-router-password',
            'api_port' => 8728,
            'router_type' => 'MikroTik',
            'notes' => 'Hotspot package profile regression test.',
            'is_active' => true,
        ]);
    }

    private function createPackage(
        User $admin,
        string $name,
        string $profile
    ): Package {
        return Package::create([
            'admin_id' => $admin->id,
            'name' => $name,
            'price' => 10.00,
            'duration_minutes' => 60,
            'is_speed_unlimited' => true,
            'speed' => null,
            'download_speed' => null,
            'upload_speed' => null,
            'package_type' => 'hotspot',
            'router_profile' => $profile,
            'description' => 'Hotspot profile regression package.',
            'is_active' => true,
        ]);
    }
}