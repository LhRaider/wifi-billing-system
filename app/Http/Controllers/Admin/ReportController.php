<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicensePayment;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Display platform or ISP reports.
     */
    public function index()
    {
        $user = auth()->user();

        $this->ensureAllowedRole($user);

        /*
        |--------------------------------------------------------------------------
        | Super Admin Platform Reports
        |--------------------------------------------------------------------------
        |
        | Super Admin only sees:
        | - ISP Admin accounts
        | - Platform license payments
        |
        | Individual ISP customers are intentionally excluded.
        |
        */

        if ($user->role === 'super_admin') {

            $totalRevenue = LicensePayment::where(
                'status',
                'approved'
            )->sum('amount');

            $totalPayments = LicensePayment::count();

            $completedPayments = LicensePayment::where(
                'status',
                'approved'
            )->count();

            $pendingPayments = LicensePayment::where(
                'status',
                'pending'
            )->count();

            $totalCustomers = User::where(
                'role',
                'admin'
            )->count();

            /*
            |--------------------------------------------------------------------------
            | Active ISP Admins
            |--------------------------------------------------------------------------
            |
            | An Admin is active if they have:
            | - a valid trial, or
            | - a valid paid platform subscription.
            |
            */

            $activeSubscriptions = User::where(
                'role',
                'admin'
            )
                ->where(function ($query) {

                    $query->where(function ($trialQuery) {

                        $trialQuery
                            ->where(
                                'service_status',
                                'trial'
                            )
                            ->whereNotNull(
                                'trial_ends_at'
                            )
                            ->where(
                                'trial_ends_at',
                                '>=',
                                now()
                            );

                    })->orWhere(function ($activeQuery) {

                        $activeQuery
                            ->where(
                                'service_status',
                                'active'
                            )
                            ->whereNotNull(
                                'service_expires_at'
                            )
                            ->where(
                                'service_expires_at',
                                '>=',
                                now()
                            );

                    });

                })
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Expired / Inactive ISP Admins
            |--------------------------------------------------------------------------
            */

            $expiredSubscriptions = User::where(
                'role',
                'admin'
            )
                ->where(function ($query) {

                    $query->where(function ($trialQuery) {

                        $trialQuery
                            ->where(
                                'service_status',
                                'trial'
                            )
                            ->where(function ($dateQuery) {

                                $dateQuery
                                    ->whereNull(
                                        'trial_ends_at'
                                    )
                                    ->orWhere(
                                        'trial_ends_at',
                                        '<',
                                        now()
                                    );

                            });

                    })->orWhere(function ($activeQuery) {

                        $activeQuery
                            ->where(
                                'service_status',
                                'active'
                            )
                            ->where(function ($dateQuery) {

                                $dateQuery
                                    ->whereNull(
                                        'service_expires_at'
                                    )
                                    ->orWhere(
                                        'service_expires_at',
                                        '<',
                                        now()
                                    );

                            });

                    })->orWhere(function ($statusQuery) {

                        $statusQuery
                            ->whereNotIn(
                                'service_status',
                                [
                                    'trial',
                                    'active',
                                ]
                            )
                            ->orWhereNull(
                                'service_status'
                            );

                    });

                })
                ->count();

        } else {

            /*
            |--------------------------------------------------------------------------
            | ISP Admin Reports
            |--------------------------------------------------------------------------
            |
            | Every customer-related query is restricted to customers belonging
            | to the currently authenticated ISP Admin.
            |
            */

            $adminId = $user->id;

            $customerIds = User::where(
                'role',
                'customer'
            )
                ->where(
                    'admin_id',
                    $adminId
                )
                ->pluck('id');

            $totalRevenue = Payment::whereIn(
                'user_id',
                $customerIds
            )
                ->where(
                    'status',
                    'completed'
                )
                ->sum('amount');

            $totalPayments = Payment::whereIn(
                'user_id',
                $customerIds
            )->count();

            $completedPayments = Payment::whereIn(
                'user_id',
                $customerIds
            )
                ->where(
                    'status',
                    'completed'
                )
                ->count();

            $pendingPayments = Payment::whereIn(
                'user_id',
                $customerIds
            )
                ->where(
                    'status',
                    'pending'
                )
                ->count();

            $totalCustomers = User::where(
                'role',
                'customer'
            )
                ->where(
                    'admin_id',
                    $adminId
                )
                ->count();

            $activeSubscriptions = Subscription::whereIn(
                'user_id',
                $customerIds
            )
                ->where(
                    'status',
                    'active'
                )
                ->count();

            $expiredSubscriptions = Subscription::whereIn(
                'user_id',
                $customerIds
            )
                ->where(
                    'status',
                    'expired'
                )
                ->count();
        }

        return view(
            'admin.reports.index',
            compact(
                'totalRevenue',
                'totalPayments',
                'completedPayments',
                'pendingPayments',
                'totalCustomers',
                'activeSubscriptions',
                'expiredSubscriptions'
            )
        );
    }

    /**
     * Export platform license payments or ISP customer payments.
     */
    public function exportPaymentsCsv()
    {
        $user = auth()->user();

        $this->ensureAllowedRole($user);

        $isSuperAdmin =
            $user->role === 'super_admin';

        $adminId =
            $user->id;

        $filename = $isSuperAdmin
            ? 'license-payments-report.csv'
            : 'payments-report.csv';

        $headers = [
            'Content-Type' =>
                'text/csv; charset=UTF-8',

            'Content-Disposition' =>
                'attachment; filename="' .
                $filename .
                '"',

            'X-Content-Type-Options' =>
                'nosniff',
        ];

        $callback = function () use (
            $isSuperAdmin,
            $adminId
        ) {

            $file = fopen(
                'php://output',
                'w'
            );

            /*
            |--------------------------------------------------------------------------
            | UTF-8 BOM
            |--------------------------------------------------------------------------
            |
            | Helps Microsoft Excel correctly display UTF-8 text.
            |
            */

            fwrite(
                $file,
                "\xEF\xBB\xBF"
            );

            if ($isSuperAdmin) {

                fputcsv(
                    $file,
                    [
                        'ID',
                        'ISP Admin',
                        'Months',
                        'Amount',
                        'Method',
                        'Transaction Code',
                        'Status',
                        'Start Date',
                        'Expiry Date',
                        'Created At',
                    ]
                );

                $payments = LicensePayment::with(
                    'user'
                )
                    ->latest()
                    ->get();

                foreach ($payments as $payment) {

                    fputcsv(
                        $file,
                        [
                            $payment->id,

                            $this->safeCsvValue(
                                $payment->user->name
                                ?? 'Unknown Admin'
                            ),

                            $payment->months,

                            $payment->amount,

                            $this->safeCsvValue(
                                $payment->payment_method
                            ),

                            $this->safeCsvValue(
                                $payment->transaction_code
                            ),

                            $this->safeCsvValue(
                                $payment->status
                            ),

                            $payment->start_date,

                            $payment->expiry_date,

                            $payment->created_at,
                        ]
                    );
                }

            } else {

                $customerIds = User::where(
                    'role',
                    'customer'
                )
                    ->where(
                        'admin_id',
                        $adminId
                    )
                    ->pluck('id');

                fputcsv(
                    $file,
                    [
                        'ID',
                        'User ID',
                        'Package ID',
                        'Amount',
                        'Phone Number',
                        'Status',
                        'Paid At',
                        'Created At',
                    ]
                );

                $payments = Payment::whereIn(
                    'user_id',
                    $customerIds
                )
                    ->latest()
                    ->get();

                foreach ($payments as $payment) {

                    fputcsv(
                        $file,
                        [
                            $payment->id,

                            $payment->user_id,

                            $payment->package_id,

                            $payment->amount,

                            $this->safeCsvValue(
                                $payment->phone_number
                            ),

                            $this->safeCsvValue(
                                $payment->status
                            ),

                            $payment->paid_at,

                            $payment->created_at,
                        ]
                    );
                }
            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }

    /**
     * Export ISP Admin accounts or customers.
     */
    public function exportCustomersCsv()
    {
        $user = auth()->user();

        $this->ensureAllowedRole($user);

        $isSuperAdmin =
            $user->role === 'super_admin';

        $adminId =
            $user->id;

        $filename = $isSuperAdmin
            ? 'isp-admins-report.csv'
            : 'customers-report.csv';

        $headers = [
            'Content-Type' =>
                'text/csv; charset=UTF-8',

            'Content-Disposition' =>
                'attachment; filename="' .
                $filename .
                '"',

            'X-Content-Type-Options' =>
                'nosniff',
        ];

        $callback = function () use (
            $isSuperAdmin,
            $adminId
        ) {

            $file = fopen(
                'php://output',
                'w'
            );

            fwrite(
                $file,
                "\xEF\xBB\xBF"
            );

            if ($isSuperAdmin) {

                fputcsv(
                    $file,
                    [
                        'ID',
                        'Name',
                        'Email',
                        'Suspended',
                        'Service Status',
                        'Trial Ends At',
                        'Service Expires At',
                        'Created At',
                    ]
                );

                $records = User::where(
                    'role',
                    'admin'
                )
                    ->latest()
                    ->get();

                foreach ($records as $admin) {

                    fputcsv(
                        $file,
                        [
                            $admin->id,

                            $this->safeCsvValue(
                                $admin->name
                            ),

                            $this->safeCsvValue(
                                $admin->email
                            ),

                            $admin->is_suspended
                                ? 'Yes'
                                : 'No',

                            $this->safeCsvValue(
                                $admin->service_status
                                ?? 'N/A'
                            ),

                            $admin->trial_ends_at
                                ?? 'N/A',

                            $admin->service_expires_at
                                ?? 'N/A',

                            $admin->created_at,
                        ]
                    );
                }

            } else {

                fputcsv(
                    $file,
                    [
                        'ID',
                        'Name',
                        'Email',
                        'Suspended',
                        'Created At',
                    ]
                );

                $records = User::where(
                    'role',
                    'customer'
                )
                    ->where(
                        'admin_id',
                        $adminId
                    )
                    ->latest()
                    ->get();

                foreach ($records as $customer) {

                    fputcsv(
                        $file,
                        [
                            $customer->id,

                            $this->safeCsvValue(
                                $customer->name
                            ),

                            $this->safeCsvValue(
                                $customer->email
                            ),

                            $customer->is_suspended
                                ? 'Yes'
                                : 'No',

                            $customer->created_at,
                        ]
                    );
                }
            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }

    /**
     * Export platform Admin licenses or ISP customer subscriptions.
     */
    public function exportSubscriptionsCsv()
    {
        $user = auth()->user();

        $this->ensureAllowedRole($user);

        $isSuperAdmin =
            $user->role === 'super_admin';

        $adminId =
            $user->id;

        $filename = $isSuperAdmin
            ? 'isp-admin-license-status-report.csv'
            : 'subscriptions-report.csv';

        $headers = [
            'Content-Type' =>
                'text/csv; charset=UTF-8',

            'Content-Disposition' =>
                'attachment; filename="' .
                $filename .
                '"',

            'X-Content-Type-Options' =>
                'nosniff',
        ];

        $callback = function () use (
            $isSuperAdmin,
            $adminId
        ) {

            $file = fopen(
                'php://output',
                'w'
            );

            fwrite(
                $file,
                "\xEF\xBB\xBF"
            );

            if ($isSuperAdmin) {

                fputcsv(
                    $file,
                    [
                        'Admin ID',
                        'Name',
                        'Email',
                        'Service Status',
                        'Trial Ends',
                        'Service Expires',
                        'Suspended',
                        'Created At',
                    ]
                );

                $admins = User::where(
                    'role',
                    'admin'
                )
                    ->latest()
                    ->get();

                foreach ($admins as $admin) {

                    fputcsv(
                        $file,
                        [
                            $admin->id,

                            $this->safeCsvValue(
                                $admin->name
                            ),

                            $this->safeCsvValue(
                                $admin->email
                            ),

                            $this->safeCsvValue(
                                $admin->service_status
                            ),

                            $admin->trial_ends_at,

                            $admin->service_expires_at,

                            $admin->is_suspended
                                ? 'Yes'
                                : 'No',

                            $admin->created_at,
                        ]
                    );
                }

            } else {

                $customerIds = User::where(
                    'role',
                    'customer'
                )
                    ->where(
                        'admin_id',
                        $adminId
                    )
                    ->pluck('id');

                fputcsv(
                    $file,
                    [
                        'ID',
                        'User ID',
                        'Package ID',
                        'Start Time',
                        'End Time',
                        'Status',
                        'Created At',
                    ]
                );

                $subscriptions = Subscription::whereIn(
                    'user_id',
                    $customerIds
                )
                    ->latest()
                    ->get();

                foreach ($subscriptions as $subscription) {

                    fputcsv(
                        $file,
                        [
                            $subscription->id,

                            $subscription->user_id,

                            $subscription->package_id,

                            $subscription->start_time,

                            $subscription->end_time,

                            $this->safeCsvValue(
                                $subscription->status
                            ),

                            $subscription->created_at,
                        ]
                    );
                }
            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }

    /**
     * Protect CSV cells from spreadsheet formula injection.
     */
    private function safeCsvValue($value): string
    {
        if ($value === null) {
            return '';
        }

        $value = (string) $value;

        if (
            $value !== ''
            &&
            in_array(
                $value[0],
                [
                    '=',
                    '+',
                    '-',
                    '@',
                ],
                true
            )
        ) {
            return "'" . $value;
        }

        return $value;
    }

    /**
     * Defense-in-depth role validation.
     */
    private function ensureAllowedRole($user): void
    {
        if (
            !$user
            ||
            !in_array(
                $user->role,
                [
                    'admin',
                    'super_admin',
                ],
                true
            )
        ) {
            abort(
                403,
                'You are not authorized to access reports.'
            );
        }
    }
}