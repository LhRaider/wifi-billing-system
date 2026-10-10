<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Customer Subscription Expiration
|--------------------------------------------------------------------------
|
| Expires ended customer subscriptions and disables/disconnects the
| associated MikroTik access.
|
*/

Schedule::command('subscriptions:expire')
    ->everyMinute()
    ->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| ISP Admin Platform License Expiration
|--------------------------------------------------------------------------
|
| Detects expired ISP Admin platform licenses and disables/disconnects
| customer router access while preserving customer subscription history.
|
*/

Schedule::command('admin-licenses:expire')
    ->everyMinute()
    ->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| ISP Admin Network Restoration
|--------------------------------------------------------------------------
|
| Restores router access for eligible customers belonging to ISP Admins
| whose platform trial or paid license is currently valid.
|
| Customer entitlement, suspension status, subscription dates, package
| ownership, tenant ownership, and router ownership are revalidated before
| network access can be restored.
|
*/

Schedule::command('admin-licenses:restore-network')
    ->everyMinute()
    ->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| MikroTik Hotspot Session Synchronization
|--------------------------------------------------------------------------
|
| Synchronizes active RouterOS Hotspot sessions with the local session
| history used by ISP Admin session monitoring.
|
| A failed router query does not close local sessions because failure to
| communicate with a router is not proof that customers disconnected.
|
*/

Schedule::command('mikrotik:sync-sessions')
    ->everyMinute()
    ->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| License M-Pesa Reconciliation
|--------------------------------------------------------------------------
|
| Recovers verified ISP Admin license payments when Safaricom callbacks
| are delayed or not delivered.
|
*/

Schedule::command('license-payments:reconcile-mpesa')
    ->everyMinute()
    ->withoutOverlapping();