<?php

namespace App\Http\Controllers;

use App\Models\HotspotUser;
use App\Models\LicensePayment;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $user = $this->currentAdminAccount();

        $isSuperAdmin = $user->role === 'super_admin';

        $hour = (int) now()->format('H');

        $greeting = $hour < 12
            ? 'Good morning'
            : ($hour < 17
                ? 'Good afternoon'
                : 'Good evening');

        $adminName = $user->name ?: 'Admin';

        if ($isSuperAdmin) {
            /*
            |--------------------------------------------------------------------------
            | Super Admin Dashboard
            |--------------------------------------------------------------------------
            |
            | Platform-only information.
            | Customer-level ISP data is intentionally excluded.
            |--------------------------------------------------------------------------
            */

            $totalCustomers = User::query()
                ->where('role', 'admin')
                ->count();

            $approvedLicenseQuery = LicensePayment::query()
                ->where('status', 'approved');

            $totalPayments = (clone $approvedLicenseQuery)->count();

            $totalRevenue = (clone $approvedLicenseQuery)
                ->sum('amount');

            $todayRevenue = (clone $approvedLicenseQuery)
                ->whereDate('created_at', today())
                ->sum('amount');

            $monthRevenue = (clone $approvedLicenseQuery)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('amount');

            $activeSubscriptions = User::query()
                ->where('role', 'admin')
                ->where(function ($query) {
                    $query
                        ->where(function ($trialQuery) {
                            $trialQuery
                                ->where('service_status', 'trial')
                                ->whereNotNull('trial_ends_at')
                                ->where('trial_ends_at', '>=', now());
                        })
                        ->orWhere(function ($activeQuery) {
                            $activeQuery
                                ->where('service_status', 'active')
                                ->whereNotNull('service_expires_at')
                                ->where('service_expires_at', '>=', now());
                        });
                })
                ->count();

            $expiredSubscriptions = User::query()
                ->where('role', 'admin')
                ->where(function ($query) {
                    $query
                        ->where(function ($trialQuery) {
                            $trialQuery
                                ->where('service_status', 'trial')
                                ->where(function ($dateQuery) {
                                    $dateQuery
                                        ->whereNull('trial_ends_at')
                                        ->orWhere('trial_ends_at', '<', now());
                                });
                        })
                        ->orWhere(function ($activeQuery) {
                            $activeQuery
                                ->where('service_status', 'active')
                                ->where(function ($dateQuery) {
                                    $dateQuery
                                        ->whereNull('service_expires_at')
                                        ->orWhere('service_expires_at', '<', now());
                                });
                        })
                        ->orWhere(function ($statusQuery) {
                            $statusQuery
                                ->whereNull('service_status')
                                ->orWhereNotIn(
                                    'service_status',
                                    ['trial', 'active']
                                );
                        });
                })
                ->count();

            /*
             * Customer-level ISP metrics are intentionally hidden
             * from Super Admin.
             */
            $totalPackages = 0;
            $totalRouters = 0;
            $totalHotspotUsers = 0;
            $activeHotspotUsers = 0;

            $recentPayments = LicensePayment::query()
                ->with('user')
                ->latest('id')
                ->limit(7)
                ->get();

            $monthlyRevenue = LicensePayment::query()
                ->where('status', 'approved')
                ->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as month')
                ->selectRaw('SUM(amount) as total')
                ->groupByRaw('MONTH(created_at)')
                ->orderBy('month')
                ->get();

            $packageDistributionLabels = collect([
                '1 Month',
                '2 Months',
                '3 Months',
            ]);

            $packageDistributionData = collect([
                LicensePayment::query()
                    ->where('months', 1)
                    ->where('status', 'approved')
                    ->count(),

                LicensePayment::query()
                    ->where('months', 2)
                    ->where('status', 'approved')
                    ->count(),

                LicensePayment::query()
                    ->where('months', 3)
                    ->where('status', 'approved')
                    ->count(),
            ]);

            $customerRegistrations = User::query()
                ->where('role', 'admin')
                ->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as month')
                ->selectRaw('COUNT(*) as total')
                ->groupByRaw('MONTH(created_at)')
                ->orderBy('month')
                ->get();

            $packagePerformance = collect();

            $recentActivities = $this
                ->superAdminActivityQuery()
                ->orderByDesc('time')
                ->limit(6)
                ->get()
                ->map(fn ($activity) => $this->formatActivity($activity));

            $topCustomers = $this
                ->topIspAdminsQuery()
                ->limit(7)
                ->get()
                ->map(function ($admin) {
                    return [
                        'name' => $admin->name,
                        'payments' => (int) $admin->payments,
                        'spent' => (float) $admin->spent,
                    ];
                });
        } else {
            /*
            |--------------------------------------------------------------------------
            | ISP Admin Dashboard
            |--------------------------------------------------------------------------
            */

            $adminId = $user->id;

            $totalCustomers = User::query()
                ->where('role', 'customer')
                ->where('admin_id', $adminId)
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Tenant-safe Payments
            |--------------------------------------------------------------------------
            |
            | Both the customer AND package must belong to this ISP Admin.
            |--------------------------------------------------------------------------
            */

            $tenantPayments = $this->tenantPaymentQuery($adminId);

            $completedPayments = (clone $tenantPayments)
                ->where('status', 'completed');

            $totalPayments = (clone $completedPayments)->count();

            $totalRevenue = (clone $completedPayments)
                ->sum('amount');

            $todayRevenue = (clone $completedPayments)
                ->whereDate('created_at', today())
                ->sum('amount');

            $monthRevenue = (clone $completedPayments)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | Tenant-safe Subscriptions
            |--------------------------------------------------------------------------
            */

            $tenantSubscriptions = $this
                ->tenantSubscriptionQuery($adminId);

            $activeSubscriptions = (clone $tenantSubscriptions)
                ->where('status', 'active')
                ->where('start_time', '<=', now())
                ->where('end_time', '>', now())
                ->count();

            $expiredSubscriptions = (clone $tenantSubscriptions)
                ->where('status', 'expired')
                ->count();

            $totalPackages = Package::query()
                ->where('admin_id', $adminId)
                ->count();

            $totalRouters = Router::query()
                ->where('admin_id', $adminId)
                ->count();

            $totalHotspotUsers = HotspotUser::query()
                ->whereHas('user', function ($query) use ($adminId) {
                    $query
                        ->where('role', 'customer')
                        ->where('admin_id', $adminId);
                })
                ->whereHas('router', function ($query) use ($adminId) {
                    $query->where('admin_id', $adminId);
                })
                ->count();

            $activeHotspotUsers = HotspotUser::query()
                ->where('status', 'active')
                ->whereHas('user', function ($query) use ($adminId) {
                    $query
                        ->where('role', 'customer')
                        ->where('admin_id', $adminId);
                })
                ->whereHas('router', function ($query) use ($adminId) {
                    $query->where('admin_id', $adminId);
                })
                ->count();

            $recentPayments = $this
                ->tenantPaymentQuery($adminId)
                ->with([
                    'user',
                    'package',
                ])
                ->latest('id')
                ->limit(7)
                ->get();

            $monthlyRevenue = $this
                ->tenantPaymentQuery($adminId)
                ->where('status', 'completed')
                ->whereYear('payments.created_at', now()->year)
                ->selectRaw(
                    'MONTH(payments.created_at) as month'
                )
                ->selectRaw(
                    'SUM(payments.amount) as total'
                )
                ->groupByRaw(
                    'MONTH(payments.created_at)'
                )
                ->orderBy('month')
                ->get();

            $adminPackages = Package::query()
                ->where('admin_id', $adminId)
                ->orderBy('name')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Package Performance
            |--------------------------------------------------------------------------
            */

            $packagePerformance = $adminPackages
                ->map(function ($package) use ($adminId) {
                    $activeUsers = $this
                        ->tenantSubscriptionQuery($adminId)
                        ->where(
                            'package_id',
                            $package->id
                        )
                        ->where('status', 'active')
                        ->where('start_time', '<=', now())
                        ->where('end_time', '>', now())
                        ->count();

                    $monthlyPackageRevenue = $this
                        ->tenantPaymentQuery($adminId)
                        ->where(
                            'package_id',
                            $package->id
                        )
                        ->where('status', 'completed')
                        ->whereYear(
                            'payments.created_at',
                            now()->year
                        )
                        ->whereMonth(
                            'payments.created_at',
                            now()->month
                        )
                        ->sum('amount');

                    return [
                        'name' => $package->name,
                        'price' => $package->price,
                        'active_users' => $activeUsers,
                        'monthly_revenue' => $monthlyPackageRevenue,
                    ];
                });

            $packageDistributionLabels = $adminPackages
                ->pluck('name');

            $packagePaymentCounts = $this
                ->tenantPaymentQuery($adminId)
                ->where('status', 'completed')
                ->selectRaw(
                    'payments.package_id, COUNT(*) as total'
                )
                ->groupBy('payments.package_id')
                ->pluck('total', 'payments.package_id');

            $packageDistributionData = $adminPackages
                ->map(function ($package) use ($packagePaymentCounts) {
                    return (int) (
                        $packagePaymentCounts[$package->id]
                        ?? 0
                    );
                });

            $customerRegistrations = User::query()
                ->where('role', 'customer')
                ->where('admin_id', $adminId)
                ->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as month')
                ->selectRaw('COUNT(*) as total')
                ->groupByRaw('MONTH(created_at)')
                ->orderBy('month')
                ->get();

            $recentActivities = $this
                ->adminActivityQuery($adminId)
                ->orderByDesc('time')
                ->limit(6)
                ->get()
                ->map(fn ($activity) => $this->formatActivity($activity));

            $topCustomers = $this
                ->topCustomersQuery($adminId)
                ->limit(7)
                ->get()
                ->map(function ($customer) {
                    return [
                        'name' => $customer->name,
                        'payments' => (int) $customer->payments,
                        'spent' => (float) $customer->spent,
                    ];
                });
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Chart Data
        |--------------------------------------------------------------------------
        */

        $chartLabels = $monthlyRevenue
            ->pluck('month')
            ->map(
                fn ($month) => date(
                    'M',
                    mktime(
                        0,
                        0,
                        0,
                        (int) $month,
                        1
                    )
                )
            );

        $chartData = $monthlyRevenue
            ->pluck('total');

        $customerChartLabels = $customerRegistrations
            ->pluck('month')
            ->map(
                fn ($month) => date(
                    'M',
                    mktime(
                        0,
                        0,
                        0,
                        (int) $month,
                        1
                    )
                )
            );

        $customerChartData = $customerRegistrations
            ->pluck('total');

        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        if ($isSuperAdmin) {
            $unreadNotifications = Notification::query()
                ->where('is_read', false)
                ->where('audience', 'super_admin')
                ->count();
        } else {
            $unreadNotifications = Notification::query()
                ->where('is_read', false)
                ->where('audience', 'admin')
                ->where('user_id', $user->id)
                ->count();
        }

        return view(
            'admin.dashboard',
            compact(
                'isSuperAdmin',
                'totalCustomers',
                'totalPayments',
                'totalRevenue',
                'todayRevenue',
                'monthRevenue',
                'activeSubscriptions',
                'expiredSubscriptions',
                'totalPackages',
                'totalRouters',
                'totalHotspotUsers',
                'activeHotspotUsers',
                'unreadNotifications',
                'recentPayments',
                'chartLabels',
                'chartData',
                'packagePerformance',
                'packageDistributionLabels',
                'packageDistributionData',
                'customerChartLabels',
                'customerChartData',
                'greeting',
                'adminName',
                'recentActivities',
                'topCustomers'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payments Page
    |--------------------------------------------------------------------------
    */

    public function payments()
    {
        $user = $this->currentAdminAccount();

        $isSuperAdmin = $user->role === 'super_admin';

        if ($isSuperAdmin) {
            $payments = LicensePayment::query()
                ->with('user')
                ->latest('id')
                ->paginate(15)
                ->withQueryString();
        } else {
            $payments = $this
                ->tenantPaymentQuery($user->id)
                ->with([
                    'user',
                    'package',
                ])
                ->latest('payments.id')
                ->paginate(15)
                ->withQueryString();
        }

        return view(
            'admin.payments.index',
            compact(
                'payments',
                'isSuperAdmin'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Top Customers / Top ISP Admins
    |--------------------------------------------------------------------------
    */

    public function topCustomers()
    {
        $user = $this->currentAdminAccount();

        $isSuperAdmin = $user->role === 'super_admin';

        if ($isSuperAdmin) {
            $topCustomers = $this
                ->topIspAdminsQuery()
                ->paginate(15)
                ->withQueryString();
        } else {
            $topCustomers = $this
                ->topCustomersQuery($user->id)
                ->paginate(15)
                ->withQueryString();
        }

        $topCustomers->setCollection(
            $topCustomers
                ->getCollection()
                ->map(function ($row) {
                    return [
                        'name' => $row->name,
                        'email' => $row->email,
                        'payments' => (int) $row->payments,
                        'spent' => (float) $row->spent,
                    ];
                })
        );

        return view(
            'admin.top-customers.index',
            compact(
                'topCustomers',
                'isSuperAdmin'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Recent Activity
    |--------------------------------------------------------------------------
    */

    public function recentActivity()
    {
        $user = $this->currentAdminAccount();

        $isSuperAdmin = $user->role === 'super_admin';

        $activityQuery = $isSuperAdmin
            ? $this->superAdminActivityQuery()
            : $this->adminActivityQuery($user->id);

        $recentActivities = $activityQuery
            ->orderByDesc('time')
            ->paginate(15)
            ->withQueryString();

        $recentActivities->setCollection(
            $recentActivities
                ->getCollection()
                ->map(
                    fn ($activity) =>
                        $this->formatActivity($activity)
                )
        );

        return view(
            'admin.recent-activity.index',
            compact(
                'recentActivities',
                'isSuperAdmin'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Tenant-safe Customer Payment Query
    |--------------------------------------------------------------------------
    */

    private function tenantPaymentQuery(int $adminId): Builder
    {
        return Payment::query()
            ->whereHas('user', function ($query) use ($adminId) {
                $query
                    ->where('role', 'customer')
                    ->where('admin_id', $adminId);
            })
            ->whereHas('package', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Tenant-safe Subscription Query
    |--------------------------------------------------------------------------
    */

    private function tenantSubscriptionQuery(int $adminId): Builder
    {
        return Subscription::query()
            ->whereHas('user', function ($query) use ($adminId) {
                $query
                    ->where('role', 'customer')
                    ->where('admin_id', $adminId);
            })
            ->whereHas('package', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Top ISP Admins Query
    |--------------------------------------------------------------------------
    */

    private function topIspAdminsQuery()
    {
        return User::query()
            ->where('users.role', 'admin')
            ->leftJoin(
                'license_payments',
                function ($join) {
                    $join
                        ->on(
                            'license_payments.user_id',
                            '=',
                            'users.id'
                        )
                        ->where(
                            'license_payments.status',
                            '=',
                            'approved'
                        );
                }
            )
            ->select([
                'users.id',
                'users.name',
                'users.email',
            ])
            ->selectRaw(
                'COUNT(license_payments.id) as payments'
            )
            ->selectRaw(
                'COALESCE(SUM(license_payments.amount), 0) as spent'
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.email'
            )
            ->orderByDesc('spent')
            ->orderByDesc('payments')
            ->orderBy('users.name');
    }

    /*
    |--------------------------------------------------------------------------
    | Top Tenant Customers Query
    |--------------------------------------------------------------------------
    */

    private function topCustomersQuery(int $adminId)
    {
        return User::query()
            ->where('users.role', 'customer')
            ->where('users.admin_id', $adminId)
            ->leftJoin(
                'payments',
                function ($join) {
                    $join
                        ->on(
                            'payments.user_id',
                            '=',
                            'users.id'
                        )
                        ->where(
                            'payments.status',
                            '=',
                            'completed'
                        );
                }
            )
            ->leftJoin(
                'packages',
                function ($join) use ($adminId) {
                    $join
                        ->on(
                            'packages.id',
                            '=',
                            'payments.package_id'
                        )
                        ->where(
                            'packages.admin_id',
                            '=',
                            $adminId
                        );
                }
            )
            ->select([
                'users.id',
                'users.name',
                'users.email',
            ])
            ->selectRaw(
                'COUNT(packages.id) as payments'
            )
            ->selectRaw(
                'COALESCE(SUM(
                    CASE
                        WHEN packages.id IS NOT NULL
                        THEN payments.amount
                        ELSE 0
                    END
                ), 0) as spent'
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.email'
            )
            ->orderByDesc('spent')
            ->orderByDesc('payments')
            ->orderBy('users.name');
    }

    /*
    |--------------------------------------------------------------------------
    | Super Admin Activity Query
    |--------------------------------------------------------------------------
    */

    private function superAdminActivityQuery()
    {
        $licensePayments = DB::table('license_payments')
            ->leftJoin(
                'users',
                'users.id',
                '=',
                'license_payments.user_id'
            )
            ->selectRaw(
                "
                CONCAT(
                    'License payment ',
                    license_payments.status,
                    ' from ',
                    COALESCE(users.name, 'ISP Admin'),
                    ' - KES ',
                    FORMAT(license_payments.amount, 2)
                ) as message
                "
            )
            ->selectRaw(
                'license_payments.created_at as time'
            )
            ->selectRaw(
                "'fas fa-money-check-alt' as icon"
            )
            ->selectRaw(
                "'#16a34a' as color"
            );

        $registrations = DB::table('users')
            ->where('role', 'admin')
            ->selectRaw(
                "
                CONCAT(
                    'ISP Admin registered: ',
                    users.name
                ) as message
                "
            )
            ->selectRaw(
                'users.created_at as time'
            )
            ->selectRaw(
                "'fas fa-user-shield' as icon"
            )
            ->selectRaw(
                "'#2563eb' as color"
            );

        $union = $licensePayments
            ->unionAll($registrations);

        return DB::query()
            ->fromSub(
                $union,
                'platform_activities'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ISP Admin Activity Query
    |--------------------------------------------------------------------------
    */

    private function adminActivityQuery(int $adminId)
    {
        $payments = DB::table('payments')
            ->join(
                'users',
                'users.id',
                '=',
                'payments.user_id'
            )
            ->join(
                'packages',
                'packages.id',
                '=',
                'payments.package_id'
            )
            ->where('users.role', 'customer')
            ->where('users.admin_id', $adminId)
            ->where('packages.admin_id', $adminId)
            ->selectRaw(
                "
                CONCAT(
                    'Payment ',
                    payments.status,
                    ' from ',
                    COALESCE(
                        users.name,
                        payments.phone_number,
                        'Customer'
                    )
                ) as message
                "
            )
            ->selectRaw(
                'payments.created_at as time'
            )
            ->selectRaw(
                "'fas fa-money-bill-wave' as icon"
            )
            ->selectRaw(
                "'#16a34a' as color"
            );

        $subscriptions = DB::table('subscriptions')
            ->join(
                'users',
                'users.id',
                '=',
                'subscriptions.user_id'
            )
            ->join(
                'packages',
                'packages.id',
                '=',
                'subscriptions.package_id'
            )
            ->where('users.role', 'customer')
            ->where('users.admin_id', $adminId)
            ->where('packages.admin_id', $adminId)
            ->selectRaw(
                "
                CONCAT(
                    'Subscription ',
                    subscriptions.status,
                    ' for ',
                    users.name
                ) as message
                "
            )
            ->selectRaw(
                'subscriptions.created_at as time'
            )
            ->selectRaw(
                "'fas fa-file-invoice' as icon"
            )
            ->selectRaw(
                "'#2563eb' as color"
            );

        $registrations = DB::table('users')
            ->where('role', 'customer')
            ->where('admin_id', $adminId)
            ->selectRaw(
                "
                CONCAT(
                    'New customer registered: ',
                    users.name
                ) as message
                "
            )
            ->selectRaw(
                'users.created_at as time'
            )
            ->selectRaw(
                "'fas fa-user-plus' as icon"
            )
            ->selectRaw(
                "'#7c3aed' as color"
            );

        $union = $payments
            ->unionAll($subscriptions)
            ->unionAll($registrations);

        return DB::query()
            ->fromSub(
                $union,
                'tenant_activities'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Formatter
    |--------------------------------------------------------------------------
    */

    private function formatActivity(object $activity): array
    {
        return [
            'message' => $activity->message,
            'time' => Carbon::parse($activity->time),
            'icon' => $activity->icon,
            'color' => $activity->color,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Current Admin Account
    |--------------------------------------------------------------------------
    */

    private function currentAdminAccount(): User
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        if (
            !in_array(
                $user->role,
                ['admin', 'super_admin'],
                true
            )
        ) {
            abort(403);
        }

        return $user;
    }
}