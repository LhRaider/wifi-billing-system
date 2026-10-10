<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the authenticated user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                Password::defaults(),
                'confirmed',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Invalidate Other Authenticated Sessions
        |--------------------------------------------------------------------------
        |
        | This verifies the current password and causes older authenticated
        | sessions on other devices to become invalid when used again.
        |
        */

        Auth::logoutOtherDevices(
            $validated['current_password']
        );

        /*
        |--------------------------------------------------------------------------
        | Save New Password
        |--------------------------------------------------------------------------
        */

        $request->user()->update([
            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Rotate Current Session ID
        |--------------------------------------------------------------------------
        |
        | Keep the current device authenticated while replacing its session ID
        | after this sensitive account change.
        |
        */

        $request->session()->regenerate();

        return back()->with(
            'status',
            'password-updated'
        );
    }
}