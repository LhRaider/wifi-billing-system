<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the authenticated user's personal profile.
     */
    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {
        $user = $request->user();

        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Profile Photo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('profile_photo')) {

            if (
                $user->profile_photo
                &&
                Storage::disk('public')
                    ->exists($user->profile_photo)
            ) {
                Storage::disk('public')
                    ->delete($user->profile_photo);
            }

            $validated['profile_photo'] =
                $request
                    ->file('profile_photo')
                    ->store(
                        'profile-photos',
                        'public'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Personal Profile Only
        |--------------------------------------------------------------------------
        |
        | Business/payment settings are intentionally not handled here.
        | ISP Admin business settings belong to BusinessProfileController.
        |
        */

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with(
                'success',
                'Profile updated successfully.'
            );
    }

    /**
     * Delete a customer account.
     *
     * ISP Admin and Super Admin accounts must not be removed through the
     * ordinary personal profile page.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        if (
            !$user
            ||
            $user->role !== 'customer'
        ) {
            abort(
                403,
                'Staff accounts cannot be deleted from this page.'
            );
        }

        $request->validateWithBag(
            'userDeletion',
            [
                'password' => [
                    'required',
                    'current_password',
                ],
            ]
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}