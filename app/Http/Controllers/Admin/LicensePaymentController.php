<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\LicensePayment;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LicensePaymentController extends Controller
{
    /**
     * Display license payments.
     *
     * Super Admin sees all ISP Admin license payments.
     * ISP Admin sees only their own payments.
     */
    public function index()
    {
        $user = auth()->user();

        $this->ensureAllowedRole($user);

        $settings = SystemSetting::firstOrCreate([]);

        $monthlyFee = (float) (
            $settings->license_monthly_fee ?? 400
        );

        $plans = [
            1 => $monthlyFee,
            2 => $monthlyFee * 2,
            3 => $monthlyFee * 3,
            4 => $monthlyFee * 4,
        ];

        if ($user->role === 'super_admin') {

            $payments = LicensePayment::with('user')
                ->latest()
                ->paginate(10);

        } else {

            $payments = LicensePayment::where(
                'user_id',
                $user->id
            )
                ->latest()
                ->paginate(10);
        }

        return view(
            'admin.license-payments.index',
            compact(
                'user',
                'payments',
                'plans',
                'settings'
            )
        );
    }

    /**
     * Submit a manual license payment request.
     *
     * Only ISP Admins may create license payments.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $this->ensureAdmin($user);

        $validated = $request->validate([
            'months' =>
                'required|integer|in:1,2,3,4',

            'transaction_code' =>
                'nullable|string|max:100',
        ]);

        $settings = SystemSetting::firstOrCreate([]);

        $monthlyFee = (float) (
            $settings->license_monthly_fee ?? 400
        );

        $months = (int) $validated['months'];

        $amount =
            $monthlyFee * $months;

        $paymentType = match (
            $settings->license_payment_type
        ) {
            'paybill' => 'PayBill',
            'phone' => 'Phone',
            default => 'Till',
        };

        $payment = LicensePayment::create([
            'user_id' =>
                $user->id,

            'months' =>
                $months,

            'amount' =>
                $amount,

            'payment_method' =>
                $paymentType,

            'transaction_code' =>
                $validated['transaction_code'] ?? null,

            'status' =>
                'pending',
        ]);

        NotificationHelper::create(
            'License Payment Submitted',
            'Admin '.$user->name.
            ' submitted a license payment request for '.
            $months.' month(s).',
            'info',
            'super_admin'
        );

        AuditHelper::log(
            'Submit License Payment',
            'License Payments',
            'Admin '.$user->name.
            ' submitted license payment ID '.
            $payment->id.'.'
        );

        return back()->with(
            'success',
            'License payment submitted successfully. Awaiting confirmation.'
        );
    }

    /**
     * Approve an ISP Admin license payment.
     *
     * Super Admin only.
     */
    public function approve(
        LicensePayment $licensePayment
    ) {
        $this->ensureSuperAdmin();

        $result = DB::transaction(
            function () use ($licensePayment) {

                /*
                |--------------------------------------------------------------------------
                | Lock Payment
                |--------------------------------------------------------------------------
                |
                | Prevent two approval requests from extending the same
                | license more than once.
                |
                */

                $payment = LicensePayment::whereKey(
                    $licensePayment->id
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($payment->status === 'approved') {
                    return [
                        'already_approved' => true,
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Lock ISP Admin Account
                |--------------------------------------------------------------------------
                */

                $admin = User::whereKey(
                    $payment->user_id
                )
                    ->lockForUpdate()
                    ->first();

                if (!$admin || $admin->role !== 'admin') {
                    return [
                        'invalid_admin' => true,
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Determine License Start Point
                |--------------------------------------------------------------------------
                |
                | If the Admin still has paid time remaining, the new months
                | are added to the existing expiry date.
                |
                | Otherwise the new license starts now.
                |
                */

                $currentExpiry =
                    $admin->service_expires_at
                    &&
                    Carbon::parse(
                        $admin->service_expires_at
                    )->isFuture()
                        ? Carbon::parse(
                            $admin->service_expires_at
                        )
                        : now();

                $expiryDate =
                    $currentExpiry
                        ->copy()
                        ->addMonths(
                            (int) $payment->months
                        );

                $payment->update([
                    'start_date' =>
                        now(),

                    'expiry_date' =>
                        $expiryDate,

                    'status' =>
                        'approved',
                ]);

                $admin->update([
                    'service_status' =>
                        'active',

                    'service_expires_at' =>
                        $expiryDate,

                    'is_suspended' =>
                        false,
                ]);

                return [
                    'already_approved' =>
                        false,

                    'admin' =>
                        $admin,

                    'payment' =>
                        $payment,

                    'expiry_date' =>
                        $expiryDate,
                ];
            }
        );

        if (
            $result['already_approved']
            ?? false
        ) {
            return back()->with(
                'success',
                'This payment has already been approved.'
            );
        }

        if (
            $result['invalid_admin']
            ?? false
        ) {
            return back()->with(
                'error',
                'This license payment is not linked to a valid ISP Admin account.'
            );
        }

        $admin =
            $result['admin'];

        $payment =
            $result['payment'];

        $expiryDate =
            $result['expiry_date'];

        AuditHelper::log(
            'Approve License Payment',
            'License Payments',
            'License payment for '.
            $admin->name.
            ' approved for '.
            $payment->months.
            ' month(s).'
        );

        NotificationHelper::create(
            'License Activated',
            'License for admin '.
            $admin->name.
            ' activated until '.
            $expiryDate->format('d M Y').'.',
            'success',
            'super_admin'
        );

        NotificationHelper::create(
            'Subscription Activated',
            'Your subscription is active until '.
            $expiryDate->format('d M Y').'.',
            'success',
            'admin',
            $admin->id
        );

        return back()->with(
            'success',
            'License payment approved and admin subscription activated.'
        );
    }

    /**
     * Reject a license payment.
     *
     * Super Admin only.
     */
    public function reject(
        LicensePayment $licensePayment
    ) {
        $this->ensureSuperAdmin();

        $licensePayment->loadMissing('user');

        if (
            !$licensePayment->user
            ||
            $licensePayment->user->role !== 'admin'
        ) {
            abort(
                403,
                'This payment does not belong to a valid ISP Admin.'
            );
        }

        if (
            $licensePayment->status === 'approved'
        ) {
            return back()->with(
                'error',
                'An approved payment cannot be rejected.'
            );
        }

        if (
            $licensePayment->status === 'rejected'
        ) {
            return back()->with(
                'success',
                'This payment has already been rejected.'
            );
        }

        $licensePayment->update([
            'status' => 'rejected',
        ]);

        $admin =
            $licensePayment->user;

        AuditHelper::log(
            'Reject License Payment',
            'License Payments',
            'License payment for '.
            $admin->name.
            ' rejected.'
        );

        NotificationHelper::create(
            'License Payment Rejected',
            'License payment request for admin '.
            $admin->name.
            ' was rejected.',
            'warning',
            'super_admin'
        );

        NotificationHelper::create(
            'Subscription Payment Rejected',
            'Your subscription payment was rejected. Please confirm your transaction and try again.',
            'warning',
            'admin',
            $admin->id
        );

        return back()->with(
            'success',
            'License payment rejected.'
        );
    }

    /**
     * Display a license payment receipt.
     *
     * Super Admin only.
     */
    public function showReceipt(
        LicensePayment $licensePayment
    ) {
        $this->ensureSuperAdmin();

        $licensePayment->loadMissing('user');

        if (
            !$licensePayment->user
            ||
            $licensePayment->user->role !== 'admin'
        ) {
            abort(
                403,
                'This payment does not belong to a valid ISP Admin.'
            );
        }

        return view(
            'receipts.license-show',
            compact('licensePayment')
        );
    }

    /**
     * Allow only platform staff roles into shared license pages.
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
                'You are not authorized to access license payments.'
            );
        }
    }

    /**
     * Require an ISP Admin.
     */
    private function ensureAdmin($user): void
    {
        if (
            !$user
            ||
            $user->role !== 'admin'
        ) {
            abort(
                403,
                'Only ISP Admins can submit license payments.'
            );
        }
    }

    /**
     * Require the Super Admin.
     */
    private function ensureSuperAdmin(): void
    {
        if (
            !auth()->check()
            ||
            auth()->user()->role !== 'super_admin'
        ) {
            abort(
                403,
                'Only the Super Admin can manage license payment approvals.'
            );
        }
    }
}