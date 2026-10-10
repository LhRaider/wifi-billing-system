<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Mail\AdminWelcomeMail;
use App\Models\User;
use App\Services\DefaultPackageService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the ISP Admin registration form.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new ISP Admin and create the standard starter packages.
     */
    public function store(
        Request $request,
        DefaultPackageService $defaultPackageService
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        try {
            $user = DB::transaction(
                function () use (
                    $validated,
                    $defaultPackageService
                ): User {
                    $user = User::create([
                        'admin_id' => null,
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make(
                            $validated['password']
                        ),
                        'role' => 'admin',
                        'service_status' => 'trial',
                        'trial_ends_at' => now()->addDays(15),
                        'service_expires_at' => null,
                        'is_suspended' => false,
                    ]);

                    $defaultPackageService
                        ->createMissingForAdmin($user);

                    return $user;
                }
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput(
                    $request->except([
                        'password',
                        'password_confirmation',
                    ])
                )
                ->with(
                    'error',
                    'Registration could not be completed. Please try again.'
                );
        }

        NotificationHelper::create(
            'New Admin Registration',
            'Admin '
                .$user->name
                .' registered successfully and started a 15-day trial.',
            'success',
            'super_admin'
        );

        AuditHelper::log(
            'Admin Registration',
            'Admin Users',
            'Admin '
                .$user->name
                .' registered with a 15-day trial.'
        );

        event(new Registered($user));

        /*
         * Send the one-time ISP Admin welcome email only after the
         * account and starter packages have been created successfully.
         *
         * Email delivery is non-critical. A temporary mail-provider
         * failure must not undo or block an otherwise valid account
         * registration.
         */
        try {
            Mail::to($user->email)->send(
                new AdminWelcomeMail($user)
            );
        } catch (Throwable $exception) {
            report($exception);
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}