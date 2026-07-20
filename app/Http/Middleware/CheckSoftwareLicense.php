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
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'super_admin') {
            return $next($request);
        }

        $admin = $user->role === 'admin' ? $user : $user->admin;

        if (!$admin) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No service owner found for this account.');
        }

        $trialActive = $admin->service_status === 'trial'
            && $admin->trial_ends_at
            && now()->lessThanOrEqualTo($admin->trial_ends_at);

        $subscriptionActive = $admin->service_status === 'active'
            && $admin->service_expires_at
            && now()->lessThanOrEqualTo($admin->service_expires_at);

        if (!$trialActive && !$subscriptionActive) {
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

            return redirect()
                ->route('admin.license-payments.index')
                ->with('error', 'Your subscription is over. Please renew to continue providing services.');
        }

        return $next($request);
    }
}