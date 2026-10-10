<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Router;
use App\Models\User;
use App\Services\MikrotikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageSpeedEnforcementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A limited-speed Hotspot package must synchronize its
     * configured upload/download limits to RouterOS.
     */
    public function test_limited_hotspot_package_applies_rate_limit(): void
    {
        $admin = $this->createAdmin();

        $router = $this->createRouter(
            $admin,
            'Limited Hotspot Router'
        );

        $package = $this->createPackage(
            $admin,
            [
                'name' => 'Limited Hotspot Package',
                'package_type' => 'hotspot',
                'router_profile' => 'limited-hotspot-profile',
                'is_speed_unlimited' => false,
                'upload_speed' => '5M',
                'download_speed' => '10M',
                'speed' => '10M/5M',
            ]
        );

        $result = app(MikrotikService::class)
            ->syncPackageProfile(
                $package,
                $router
            );

        $this->assertTrue(
            $result['success'],
            $result['message']
        );

        /*
         * MikroTikService builds RouterOS rate-limit values
         * using upload/download order.
         */
        $this->assertStringContainsString(
            '5M/10M',
            $result['message']
        );
    }

    /**
     * An unlimited-speed Hotspot package must synchronize
     * without a configured bandwidth cap.
     */
    public function test_unlimited_hotspot_package_has_no_rate_limit(): void
    {
        $admin = $this->createAdmin();

        $router = $this->createRouter(
            $admin,
            'Unlimited Hotspot Router'
        );

        $package = $this->createPackage(
            $admin,
            [
                'name' => 'Unlimited Hotspot Package',
                'package_type' => 'hotspot',
                'router_profile' => 'unlimited-hotspot-profile',
                'is_speed_unlimited' => true,
                'upload_speed' => null,
                'download_speed' => null,
                'speed' => null,
            ]
        );

        $result = app(MikrotikService::class)
            ->syncPackageProfile(
                $package,
                $router
            );

        $this->assertTrue(
            $result['success'],
            $result['message']
        );

        $this->assertStringContainsString(
            'Unlimited',
            $result['message']
        );

        $this->assertStringNotContainsString(
            '/',
            $result['message']
        );
    }

    /**
     * A limited-speed PPPoE package must synchronize its
     * configured upload/download limits to RouterOS.
     */
    public function test_limited_pppoe_package_applies_rate_limit(): void
    {
        $admin = $this->createAdmin();

        $router = $this->createRouter(
            $admin,
            'Limited PPPoE Router'
        );

        $package = $this->createPackage(
            $admin,
            [
                'name' => 'Limited PPPoE Package',
                'package_type' => 'pppoe',
                'router_profile' => 'limited-pppoe-profile',
                'is_speed_unlimited' => false,
                'upload_speed' => '3M',
                'download_speed' => '8M',
                'speed' => '8M/3M',
            ]
        );

        $result = app(MikrotikService::class)
            ->syncPackageProfile(
                $package,
                $router
            );

        $this->assertTrue(
            $result['success'],
            $result['message']
        );

        $this->assertStringContainsString(
            '3M/8M',
            $result['message']
        );
    }

    /**
     * An unlimited-speed PPPoE package must synchronize
     * without a configured bandwidth cap.
     */
    public function test_unlimited_pppoe_package_has_no_rate_limit(): void
    {
        $admin = $this->createAdmin();

        $router = $this->createRouter(
            $admin,
            'Unlimited PPPoE Router'
        );

        $package = $this->createPackage(
            $admin,
            [
                'name' => 'Unlimited PPPoE Package',
                'package_type' => 'pppoe',
                'router_profile' => 'unlimited-pppoe-profile',
                'is_speed_unlimited' => true,
                'upload_speed' => null,
                'download_speed' => null,
                'speed' => null,
            ]
        );

        $result = app(MikrotikService::class)
            ->syncPackageProfile(
                $package,
                $router
            );

        $this->assertTrue(
            $result['success'],
            $result['message']
        );

        $this->assertStringContainsString(
            'Unlimited',
            $result['message']
        );

        $this->assertStringNotContainsString(
            '/',
            $result['message']
        );
    }

    /**
     * A package belonging to Admin A must never be
     * synchronized to Admin B's router.
     */
    public function test_package_cannot_be_synced_to_another_admin_router(): void
    {
        $adminA = $this->createAdmin();
        $adminB = $this->createAdmin();

        $routerB = $this->createRouter(
            $adminB,
            'Admin B Router'
        );

        $packageA = $this->createPackage(
            $adminA,
            [
                'name' => 'Admin A Package',
                'package_type' => 'hotspot',
                'router_profile' => 'admin-a-profile',
                'is_speed_unlimited' => false,
                'upload_speed' => '5M',
                'download_speed' => '10M',
                'speed' => '10M/5M',
            ]
        );

        $result = app(MikrotikService::class)
            ->syncPackageProfile(
                $packageA,
                $routerB
            );

        $this->assertFalse(
            $result['success']
        );

        $this->assertStringContainsString(
            'different Admins',
            $result['message']
        );
    }

    /**
     * An inactive router must not receive package profile
     * synchronization.
     */
    public function test_package_cannot_be_synced_to_inactive_router(): void
    {
        $admin = $this->createAdmin();

        $router = $this->createRouter(
            $admin,
            'Inactive Speed Router'
        );

        $router->update([
            'is_active' => false,
        ]);

        $router->refresh();

        $package = $this->createPackage(
            $admin,
            [
                'name' => 'Inactive Router Package',
                'package_type' => 'hotspot',
                'router_profile' => 'inactive-router-profile',
                'is_speed_unlimited' => false,
                'upload_speed' => '5M',
                'download_speed' => '10M',
                'speed' => '10M/5M',
            ]
        );

        $this->assertFalse(
            $router->is_active
        );

        $result = app(MikrotikService::class)
            ->syncPackageProfile(
                $package,
                $router
            );

        $this->assertFalse(
            $result['success']
        );

        $this->assertStringContainsString(
            'Router is inactive',
            $result['message']
        );
    }

    /**
     * Create an ISP Admin for package speed tests.
     */
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

    /**
     * Create an active router owned by the supplied Admin.
     */
    private function createRouter(
        User $admin,
        string $name
    ): Router {
        return Router::create([
            'admin_id' => $admin->id,
            'name' => $name,
            'ip_address' =>
                '192.0.2.'.random_int(1, 200),
            'username' => 'test-admin',
            'password' => 'test-router-password',
            'api_port' => 8728,
            'router_type' => 'MikroTik',
            'notes' =>
                'Package speed enforcement test router.',
            'is_active' => true,
        ]);
    }

    /**
     * Create a package owned by the supplied Admin.
     */
    private function createPackage(
        User $admin,
        array $attributes
    ): Package {
        return Package::create(
            array_merge([
                'admin_id' => $admin->id,
                'name' => 'Speed Test Package',
                'price' => 10.00,
                'duration_minutes' => 60,
                'is_speed_unlimited' => false,
                'speed' => '10M/5M',
                'download_speed' => '10M',
                'upload_speed' => '5M',
                'package_type' => 'hotspot',
                'router_profile' =>
                    'speed-test-profile-'.uniqid(),
                'description' =>
                    'Package speed enforcement regression test.',
                'is_active' => true,
            ], $attributes)
        );
    }
}