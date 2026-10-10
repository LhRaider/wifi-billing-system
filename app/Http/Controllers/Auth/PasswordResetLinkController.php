<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * The public response deliberately does not reveal whether the submitted
     * email address belongs to an existing account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Send Password Reset Link
        |--------------------------------------------------------------------------
        |
        | Laravel's password broker handles token generation, storage,
        | expiration, and delivery. We intentionally ignore the public-facing
        | broker status so an attacker cannot determine whether an account
        | exists from the response.
        |
        */

        Password::sendResetLink([
            'email' => $validated['email'],
        ]);

        return back()->with(
            'status',
            'If an account exists for that email address, a password reset link will be sent shortly.'
        );
    }
}