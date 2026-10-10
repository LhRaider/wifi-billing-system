<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertOk();
});

test('reset password link can be requested for an existing account', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertSessionHas(
            'status',
            'If an account exists for that email address, a password reset link will be sent shortly.'
        );

    Notification::assertSentTo(
        $user,
        ResetPassword::class
    );
});

test('unknown email receives the same public password reset response', function () {
    Notification::fake();

    $response = $this->post('/forgot-password', [
        'email' => 'unknown@example.com',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertSessionHas(
            'status',
            'If an account exists for that email address, a password reset link will be sent shortly.'
        );

    Notification::assertNothingSent();
});

test('password reset request requires a valid email address', function () {
    Notification::fake();

    $response = $this->post('/forgot-password', [
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors('email');

    Notification::assertNothingSent();
});

test('reset password screen can be rendered from notification link', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            $response = $this->get(
                route('password.reset', [
                    'token' => $notification->token,
                    'email' => $user->email,
                ])
            );

            $response
                ->assertOk()
                ->assertSee($user->email);

            return true;
        }
    );
});

test('password can be reset with a valid token', function () {
    Notification::fake();

    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            $user->refresh();

            expect(
                Hash::check('NewPassword123!', $user->password)
            )->toBeTrue();

            expect(
                Hash::check('OldPassword123!', $user->password)
            )->toBeFalse();

            return true;
        }
    );
});

test('password cannot be reset with an invalid token', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $response = $this->post('/reset-password', [
        'token' => 'invalid-reset-token',
        'email' => $user->email,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response->assertSessionHasErrors('email');

    $user->refresh();

    expect(
        Hash::check('OldPassword123!', $user->password)
    )->toBeTrue();
});

test('password reset requires matching password confirmation', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'NewPassword123!',
                'password_confirmation' => 'DifferentPassword123!',
            ]);

            $response->assertSessionHasErrors('password');

            return true;
        }
    );
});

test('password reset token cannot be reused after successful reset', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            $firstResponse = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'FirstNewPassword123!',
                'password_confirmation' => 'FirstNewPassword123!',
            ]);

            $firstResponse
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            $secondResponse = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'SecondNewPassword123!',
                'password_confirmation' => 'SecondNewPassword123!',
            ]);

            $secondResponse->assertSessionHasErrors('email');

            $user->refresh();

            expect(
                Hash::check('FirstNewPassword123!', $user->password)
            )->toBeTrue();

            expect(
                Hash::check('SecondNewPassword123!', $user->password)
            )->toBeFalse();

            return true;
        }
    );
});