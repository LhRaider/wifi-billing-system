<?php

use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Admin\HotspotSessionController;
use App\Http\Controllers\Admin\HotspotUserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\Admin\SubscriptionManagementController;
use App\Http\Controllers\Admin\PackageManagementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\LicensePaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PackageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RouterController;
use App\Http\Controllers\Admin\BusinessProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'suspended'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');

    Route::get('/payments', [PaymentController::class, 'index'])
        ->middleware('active.subscription')
        ->name('payments.index');

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])
        ->middleware('active.subscription')
        ->name('subscriptions.index');

    Route::get('/receipts', [ReceiptController::class, 'index'])
        ->name('receipts.index');

    Route::get('/receipts/{payment}', [ReceiptController::class, 'show'])
        ->name('receipts.show');

    Route::get('/mpesa/pay/{package}', [MpesaController::class, 'showPaymentForm'])
        ->middleware('software.license')
        ->name('mpesa.pay');

    Route::post('/mpesa/pay/{package}', [MpesaController::class, 'initiatePayment'])
        ->middleware('software.license')
        ->name('mpesa.initiate');
});

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    Route::middleware('super.admin')->group(function () {

        Route::get('/admin/admin-users', [AdminUserController::class, 'index'])
            ->name('admin.admin-users.index');

        Route::post('/admin/admin-users/{admin}/activate', [AdminUserController::class, 'activate'])
            ->name('admin.admin-users.activate');

        Route::post('/admin/admin-users/{admin}/deactivate', [AdminUserController::class, 'deactivate'])
            ->name('admin.admin-users.deactivate');

        Route::post('/admin/license-payments/{licensePayment}/approve', [LicensePaymentController::class, 'approve'])
            ->name('admin.license-payments.approve');

        Route::post('/admin/license-payments/{licensePayment}/reject', [LicensePaymentController::class, 'reject'])
            ->name('admin.license-payments.reject');

        Route::get('/admin/license-receipts/{licensePayment}', [LicensePaymentController::class, 'showReceipt'])
            ->name('admin.license-receipts.show');

    });

    Route::get('/admin/license-payments', [LicensePaymentController::class, 'index'])->name('admin.license-payments.index');
    Route::post('/admin/license-payments', [LicensePaymentController::class, 'store'])->name('admin.license-payments.store');

    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::post('/admin/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read-all');

    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/payments/csv', [ReportController::class, 'exportPaymentsCsv'])->name('admin.reports.payments.csv');
    Route::get('/admin/reports/customers/csv', [ReportController::class, 'exportCustomersCsv'])->name('admin.reports.customers.csv');
    Route::get('/admin/reports/subscriptions/csv', [ReportController::class, 'exportSubscriptionsCsv'])->name('admin.reports.subscriptions.csv');

    Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit-logs.index');

    Route::get('/admin/top-customers', [AdminController::class, 'topCustomers'])->name('admin.top-customers');
    Route::get('/admin/payments', [AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/admin/recent-activity', [AdminController::class, 'recentActivity'])->name('admin.recent-activity');

    Route::middleware('software.license')->group(function () {

        Route::resource('/admin/packages', PackageManagementController::class)->names('admin.packages');

        Route::post('/admin/packages/{package}/sync-profile',
            [PackageManagementController::class, 'syncProfile'])
            ->name('admin.packages.sync-profile');

        Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
        Route::get('/admin/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
        Route::post('/admin/customers/{customer}/suspend', [CustomerController::class, 'suspend'])->name('admin.customers.suspend');
        Route::post('/admin/customers/{customer}/activate', [CustomerController::class, 'activate'])->name('admin.customers.activate');

        Route::resource('/admin/routers', RouterController::class)->names('admin.routers');

        Route::get('/admin/routers/{router}/setup-script',
            [RouterController::class, 'setupScript'])
            ->name('admin.routers.setup-script');

        Route::get('/admin/routers/{router}/test',
            [RouterController::class, 'testConnection'])
            ->name('admin.routers.test');

        Route::get('/admin/business-profile',
            [BusinessProfileController::class,'edit'])
            ->name('admin.business-profile.edit');

        Route::post('/admin/business-profile',
            [BusinessProfileController::class,'update'])
            ->name('admin.business-profile.update');

        Route::get('/admin/hotspot-users',
            [HotspotUserController::class,'index'])
            ->name('admin.hotspot-users.index');

        Route::post('/admin/hotspot-users/{id}/activate',
            [HotspotUserController::class,'activate'])
            ->name('admin.hotspot-users.activate');

        Route::post('/admin/hotspot-users/{id}/deactivate',
            [HotspotUserController::class,'deactivate'])
            ->name('admin.hotspot-users.deactivate');

        Route::post('/admin/hotspot-users/{id}/reset-password',
            [HotspotUserController::class,'resetPassword'])
            ->name('admin.hotspot-users.reset-password');

        Route::post('/admin/hotspot-users/{id}/sync-router',
            [HotspotUserController::class,'syncRouter'])
            ->name('admin.hotspot-users.sync-router');

        Route::get('/admin/hotspot-sessions',
            [HotspotSessionController::class,'index'])
            ->name('admin.hotspot-sessions.index');

        Route::get('/admin/subscriptions',
            [SubscriptionManagementController::class,'index'])
            ->name('admin.subscriptions.index');

        Route::post('/admin/subscriptions/{subscription}/activate',
            [SubscriptionManagementController::class,'activate'])
            ->name('admin.subscriptions.activate');

        Route::post('/admin/subscriptions/{subscription}/expire',
            [SubscriptionManagementController::class,'expire'])
            ->name('admin.subscriptions.expire');

        Route::get('/admin/settings',
            [SystemSettingController::class,'edit'])
            ->name('admin.settings.edit');

        Route::post('/admin/settings',
            [SystemSettingController::class,'update'])
            ->name('admin.settings.update');

    });

});

Route::post('/profile/business', [ProfileController::class, 'updateBusiness'])
    ->middleware(['auth', 'suspended'])
    ->name('profile.business.update');

Route::post('/mpesa/callback', [MpesaController::class, 'callback'])
    ->name('mpesa.callback');

require __DIR__.'/auth.php';