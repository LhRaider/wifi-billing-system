<?php

use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Admin\HotspotSessionController;
use App\Http\Controllers\Admin\HotspotUserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\SubscriptionManagementController;
use App\Http\Controllers\Admin\PackageManagementController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\LicensePaymentController;
use App\Http\Controllers\Admin\LicenseMpesaController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\RouterController;
use App\Http\Controllers\Admin\BusinessProfileController;

use App\Http\Controllers\MpesaController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PortalController;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Public WiFi Captive Portal
|--------------------------------------------------------------------------
|
| MikroTik routers will redirect unauthenticated WiFi customers to this
| router-specific portal. The random portal token identifies the router and
| its ISP Admin without exposing or accepting a customer-supplied admin ID.
|
*/

Route::get(
    '/portal/{portalToken}',
    [PortalController::class, 'show']
)->name('portal.show');


/*
|--------------------------------------------------------------------------
| Public WiFi Customer Registration
|--------------------------------------------------------------------------
|
| Customer ownership is derived exclusively from the router portal token.
| Neither role nor admin_id is accepted from the public registration form.
|
*/

Route::get(
    '/portal/{portalToken}/register',
    [PortalController::class, 'createRegistration']
)->name('portal.register');


Route::post(
    '/portal/{portalToken}/register',
    [PortalController::class, 'storeRegistration']
)->name('portal.register.store');


/*
|--------------------------------------------------------------------------
| Public WiFi Customer Login
|--------------------------------------------------------------------------
|
| Customers authenticate using the normal Laravel authentication system.
| PortalController additionally verifies that the authenticated customer
| belongs to the ISP Admin that owns the router identified by portalToken.
|
*/

Route::get(
    '/portal/{portalToken}/login',
    [PortalController::class, 'createLogin']
)->name('portal.login');


Route::post(
    '/portal/{portalToken}/login',
    [PortalController::class, 'storeLogin']
)->name('portal.login.store');


