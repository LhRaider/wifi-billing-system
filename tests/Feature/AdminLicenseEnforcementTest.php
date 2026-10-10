<?php

namespace Tests\Feature;

use App\Models\HotspotUser;
use App\Models\Package;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLicenseEnforcementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A valid ISP Admin can access routes protected by software.license.
     */
    public function test_active_isp_admin_can_access_licensed_package_management(): void
    {
        $admin = $this->createActiveAdmin();

        $response = $this
            ->actingAs($admin)
            ->get('/admin/packages');

        $response->assertOk();
    }

    /**
     * An expired ISP Admin can log in but cannot access licensed ISP operations.
     */
    public function test_expired_isp_admin_is_redirected_from_licensed_operations(): void
    {
        $admin = $this->createExpiredAdmin();

        $response = $this
            ->actingAs($admin)
            ->get('/admin/packages');

        $response->assertRedirect(route('admin.license-payments.index'));

        $admin->refresh();

        $this->assertSame('expired', $admin->service_status);
    }

    /**
     * An expired ISP Admin must still be able to reach the renewal page.
     */
    public function test_expired_isp_admin_can_access_license_renewal_page(): void
    {
        $admin = $this->createExpiredAdmin();

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.license-payments.index'));

        $response->assertOk();
    }

    /**
     * A customer cannot use software-license protected service operations
     * when the owning ISP Admin's platform license has expired.
     */
    public function test_customer_under_expired_admin_is_blocked_by_software_license(): void
    {
        $admin = $this->createExpiredAdmin();

        $customer = User::factory()
            ->customer($admin)
            ->create();

        $package = $this->createPackage($admin);

        $response = $this
            ->actingAs($customer)
            ->get(route('mpesa.pay', $package));

        $response->assertRedirect(route('dashboard'));

        $response->assertSessionHas(
            'error',
            'Service is currently unavailable because your provider subscription is inactive.'
        );
    }

    /**
     * Expiring an ISP Admin license must disable the customer's router
     * credential without destroying the customer's own paid entitlement.
     */
    public function test_admin_license_expiry_preserves_customer_subscription_and_local_credential(): void
    {
        $admin = $this->createActiveAdmin([
            'service_expires_at' => now()->subMinute(),
        ]);

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess($admin, 'hotspot');

        $this->artisan('admin-licenses:expire')
            ->assertSuccessful();

        $admin->refresh();
        $subscription->refresh();
        $credential->refresh();

        $this->assertSame('expired', $admin->service_status);

        $this->assertSame('active', $subscription->status);
        $this->assertTrue($subscription->end_time->isFuture());

        $this->assertSame('active', $credential->status);
        $this->assertTrue($credential->router_synced);
        $this->assertSame('Success', $credential->last_sync_status);

        $this->assertStringContainsString(
            'disabled',
            strtolower((string) $credential->last_sync_message)
        );
    }

    /**
     * The PPPoE branch must also disable/disconnect access when the
     * owning ISP Admin license expires.
     */
    public function test_admin_license_expiry_processes_pppoe_credentials(): void
    {
        $admin = $this->createActiveAdmin([
            'service_expires_at' => now()->subMinute(),
        ]);

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess($admin, 'pppoe');

        $this->artisan('admin-licenses:expire')
            ->assertSuccessful();

        $credential->refresh();
        $subscription->refresh();

        $this->assertSame('active', $credential->status);
        $this->assertTrue($credential->router_synced);
        $this->assertSame('Success', $credential->last_sync_status);

        $this->assertStringContainsString(
            'pppoe',
            strtolower((string) $credential->last_sync_message)
        );

        $this->assertStringContainsString(
            'disconnected',
            strtolower((string) $credential->last_sync_message)
        );

        $this->assertSame('active', $subscription->status);
    }

    /**
     * When an ISP Admin has a valid license again, a customer with a
     * valid paid entitlement can have network access restored.
     */
    public function test_valid_admin_license_restores_eligible_hotspot_customer(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess($admin, 'hotspot', [
                'router_synced' => false,
                'last_sync_status' => 'Pending',
                'last_sync_message' => null,
            ]);

        $this->artisan('admin-licenses:restore-network')
            ->assertSuccessful();

        $credential->refresh();

        $this->assertSame('active', $credential->status);
        $this->assertTrue($credential->router_synced);
        $this->assertSame('Success', $credential->last_sync_status);

        $this->assertStringContainsString(
            'enabled',
            strtolower((string) $credential->last_sync_message)
        );
    }

    /**
     * PPPoE access must also be restorable after the ISP Admin has
     * a valid platform license.
     */
    public function test_valid_admin_license_restores_eligible_pppoe_customer(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess($admin, 'pppoe', [
                'router_synced' => false,
                'last_sync_status' => 'Pending',
                'last_sync_message' => null,
            ]);

        $this->artisan('admin-licenses:restore-network')
            ->assertSuccessful();

        $credential->refresh();

        $this->assertSame('active', $credential->status);
        $this->assertTrue($credential->router_synced);
        $this->assertSame('Success', $credential->last_sync_status);

        $this->assertStringContainsString(
            'pppoe',
            strtolower((string) $credential->last_sync_message)
        );

        $this->assertStringContainsString(
            'enabled',
            strtolower((string) $credential->last_sync_message)
        );
    }

    /**
     * Renewing the ISP Admin license must never restore a suspended customer.
     */
    public function test_suspended_customer_is_not_restored(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess($admin, 'hotspot', [
                'router_synced' => false,
                'last_sync_status' => 'Pending',
                'last_sync_message' => null,
            ]);

        $customer->update([
            'is_suspended' => true,
        ]);

        $this->artisan('admin-licenses:restore-network')
            ->assertSuccessful();

        $credential->refresh();

        $this->assertSame('active', $credential->status);
        $this->assertFalse($credential->router_synced);
        $this->assertSame('Skipped', $credential->last_sync_status);

        $this->assertStringContainsString(
            'suspended',
            strtolower((string) $credential->last_sync_message)
        );
    }

    /**
     * A customer's expired subscription cannot be revived merely because
     * the ISP Admin has a valid platform license.
     */
    public function test_expired_customer_subscription_is_not_restored(): void
    {
        $admin = $this->createActiveAdmin();

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess($admin, 'hotspot', [
                'router_synced' => false,
                'last_sync_status' => 'Pending',
                'last_sync_message' => null,
            ]);

        $subscription->update([
            'status' => 'expired',
            'active_user_id' => null,
            'start_time' => now()->subHours(2),
            'end_time' => now()->subHour(),
        ]);

        $this->artisan('admin-licenses:restore-network')
            ->assertSuccessful();

        $credential->refresh();
        $subscription->refresh();

        $this->assertSame('expired', $subscription->status);
        $this->assertTrue($subscription->end_time->isPast());

        $this->assertSame('active', $credential->status);
        $this->assertFalse($credential->router_synced);
        $this->assertSame('Skipped', $credential->last_sync_status);
    }

    /**
     * Processing one expired ISP Admin must never alter another tenant's
     * customer credential.
     */
    public function test_expiring_one_admin_does_not_modify_another_admins_credentials(): void
    {
        $expiredAdmin = $this->createActiveAdmin([
            'service_expires_at' => now()->subMinute(),
        ]);

        $otherAdmin = $this->createActiveAdmin();

        [$customerA, $packageA, $subscriptionA, $routerA, $credentialA] =
            $this->createCustomerAccess($expiredAdmin, 'hotspot');

        [$customerB, $packageB, $subscriptionB, $routerB, $credentialB] =
            $this->createCustomerAccess($otherAdmin, 'hotspot', [
                'router_synced' => false,
                'last_sync_status' => 'Tenant Control',
                'last_sync_message' => 'Must remain unchanged.',
            ]);

        $before = [
            'status' => $credentialB->status,
            'router_synced' => $credentialB->router_synced,
            'last_sync_status' => $credentialB->last_sync_status,
            'last_sync_message' => $credentialB->last_sync_message,
        ];

        $this->artisan('admin-licenses:expire')
            ->assertSuccessful();

        $credentialA->refresh();
        $credentialB->refresh();

        $this->assertTrue($credentialA->router_synced);
        $this->assertSame('Success', $credentialA->last_sync_status);

        $this->assertSame($before['status'], $credentialB->status);
        $this->assertSame($before['router_synced'], $credentialB->router_synced);
        $this->assertSame($before['last_sync_status'], $credentialB->last_sync_status);
        $this->assertSame($before['last_sync_message'], $credentialB->last_sync_message);

        $this->assertSame($otherAdmin->id, $customerB->admin_id);
        $this->assertSame($otherAdmin->id, $packageB->admin_id);
        $this->assertSame($otherAdmin->id, $routerB->admin_id);
    }

    /**
     * Running the expiry command repeatedly must not corrupt the ISP
     * license state or the customer's independent paid entitlement.
     */
    public function test_admin_license_expiry_command_is_idempotent_for_license_state(): void
    {
        $admin = $this->createActiveAdmin([
            'service_expires_at' => now()->subMinute(),
        ]);

        [$customer, $package, $subscription, $router, $credential] =
            $this->createCustomerAccess($admin, 'hotspot');

        $this->artisan('admin-licenses:expire')
            ->assertSuccessful();

        $admin->refresh();
        $subscription->refresh();
        $credential->refresh();

        $firstSubscriptionEnd = $subscription->end_time->copy();

        $this->assertSame('expired', $admin->service_status);
        $this->assertSame('active', $subscription->status);
        $this->assertSame('active', $credential->status);

        $this->artisan('admin-licenses:expire')
            ->assertSuccessful();

        $admin->refresh();
        $subscription->refresh();
        $credential->refresh();

        $this->assertSame('expired', $admin->service_status);
        $this->assertSame('active', $subscription->status);
        $this->assertTrue(
            $subscription->end_time->equalTo($firstSubscriptionEnd)
        );
        $this->assertSame('active', $credential->status);
    }

    /**
     * A valid trial is equivalent to a valid paid platform license for
     * software-license protected ISP operations.
     */
    public function test_valid_trial_admin_can_access_licensed_operations(): void
    {
        $admin = User::factory()->admin()->create([
            'service_status' => 'trial',
            'trial_ends_at' => now()->addDay(),
            'service_expires_at' => null,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin/packages');

        $response->assertOk();
    }

    /**
     * An expired trial must be blocked in the same way as an expired
     * paid platform license.
     */
    public function test_expired_trial_admin_is_redirected_to_renewal(): void
    {
        $admin = User::factory()->admin()->create([
            'service_status' => 'trial',
            'trial_ends_at' => now()->subMinute(),
            'service_expires_at' => null,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin/packages');

        $response->assertRedirect(
            route('admin.license-payments.index')
        );

        $this->assertSame(
            'expired',
            $admin->fresh()->service_status
        );
    }

    private function createActiveAdmin(array $attributes = []): User
    {
        return User::factory()->admin()->create(array_merge([
            'service_status' => 'active',
            'trial_ends_at' => null,
            'service_expires_at' => now()->addDay(),
        ], $attributes));
    }

    private function createExpiredAdmin(array $attributes = []): User
    {
        return User::factory()->admin()->create(array_merge([
            'service_status' => 'expired',
            'trial_ends_at' => null,
            'service_expires_at' => now()->subDay(),
        ], $attributes));
    }

    /**
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
        array $credentialAttributes = []
    ): array {
        $customer = User::factory()
            ->customer($admin)
            ->create([
                'is_suspended' => false,
            ]);

        $package = $this->createPackage($admin, $packageType);

        $router = Router::create([
            'admin_id' => $admin->id,
            'name' => 'Test Router '.$admin->id,
            'ip_address' => '192.0.2.'.(($admin->id % 200) + 1),
            'username' => 'test-admin',
            'password' => 'test-router-password',
            'api_port' => 8728,
            'router_type' => 'MikroTik',
            'notes' => 'Automated license enforcement test router.',
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

        $credential = HotspotUser::create(array_merge([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'router_id' => $router->id,
            'username' => 'license-test-'.$customer->id,
            'password' => 'temporary-test-password',
            'profile' => $package->router_profile,
            'status' => 'active',
            'router_synced' => true,
            'last_sync_status' => 'Success',
            'last_sync_message' => 'Initial test state.',
        ], $credentialAttributes));

        return [
            $customer,
            $package,
            $subscription,
            $router,
            $credential,
        ];
    }

    private function createPackage(
        User $admin,
        string $packageType = 'hotspot'
    ): Package {
        $unique = uniqid();

        return Package::create([
            'admin_id' => $admin->id,
            'name' => 'Test Package '.$unique,
            'price' => 10.00,
            'duration_minutes' => 60,
            'is_speed_unlimited' => false,
            'speed' => '5M/5M',
            'download_speed' => '5M',
            'upload_speed' => '5M',
            'package_type' => $packageType,
            'router_profile' => 'test-profile-'.$unique,
            'description' => 'Automated license enforcement test package.',
            'is_active' => true,
        ]);
    }
}