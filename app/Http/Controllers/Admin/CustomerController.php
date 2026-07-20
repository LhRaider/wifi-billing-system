<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role === 'super_admin') {
            $customers = User::where('role', 'admin')
                ->latest()
                ->paginate(10);
        } else {
            $customers = User::where('role', 'customer')
                ->where('admin_id', auth()->id())
                ->latest()
                ->paginate(10);
        }

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        if (
            auth()->user()->role !== 'super_admin' &&
            $customer->admin_id !== auth()->id()
        ) {
            abort(403);
        }

        $totalPayments = $customer->payments()->count();

        $totalSpent = $customer->payments()->sum('amount');

        $lastPayment = $customer->payments()
            ->latest()
            ->first();

        $latestSubscription = $customer->subscriptions()
            ->with('package')
            ->latest()
            ->first();

        $latestHotspotUser = $customer->hotspotUsers()
            ->latest()
            ->first();

        return view('admin.customers.show', compact(
            'customer',
            'totalPayments',
            'totalSpent',
            'lastPayment',
            'latestSubscription',
            'latestHotspotUser'
        ));
    }

    public function suspend(User $customer)
    {
        if ($customer->role === 'super_admin') {
            return back()->with('error', 'Super admin cannot be suspended.');
        }

        if (
            auth()->user()->role !== 'super_admin' &&
            $customer->admin_id !== auth()->id()
        ) {
            abort(403);
        }

        $customer->update([
            'is_suspended' => true,
        ]);

        AuditHelper::log(
            'Suspend Customer',
            'Customers',
            'Customer '.$customer->name.' was suspended'
        );

        NotificationHelper::create(
    auth()->user()->role === 'super_admin' ? 'Admin Suspended' : 'Customer Suspended',
    $customer->name . ' has been suspended.',
    'warning',
    auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin'
);

        return back()->with('success', 'Customer suspended successfully.');
    }

    public function activate(User $customer)
    {
        if (
            auth()->user()->role !== 'super_admin' &&
            $customer->admin_id !== auth()->id()
        ) {
            abort(403);
        }

        $customer->update([
            'is_suspended' => false,
        ]);

        AuditHelper::log(
            'Activate Customer',
            'Customers',
            'Customer '.$customer->name.' was activated'
        );

       NotificationHelper::create(
    auth()->user()->role === 'super_admin' ? 'Admin Activated' : 'Customer Activated',
    $customer->name . ' has been activated.',
    'success',
    auth()->user()->role === 'super_admin' ? 'super_admin' : 'admin'
);

        return back()->with('success', 'Customer activated successfully.');
    }
}