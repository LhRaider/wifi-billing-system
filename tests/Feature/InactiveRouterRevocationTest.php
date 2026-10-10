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

class InactiveRouterRevocationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Revocation must still target the Hotspot user's assigned
     * router after that router has been marked inactive locally.
     *
     * Router inactivity must prevent new activation, but it must
     * not prevent the application from attempting to revoke
     * credentials that already belong to that router.
     */
    public function test_hotspot_access_can_be_revoked_from_assigned_inactive_router(): void
    {
        $admin = $this->createActiveAdmin();

        [
            $customer,
            $package,
            $subscription,
            $router,
            $credential,
        ] = $this->createCustomerAccess($admin);

        /*
         * The router may be taken out of service in the
         * application while RouterOS is still physically
         * running and the customer's credential still exists.
         */
        $router->update([
            'is_active' => false,
        ]);

        $router->refresh();

        $this->assertFalse(
            $router->is_active
        );

        $this->assertSame(
            $router->id,
            $credential->router_id
        );

        /*
         * Revocation must use the assigned router rather than
         * requiring that router to remain active locally.
         *
         * In simulation mode, success proves that the service
         * resolved the assigned inactive router and reached the
         * revocation path.
         */
        $result = app(MikrotikService::class)
            ->disableHotspotUser($credential);

        $this->assertTrue(
            $result['success'],
            $result['message']
        );

        $this->assertStringContainsString(
            'disabled',
            strtolower($result['message'])
        );
    }

    /**
     * An inactive router must still be rejected for new or
     * restored Hotspot access.
     *
     * This ensures that fixing revocation does not weaken the
     * existing activation protection.
     */
    public function test_inactive_router_cannot_be_used_to_enable_hotspot_access(): void
    {
        $admin = $this->createActiveAdmin();

        [
            $customer,
            $package,
            $subscription,
            $router,
            $credential,
        ] = $this->createCustomerAccess(
            $admin,
            [
                'status' => 'inactive',
                'router_synced' => false,
                'last_sync_status' => 'Not Synced',
            ]
        );

        $router->update([
            'is_active' => false,
        ]);

        $router->refresh();

        $this->assertFalse(
            $router->is_active
        );

        $result = app(MikrotikService::class)
            ->enableHotspotUser($credential);

        $this->assertFalse(
            $result['success']
        );

        $this->assertStringContainsString(
            'no active router',
            strtolower($result['message'])
        );
    }

    /**
     * Revocation must never fall back to another active router
     * when the credential already belongs to an assigned router.
     *
     * The router that owns the credential is the only correct
     * revocation target.
     */
    public function test_revocation_uses_assigned_inactive_router_instead_of_another_active_router(): void
    {
        $admin = $this->createActiveAdmin();

        [
            $customer,
            $package,
            $subscription,
            $assignedRouter,
            $credential,
        ] = $this->createCustomerAccess($admin);

        /*
         * Add another active router belonging to the same ISP
         * Admin. Revocation must not silently move to this router.
         */
        $otherRouter = Router::create([
            'admin_id' => $admin->id,
            'name' => 'Other Active Router',
            'ip_address' => '192.0.2.250',
            'username' => 'test-admin',
            'password' => 'test-router-password',
            'api_port' => 8728,
            'router_type' => 'MikroTik',
            'notes' =>
                'Alternative router for revocation protection test.',
            'is_active' => true,
        ]);

        $assignedRouter->update([
            'is_active' => false,
        ]);

        $assignedRouter->refresh();
        $credential->refresh();

        $this->assertFalse(
            $assignedRouter->is_active
        );

        $this->assertTrue(
            $otherRouter->is_active
        );

        $this->assertSame(
            $assignedRouter->id,
            $credential->router_id
        );

        $result = app(MikrotikService::class)
            ->disableHotspotUser($credential);

        $this->assertTrue(
            $result['success'],
            $result['message']
        );

        /*
         * Revocation must not rewrite the credential's router
         * assignment to the alternative active router.
         */
        $credential->refresh();

        $this->assertSame(
            $assignedRouter->id,
            $credential->router_id
        );
    }

    /**
     * Create an ISP Admin with a valid platform license.
     */
    private function createActiveAdmin(): User
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
     * Create a customer with a valid Hotspot subscription,
     * assigned router, and RouterOS credential.
     *
     * @return array{
     *     0: User,
     *     1: Package,
     *     2: Subscription,
     *     3: Router,
     *     4: HotspotUser
     * }
     */
    private function createCustomerAccess(
        User $admin,
        array $credentialAttributes = []
    ): array {
        $customer = User::factory()
            ->customer($admin)
            ->create([
                'is_suspended' => false,
            ]);

        $unique = uniqid();

        $package = Package::create([
            'admin_id' => $admin->id,
            'name' =>
                'Inactive Router Test Package '.$unique,
            'price' => 10.00,
            'duration_minutes' => 60,
            'is_speed_unlimited' => false,
            'speed' => '5M/5M',
            'download_speed' => '5M',
            'upload_speed' => '5M',
            'package_type' => 'hotspot',
            'router_profile' =>
                'inactive-router-profile-'.$unique,
            'description' =>
                'Inactive router revocation regression test package.',
            'is_active' => true,
        ]);

        $router = Router::create([
            'admin_id' => $admin->id,
            'name' =>
                'Inactive Router Test '.$unique,
            'ip_address' =>
                '192.0.2.'.random_int(1, 200),
            'username' => 'test-admin',
            'password' => 'test-router-password',
            'api_port' => 8728,
            'router_type' => 'MikroTik',
            'notes' =>
                'Inactive router revocation regression test.',
            'is_active' => true,
        ]);

        $subscription = Subscription::create([
            'user_id' => $customer->id,
            'active_user_id' => $customer->id,
            'package_id' => $package->id,
            'start_time' => now()->subMinute(),
            'end_time' => now()->addHour(),
            'status' => 'active',
        ]);

        $credential = HotspotUser::create(
            array_merge([
                'user_id' => $customer->id,
                'subscription_id' => $subscription->id,
                'router_id' => $router->id,
                'username' =>
                    'inactive-router-test-'.$customer->id,
                'password' =>
                    'temporary-test-password',
                'profile' => $package->router_profile,
                'status' => 'active',
                'router_synced' => true,
                'last_sync_status' => 'Success',
                'last_sync_message' =>
                    'Initial inactive router revocation test state.',
            ], $credentialAttributes)
        );

        return [
            $customer,
            $package,
            $subscription,
            $router,
            $credential,
        ];
    }
}