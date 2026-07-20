<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspendedUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role !== 'admin' && Auth::user()->is_suspended) {
            return redirect()
                ->route('packages.index')
                ->with('error', 'Your account is suspended. Please buy a new package to continue.');
        }

        return $next($request);
    }
}