<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspendedUser
{
    /**
     * Prevent suspended customers from accessing protected
     * customer service areas.
     *
     * ISP Admin and Super Admin account restrictions are handled
     * separately by their own platform access controls.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        /*
         * This middleware applies only to customer accounts.
         */
        if (
            $user->role === 'customer' &&
            $user->is_suspended
        ) {
            /*
             * The package page remains the safe destination because
             * customers can view their ISP Admin's available packages
             * and payment instructions there.
             *
             * Automatic customer-to-ISP payment remains intentionally
             * disabled until the final payment provider is implemented.
             */
            if ($request->routeIs('packages.index')) {
                return $next($request);
            }

            return redirect()
                ->route('packages.index')
                ->with(
                    'error',
                    'Your account is suspended. Please contact your ISP Admin or follow the available payment instructions to restore service.'
                );
        }

        return $next($request);
    }
}