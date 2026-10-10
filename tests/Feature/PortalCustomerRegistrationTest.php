<?php

use App\Mail\CustomerRegisteredMail;
use App\Models\Router;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function createPortalAdmin(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'role' => 'admin',
        'admin_id' => null,
        'is_suspended' => false,
        'service_status' => 'active',
        'service_expires_at' => now()->addMonth(),
    ], $attributes));
}

function createPortalRouter(User $admin): Router
{
    return Router::create([
        'admin_id' => $admin->id,
        'name' => 'Test Portal Router',
        'ip_address' => '192.168.88.1',
        'username' => 'admin',
        'password' => 'router-password',
        'api_port' => 8728,
        'router_type' => 'hotspot',
        'is_active' => true,
    ]);
}

test('customer registration belongs to the isp admin identified by the portal token', function () {
    Mail::fake();

    $correctAdmin = createPortalAdmin([
        'email' => 'correct-admin@example.com',
        'network_name' => 'Correct ISP',
    ]);

    $otherAdmin = createPortalAdmin([
        'email' => 'other-admin@example.com',
        'network_name' => 'Other ISP',
    ]);

    $router = createPortalRouter($correctAdmin);

    $response = $this->post(
        route('portal.register.store', $router->portal_token),
        [
            'name' => 'Portal Customer',
            'email' => 'portal-customer@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',

            /*
             * Malicious values that must be ignored.
             */
            'role' => 'super_admin',
            'admin_id' => $otherAdmin->id,
        ]
    );

    $customer = User::query()
        ->where('email', 'portal-customer@example.com')
        ->firstOrFail();

    expect($customer->role)
        ->toBe('customer');

    expect((int) $customer->admin_id)
        ->toBe($correctAdmin->id);

    expect($customer->is_suspended)
        ->toBeFalse();

    $this->assertAuthenticatedAs($customer);

    $response->assertRedirect(
        route('packages.index')
    );
});

test('successful customer registration emails the correct isp admin exactly once', function () {
    Mail::fake();

    $correctAdmin = createPortalAdmin([
        'email' => 'correct-notification@example.com',
        'network_name' => 'Correct Network',
    ]);

    $otherAdmin = createPortalAdmin([
        'email' => 'wrong-notification@example.com',
        'network_name' => 'Wrong Network',
    ]);

    $router = createPortalRouter($correctAdmin);

    $this->post(
        route('portal.register.store', $router->portal_token),
        [
            'name' => 'New Customer',
            'email' => 'new-customer@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]
    )->assertRedirect(
        route('packages.index')
    );

    $customer = User::query()
        ->where('email', 'new-customer@example.com')
        ->firstOrFail();

    Mail::assertSent(
        CustomerRegisteredMail::class,
        function (
            CustomerRegisteredMail $mail
        ) use (
            $correctAdmin,
            $customer
        ) {
            return $mail->hasTo($correctAdmin->email)
                && $mail->customer->is($customer)
                && $mail->admin->is($correctAdmin);
        }
    );

    Mail::assertNotSent(
        CustomerRegisteredMail::class,
        function (
            CustomerRegisteredMail $mail
        ) use ($otherAdmin) {
            return $mail->hasTo($otherAdmin->email);
        }
    );

    Mail::assertSentCount(1);
});

test('failed customer registration sends no isp admin email', function () {
    Mail::fake();

    $admin = createPortalAdmin([
        'email' => 'failed-registration-admin@example.com',
    ]);

    $router = createPortalRouter($admin);

    User::factory()->create([
        'email' => 'already-used@example.com',
    ]);

    $response = $this
        ->from(
            route(
                'portal.register',
                $router->portal_token
            )
        )
        ->post(
            route(
                'portal.register.store',
                $router->portal_token
            ),
            [
                'name' => 'Duplicate Customer',
                'email' => 'already-used@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]
        );

    $response->assertSessionHasErrors('email');

    Mail::assertNothingSent();
});

test('customer registration email is not duplicated by later customer login', function () {
    Mail::fake();

    $admin = createPortalAdmin([
        'email' => 'single-email-admin@example.com',
        'network_name' => 'Single Email ISP',
    ]);

    $router = createPortalRouter($admin);

    $password = 'Password123!';

    $this->post(
        route('portal.register.store', $router->portal_token),
        [
            'name' => 'Single Email Customer',
            'email' => 'single-email-customer@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]
    )->assertRedirect(
        route('packages.index')
    );

    Mail::assertSentCount(1);

    auth()->logout();

    $this->post(
        route('portal.login.store', $router->portal_token),
        [
            'email' => 'single-email-customer@example.com',
            'password' => $password,
        ]
    )->assertRedirect(
        route('packages.index')
    );

    Mail::assertSentCount(1);
});