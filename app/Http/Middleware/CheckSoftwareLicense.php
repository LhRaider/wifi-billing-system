<?php

namespace App\Http\Middleware;

use App\Helpers\NotificationHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSoftwareLicense
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | The platform owner does not depend on an ISP Admin software license.
        |
        */

        if ($user->role === 'super_admin') {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Service Owner
        |--------------------------------------------------------------------------
        |
        | ISP Admins own their own platform license. Customers inherit the
        | license status of the ISP Admin that owns their account.
        |
        */

        $admin = $user->role === 'admin'
            ? $user
            : $user->admin;

        if (!$admin || $admin->role !== 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No service owner found for this account.');
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Current License Validity
        |--------------------------------------------------------------------------
        */

        $trialActive = $admin->service_status === 'trial'
            && $admin->trial_ends_at !== null
            && now()->lessThanOrEqualTo($admin->trial_ends_at);

        $subscriptionActive = $admin->service_status === 'active'
            && $admin->service_expires_at !== null
            && now()->lessThanOrEqualTo($admin->service_expires_at);

        if ($trialActive || $subscriptionActive) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Expired License
        |--------------------------------------------------------------------------
        |
        | Mark the ISP Admin expired only once. Notifications are also created
        | only during the transition to expired so repeated blocked requests
        | cannot create duplicate notification records.
        |
        */

        if ($admin->service_status !== 'expired') {
            $admin->update([
                'service_status' => 'expired',
            ]);

            NotificationHelper::create(
                'Admin Subscription Expired',
                'Admin '.$admin->name.' subscription is over.',
                'warning',
                'super_admin'
            );

            NotificationHelper::create(
                'Subscription Required',
                'Your subscription is over. Please renew to continue providing services.',
                'warning',
                'admin',
                $admin->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Block Licensed Service Access
        |--------------------------------------------------------------------------
        |
        | ISP Admins are redirected to their renewal page. The renewal page
        | already displays the current expired status, so no duplicate flash
        | message is added here.
        |
        | Customers cannot renew the ISP Admin's platform license themselves.
        | They return to their dashboard instead.
        |
        */

        if ($user->role === 'admin') {
            return redirect()->route('admin.license-payments.index');
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'error',
                'Service is currently unavailable because your provider subscription is inactive.'
            );
    }
}