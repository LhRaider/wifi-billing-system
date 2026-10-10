<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display subscription history for the authenticated customer only.
     */
    public function index()
    {
        $customer = $this->currentCustomer();

        $baseQuery = Subscription::query()
            ->where('user_id', $customer->id);

        /*
         * Lifetime summary values.
         */
        $totalSubscriptions = (clone $baseQuery)->count();

        $activeSubscriptions = (clone $baseQuery)
            ->where('status', 'active')
            ->count();

        $suspendedSubscriptions = (clone $baseQuery)
            ->where('status', 'suspended')
            ->count();

        $expiredSubscriptions = (clone $baseQuery)
            ->where('status', 'expired')
            ->count();

        /*
         * Paginated history with package information.
         */
        $subscriptions = Subscription::with('package')
            ->where('user_id', $customer->id)
            ->latest('id')
            ->paginate(15);

        return view('subscriptions.index', compact(
            'subscriptions',
            'totalSubscriptions',
            'activeSubscriptions',
            'suspendedSubscriptions',
            'expiredSubscriptions'
        ));
    }

    /**
     * Return the authenticated customer.
     */
    private function currentCustomer(): User
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        if ($user->role !== 'customer') {
            abort(403);
        }

        return $user;
    }
}