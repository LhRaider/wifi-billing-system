<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function store(Request $request, Package $package)
    {
        $user = Auth::user();

        $payment = Payment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'phone_number' => 'pending',
            'status' => 'pending',
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addHours($package->duration_hours),
            'status' => 'active',
        ]);

        return redirect()->route('packages.index')
            ->with('success', 'Package purchased successfully. M-Pesa will be added next.');
    }
}