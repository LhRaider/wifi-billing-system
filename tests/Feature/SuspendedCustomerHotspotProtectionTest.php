<?php

namespace Tests\Feature;

use App\Models\HotspotUser;
use App\Models\Package;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuspendedCustomerHotspotProtectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A suspended customer must never be directly re-enabled
     * through Hotspot activation even if inconsistent database
     * state leaves the subscription marked active.
     */
    public function test_suspended_customer_cannot_be_directly_activated_with_active_subscription(): void
    {
        $admin = $this->createActiveAdmin();

        [
            $customer,
            $package,
            $subscription,
            $router,
            $credential,
        ] = $this->createSuspendedCustomerAccess($admin);

        /*
         * Deliberately create the inconsistent state that this
         * regression test protects against:
         *
         * Customer: suspended
         * Subscription: active and unexpired
         * Credential: inactive
         */
        $this->assertTrue(
            $customer->is_suspended
        );

        $this->assertSame(
            'active',
            $subscription->status
        );

        $this->assertTrue(
            $subscription->end_time->isFuture()
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        /*
         * Attempt to bypass suspension by directly activating
         * the Hotspot credential.
         */
        $response = $this
            ->actingAs($admin)
            ->post(
                route(
                    'admin.hotspot-users.activate',
                    $credential
                )
            );

        $response->assertSessionHas(
            'error'
        );

        $customer->refresh();
        $subscription->refresh();
        $credential->refresh();

        /*
         * Direct Hotspot activation must not change any of
         * the protected access state.
         */
        $this->assertTrue(
            $customer->is_suspended
        );

        $this->assertSame(
            'active',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        $this->assertFalse(
            $credential->router_synced
        );

        $this->assertSame(
            'Not Synced',
            $credential->last_sync_status
        );
    }

    /**
     * Router synchronization must also refuse to recreate or
     * enable an active Hotspot credential for a suspended
     * customer with an apparently valid subscription.
     */
    public function test_suspended_customer_cannot_be_reenabled_through_router_sync(): void
    {
        $admin = $this->createActiveAdmin();

        [
            $customer,
            $package,
            $subscription,
            $router,
            $credential,
        ] = $this->createSuspendedCustomerAccess(
            $admin,
            [
                /*
                 * Simulate inconsistent local state where the
                 * credential is marked active while the customer
                 * account itself remains suspended.
                 */
                'status' => 'active',
            ]
        );

        $this->assertTrue(
            $customer->is_suspended
        );

        $this->assertSame(
            'active',
            $subscription->status
        );

        $this->assertSame(
            'active',
            $credential->status
        );

        /*
         * Without the suspension protection, syncRouter()
         * could call createHotspotUser() and recreate/enable
         * the RouterOS credential.
         */
        $response = $this
            ->actingAs($admin)
            ->post(
                route(
                    'admin.hotspot-users.sync-router',
                    $credential
                )
            );

        $response->assertSessionHas(
            'error'
        );

        $customer->refresh();
        $subscription->refresh();
        $credential->refresh();

        $this->assertTrue(
            $customer->is_suspended
        );

        $this->assertSame(
            'active',
            $subscription->status
        );

        /*
         * The important protection here is that synchronization
         * was rejected before RouterOS enable/create logic ran.
         * Therefore the local record is left unchanged.
         */
        $this->assertSame(
            'active',
            $credential->status
        );

        $this->assertFalse(
            $credential->router_synced
        );

        $this->assertSame(
            'Not Synced',
            $credential->last_sync_status
        );
    }

    /**
     * Create an ISP Admin with a currently valid
     * platform license.
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
     * Create deliberately inconsistent access state so the
     * controller's defense-in-depth protection can be tested.
     *
     * The customer is suspended while the subscription remains
     * active and within its valid time period.
     *
     * @return array{
     *     0: User,
     *     1: Package,
     *     2: Subscription,
     *     3: Router,
     *     4: HotspotUser
     * }
     */
    private function createSuspendedCustomerAccess(
        User $admin,
        array $credentialAttributes = []
    ): array {
        $customer = User::factory()
            ->customer($admin)
            ->create([
                'is_suspended' => true,
            ]);

        $unique = uniqid();

        $package = Package::create([
            'admin_id' => $admin->id,
            'name' =>
                'Suspension Protection Package '.$unique,
            'price' => 10.00,
            'duration_minutes' => 60,
            'is_speed_unlimited' => false,
            'speed' => '5M/5M',
            'download_speed' => '5M',
            'upload_speed' => '5M',
            'package_type' => 'hotspot',
            'router_profile' =>
                'suspension-protection-'.$unique,
            'description' =>
                'Suspended customer Hotspot protection test package.',
            'is_active' => true,
        ]);

        $router = Router::create([
            'admin_id' => $admin->id,
            'name' =>
                'Suspension Protection Router '.$unique,
            'ip_address' =>
                '192.0.2.'.random_int(1, 200),
            'username' => 'test-admin',
            'password' => 'test-router-password',
            'api_port' => 8728,
            'router_type' => 'MikroTik',
            'notes' =>
                'Suspended customer Hotspot protection test router.',
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
                    'suspended-test-'.$customer->id,
                'password' =>
                    'temporary-test-password',
                'profile' => $package->router_profile,
                'status' => 'inactive',
                'router_synced' => false,
                'last_sync_status' => 'Not Synced',
                'last_sync_message' =>
                    'Suspended customer protection test state.',
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