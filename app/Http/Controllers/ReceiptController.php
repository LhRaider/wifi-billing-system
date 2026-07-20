<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\LicensePayment;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            $receipts = LicensePayment::with('user')
                ->where('status', 'approved')
                ->latest()
                ->paginate(10);

            return view('receipts.license-index', compact('receipts'));
        }

        if ($user->role === 'admin') {
            $receipts = Payment::where('status', 'completed')
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('admin_id', $user->id);
                })
                ->latest()
                ->paginate(10);

            return view('receipts.index', compact('receipts'));
        }

        $receipts = Payment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->paginate(10);

        return view('receipts.index', compact('receipts'));
    }

    public function show(Payment $payment)
    {
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return redirect()
                ->route('receipts.index')
                ->with('error', 'Super admin uses admin subscription receipts only.');
        }

        if ($user->role === 'admin' && $payment->user->admin_id !== $user->id) {
            abort(403);
        }

        if ($user->role === 'customer' && $payment->user_id !== $user->id) {
            abort(403);
        }

        $settings = SystemSetting::first();

        return view('receipts.show', compact('payment', 'settings'));
    }
}