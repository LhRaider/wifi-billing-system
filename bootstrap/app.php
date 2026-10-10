<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Web Session Authentication
        |--------------------------------------------------------------------------
        |
        | AuthenticateSession detects password changes and prevents older
        | authenticated browser sessions from continuing to use stale
        | credentials.
        |
        */

        $middleware->web(append: [
            \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | M-Pesa Callback CSRF Exclusions
        |--------------------------------------------------------------------------
        |
        | Safaricom callbacks originate outside the browser session and must
        | therefore bypass normal CSRF verification.
        |
        */

        $middleware->preventRequestForgery(except: [
            'mpesa/callback',
            'mpesa/license/callback',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Application Middleware Aliases
        |--------------------------------------------------------------------------
        */

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'only.admin' => \App\Http\Middleware\OnlyAdminMiddleware::class,
            'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'active.subscription' => \App\Http\Middleware\CheckActiveSubscription::class,
            'suspended' => \App\Http\Middleware\CheckSuspendedUser::class,
            'software.license' => \App\Http\Middleware\CheckSoftwareLicense::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->create();