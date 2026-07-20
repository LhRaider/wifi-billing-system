<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Package;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

            'role' => 'admin',
            'service_status' => 'trial',
            'trial_ends_at' => now()->addDays(15),
            'is_suspended' => false,
        ]);

        Package::insert([
    [
        'admin_id' => $user->id,
        'name' => '1 Hour',
        'price' => 10,
        'duration_hours' => 1,
        'speed' => '5 Mbps',
        'package_type' => 'hotspot',
        'router_profile' => '1hour',
        'description' => '1 hour package',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'admin_id' => $user->id,
        'name' => '3 Hours',
        'price' => 20,
        'duration_hours' => 3,
        'speed' => '10 Mbps',
        'package_type' => 'hotspot',
        'router_profile' => '3hours',
        'description' => '3 hour package',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'admin_id' => $user->id,
        'name' => '24 Hours',
        'price' => 50,
        'duration_hours' => 24,
        'speed' => '15 Mbps',
        'package_type' => 'hotspot',
        'router_profile' => '24hours',
        'description' => 'Daily package',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);

        \App\Helpers\NotificationHelper::create(
            'New Admin Registration',
            'Admin ' . $user->name . ' registered successfully and started a 15-day trial.',
            'success',
            'super_admin'
        );

        \App\Helpers\AuditHelper::log(
            'Admin Registration',
            'Admin Users',
            'Admin ' . $user->name . ' registered with a 15-day trial.'
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('admin.dashboard', absolute: false));
    }
}
