<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\HotspotUser;
use App\Models\HotspotSession;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('admin.dashboard');
        }

        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_time', '>', now())
            ->latest()
            ->first();

        $hotspotUser = HotspotUser::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($activeSubscription && $hotspotUser) {
            HotspotSession::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'logout_time' => null,
                ],
                [
                    'mac_address' => null,
                    'ip_address' => request()->ip(),
                    'login_time' => now(),
                    'data_used' => 0,
                ]
            );
        }

        $totalPurchases = Payment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $totalSpent = Payment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $remainingTime = 'No active package';

        if ($activeSubscription) {
            $endTime = Carbon::parse($activeSubscription->end_time);
            $remainingTime = now()->diffForHumans($endTime, [
                'parts' => 2,
                'short' => true,
                'syntax' => Carbon::DIFF_ABSOLUTE,
            ]);
        }

       $package = null;
$admin = null;

if ($activeSubscription) {

    $package = $activeSubscription->package;

    if ($package) {
        $admin = $package->admin;
    }

}

return view('dashboard', compact(
    'activeSubscription',
    'hotspotUser',
    'totalPurchases',
    'totalSpent',
    'remainingTime',
    'package',
    'admin'
));
    }
}