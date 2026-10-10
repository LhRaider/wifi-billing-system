<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    /**
     * Display the customer payment page.
     *
     * Customer M-Pesa payments are currently disabled, but the page may still
     * display the ISP Admin's business/payment information.
     */
    public function showPaymentForm(Package $package)
    {
        $user = Auth::user();

        $this->ensureCustomerOwnsPackage($user, $package);

        $admin = User::where('id', $package->admin_id)
            ->where('role', 'admin')
            ->firstOrFail();

        return view('mpesa.pay', [
            'package' => $package,
            'admin' => $admin,
            'customerMpesaEnabled' => false,
        ]);
    }

    /**
     * Customer STK Push is intentionally disabled.
     *
     * We are not allowing customer payments through the platform until the
     * final multi-tenant payment provider architecture has been selected.
     */
    public function initiatePayment(
        Request $request,
        Package $package
    ) {
        $user = Auth::user();

        $this->ensureCustomerOwnsPackage(
            $user,
            $package
        );

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER M-PESA PAUSED
        |--------------------------------------------------------------------------
        |
        | Do not create a pending Payment record.
        | Do not contact Safaricom.
        | Do not initiate STK Push.
        |
        | This protects the platform while the customer-to-ISP payment
        | architecture is still being finalized.
        |
        */

        Log::info(
            'Blocked customer M-Pesa initiation because customer payments are disabled.',
            [
                'user_id' => $user->id,
                'package_id' => $package->id,
                'admin_id' => $user->admin_id,
            ]
        );

        return back()->with(
            'error',
            'Automatic M-Pesa package payments are not currently available. Please use the payment instructions provided by your ISP.'
        );
    }

    /**
     * Receive customer M-Pesa callbacks safely.
     *
     * Customer automatic payment activation is currently disabled, therefore
     * callbacks are acknowledged but must never activate a package.
     */
    public function callback(Request $request)
    {
        $checkoutRequestId = $request->input(
            'Body.stkCallback.CheckoutRequestID'
        );

        /*
        |--------------------------------------------------------------------------
        | Do Not Log Complete Callback
        |--------------------------------------------------------------------------
        |
        | Customer payment callbacks can contain phone numbers, receipt
        | numbers and other payment information.
        |
        */

        Log::warning(
            'Customer M-Pesa callback received while customer M-Pesa is disabled.',
            [
                'checkout_request_id' =>
                    $checkoutRequestId,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | NO ACTIVATION
        |--------------------------------------------------------------------------
        |
        | We intentionally do not:
        |
        | - update a Payment
        | - create a Subscription
        | - activate a HotspotUser
        | - activate a PPPoE user
        | - synchronize MikroTik
        |
        */

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Callback received',
        ]);
    }

    /**
     * Ensure a customer can only access packages belonging to their ISP Admin.
     */
    private function ensureCustomerOwnsPackage(
        $user,
        Package $package
    ): void {
        if (
            !$user
            ||
            $user->role !== 'customer'
            ||
            !$user->admin_id
            ||
            (int) $package->admin_id !==
                (int) $user->admin_id
        ) {
            abort(
                403,
                'You cannot access this package.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Package Availability
        |--------------------------------------------------------------------------
        */

        if (!$package->is_active) {
            abort(
                404,
                'This package is no longer available.'
            );
        }
    }
}