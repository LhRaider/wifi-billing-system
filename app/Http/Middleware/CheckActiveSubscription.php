<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        $hasActiveSubscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_time', '>', now())
            ->exists();

        if (!$hasActiveSubscription) {
            return redirect()
                ->route('packages.index')
                ->with('error', 'Your package has expired. Please buy a new package to continue.');
        }

        return $next($request);
    }
}