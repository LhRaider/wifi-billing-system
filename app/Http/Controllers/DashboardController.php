<?php

namespace App\Http\Controllers;

use App\Models\HotspotUser;
use App\Models\Payment;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the authenticated customer's dashboard.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | Staff Accounts
        |--------------------------------------------------------------------------
        |
        | ISP Admins and Super Admins use the administration dashboard.
        |
        */

        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            return redirect()->route('admin.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Customer Authorization
        |--------------------------------------------------------------------------
        |
        | A valid customer must belong to an ISP Admin tenant.
        |
        */

        if ($user->role !== 'customer' || !$user->admin_id) {
            abort(
                403,
                'This dashboard is only available to valid customer accounts.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ISP Admin
        |--------------------------------------------------------------------------
        |
        | Load and validate the customer's owning ISP Admin once. The Blade
        | view does not perform relationship queries itself.
        |
        */

        $user->loadMissing('admin');

        $admin = $user->admin;

        if (
            !$admin
            || $admin->role !== 'admin'
            || (int) $admin->id !== (int) $user->admin_id
        ) {
            abort(
                403,
                'This customer account is not linked to a valid ISP Admin.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Active Subscription
        |--------------------------------------------------------------------------
        |
        | Every package has a finite duration. Therefore an active
        | subscription must always have a future end time.
        |
        | Unlimited package speed does not affect subscription expiry.
        |
        */

        $activeSubscription = Subscription::query()
            ->with('package')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('end_time')
            ->where('end_time', '>', now())
            ->whereHas('package', function ($query) use ($user) {
                $query->where('admin_id', $user->admin_id);
            })
            ->latest('end_time')
            ->first();

        $package = $activeSubscription?->package;

        /*
        |--------------------------------------------------------------------------
        | Hotspot / PPPoE Account
        |--------------------------------------------------------------------------
        |
        | hotspot_users is owned through user_id. The authenticated customer
        | has already been verified as belonging to a valid ISP Admin above.
        | Therefore the tenant chain is:
        |
        | ISP Admin -> Customer -> Hotspot User
        |
        | Viewing the dashboard never creates a network session.
        |
        */

        $hotspotUser = HotspotUser::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Customer Payment Statistics
        |--------------------------------------------------------------------------
        |
        | Calculate the completed-payment count and total in one aggregate
        | query. Only this authenticated customer's payments are included.
        |
        */

        $paymentSummary = Payment::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->selectRaw(
                'COUNT(*) as total_purchases, COALESCE(SUM(amount), 0) as total_spent'
            )
            ->first();

        $totalPurchases = (int) ($paymentSummary?->total_purchases ?? 0);
        $totalSpent = (float) ($paymentSummary?->total_spent ?? 0);

        /*
        |--------------------------------------------------------------------------
        | Subscription Presentation
        |--------------------------------------------------------------------------
        |
        | Package duration is always finite. Remaining access time is derived
        | exclusively from the verified subscription end time.
        |
        */

        $remainingTime = 'No active package';
        $expiryDate = 'Expired';

        if (
            $activeSubscription
            && $package
            && $activeSubscription->end_time
        ) {
            $endTime = Carbon::parse(
                $activeSubscription->end_time
            );

            $expiryDate = $endTime->format(
                'd M Y H:i'
            );

            $remainingTime = now()->diffForHumans(
                $endTime,
                [
                    'parts' => 2,
                    'short' => true,
                    'syntax' => Carbon::DIFF_ABSOLUTE,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ISP Contact Information
        |--------------------------------------------------------------------------
        |
        | Only information intentionally displayed to the ISP's own customer
        | is passed to the dashboard view.
        |
        */

        $ispName = $admin->network_name
            ?: $admin->business_name
            ?: 'Your Internet Service Provider';

        $ispPhone = $admin->business_phone
            ?: 'Not Available';

        return view('dashboard', [
            'activeSubscription' => $activeSubscription,
            'hotspotUser' => $hotspotUser,
            'totalPurchases' => $totalPurchases,
            'totalSpent' => $totalSpent,
            'remainingTime' => $remainingTime,
            'expiryDate' => $expiryDate,
            'package' => $package,
            'ispName' => $ispName,
            'ispPhone' => $ispPhone,
        ]);
    }
}