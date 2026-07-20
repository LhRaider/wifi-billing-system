<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\LicensePayment;

class ReportController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'super_admin') {
            $totalRevenue = LicensePayment::where('status', 'approved')->sum('amount');
            $totalPayments = LicensePayment::count();
            $completedPayments = LicensePayment::where('status', 'approved')->count();
            $pendingPayments = LicensePayment::where('status', 'pending')->count();
            $totalCustomers = User::where('role', 'admin')->count();
            $activeSubscriptions = User::where('role', 'admin')
                ->where('service_status', 'active')
                ->where('service_expires_at', '>', now())
                ->count();
            $expiredSubscriptions = User::where('role', 'admin')
                ->where(function ($query) {
                    $query->where('service_status', 'expired')
                        ->orWhere('service_expires_at', '<', now());
                })
                ->count();
        } else {
            $adminId = auth()->id();

            $customerIds = User::where('role', 'customer')
                ->where('admin_id', $adminId)
                ->pluck('id');

            $totalRevenue = Payment::where('status', 'completed')
                ->whereIn('user_id', $customerIds)
                ->sum('amount');

            $totalPayments = Payment::whereIn('user_id', $customerIds)->count();

            $completedPayments = Payment::whereIn('user_id', $customerIds)
                ->where('status', 'completed')
                ->count();

            $pendingPayments = Payment::whereIn('user_id', $customerIds)
                ->where('status', 'pending')
                ->count();

            $totalCustomers = User::where('role', 'customer')
                ->where('admin_id', $adminId)
                ->count();

            $activeSubscriptions = Subscription::whereIn('user_id', $customerIds)
                ->where('status', 'active')
                ->count();

            $expiredSubscriptions = Subscription::whereIn('user_id', $customerIds)
                ->where('status', 'expired')
                ->count();
        }

        return view('admin.reports.index', compact(
            'totalRevenue',
            'totalPayments',
            'completedPayments',
            'pendingPayments',
            'totalCustomers',
            'activeSubscriptions',
            'expiredSubscriptions'
        ));
    }

    public function exportPaymentsCsv()
    {
        $filename = auth()->user()->role === 'super_admin'
            ? 'license-payments-report.csv'
            : 'payments-report.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            if (auth()->user()->role === 'super_admin') {
                fputcsv($file, ['ID', 'Months', 'Amount', 'Method', 'Transaction Code', 'Status', 'Start Date', 'Expiry Date', 'Created At']);

                $payments = LicensePayment::latest()->get();

                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->id,
                        $payment->months,
                        $payment->amount,
                        $payment->payment_method,
                        $payment->transaction_code,
                        $payment->status,
                        $payment->start_date,
                        $payment->expiry_date,
                        $payment->created_at,
                    ]);
                }
            } else {
                $customerIds = User::where('role', 'customer')
                    ->where('admin_id', auth()->id())
                    ->pluck('id');

                fputcsv($file, ['ID', 'User ID', 'Package ID', 'Amount', 'Phone Number', 'Status', 'Paid At', 'Created At']);

                $payments = Payment::whereIn('user_id', $customerIds)->latest()->get();

                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->id,
                        $payment->user_id,
                        $payment->package_id,
                        $payment->amount,
                        $payment->phone_number,
                        $payment->status,
                        $payment->paid_at,
                        $payment->created_at,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCustomersCsv()
    {
        $filename = auth()->user()->role === 'super_admin'
            ? 'admin-clients-report.csv'
            : 'customers-report.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Suspended', 'Service Status', 'Service Expires At', 'Created At']);

            if (auth()->user()->role === 'super_admin') {
                $customers = User::where('role', 'admin')->latest()->get();
            } else {
                $customers = User::where('role', 'customer')
                    ->where('admin_id', auth()->id())
                    ->latest()
                    ->get();
            }

            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->role,
                    $customer->is_suspended ? 'Yes' : 'No',
                    $customer->service_status ?? 'N/A',
                    $customer->service_expires_at ?? 'N/A',
                    $customer->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSubscriptionsCsv()
    {
        $filename = auth()->user()->role === 'super_admin'
            ? 'admin-subscriptions-report.csv'
            : 'subscriptions-report.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            if (auth()->user()->role === 'super_admin') {
                fputcsv($file, ['Admin ID', 'Name', 'Email', 'Service Status', 'Trial Ends', 'Service Expires', 'Created At']);

                $admins = User::where('role', 'admin')->latest()->get();

                foreach ($admins as $admin) {
                    fputcsv($file, [
                        $admin->id,
                        $admin->name,
                        $admin->email,
                        $admin->service_status,
                        $admin->trial_ends_at,
                        $admin->service_expires_at,
                        $admin->created_at,
                    ]);
                }
            } else {
                $customerIds = User::where('role', 'customer')
                    ->where('admin_id', auth()->id())
                    ->pluck('id');

                fputcsv($file, ['ID', 'User ID', 'Package ID', 'Start Time', 'End Time', 'Status', 'Created At']);

                $subscriptions = Subscription::whereIn('user_id', $customerIds)->latest()->get();

                foreach ($subscriptions as $subscription) {
                    fputcsv($file, [
                        $subscription->id,
                        $subscription->user_id,
                        $subscription->package_id,
                        $subscription->start_time,
                        $subscription->end_time,
                        $subscription->status,
                        $subscription->created_at,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}