<?php

namespace App\Http\Controllers;

use App\Models\LicensePayment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    /**
     * Display receipts appropriate to the authenticated account.
     */
    public function index()
    {
        $user = $this->currentUser();

        /*
         * Super Admin sees only approved platform-license receipts.
         * Individual ISP customer receipts remain tenant-private.
         */
        if ($user->role === 'super_admin') {
            $receipts = LicensePayment::with('user')
                ->where('status', 'approved')
                ->latest('id')
                ->paginate(10);

            return view(
                'receipts.license-index',
                compact('receipts')
            );
        }

        /*
         * ISP Admin sees completed customer payments belonging
         * entirely to their own tenant.
         */
        if ($user->role === 'admin') {
            $receipts = Payment::with([
                'user',
                'package',
            ])
                ->where('status', 'completed')
                ->whereHas('user', function ($query) use ($user) {
                    $query
                        ->where('role', 'customer')
                        ->where('admin_id', $user->id);
                })
                ->whereHas('package', function ($query) use ($user) {
                    $query->where('admin_id', $user->id);
                })
                ->latest('id')
                ->paginate(10);

            return view(
                'receipts.index',
                compact('receipts')
            );
        }

        /*
         * Customers see only their own completed receipts and only
         * for packages belonging to their assigned ISP Admin.
         */
        if ($user->role === 'customer') {
            if (!$user->admin_id) {
                abort(403);
            }

            $receipts = Payment::with('package')
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereHas('package', function ($query) use ($user) {
                    $query->where(
                        'admin_id',
                        $user->admin_id
                    );
                })
                ->latest('id')
                ->paginate(10);

            return view(
                'receipts.index',
                compact('receipts')
            );
        }

        abort(403);
    }

    /**
     * Display one completed customer-payment receipt.
     */
    public function show(Payment $payment)
    {
        $user = $this->currentUser();

        /*
         * Super Admin does not access ISP customer-payment receipts.
         */
        if ($user->role === 'super_admin') {
            return redirect()
                ->route('receipts.index')
                ->with(
                    'error',
                    'Super Admin uses platform license receipts only.'
                );
        }

        /*
         * A receipt must never be generated for pending or failed
         * payment records.
         */
        if ($payment->status !== 'completed') {
            abort(404);
        }

        $payment->loadMissing([
            'user',
            'package.admin',
        ]);

        $customer = $payment->user;
        $package = $payment->package;

        /*
         * Historical receipt data must remain complete enough to prove
         * ownership before it is displayed.
         */
        if (
            !$customer ||
            !$package ||
            !$package->admin ||
            $customer->role !== 'customer'
        ) {
            abort(404);
        }

        /*
         * Payment customer and purchased package must belong to the
         * same ISP tenant.
         */
        if (
            !$customer->admin_id ||
            (int) $customer->admin_id !== (int) $package->admin_id
        ) {
            abort(403);
        }

        if ($user->role === 'admin') {
            /*
             * ISP Admin may only view receipts belonging to their tenant.
             */
            if (
                (int) $customer->admin_id !== (int) $user->id ||
                (int) $package->admin_id !== (int) $user->id
            ) {
                abort(403);
            }
        } elseif ($user->role === 'customer') {
            /*
             * Customer may only view their own receipt.
             */
            if (
                (int) $payment->user_id !== (int) $user->id ||
                !$user->admin_id ||
                (int) $package->admin_id !== (int) $user->admin_id
            ) {
                abort(403);
            }
        } else {
            abort(403);
        }

        return view(
            'receipts.show',
            compact('payment')
        );
    }

    /**
     * Return the authenticated account.
     */
    private function currentUser(): User
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        if (
            !in_array(
                $user->role,
                ['customer', 'admin', 'super_admin'],
                true
            )
        ) {
            abort(403);
        }

        return $user;
    }
}