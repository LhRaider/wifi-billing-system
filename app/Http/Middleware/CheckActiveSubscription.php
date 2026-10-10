<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Only Customers Require an Active Package
        |--------------------------------------------------------------------------
        |
        | ISP Admins and Super Admins are controlled by their own middleware
        | and platform-license rules. This middleware must never accidentally
        | apply customer subscription rules to those roles.
        |
        */

        if ($user->role !== 'customer') {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Current Customer Subscription
        |--------------------------------------------------------------------------
        |
        | A usable subscription must:
        | - belong to the authenticated customer
        | - have active status
        | - already have started
        | - not yet have expired
        |
        */

        $hasActiveSubscription = Subscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>', now())
            ->exists();

        if (!$hasActiveSubscription) {
            return redirect()
                ->route('packages.index')
                ->with(
                    'error',
                    'You do not currently have an active package. Please select a package to continue.'
                );
        }

        return $next($request);
    }
}