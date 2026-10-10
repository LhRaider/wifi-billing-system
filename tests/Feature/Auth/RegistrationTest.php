<?php

use App\Mail\AdminWelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertOk();
});

test('new ISP Admin users can register and receive one welcome email', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $response->assertRedirect('/admin/dashboard');

    $user = User::query()
        ->where('email', 'test@example.com')
        ->first();

    $this->assertNotNull($user);
    $this->assertSame('admin', $user->role);
    $this->assertNull($user->admin_id);

    Mail::assertSent(
        AdminWelcomeMail::class,
        function (AdminWelcomeMail $mail) use ($user) {
            return $mail->hasTo($user->email)
                && $mail->user->is($user);
        }
    );

    Mail::assertSentCount(1);
});

test('failed ISP Admin registration does not send a welcome email', function () {
    Mail::fake();

    User::factory()->create([
        'email' => 'existing@example.com',
        'role' => 'admin',
        'admin_id' => null,
    ]);

    $response = $this->post('/register', [
        'name' => 'Duplicate User',
        'email' => 'existing@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');

    Mail::assertNothingSent();
});