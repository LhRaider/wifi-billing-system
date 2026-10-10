<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $user = Auth::user();
            $unreadNotifications = 0;

            if ($user?->role === 'super_admin') {
                $unreadNotifications = Notification::query()
                    ->where('is_read', false)
                    ->where('audience', 'super_admin')
                    ->count();
            } elseif ($user?->role === 'admin') {
                $unreadNotifications = Notification::query()
                    ->where('is_read', false)
                    ->where('audience', 'admin')
                    ->where('user_id', $user->id)
                    ->count();
            }

            $view->with('unreadNotifications', $unreadNotifications);
        });
    }
}