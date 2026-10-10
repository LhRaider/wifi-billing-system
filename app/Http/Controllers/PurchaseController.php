<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    /**
     * Customer package purchasing is intentionally disabled until the
     * customer-to-ISP Admin payment provider is fully implemented.
     *
     * This controller must never create a Payment or Subscription before
     * verified payment confirmation has been received.
     */
    public function store(Request $request, Package $package): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        if ($user->role !== 'customer') {
            abort(403);
        }

        /*
         * Prevent a customer from ever attempting to purchase a package
         * belonging to another ISP Admin.
         */
        if (
            !$user->admin_id ||
            !$package->admin_id ||
            (int) $user->admin_id !== (int) $package->admin_id
        ) {
            abort(403);
        }

        if (!$package->is_active) {
            return redirect()
                ->route('packages.index')
                ->with(
                    'error',
                    'This package is currently unavailable.'
                );
        }

        /*
         * Customer-to-ISP Admin payment processing is intentionally paused.
         *
         * No Payment, Subscription, HotspotUser, PPPoE user, or MikroTik
         * access must be created from this legacy purchase endpoint.
         */
        Log::warning('Blocked legacy package purchase attempt.', [
            'user_id' => $user->id,
            'package_id' => $package->id,
            'admin_id' => $user->admin_id,
        ]);

        return redirect()
            ->route('mpesa.pay', $package)
            ->with(
                'error',
                'Automatic package purchasing is currently unavailable. Please follow your ISP Admin payment instructions.'
            );
    }
}