/*
|--------------------------------------------------------------------------
| Authenticated Account Routes
|--------------------------------------------------------------------------
|
| These routes remain available to authenticated users regardless of whether
| a customer's current internet package is active, expired, or suspended.
| Individual controllers enforce exact role, ownership, and tenant rules.
|
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )
        ->middleware('verified')
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Personal Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');


    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');


    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Customer Payment History
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/payments',
        [PaymentController::class, 'index']
    )->name('payments.index');


    /*
    |--------------------------------------------------------------------------
    | Customer Subscription History
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/subscriptions',
        [SubscriptionController::class, 'index']
    )->name('subscriptions.index');


    /*
    |--------------------------------------------------------------------------
    | Receipts
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/receipts',
        [ReceiptController::class, 'index']
    )->name('receipts.index');


    Route::get(
        '/receipts/{payment}',
        [ReceiptController::class, 'show']
    )->name('receipts.show');

});


/*
|--------------------------------------------------------------------------
| Customer Package and Payment Instruction Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'suspended'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Available Packages
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/packages',
        [PackageController::class, 'index']
    )->name('packages.index');


    /*
    |--------------------------------------------------------------------------
    | Customer Package M-Pesa Payment
    |--------------------------------------------------------------------------
    |
    | Customer-to-ISP Admin automatic payment remains intentionally paused.
    |
    */

    Route::get(
        '/mpesa/pay/{package}',
        [MpesaController::class, 'showPaymentForm']
    )
        ->middleware('software.license')
        ->name('mpesa.pay');


    Route::post(
        '/mpesa/pay/{package}',
        [MpesaController::class, 'initiatePayment']
    )
        ->middleware('software.license')
        ->name('mpesa.initiate');

});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/dashboard',
        [AdminController::class, 'dashboard']
    )->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Super Admin Only
    |--------------------------------------------------------------------------
    */

    Route::middleware('super.admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ISP Admin Accounts
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/admin-users',
            [AdminUserController::class, 'index']
        )->name('admin.admin-users.index');


        Route::post(
            '/admin/admin-users/{admin}/activate',
            [AdminUserController::class, 'activate']
        )->name('admin.admin-users.activate');


        Route::post(
            '/admin/admin-users/{admin}/deactivate',
            [AdminUserController::class, 'deactivate']
        )->name('admin.admin-users.deactivate');


        /*
        |--------------------------------------------------------------------------
        | Platform License Payment Approval
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/admin/license-payments/{licensePayment}/approve',
            [LicensePaymentController::class, 'approve']
        )->name('admin.license-payments.approve');


        Route::post(
            '/admin/license-payments/{licensePayment}/reject',
            [LicensePaymentController::class, 'reject']
        )->name('admin.license-payments.reject');


        Route::get(
            '/admin/license-receipts/{licensePayment}',
            [LicensePaymentController::class, 'showReceipt']
        )->name('admin.license-receipts.show');


        /*
        |--------------------------------------------------------------------------
        | Platform Settings
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/settings',
            [SystemSettingController::class, 'edit']
        )->name('admin.settings.edit');


        Route::post(
            '/admin/settings',
            [SystemSettingController::class, 'update']
        )->name('admin.settings.update');

    });


    /*
    |--------------------------------------------------------------------------
    | Admin Platform License Payments
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/license-payments',
        [LicensePaymentController::class, 'index']
    )->name('admin.license-payments.index');


    Route::post(
        '/admin/license-payments',
        [LicensePaymentController::class, 'store']
    )->name('admin.license-payments.store');


    /*
    |--------------------------------------------------------------------------
    | Automatic License M-Pesa STK Push
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/admin/license-payments/mpesa',
        [LicenseMpesaController::class, 'initiate']
    )
        ->middleware('only.admin')
        ->name('admin.license-payments.mpesa.initiate');


    Route::post(
        '/admin/license-payments/{payment}/mpesa/reconcile',
        [LicenseMpesaController::class, 'reconcile']
    )
        ->middleware('only.admin')
        ->name('admin.license-payments.mpesa.reconcile');


    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/notifications',
        [NotificationController::class, 'index']
    )->name('admin.notifications.index');


    Route::post(
        '/admin/notifications/{notification}/read',
        [NotificationController::class, 'markAsRead']
    )->name('admin.notifications.read');


    Route::post(
        '/admin/notifications/read-all',
        [NotificationController::class, 'markAllAsRead']
    )->name('admin.notifications.read-all');


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/reports',
        [ReportController::class, 'index']
    )->name('admin.reports.index');


    Route::get(
        '/admin/reports/payments/csv',
        [ReportController::class, 'exportPaymentsCsv']
    )->name('admin.reports.payments.csv');


    Route::get(
        '/admin/reports/customers/csv',
        [ReportController::class, 'exportCustomersCsv']
    )->name('admin.reports.customers.csv');


    Route::get(
        '/admin/reports/subscriptions/csv',
        [ReportController::class, 'exportSubscriptionsCsv']
    )->name('admin.reports.subscriptions.csv');


    /*
    |--------------------------------------------------------------------------
    | Audit Logs
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/audit-logs',
        [AuditLogController::class, 'index']
    )->name('admin.audit-logs.index');


    /*
    |--------------------------------------------------------------------------
    | Admin Activity
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/top-customers',
        [AdminController::class, 'topCustomers']
    )->name('admin.top-customers');


    Route::get(
        '/admin/payments',
        [AdminController::class, 'payments']
    )->name('admin.payments');


    Route::get(
        '/admin/recent-activity',
        [AdminController::class, 'recentActivity']
    )->name('admin.recent-activity');


    /*
    |--------------------------------------------------------------------------
    | Licensed ISP Admin Features
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'only.admin',
        'software.license',
    ])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Packages
        |--------------------------------------------------------------------------
        */

        Route::resource(
            '/admin/packages',
            PackageManagementController::class
        )
            ->except(['show'])
            ->names('admin.packages');


        Route::patch(
            '/admin/packages/{package}/status',
            [PackageManagementController::class, 'toggleStatus']
        )->name('admin.packages.toggle-status');


        Route::post(
            '/admin/packages/{package}/sync-profile',
            [PackageManagementController::class, 'syncProfile']
        )->name('admin.packages.sync-profile');


        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/customers',
            [CustomerController::class, 'index']
        )->name('admin.customers.index');


        Route::get(
            '/admin/customers/{customer}',
            [CustomerController::class, 'show']
        )->name('admin.customers.show');


        Route::post(
            '/admin/customers/{customer}/network-access',
            [CustomerController::class, 'provisionNetworkAccess']
        )->name('admin.customers.network-access');


        Route::post(
            '/admin/customers/{customer}/suspend',
            [CustomerController::class, 'suspend']
        )->name('admin.customers.suspend');


        Route::post(
            '/admin/customers/{customer}/activate',
            [CustomerController::class, 'activate']
        )->name('admin.customers.activate');


        /*
        |--------------------------------------------------------------------------
        | MikroTik Routers
        |--------------------------------------------------------------------------
        */

        Route::resource(
            '/admin/routers',
            RouterController::class
        )
            ->except(['show'])
            ->names('admin.routers');


        Route::get(
            '/admin/routers/{router}/setup-script',
            [RouterController::class, 'setupScript']
        )->name('admin.routers.setup-script');


        Route::post(
            '/admin/routers/{router}/test',
            [RouterController::class, 'testConnection']
        )->name('admin.routers.test');


        /*
        |--------------------------------------------------------------------------
        | Business Profile
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/business-profile',
            [BusinessProfileController::class, 'edit']
        )->name('admin.business-profile.edit');


        Route::post(
            '/admin/business-profile',
            [BusinessProfileController::class, 'update']
        )->name('admin.business-profile.update');


        /*
        |--------------------------------------------------------------------------
        | Hotspot Users
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/hotspot-users',
            [HotspotUserController::class, 'index']
        )->name('admin.hotspot-users.index');


        Route::post(
            '/admin/hotspot-users/{id}/activate',
            [HotspotUserController::class, 'activate']
        )->name('admin.hotspot-users.activate');


        Route::post(
            '/admin/hotspot-users/{id}/deactivate',
            [HotspotUserController::class, 'deactivate']
        )->name('admin.hotspot-users.deactivate');


        Route::post(
            '/admin/hotspot-users/{id}/reset-password',
            [HotspotUserController::class, 'resetPassword']
        )->name('admin.hotspot-users.reset-password');


        Route::post(
            '/admin/hotspot-users/{id}/sync-router',
            [HotspotUserController::class, 'syncRouter']
        )->name('admin.hotspot-users.sync-router');


        /*
        |--------------------------------------------------------------------------
        | Sessions
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/hotspot-sessions',
            [HotspotSessionController::class, 'index']
        )->name('admin.hotspot-sessions.index');


        /*
        |--------------------------------------------------------------------------
        | Subscriptions
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/admin/subscriptions',
            [SubscriptionManagementController::class, 'index']
        )->name('admin.subscriptions.index');


        Route::post(
            '/admin/subscriptions/{subscription}/activate',
            [SubscriptionManagementController::class, 'activate']
        )->name('admin.subscriptions.activate');


        Route::post(
            '/admin/subscriptions/{subscription}/expire',
            [SubscriptionManagementController::class, 'expire']
        )->name('admin.subscriptions.expire');

    });

});


/*
|--------------------------------------------------------------------------
| Public M-Pesa Callbacks
|--------------------------------------------------------------------------
*/

Route::post(
    '/mpesa/callback',
    [MpesaController::class, 'callback']
)->name('mpesa.callback');


Route::post(
    '/mpesa/license/callback',
    [LicenseMpesaController::class, 'callback']
)->name('license.mpesa.callback');


require __DIR__.'/auth.php';