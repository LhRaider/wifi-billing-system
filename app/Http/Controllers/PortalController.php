<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Mail\CustomerRegisteredMail;
use App\Models\Package;
use App\Models\Router;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PortalController extends Controller
{
    /**
     * Display the public captive portal for a specific router.
     */
    public function show(string $portalToken): View
    {
        [$router, $admin] = $this->resolvePortal($portalToken);

        $packages = Package::query()
            ->where('admin_id', $admin->id)
            ->where('is_active', true)
            ->orderBy('price')
            ->orderBy('id')
            ->get();

        return view(
            'portal.show',
            compact(
                'router',
                'admin',
                'packages'
            )
        );
    }

    /**
     * Display customer registration for this ISP portal.
     */
    public function createRegistration(
        string $portalToken
    ): View {
        [$router, $admin] = $this->resolvePortal($portalToken);

        return view(
            'portal.register',
            compact(
                'router',
                'admin'
            )
        );
    }

    /**
     * Register a new customer under the ISP that owns this router.
     */
    public function storeRegistration(
        Request $request,
        string $portalToken
    ): RedirectResponse {
        [$router, $admin] = $this->resolvePortal($portalToken);

        /*
        |--------------------------------------------------------------------------
        | Customer-Controlled Input
        |--------------------------------------------------------------------------
        |
        | admin_id and role are intentionally NOT accepted from the request.
        | Tenant ownership comes exclusively from the router portal token.
        |
        */

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
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        try {
            $customer = DB::transaction(
                function () use ($validated, $admin): User {
                    return User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => $validated['password'],

                        /*
                         * These values are determined by the application,
                         * never by customer-submitted form data.
                         */
                        'role' => 'customer',
                        'admin_id' => $admin->id,
                        'is_suspended' => false,
                    ]);
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
                    'Your account could not be created. Please try again.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Notify the Correct ISP Admin
        |--------------------------------------------------------------------------
        |
        | The recipient comes from the router's resolved ISP Admin, never from
        | customer-submitted data. The email is sent only after the customer
        | has been successfully committed to the database.
        |
        | Mail delivery failure must never roll back or block registration.
        |
        */

        try {
            Mail::to($admin->email)->send(
                new CustomerRegisteredMail(
                    $customer,
                    $admin
                )
            );
        } catch (Throwable $exception) {
            report($exception);
        }

        Auth::login($customer);

        $request->session()->regenerate();

        return redirect()
            ->route('packages.index')
            ->with(
                'success',
                'Your customer account was created successfully.'
            );
    }

    /**
     * Display the customer login screen for this ISP portal.
     */
    public function createLogin(
        string $portalToken
    ): View {
        [$router, $admin] = $this->resolvePortal($portalToken);

        return view(
            'portal.login',
            compact(
                'router',
                'admin'
            )
        );
    }

    /**
     * Authenticate an existing customer through this ISP portal.
     */
    public function storeLogin(
        LoginRequest $request,
        string $portalToken
    ): RedirectResponse {
        [$router, $admin] = $this->resolvePortal($portalToken);

        /*
        |--------------------------------------------------------------------------
        | Standard Laravel Authentication
        |--------------------------------------------------------------------------
        */

        $request->authenticate();

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Portal Tenant Boundary
        |--------------------------------------------------------------------------
        |
        | The authenticated account must be a customer belonging to the ISP
        | that owns this router.
        |
        */

        if (
            !$user
            || $user->role !== 'customer'
            || (int) $user->admin_id !== (int) $admin->id
        ) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Customer Suspension
        |--------------------------------------------------------------------------
        */

        if ($user->is_suspended) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This customer account is currently suspended.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()
            ->route('packages.index')
            ->with(
                'success',
                'You have signed in successfully.'
            );
    }

    /**
     * Resolve and validate a public router portal.
     *
     * The portal token is the only public tenant identifier used here.
     */
    private function resolvePortal(
        string $portalToken
    ): array {
        $router = Router::query()
            ->where('portal_token', $portalToken)
            ->where('is_active', true)
            ->firstOrFail();

        $admin = $router->admin;

        /*
        |--------------------------------------------------------------------------
        | Validate ISP Ownership
        |--------------------------------------------------------------------------
        */

        if (!$admin || $admin->role !== 'admin') {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | ISP Admin Suspension
        |--------------------------------------------------------------------------
        */

        if ($admin->is_suspended) {
            abort(
                503,
                'This internet service is currently unavailable.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Platform License Validation
        |--------------------------------------------------------------------------
        */

        if (!$this->adminHasActiveLicense($admin)) {
            abort(
                503,
                'This internet service is currently unavailable.'
            );
        }

        return [$router, $admin];
    }

    /**
     * Determine whether an ISP Admin currently has a valid platform license.
     */
    private function adminHasActiveLicense(User $admin): bool
    {
        $trialActive = $admin->service_status === 'trial'
            && $admin->trial_ends_at !== null
            && now()->lessThanOrEqualTo($admin->trial_ends_at);

        $subscriptionActive = $admin->service_status === 'active'
            && $admin->service_expires_at !== null
            && now()->lessThanOrEqualTo(
                $admin->service_expires_at
            );

        return $trialActive || $subscriptionActive;
    }
}