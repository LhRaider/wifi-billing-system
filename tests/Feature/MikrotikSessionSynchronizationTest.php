<?php

use App\Models\HotspotSession;
use App\Models\HotspotUser;
use App\Models\Router;
use App\Models\User;
use App\Services\MikrotikService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    /*
     * These tests deliberately run with real MikroTik communication
     * disabled. No physical router is contacted.
     */
    putenv('MIKROTIK_REAL_CONNECTION=false');
    $_ENV['MIKROTIK_REAL_CONNECTION'] = 'false';
    $_SERVER['MIKROTIK_REAL_CONNECTION'] = 'false';
});

afterEach(function () {
    putenv('MIKROTIK_REAL_CONNECTION');
    unset(
        $_ENV['MIKROTIK_REAL_CONNECTION'],
        $_SERVER['MIKROTIK_REAL_CONNECTION']
    );
});

function createSessionTestAdmin(
    string $email = 'session-admin@example.com'
): User {
    return User::factory()->create([
        'name' => 'Session Test Admin',
        'email' => $email,
        'role' => 'admin',
        'admin_id' => null,
    ]);
}

function createSessionTestCustomer(
    User $admin,
    string $email = 'session-customer@example.com'
): User {
    return User::factory()->create([
        'name' => 'Session Test Customer',
        'email' => $email,
        'role' => 'customer',
        'admin_id' => $admin->id,
        'is_suspended' => false,
    ]);
}

function createSessionTestRouter(
    User $admin,
    array $overrides = []
): Router {
    return Router::create(array_merge([
        'admin_id' => $admin->id,
        'name' => 'Session Test Router',
        'ip_address' => '192.168.88.1',
        'username' => 'api-session-test',
        'password' => 'test-router-password',
        'api_port' => 8728,
        'router_type' => 'mikrotik',
        'notes' => 'Automated session synchronization test.',
        'is_active' => true,
    ], $overrides));
}

test(
    'session synchronization simulation does not invent sessions',
    function () {
        $admin = createSessionTestAdmin();

        $customer = createSessionTestCustomer(
            $admin
        );

        $router = createSessionTestRouter(
            $admin
        );

        HotspotUser::create([
            'user_id' => $customer->id,
            'subscription_id' => null,
            'router_id' => $router->id,
            'username' => 'session-user-1',
            'password' => '123456',
            'profile' => 'default',
            'status' => 'active',
            'router_synced' => true,
            'last_sync_status' => 'success',
            'last_sync_message' => 'Test credential.',
        ]);

        $result = app(
            MikrotikService::class
        )->syncHotspotSessions(
            $router
        );

        expect($result['success'])
            ->toBeTrue();

        expect($result['created'])
            ->toBe(0);

        expect($result['updated'])
            ->toBe(0);

        expect($result['closed'])
            ->toBe(0);

        expect($result['ignored'])
            ->toBe(0);

        expect(
            HotspotSession::query()->count()
        )->toBe(0);
    }
);

test(
    'session synchronization simulation preserves existing open sessions',
    function () {
        $admin = createSessionTestAdmin(
            'preserve-admin@example.com'
        );

        $customer = createSessionTestCustomer(
            $admin,
            'preserve-customer@example.com'
        );

        $router = createSessionTestRouter(
            $admin,
            [
                'name' => 'Preserve Router',
                'ip_address' => '192.168.88.2',
            ]
        );

        $session = HotspotSession::create([
            'user_id' => $customer->id,
            'router_id' => $router->id,
            'mac_address' => 'AA:BB:CC:DD:EE:01',
            'ip_address' => '10.10.10.2',
            'login_time' => now()->subMinutes(10),
            'logout_time' => null,
            'data_used' => 2048,
        ]);

        $result = app(
            MikrotikService::class
        )->syncHotspotSessions(
            $router
        );

        expect($result['success'])
            ->toBeTrue();

        expect($result['closed'])
            ->toBe(0);

        $session->refresh();

        expect($session->logout_time)
            ->toBeNull();

        expect($session->data_used)
            ->toBe(2048);
    }
);

test(
    'inactive router is rejected without changing session history',
    function () {
        $admin = createSessionTestAdmin(
            'inactive-admin@example.com'
        );

        $customer = createSessionTestCustomer(
            $admin,
            'inactive-customer@example.com'
        );

        $router = createSessionTestRouter(
            $admin,
            [
                'name' => 'Inactive Session Router',
                'ip_address' => '192.168.88.3',
                'is_active' => false,
            ]
        );

        $session = HotspotSession::create([
            'user_id' => $customer->id,
            'router_id' => $router->id,
            'mac_address' => 'AA:BB:CC:DD:EE:02',
            'ip_address' => '10.10.10.3',
            'login_time' => now()->subMinutes(20),
            'logout_time' => null,
            'data_used' => 4096,
        ]);

        $result = app(
            MikrotikService::class
        )->syncHotspotSessions(
            $router
        );

        expect($result['success'])
            ->toBeFalse();

        expect($result['created'])
            ->toBe(0);

        expect($result['updated'])
            ->toBe(0);

        expect($result['closed'])
            ->toBe(0);

        $session->refresh();

        expect($session->logout_time)
            ->toBeNull();
    }
);

test(
    'scheduled command processes active routers without creating fake sessions',
    function () {
        $admin = createSessionTestAdmin(
            'command-admin@example.com'
        );

        createSessionTestRouter(
            $admin,
            [
                'name' => 'Command Session Router',
                'ip_address' => '192.168.88.4',
            ]
        );

        $this->artisan(
            'mikrotik:sync-sessions'
        )
            ->assertSuccessful();

        expect(
            HotspotSession::query()->count()
        )->toBe(0);
    }
);

test(
    'scheduled command ignores inactive routers',
    function () {
        $admin = createSessionTestAdmin(
            'ignored-admin@example.com'
        );

        $customer = createSessionTestCustomer(
            $admin,
            'ignored-customer@example.com'
        );

        $router = createSessionTestRouter(
            $admin,
            [
                'name' => 'Ignored Inactive Router',
                'ip_address' => '192.168.88.5',
                'is_active' => false,
            ]
        );

        $session = HotspotSession::create([
            'user_id' => $customer->id,
            'router_id' => $router->id,
            'mac_address' => 'AA:BB:CC:DD:EE:03',
            'ip_address' => '10.10.10.5',
            'login_time' => now()->subMinutes(30),
            'logout_time' => null,
            'data_used' => 8192,
        ]);

        $this->artisan(
            'mikrotik:sync-sessions'
        )
            ->assertSuccessful();

        $session->refresh();

        expect($session->logout_time)
            ->toBeNull();

        expect($session->data_used)
            ->toBe(8192);
    }
);