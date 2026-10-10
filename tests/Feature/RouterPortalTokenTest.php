<?php

use App\Models\Router;
use App\Models\User;

test('new routers automatically receive a unique portal token', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'admin_id' => null,
    ]);

    $router = Router::create([
        'admin_id' => $admin->id,
        'name' => 'Portal Token Test Router',
        'ip_address' => '192.168.88.1',
        'username' => 'test-admin',
        'password' => 'temporary-test-password',
        'api_port' => 8728,
        'router_type' => 'mikrotik',
        'notes' => 'Automated portal token test.',
        'is_active' => true,
    ]);

    expect($router->portal_token)
        ->not->toBeNull()
        ->toHaveLength(64);

    $this->assertDatabaseHas('routers', [
        'id' => $router->id,
        'admin_id' => $admin->id,
        'portal_token' => $router->portal_token,
    ]);
});

test('different routers receive different portal tokens', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'admin_id' => null,
    ]);

    $firstRouter = Router::create([
        'admin_id' => $admin->id,
        'name' => 'First Portal Router',
        'ip_address' => '192.168.88.1',
        'username' => 'first-admin',
        'password' => 'temporary-test-password',
        'api_port' => 8728,
        'router_type' => 'mikrotik',
        'is_active' => true,
    ]);

    $secondRouter = Router::create([
        'admin_id' => $admin->id,
        'name' => 'Second Portal Router',
        'ip_address' => '192.168.89.1',
        'username' => 'second-admin',
        'password' => 'temporary-test-password',
        'api_port' => 8728,
        'router_type' => 'mikrotik',
        'is_active' => true,
    ]);

    expect($firstRouter->portal_token)
        ->not->toBeNull()
        ->toHaveLength(64);

    expect($secondRouter->portal_token)
        ->not->toBeNull()
        ->toHaveLength(64);

    expect($firstRouter->portal_token)
        ->not->toBe($secondRouter->portal_token);
});

test('portal token cannot be supplied through normal mass assignment', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'admin_id' => null,
    ]);

    $suppliedToken = str_repeat('A', 64);

    $router = Router::create([
        'admin_id' => $admin->id,
        'name' => 'Protected Portal Router',
        'ip_address' => '192.168.90.1',
        'username' => 'protected-admin',
        'password' => 'temporary-test-password',
        'api_port' => 8728,
        'router_type' => 'mikrotik',
        'is_active' => true,
        'portal_token' => $suppliedToken,
    ]);

    expect($router->portal_token)
        ->not->toBe($suppliedToken)
        ->toHaveLength(64);
});