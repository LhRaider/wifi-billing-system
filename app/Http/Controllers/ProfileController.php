<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display profile page
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update profile
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validated();

        if ($request->hasFile('profile_photo')) {

            if (
                $user->profile_photo &&
                Storage::disk('public')->exists($user->profile_photo)
            ) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $validated['profile_photo'] = $request
                ->file('profile_photo')
                ->store('profile-photos', 'public');
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete account
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update Business Profile
     */
    public function updateBusiness(Request $request)
    {
        $request->validate([

            'business_name'    => 'nullable|string|max:255',
            'network_name'     => 'nullable|string|max:255',
            'business_phone'   => 'nullable|string|max:30',
            'business_address' => 'nullable|string|max:255',

            'payment_type'     => 'nullable|in:till,paybill',

            'business_till'    => 'nullable|string|max:50',
            'business_paybill' => 'nullable|string|max:50',
            'business_account' => 'nullable|string|max:50',

        ]);

        $user = auth()->user();

        $user->update([

            'business_name'    => $request->business_name,
            'network_name'     => $request->network_name,
            'business_phone'   => $request->business_phone,
            'business_address' => $request->business_address,

            'payment_type'     => $request->payment_type,

            'business_till'    => $request->business_till,
            'business_paybill' => $request->business_paybill,
            'business_account' => $request->business_account,

        ]);

        return back()->with('success', 'Business profile updated successfully.');
    }
}