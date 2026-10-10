<?php

namespace Tests\Feature;

use App\Models\HotspotUser;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ApplicationSecurityAntiBypassTest extends TestCase
{
    use RefreshDatabase;

    private User $adminA;
    private User $adminB;
    private User $customerA;
    private User $customerB;
    private Package $packageA;
    private Package $packageB;
    private Router $routerA;
    private Router $routerB;
    private Subscription $subscriptionA;
    private Subscription $subscriptionB;
    private HotspotUser $credentialA;
    private HotspotUser $credentialB;
    private Payment $paymentA;
    private Payment $paymentB;
    private Notification $notificationA;
    private Notification $notificationB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminA = $this->makeAdmin('Security Admin A', 'security-admin-a@example.test');
        $this->adminB = $this->makeAdmin('Security Admin B', 'security-admin-b@example.test');

        $this->customerA = $this->makeCustomer(
            $this->adminA,
            'Security Customer A',
            'security-customer-a@example.test'
        );

        $this->customerB = $this->makeCustomer(
            $this->adminB,
            'Security Customer B',
            'security-customer-b@example.test'
        );

        $this->packageA = $this->makePackage($this->adminA, 'Security Package A', 'security-a');
        $this->packageB = $this->makePackage($this->adminB, 'Security Package B', 'security-b');

        $this->routerA = $this->makeRouter($this->adminA, 'Security Router A', '10.10.10.1');
        $this->routerB = $this->makeRouter($this->adminB, 'Security Router B', '10.20.20.1');

        $this->subscriptionA = $this->makeSubscription(
            $this->customerA,
            $this->packageA
        );

        $this->subscriptionB = $this->makeSubscription(
            $this->customerB,
            $this->packageB
        );

        $this->credentialA = $this->makeCredential(
            $this->customerA,
            $this->subscriptionA,
            $this->routerA,
            'security-user-a'
        );

        $this->credentialB = $this->makeCredential(
            $this->customerB,
            $this->subscriptionB,
            $this->routerB,
            'security-user-b'
        );

        $this->paymentA = $this->makePayment(
            $this->customerA,
            $this->packageA,
            'SECURITYA001'
        );

        $this->paymentB = $this->makePayment(
            $this->customerB,
            $this->packageB,
            'SECURITYB001'
        );

        $this->notificationA = $this->makeNotification(
            $this->adminA,
            'Security Notification A'
        );

        $this->notificationB = $this->makeNotification(
            $this->adminB,
            'Security Notification B'
        );
    }

    public function test_guest_cannot_open_protected_admin_routes(): void
    {
        $this->get(route('admin.customers.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.packages.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.routers.index'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.subscriptions.index'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_cannot_enter_isp_admin_management_routes(): void
    {
        $this->actingAs($this->customerA)
            ->get(route('admin.customers.index'))
            ->assertForbidden();

        $this->actingAs($this->customerA)
            ->get(route('admin.packages.index'))
            ->assertForbidden();

        $this->actingAs($this->customerA)
            ->get(route('admin.routers.index'))
            ->assertForbidden();

        $this->actingAs($this->customerA)
            ->get(route('admin.subscriptions.index'))
            ->assertForbidden();
    }

    public function test_isp_admin_cannot_enter_super_admin_routes(): void
    {
        $this->actingAs($this->adminA)
            ->get(route('admin.admin-users.index'))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }

    public function test_admin_cannot_view_or_change_another_tenants_customer(): void
    {
        $originalSuspended = (bool) $this->customerB->is_suspended;

        $this->actingAs($this->adminA)
            ->get(route('admin.customers.show', $this->customerB))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->post(route('admin.customers.suspend', $this->customerB))
            ->assertForbidden();

        $this->assertSame(
            $originalSuspended,
            (bool) $this->customerB->fresh()->is_suspended
        );

        $this->actingAs($this->adminA)
            ->post(route('admin.customers.activate', $this->customerB))
            ->assertForbidden();

        $this->assertSame(
            $originalSuspended,
            (bool) $this->customerB->fresh()->is_suspended
        );
    }

    public function test_admin_cannot_read_or_mutate_another_tenants_package(): void
    {
        $before = $this->packageB->fresh()->getAttributes();

        $this->actingAs($this->adminA)
            ->get(route('admin.packages.edit', $this->packageB))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->put(
                route('admin.packages.update', $this->packageB),
                $this->packagePayload('HACKED PACKAGE', 'hacked-profile')
            )
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->patch(route('admin.packages.toggle-status', $this->packageB))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->post(route('admin.packages.sync-profile', $this->packageB))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->delete(route('admin.packages.destroy', $this->packageB))
            ->assertForbidden();

        $this->assertDatabaseHas('packages', ['id' => $this->packageB->id]);
        $this->assertSame($before, $this->packageB->fresh()->getAttributes());
    }

    public function test_admin_cannot_read_or_mutate_another_tenants_router(): void
    {
        $before = $this->routerB->fresh()->getAttributes();

        $this->actingAs($this->adminA)
            ->get(route('admin.routers.edit', $this->routerB))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->get(route('admin.routers.setup-script', $this->routerB))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->put(route('admin.routers.update', $this->routerB), [
                'name' => 'HACKED ROUTER',
                'ip_address' => '192.168.250.250',
                'username' => 'attacker',
                'password' => 'attacker-password',
                'api_port' => 8728,
                'router_type' => 'mikrotik',
                'is_active' => 1,
            ])
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->post(route('admin.routers.test', $this->routerB))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->delete(route('admin.routers.destroy', $this->routerB))
            ->assertForbidden();

        $this->assertDatabaseHas('routers', ['id' => $this->routerB->id]);
        $this->assertSame($before, $this->routerB->fresh()->getAttributes());
    }

    public function test_admin_cannot_mutate_another_tenants_subscription(): void
    {
        $subscriptionBefore = $this->subscriptionB->fresh()->getAttributes();
        $credentialBefore = $this->credentialB->fresh()->getAttributes();

        $this->actingAs($this->adminA)
            ->post(route('admin.subscriptions.activate', $this->subscriptionB))
            ->assertForbidden();

        $this->actingAs($this->adminA)
            ->post(route('admin.subscriptions.expire', $this->subscriptionB))
            ->assertForbidden();

        $this->assertSame(
            $subscriptionBefore,
            $this->subscriptionB->fresh()->getAttributes()
        );

        $this->assertSame(
            $credentialBefore,
            $this->credentialB->fresh()->getAttributes()
        );
    }

    public function test_admin_cannot_mutate_another_tenants_hotspot_credential(): void
    {
        $before = $this->credentialB->fresh()->getAttributes();

        $this->assertSecureDenial(
            $this->actingAs($this->adminA)
                ->post(route('admin.hotspot-users.activate', $this->credentialB->id))
        );

        $this->assertSecureDenial(
            $this->actingAs($this->adminA)
                ->post(route('admin.hotspot-users.deactivate', $this->credentialB->id))
        );

        $this->assertSecureDenial(
            $this->actingAs($this->adminA)
                ->post(route('admin.hotspot-users.reset-password', $this->credentialB->id))
        );

        $this->assertSecureDenial(
            $this->actingAs($this->adminA)
                ->post(route('admin.hotspot-users.sync-router', $this->credentialB->id))
        );

        $this->assertSame($before, $this->credentialB->fresh()->getAttributes());
    }

    public function test_admin_cannot_mark_another_admins_notification_as_read(): void
    {
        $this->assertFalse((bool) $this->notificationB->is_read);

        $this->actingAs($this->adminA)
            ->post(route('admin.notifications.read', $this->notificationB))
            ->assertForbidden();

        $this->assertFalse((bool) $this->notificationB->fresh()->is_read);
    }

    public function test_mark_all_notifications_only_changes_current_admin_notifications(): void
    {
        $this->actingAs($this->adminA)
            ->post(route('admin.notifications.read-all'))
            ->assertRedirect();

        $this->assertTrue((bool) $this->notificationA->fresh()->is_read);
        $this->assertFalse((bool) $this->notificationB->fresh()->is_read);
    }

    public function test_admin_cannot_open_another_tenants_customer_receipt(): void
    {
        $this->actingAs($this->adminA)
            ->get(route('receipts.show', $this->paymentB))
            ->assertForbidden();
    }

    public function test_customer_cannot_open_another_customers_receipt(): void
    {
        $this->actingAs($this->customerA)
            ->get(route('receipts.show', $this->paymentB))
            ->assertForbidden();
    }

    public function test_admin_payment_page_does_not_leak_another_tenants_payment(): void
    {
        $response = $this->actingAs($this->adminA)
            ->get(route('admin.payments'))
            ->assertOk();

        $payments = $response->viewData('payments');

        $this->assertNotNull($payments);
        $this->assertTrue(
            $payments->getCollection()->contains('id', $this->paymentA->id)
        );
        $this->assertFalse(
            $payments->getCollection()->contains('id', $this->paymentB->id)
        );
    }

    public function test_admin_receipt_index_does_not_leak_another_tenants_payment(): void
    {
        $response = $this->actingAs($this->adminA)
            ->get(route('receipts.index'))
            ->assertOk();

        $receipts = $response->viewData('receipts');

        $this->assertNotNull($receipts);
        $this->assertTrue(
            $receipts->getCollection()->contains('id', $this->paymentA->id)
        );
        $this->assertFalse(
            $receipts->getCollection()->contains('id', $this->paymentB->id)
        );
    }

    public function test_admin_reports_and_csv_exports_do_not_leak_other_tenant_records(): void
    {
        $this->actingAs($this->adminA)
            ->get(route('admin.reports.index'))
            ->assertOk();

        $customersCsv = $this->actingAs($this->adminA)
            ->get(route('admin.reports.customers.csv'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString($this->customerA->email, $customersCsv);
        $this->assertStringNotContainsString($this->customerB->email, $customersCsv);

        $paymentsCsv = $this->actingAs($this->adminA)
            ->get(route('admin.reports.payments.csv'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString((string) $this->paymentA->id, $paymentsCsv);
        $this->assertStringNotContainsString('SECURITYB001', $paymentsCsv);

        $subscriptionsCsv = $this->actingAs($this->adminA)
            ->get(route('admin.reports.subscriptions.csv'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString(
            (string) $this->subscriptionA->id,
            $subscriptionsCsv
        );

        $this->assertStringNotContainsString(
            ','.$this->customerB->id.','.$this->packageB->id.',',
            $subscriptionsCsv
        );
    }

    public function test_admin_audit_log_page_does_not_leak_another_tenants_audit_record(): void
    {
        DB::table('audit_logs')->insert([
            [
                'user_id' => $this->adminA->id,
                'admin_id' => $this->adminA->id,
                'actor_role' => 'admin',
                'actor_name' => $this->adminA->name,
                'actor_email' => $this->adminA->email,
                'scope' => 'tenant',
                'action' => 'SECURITY_ADMIN_A_ACTION',
                'module' => 'Security',
                'description' => 'SECURITY_ADMIN_A_VISIBLE',
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $this->adminB->id,
                'admin_id' => $this->adminB->id,
                'actor_role' => 'admin',
                'actor_name' => $this->adminB->name,
                'actor_email' => $this->adminB->email,
                'scope' => 'tenant',
                'action' => 'SECURITY_ADMIN_B_ACTION',
                'module' => 'Security',
                'description' => 'SECURITY_ADMIN_B_SECRET',
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($this->adminA)
            ->get(route('admin.audit-logs.index'))
            ->assertOk();

        $response->assertSee('SECURITY_ADMIN_A_VISIBLE');
        $response->assertDontSee('SECURITY_ADMIN_B_SECRET');
    }

    public function test_super_admin_cannot_open_isp_customer_management_routes(): void
    {
        $superAdmin = $this->makeSuperAdmin();

        $this->actingAs($superAdmin)
            ->get(route('admin.customers.index'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('admin.packages.index'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('admin.routers.index'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('admin.subscriptions.index'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('admin.hotspot-users.index'))
            ->assertForbidden();
    }

    public function test_super_admin_cannot_open_individual_customer_payment_receipt(): void
    {
        $superAdmin = $this->makeSuperAdmin();

        $this->actingAs($superAdmin)
            ->get(route('receipts.show', $this->paymentA))
            ->assertRedirect(route('receipts.index'));
    }

    private function assertSecureDenial($response): void
    {
        $this->assertContains(
            $response->getStatusCode(),
            [403, 404],
            'Expected the cross-tenant request to be denied with HTTP 403 or 404.'
        );
    }

    private function makeAdmin(string $name, string $email): User
    {
        return User::factory()->create([
            'name' => $name,
            'email' => $email,
            'role' => 'admin',
            'admin_id' => null,
            'is_suspended' => false,
            'service_status' => 'active',
            'trial_ends_at' => null,
            'service_expires_at' => now()->addMonth(),
        ]);
    }

    private function makeSuperAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Security Super Admin',
            'email' => 'security-super-admin@example.test',
            'role' => 'super_admin',
            'admin_id' => null,
            'is_suspended' => false,
            'service_status' => 'active',
            'trial_ends_at' => null,
            'service_expires_at' => now()->addYear(),
        ]);
    }

    private function makeCustomer(
        User $admin,
        string $name,
        string $email
    ): User {
        return User::factory()->create([
            'name' => $name,
            'email' => $email,
            'role' => 'customer',
            'admin_id' => $admin->id,
            'is_suspended' => false,
        ]);
    }

    private function makePackage(
        User $admin,
        string $name,
        string $profile
    ): Package {
        return Package::create([
            'admin_id' => $admin->id,
            'name' => $name,
            'price' => 10.00,
            'duration_minutes' => 60,
            'is_speed_unlimited' => false,
            'speed' => '5M/5M',
            'download_speed' => '5M',
            'upload_speed' => '5M',
            'description' => 'Security anti-bypass fixture',
            'is_active' => true,
            'package_type' => 'hotspot',
            'router_profile' => $profile,
        ]);
    }

    private function makeRouter(
        User $admin,
        string $name,
        string $ip
    ): Router {
        return Router::create([
            'admin_id' => $admin->id,
            'name' => $name,
            'ip_address' => $ip,
            'username' => 'security-admin',
            'password' => 'security-router-password',
            'api_port' => 8728,
            'router_type' => 'mikrotik',
            'notes' => 'Security anti-bypass fixture',
            'is_active' => true,
        ]);
    }

    private function makeSubscription(
        User $customer,
        Package $package
    ): Subscription {
        return Subscription::create([
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'start_time' => now()->subMinutes(5),
            'end_time' => now()->addMinutes(55),
            'status' => 'active',
        ]);
    }

    private function makeCredential(
        User $customer,
        Subscription $subscription,
        Router $router,
        string $username
    ): HotspotUser {
        return HotspotUser::create([
            'user_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'router_id' => $router->id,
            'username' => $username,
            'password' => 'security-hotspot-password',
            'profile' => $subscription->package->router_profile,
            'status' => 'active',
            'router_synced' => true,
            'last_sync_status' => 'Success',
            'last_sync_message' => 'Security fixture synchronized.',
        ]);
    }

    private function makePayment(
        User $customer,
        Package $package,
        string $receipt
    ): Payment {
        return Payment::create([
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'phone_number' => '254700000001',
            'mpesa_receipt' => $receipt,
            'checkout_request_id' => 'ws_CO_'.strtolower($receipt),
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    private function makeNotification(
        User $admin,
        string $title
    ): Notification {
        return Notification::create([
            'user_id' => $admin->id,
            'title' => $title,
            'message' => $title.' message',
            'type' => 'info',
            'audience' => 'admin',
            'is_read' => false,
        ]);
    }

    private function packagePayload(
        string $name,
        string $profile
    ): array {
        return [
            'name' => $name,
            'price' => 999.00,
            'duration_minutes' => 120,
            'is_speed_unlimited' => 0,
            'download_speed' => '9M',
            'upload_speed' => '9M',
            'package_type' => 'hotspot',
            'router_profile' => $profile,
            'description' => 'Attempted cross-tenant modification',
            'is_active' => 1,
        ];
    }
}
