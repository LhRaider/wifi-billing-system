<?php

namespace Tests\Feature;

use App\Models\HotspotUser;
use App\Models\Package;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAccessEnforcementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Automatic expiration must expire the customer's subscription
     * and disable the associated Hotspot credential.
     */
    public function test_automatic_expiration_disables_hotspot_access(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess(
                $admin,
                'hotspot',
                [
                    'start_time' => now()->subHours(2),
                    'end_time' => now()->subMinute(),
                ]
            );

        $this->artisan('subscriptions:expire')
            ->assertSuccessful();

        $subscription->refresh();
        $credential->refresh();

        $this->assertSame(
            'expired',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        $this->assertTrue(
            $credential->router_synced
        );

        $this->assertSame(
            'Success',
            $credential->last_sync_status
        );

        $this->assertStringContainsString(
            'disabled',
            strtolower(
                (string) $credential->last_sync_message
            )
        );
    }

    /**
     * PPPoE credentials must also be disabled when their
     * customer subscription expires automatically.
     */
    public function test_automatic_expiration_disables_pppoe_access(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess(
                $admin,
                'pppoe',
                [
                    'start_time' => now()->subHours(2),
                    'end_time' => now()->subMinute(),
                ]
            );

        $this->artisan('subscriptions:expire')
            ->assertSuccessful();

        $subscription->refresh();
        $credential->refresh();

        $this->assertSame(
            'expired',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        $this->assertTrue(
            $credential->router_synced
        );

        $this->assertSame(
            'Success',
            $credential->last_sync_status
        );

        $this->assertStringContainsString(
            'pppoe',
            strtolower(
                (string) $credential->last_sync_message
            )
        );

        $this->assertStringContainsString(
            'disabled',
            strtolower(
                (string) $credential->last_sync_message
            )
        );
    }

    /**
     * Manual subscription expiration must revoke Hotspot
     * access for the customer.
     */
    public function test_admin_can_manually_expire_subscription_and_disable_access(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess(
                $admin
            );

        $response = $this
            ->actingAs($admin)
            ->post(
                route(
                    'admin.subscriptions.expire',
                    $subscription
                )
            );

        $response->assertSessionHasNoErrors();

        $subscription->refresh();
        $credential->refresh();

        $this->assertSame(
            'expired',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        $this->assertTrue(
            $credential->router_synced
        );

        $this->assertSame(
            'Success',
            $credential->last_sync_status
        );
    }

    /**
     * Suspending a customer must suspend the active subscription
     * and revoke the customer's Hotspot access.
     */
    public function test_customer_suspension_disables_active_access(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess(
                $admin
            );

        $response = $this
            ->actingAs($admin)
            ->post(
                route(
                    'admin.customers.suspend',
                    $customer
                )
            );

        $response->assertSessionHasNoErrors();

        $customer->refresh();
        $subscription->refresh();
        $credential->refresh();

        $this->assertTrue(
            $customer->is_suspended
        );

        $this->assertSame(
            'suspended',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        $this->assertTrue(
            $credential->router_synced
        );

        $this->assertSame(
            'Success',
            $credential->last_sync_status
        );
    }

    /**
     * Customer account reactivation alone must not restore
     * internet access.
     */
    public function test_customer_account_reactivation_does_not_restore_internet(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess(
                $admin
            );

        $this->actingAs($admin)
            ->post(
                route(
                    'admin.customers.suspend',
                    $customer
                )
            );

        $customer->refresh();
        $subscription->refresh();
        $credential->refresh();

        $this->assertTrue(
            $customer->is_suspended
        );

        $this->assertSame(
            'suspended',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        $response = $this
            ->actingAs($admin)
            ->post(
                route(
                    'admin.customers.activate',
                    $customer
                )
            );

        $response->assertSessionHasNoErrors();

        $customer->refresh();
        $subscription->refresh();
        $credential->refresh();

        $this->assertFalse(
            $customer->is_suspended
        );

        /*
         * Account activation must not silently reactivate
         * the customer's subscription or router credential.
         */
        $this->assertSame(
            'suspended',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );
    }

    /**
     * Explicitly activating a still-valid suspended subscription
     * should restore the customer's Hotspot access.
     */
    public function test_valid_suspended_subscription_can_be_reactivated_and_restore_access(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess(
                $admin
            );

        $this->actingAs($admin)
            ->post(
                route(
                    'admin.customers.suspend',
                    $customer
                )
            );

        /*
         * Reactivate the account first. This intentionally
         * does not restore internet service.
         */
        $this->actingAs($admin)
            ->post(
                route(
                    'admin.customers.activate',
                    $customer
                )
            );

        $subscription->refresh();
        $credential->refresh();

        $this->assertSame(
            'suspended',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        /*
         * Explicit subscription activation is the separate
         * action that restores RouterOS access.
         */
        $response = $this
            ->actingAs($admin)
            ->post(
                route(
                    'admin.subscriptions.activate',
                    $subscription
                )
            );

        $response->assertSessionHasNoErrors();

        $customer->refresh();
        $subscription->refresh();
        $credential->refresh();

        $this->assertFalse(
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

        $this->assertTrue(
            $credential->router_synced
        );

        $this->assertSame(
            'Success',
            $credential->last_sync_status
        );

        $this->assertStringContainsString(
            'enabled',
            strtolower(
                (string) $credential->last_sync_message
            )
        );
    }

    /**
     * An ended historical subscription must never be manually
     * reactivated to restore internet service.
     */
    public function test_ended_subscription_cannot_be_reactivated(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess(
                $admin,
                'hotspot',
                [
                    'start_time' => now()->subHours(2),
                    'end_time' => now()->subMinute(),
                    'status' => 'expired',
                ],
                [
                    'status' => 'inactive',
                    'router_synced' => false,
                ]
            );

        /*
         * Expired subscriptions should not retain the active
         * subscription uniqueness marker.
         */
        $subscription->update([
            'active_user_id' => null,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(
                route(
                    'admin.subscriptions.activate',
                    $subscription
                )
            );

        $response->assertSessionHas(
            'error'
        );

        $subscription->refresh();
        $credential->refresh();

        $this->assertSame(
            'expired',
            $subscription->status
        );

        $this->assertSame(
            'inactive',
            $credential->status
        );

        $this->assertFalse(
            $credential->router_synced
        );
    }

    /**
     * An ISP Admin must never suspend a customer belonging
     * to another tenant.
     */
    public function test_admin_cannot_suspend_another_admins_customer(): void
    {
        $adminA = $this->createActiveAdmin();
        $adminB = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess(
                $adminB
            );

        $response = $this
            ->actingAs($adminA)
            ->post(
                route(
                    'admin.customers.suspend',
                    $customer
                )
            );

        $response->assertForbidden();

        $customer->refresh();
        $subscription->refresh();
        $credential->refresh();

        $this->assertFalse(
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
    }

    /**
     * Expiration of one customer's subscription must never
     * disable another customer's active credential.
     */
    public function test_subscription_expiration_does_not_disable_another_customer(): void
    {
        $admin = $this->createActiveAdmin();

        [$customerA, $packageA, $subscriptionA, $routerA, $credentialA] =
            $this->createCustomerAccess(
                $admin,
                'hotspot',
                [
                    'start_time' => now()->subHours(2),
                    'end_time' => now()->subMinute(),
                ]
            );

        [$customerB, $packageB, $subscriptionB, $routerB, $credentialB] =
            $this->createCustomerAccess(
                $admin
            );

        $before = [
            'status' => $credentialB->status,
            'router_synced' => $credentialB->router_synced,
            'last_sync_status' => $credentialB->last_sync_status,
            'last_sync_message' => $credentialB->last_sync_message,
        ];

        $this->artisan('subscriptions:expire')
            ->assertSuccessful();

        $subscriptionA->refresh();
        $credentialA->refresh();

        $subscriptionB->refresh();
        $credentialB->refresh();

        $this->assertSame(
            'expired',
            $subscriptionA->status
        );

        $this->assertSame(
            'inactive',
            $credentialA->status
        );

        $this->assertSame(
            'active',
            $subscriptionB->status
        );

        $this->assertSame(
            $before['status'],
            $credentialB->status
        );

        $this->assertSame(
            $before['router_synced'],
            $credentialB->router_synced
        );

        $this->assertSame(
            $before['last_sync_status'],
            $credentialB->last_sync_status
        );

        $this->assertSame(
            $before['last_sync_message'],
            $credentialB->last_sync_message
        );
    }

    /**
     * Create an ISP Admin with a valid platform license.
     */
    private function createActiveAdmin(
        array $attributes = []
    ): User {
        return User::factory()
            ->admin()
            ->create(
                array_merge([
                    'service_status' => 'active',
                    'trial_ends_at' => null,
                    'service_expires_at' => now()->addDay(),
                ], $attributes)
            );
    }

    /**
     * Create a complete customer subscription and RouterOS
     * credential test fixture.
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
        string $packageType = 'hotspot',
        array $subscriptionAttributes = [],
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
            'name' => 'Access Test Package '.$unique,
            'price' => 10.00,
            'duration_minutes' => 60,
            'is_speed_unlimited' => false,
            'speed' => '5M/5M',
            'download_speed' => '5M',
            'upload_speed' => '5M',
            'package_type' => $packageType,
            'router_profile' => 'access-profile-'.$unique,
            'description' =>
                'Customer access enforcement test package.',
            'is_active' => true,
        ]);

        $router = Router::create([
            'admin_id' => $admin->id,
            'name' => 'Access Test Router '.$unique,
            'ip_address' =>
                '192.0.2.'.random_int(1, 200),
            'username' => 'test-admin',
            'password' => 'test-router-password',
            'api_port' => 8728,
            'router_type' => 'MikroTik',
            'notes' =>
                'Customer access enforcement test router.',
            'is_active' => true,
        ]);

        $subscription = Subscription::create(
            array_merge([
                'user_id' => $customer->id,
                'active_user_id' => $customer->id,
                'package_id' => $package->id,
                'start_time' => now()->subMinute(),
                'end_time' => now()->addHour(),
                'status' => 'active',
            ], $subscriptionAttributes)
        );

        $credential = HotspotUser::create(
            array_merge([
                'user_id' => $customer->id,
                'subscription_id' => $subscription->id,
                'router_id' => $router->id,
                'username' =>
                    'access-test-'.$customer->id,
                'password' =>
                    'temporary-test-password',
                'profile' => $package->router_profile,
                'status' => 'active',
                'router_synced' => true,
                'last_sync_status' => 'Success',
                'last_sync_message' =>
                    'Initial customer access test state.',
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