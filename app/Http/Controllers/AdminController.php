<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\Router;
use App\Models\HotspotUser;
use App\Models\SoftwareLicense;
use App\Models\Notification;
use App\Models\LicensePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';

        $hour = now()->format('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $adminName = $user->name ?? 'Admin';

        $softwareLicense = SoftwareLicense::first();
        $licenseDaysRemaining = null;

        if ($softwareLicense && $softwareLicense->expiry_date) {
            $licenseDaysRemaining = now()->diffInDays($softwareLicense->expiry_date, false);
        }

        if ($isSuperAdmin) {
            $totalCustomers = User::where('role', 'admin')->count();
            $totalPayments = LicensePayment::where('status', 'approved')->count();
            $totalRevenue = LicensePayment::where('status', 'approved')->sum('amount');

            $todayRevenue = LicensePayment::where('status', 'approved')->whereDate('created_at', today())->sum('amount');
            $monthRevenue = LicensePayment::where('status', 'approved')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');

            $activeSubscriptions = User::where('role', 'admin')->where('service_status', 'active')->where('service_expires_at', '>', now())->count();
            $expiredSubscriptions = User::where('role', 'admin')->where(function ($query) {
                $query->where('service_status', 'expired')->orWhere('service_expires_at', '<', now());
            })->count();

            $totalPackages = 0;
            $totalRouters = 0;
            $totalHotspotUsers = 0;
            $activeHotspotUsers = 0;

            $recentPayments = LicensePayment::with('user')->latest()->take(7)->get();

            $monthlyRevenue = LicensePayment::where('status', 'approved')
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $packageDistributionLabels = collect(['1 Month', '2 Months', '3 Months']);
            $packageDistributionData = collect([
                LicensePayment::where('months', 1)->where('status', 'approved')->count(),
                LicensePayment::where('months', 2)->where('status', 'approved')->count(),
                LicensePayment::where('months', 3)->where('status', 'approved')->count(),
            ]);

            $customerRegistrations = User::where('role', 'admin')
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $packagePerformance = collect();

            $recentActivities = collect();

            User::where('role', 'admin')->latest()->take(5)->get()->each(function ($admin) use ($recentActivities) {
                $recentActivities->push([
                    'message' => 'Admin account registered: '.$admin->name,
                    'time' => $admin->created_at,
                    'icon' => 'fas fa-user-shield',
                    'color' => '#2563eb',
                ]);
            });

            LicensePayment::latest()->take(5)->get()->each(function ($payment) use ($recentActivities) {
                $recentActivities->push([
                    'message' => 'License payment '.$payment->status.' - KES '.number_format($payment->amount, 2),
                    'time' => $payment->created_at,
                    'icon' => 'fas fa-money-check-alt',
                    'color' => '#16a34a',
                ]);
            });

            $recentActivities = $recentActivities->sortByDesc('time')->take(6);

            $topCustomers = User::where('role', 'admin')->get()->map(function ($admin) {
                return [
                    'name' => $admin->name,
                    'payments' => LicensePayment::where('user_id', $admin->id)->where('status', 'approved')->count(),
                    'spent' => LicensePayment::where('user_id', $admin->id)->where('status', 'approved')->sum('amount'),
                ];
            })->sortByDesc('spent')->take(7);
        } else {
            $customerIds = User::where('role', 'customer')
                ->where('admin_id', $user->id)
                ->pluck('id');

            $totalCustomers = $customerIds->count();

            $totalPayments = Payment::whereIn('user_id', $customerIds)->where('status', 'completed')->count();
            $totalRevenue = Payment::whereIn('user_id', $customerIds)->where('status', 'completed')->sum('amount');
            $todayRevenue = Payment::whereIn('user_id', $customerIds)->where('status', 'completed')->whereDate('created_at', today())->sum('amount');
            $monthRevenue = Payment::whereIn('user_id', $customerIds)->where('status', 'completed')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');

            $activeSubscriptions = Subscription::whereIn('user_id', $customerIds)->where('status', 'active')->count();
            $expiredSubscriptions = Subscription::whereIn('user_id', $customerIds)->where('status', 'expired')->count();

            $totalPackages = Package::where('admin_id', $user->id)->count();
            $totalRouters = Router::count();

            $totalHotspotUsers = HotspotUser::whereHas('user', function ($query) use ($user) {
                $query->where('admin_id', $user->id);
            })->count();

            $activeHotspotUsers = HotspotUser::where('status', 'active')->whereHas('user', function ($query) use ($user) {
                $query->where('admin_id', $user->id);
            })->count();

            $recentPayments = Payment::with(['user', 'package'])
                ->whereIn('user_id', $customerIds)
                ->latest()
                ->take(7)
                ->get();

            $monthlyRevenue = Payment::whereIn('user_id', $customerIds)
                ->where('status', 'completed')
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $adminPackages = Package::where('admin_id', $user->id)->get();

            $packagePerformance = $adminPackages->map(function ($package) use ($customerIds) {
                return [
                    'name' => $package->name,
                    'price' => $package->price,
                    'active_users' => Subscription::where('package_id', $package->id)
                        ->whereIn('user_id', $customerIds)
                        ->where('status', 'active')
                        ->count(),
                    'monthly_revenue' => Payment::where('package_id', $package->id)
                        ->whereIn('user_id', $customerIds)
                        ->where('status', 'completed')
                        ->sum('amount'),
                ];
            });

            $packageDistributionLabels = $adminPackages->pluck('name');

            $packageDistributionData = $adminPackages->map(function ($package) use ($customerIds) {
                return Payment::where('package_id', $package->id)
                    ->whereIn('user_id', $customerIds)
                    ->where('status', 'completed')
                    ->count();
            });

            $customerRegistrations = User::where('role', 'customer')
                ->where('admin_id', $user->id)
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $recentActivities = collect();

            $recentPayments->each(function ($payment) use ($recentActivities) {
                $recentActivities->push([
                    'message' => 'Payment received from '.($payment->user->name ?? $payment->phone_number),
                    'time' => $payment->created_at,
                    'icon' => 'fas fa-money-bill-wave',
                    'color' => '#16a34a',
                ]);
            });

            Subscription::whereIn('user_id', $customerIds)->latest()->take(5)->get()->each(function ($subscription) use ($recentActivities) {
                $recentActivities->push([
                    'message' => 'Subscription '.$subscription->status,
                    'time' => $subscription->created_at,
                    'icon' => 'fas fa-file-invoice',
                    'color' => '#2563eb',
                ]);
            });

            User::where('role', 'customer')->where('admin_id', $user->id)->latest()->take(5)->get()->each(function ($customer) use ($recentActivities) {
                $recentActivities->push([
                    'message' => 'New customer registered: '.$customer->name,
                    'time' => $customer->created_at,
                    'icon' => 'fas fa-user-plus',
                    'color' => '#7c3aed',
                ]);
            });

            $recentActivities = $recentActivities->sortByDesc('time')->take(6);

            $topCustomers = User::where('role', 'customer')->where('admin_id', $user->id)->get()->map(function ($customer) {
                return [
                    'name' => $customer->name,
                    'payments' => Payment::where('user_id', $customer->id)->where('status', 'completed')->count(),
                    'spent' => Payment::where('user_id', $customer->id)->where('status', 'completed')->sum('amount'),
                ];
            })->sortByDesc('spent')->take(7);
        }

        $chartLabels = $monthlyRevenue->pluck('month')->map(fn ($month) => date('M', mktime(0, 0, 0, $month, 1)));
        $chartData = $monthlyRevenue->pluck('total');

        $customerChartLabels = $customerRegistrations->pluck('month')->map(fn ($month) => date('M', mktime(0, 0, 0, $month, 1)));
        $customerChartData = $customerRegistrations->pluck('total');

        if ($isSuperAdmin) {
            $unreadNotifications = Notification::where('is_read', false)
                ->where('audience', 'super_admin')
                ->count();
        } else {
            $unreadNotifications = Notification::where('is_read', false)
                ->where('audience', 'admin')
                ->where('user_id', $user->id)
                ->count();
        }

        return view('admin.dashboard', compact(
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
            'softwareLicense',
            'licenseDaysRemaining',
            'customerChartData',
            'greeting',
            'adminName',
            'recentActivities',
            'topCustomers'
        ));
    }

    public function payments()
    {
        if (auth()->user()->role === 'super_admin') {
            $payments = LicensePayment::with('user')->latest()->paginate(7);
        } else {
            $customerIds = User::where('role', 'customer')->where('admin_id', auth()->id())->pluck('id');

            $payments = Payment::whereIn('user_id', $customerIds)
                ->latest()
                ->paginate(7);
        }

        return view('admin.payments.index', compact('payments'));
    }

    public function topCustomers()
    {
        if (auth()->user()->role === 'super_admin') {
            $customers = User::where('role', 'admin')->get()->map(function ($admin) {
                return [
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'payments' => LicensePayment::where('user_id', $admin->id)->where('status', 'approved')->count(),
                    'spent' => LicensePayment::where('user_id', $admin->id)->where('status', 'approved')->sum('amount'),
                ];
            })->sortByDesc('spent')->values();
        } else {
            $customers = User::where('role', 'customer')->where('admin_id', auth()->id())->get()->map(function ($customer) {
                return [
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'payments' => Payment::where('user_id', $customer->id)->where('status', 'completed')->count(),
                    'spent' => Payment::where('user_id', $customer->id)->where('status', 'completed')->sum('amount'),
                ];
            })->sortByDesc('spent')->values();
        }

        $page = request()->get('page', 1);
        $perPage = 7;

        $topCustomers = new LengthAwarePaginator(
            $customers->forPage($page, $perPage),
            $customers->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('admin.top-customers.index', compact('topCustomers'));
    }

    public function recentActivity()
    {
        $activities = collect();

        if (auth()->user()->role === 'super_admin') {
            LicensePayment::latest()->get()->each(function ($payment) use ($activities) {
                $activities->push([
                    'message' => 'License payment '.$payment->status.' - KES '.number_format($payment->amount, 2),
                    'time' => $payment->created_at,
                    'icon' => 'fas fa-money-check-alt',
                    'color' => '#16a34a',
                ]);
            });

            User::where('role', 'admin')->latest()->get()->each(function ($admin) use ($activities) {
                $activities->push([
                    'message' => 'Admin account registered: '.$admin->name,
                    'time' => $admin->created_at,
                    'icon' => 'fas fa-user-shield',
                    'color' => '#2563eb',
                ]);
            });
        } else {
            $customerIds = User::where('role', 'customer')->where('admin_id', auth()->id())->pluck('id');

            Payment::whereIn('user_id', $customerIds)->latest()->get()->each(function ($payment) use ($activities) {
                $activities->push([
                    'message' => 'Payment received from '.$payment->phone_number,
                    'time' => $payment->created_at,
                    'icon' => 'fas fa-money-bill-wave',
                    'color' => '#16a34a',
                ]);
            });

            Subscription::whereIn('user_id', $customerIds)->latest()->get()->each(function ($subscription) use ($activities) {
                $activities->push([
                    'message' => 'Subscription '.$subscription->status,
                    'time' => $subscription->created_at,
                    'icon' => 'fas fa-file-invoice',
                    'color' => '#2563eb',
                ]);
            });

            User::where('role', 'customer')->where('admin_id', auth()->id())->latest()->get()->each(function ($customer) use ($activities) {
                $activities->push([
                    'message' => 'New customer registered: '.$customer->name,
                    'time' => $customer->created_at,
                    'icon' => 'fas fa-user-plus',
                    'color' => '#7c3aed',
                ]);
            });
        }

        $activities = $activities->sortByDesc('time')->values();

        $page = request()->get('page', 1);
        $perPage = 7;

        $recentActivities = new LengthAwarePaginator(
            $activities->forPage($page, $perPage),
            $activities->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('admin.recent-activity.index', compact('recentActivities'));
    }
}