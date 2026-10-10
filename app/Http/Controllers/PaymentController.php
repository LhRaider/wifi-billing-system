<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display payment history for the authenticated customer only.
     */
    public function index()
    {
        $customer = $this->currentCustomer();

        /*
         * Base query is always restricted to the authenticated customer.
         */
        $baseQuery = Payment::query()
            ->where('user_id', $customer->id);

        /*
         * Lifetime summary statistics.
         *
         * Total spent includes completed payments only.
         * Pending and failed transactions must never be counted as revenue.
         */
        $totalPayments = (clone $baseQuery)->count();

        $successfulPayments = (clone $baseQuery)
            ->where('status', 'completed')
            ->count();

        $totalSpent = (clone $baseQuery)
            ->where('status', 'completed')
            ->sum('amount');

        /*
         * Payment history is paginated so the page remains efficient
         * as the customer's transaction history grows.
         */
        $payments = Payment::with('package')
            ->where('user_id', $customer->id)
            ->latest('id')
            ->paginate(15);

        return view('payments.index', compact(
            'payments',
            'totalPayments',
            'successfulPayments',
            'totalSpent'
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