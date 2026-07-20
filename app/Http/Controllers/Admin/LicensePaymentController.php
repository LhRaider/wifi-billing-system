<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicensePayment;
use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LicensePaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $payments = LicensePayment::with('user')
                ->latest()
                ->paginate(10);
        } else {
            $payments = LicensePayment::where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }

        $plans = [
            1 => 400,
            2 => 800,
            3 => 1200,
            4 => 1600,
        ];

        return view('admin.license-payments.index', compact(
            'user',
            'payments',
            'plans'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'months' => 'required|integer|in:1,2,3,4',
            'transaction_code' => 'nullable|string|max:100',
        ]);

        $plans = [
            1 => 400,
            2 => 800,
            3 => 1200,
            4 => 1600,
        ];

        $payment = LicensePayment::create([
            'user_id' => auth()->id(),
            'months' => $request->months,
            'amount' => $plans[$request->months],
            'payment_method' => 'Manual Till Payment',
            'transaction_code' => $request->transaction_code,
            'status' => 'pending',
        ]);

        NotificationHelper::create(
            'License Payment Submitted',
            'Admin '.auth()->user()->name.' submitted a license payment request for '.$request->months.' month(s).',
            'info',
            'super_admin'
        );

        AuditHelper::log(
            'Submit License Payment',
            'License Payments',
            'Admin '.auth()->user()->name.' submitted license payment ID '.$payment->id
        );

        return back()->with('success', 'License payment request submitted. Awaiting approval.');
    }

    public function approve(LicensePayment $licensePayment)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $admin = $licensePayment->user;

        if (!$admin) {
            return back()->with('error', 'Admin account not found for this payment.');
        }

        $currentExpiry = $admin->service_expires_at && Carbon::parse($admin->service_expires_at)->isFuture()
            ? Carbon::parse($admin->service_expires_at)
            : now();

        $expiryDate = $currentExpiry->copy()->addMonths($licensePayment->months);

        $licensePayment->update([
            'start_date' => now(),
            'expiry_date' => $expiryDate,
            'status' => 'approved',
        ]);

        $admin->update([
            'service_status' => 'active',
            'service_expires_at' => $expiryDate,
            'is_suspended' => false,
        ]);

        AuditHelper::log(
            'Approve License Payment',
            'License Payments',
            'License payment for '.$admin->name.' approved for '.$licensePayment->months.' month(s).'
        );

        NotificationHelper::create(
            'License Activated',
            'License for admin '.$admin->name.' activated until '.$expiryDate->format('d M Y').'.',
            'success',
            'super_admin'
        );

        NotificationHelper::create(
            'Subscription Activated',
            'Your subscription is active until '.$expiryDate->format('d M Y').'.',
            'success',
            'admin',
            $admin->id
        );

        return back()->with('success', 'License payment approved and admin subscription activated.');
    }

    public function reject(LicensePayment $licensePayment)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $licensePayment->update([
            'status' => 'rejected',
        ]);

        AuditHelper::log(
            'Reject License Payment',
            'License Payments',
            'License payment for '.($licensePayment->user->name ?? 'Admin').' rejected.'
        );

        NotificationHelper::create(
            'License Payment Rejected',
            'License payment request for admin '.($licensePayment->user->name ?? 'Admin').' was rejected.',
            'warning',
            'super_admin'
        );

        if ($licensePayment->user) {
            NotificationHelper::create(
                'Subscription Payment Rejected',
                'Your subscription payment was rejected. Please confirm your transaction and try again.',
                'warning',
                'admin',
                $licensePayment->user->id
            );
        }

        return back()->with('success', 'License payment rejected.');
    }

    public function showReceipt(LicensePayment $licensePayment)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('receipts.license-show', compact('licensePayment'));
    }
}