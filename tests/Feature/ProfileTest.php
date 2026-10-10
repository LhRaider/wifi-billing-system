<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->admin()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->admin()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->admin()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull(
        $user->refresh()->email_verified_at
    );
});

test('customer can delete their own account with correct password', function () {
    $admin = User::factory()->admin()->create();

    $customer = User::factory()
        ->customer($admin)
        ->create();

    $response = $this
        ->actingAs($customer)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($customer->fresh());

    $this->assertNotNull($admin->fresh());
});

test('correct password must be provided for customer account deletion', function () {
    $admin = User::factory()->admin()->create();

    $customer = User::factory()
        ->customer($admin)
        ->create();

    $response = $this
        ->actingAs($customer)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn(
            'userDeletion',
            'password'
        )
        ->assertRedirect('/profile');

    $this->assertNotNull($customer->fresh());
    $this->assertNotNull($admin->fresh());
});

test('ISP Admin cannot delete their account from personal profile', function () {
    $admin = User::factory()->admin()->create();

    $response = $this
        ->actingAs($admin)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response->assertForbidden();

    $this->assertNotNull($admin->fresh());
});

test('Super Admin cannot delete their account from personal profile', function () {
    $superAdmin = User::factory()
        ->superAdmin()
        ->create();

    $response = $this
        ->actingAs($superAdmin)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response->assertForbidden();

    $this->assertNotNull($superAdmin->fresh());
});