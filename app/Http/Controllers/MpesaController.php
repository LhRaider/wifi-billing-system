<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\Payment;
use App\Models\HotspotUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MpesaController extends Controller
{
    public function showPaymentForm(Package $package)
{
    $admin = User::find($package->admin_id);

    return view('mpesa.pay', [
        'package' => $package,
        'admin' => $admin,
    ]);
}

    public function initiatePayment(Request $request, Package $package)
    {
        $request->validate([
            'phone_number' => 'required',
        ]);

        $user = Auth::user();

        $payment = Payment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'phone_number' => $request->phone_number,
            'status' => 'pending',
        ]);

        try {
            $accessToken = $this->getAccessToken();
            $timestamp = now()->format('YmdHis');

            $password = base64_encode(
                env('MPESA_SHORTCODE') .
                env('MPESA_PASSKEY') .
                $timestamp
            );

            $response = Http::withToken($accessToken)->post(
                'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
                [
                    'BusinessShortCode' => env('MPESA_SHORTCODE'),
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => (int) $package->price,
                    'PartyA' => $request->phone_number,
                    'PartyB' => env('MPESA_SHORTCODE'),
                    'PhoneNumber' => $request->phone_number,
                    'CallBackURL' => env('MPESA_CALLBACK_URL'),
                    'AccountReference' => 'WiFi Billing',
                    'TransactionDesc' => 'Internet Package Payment',
                ]
            );

            $result = $response->json();

            if (isset($result['CheckoutRequestID'])) {
                $payment->update([
                    'checkout_request_id' => $result['CheckoutRequestID'],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('M-Pesa STK Push Error: '.$e->getMessage());
        }

        if (env('MPESA_AUTO_ACTIVATE', true)) {
            $this->activatePackage($payment);

            return redirect()->route('dashboard')
                ->with('success', 'Payment received. Internet package activated successfully.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Payment request sent. Your package will activate after M-Pesa confirmation.');
    }

    private function activatePackage(Payment $payment)
    {
        if ($payment->status === 'completed' && $payment->paid_at) {
            return;
        }

        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $user = User::find($payment->user_id);
        $package = Package::find($payment->package_id);

        if (!$user || !$package) {
            \Log::error('Activation failed: user or package not found for payment ID '.$payment->id);
            return;
        }

        NotificationHelper::create(
            'Payment Received',
            'KES '.number_format($payment->amount, 2).' received from '.$user->name,
            'success',
            'admin',
            $user->admin_id
        );

        $activeSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($activeSubscription && Carbon::parse($activeSubscription->end_time)->greaterThan(now())) {
            $endTime = $package->is_unlimited
                ? Carbon::parse($activeSubscription->end_time)->addYears(10)
                : Carbon::parse($activeSubscription->end_time)->addHours($package->duration_hours);

            $activeSubscription->update([
                'package_id' => $package->id,
                'end_time' => $endTime,
                'status' => 'active',
            ]);

            $subscription = $activeSubscription;
        } else {
            Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'expired',
                ]);

            $endTime = $package->is_unlimited
                ? now()->addYears(10)
                : now()->addHours($package->duration_hours);

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'start_time' => now(),
                'end_time' => $endTime,
                'status' => 'active',
            ]);
        }

        $user->update([
            'is_suspended' => false,
        ]);

        $hotspotUser = HotspotUser::where('user_id', $user->id)
            ->latest()
            ->first();

        if ($hotspotUser) {
            $hotspotUser->update([
                'subscription_id' => $subscription->id,
                'profile' => $package->router_profile,
                'status' => 'active',
                'router_synced' => false,
                'last_sync_status' => 'Pending',
                'last_sync_message' => 'Package renewed. Waiting for router synchronization.',
            ]);
        } else {
            $hotspotUser = HotspotUser::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'router_id' => null,
                'username' => 'user'.$user->id,
                'password' => rand(100000, 999999),
                'profile' => $package->router_profile,
                'status' => 'active',
                'router_synced' => false,
                'last_sync_status' => 'Pending',
                'last_sync_message' => 'Waiting for router synchronization.',
            ]);
        }

        $mikrotik = app(\App\Services\MikrotikService::class);

if ($package->package_type === 'pppoe') {

    $router = \App\Models\Router::where('admin_id', $user->admin_id)
    ->where('is_active', true)
    ->first();
    if ($router) {

        $result = $mikrotik->createPppoeUser(
            $hotspotUser->username,
            $hotspotUser->password,
            $package,
            $router
        );

        $hotspotUser->update([
            'router_id' => $router->id,
            'router_synced' => $result['success'],
            'last_sync_status' => $result['success'] ? 'Success' : 'Failed',
            'last_sync_message' => $result['message'],
        ]);

    } else {

        $hotspotUser->update([
            'router_synced' => false,
            'last_sync_status' => 'Failed',
            'last_sync_message' => 'No active router found.',
        ]);
    }

} else {

    $result = $mikrotik->createHotspotUser($hotspotUser);

    $hotspotUser->update([
        'router_synced' => $result['success'],
        'last_sync_status' => $result['success'] ? 'Success' : 'Failed',
        'last_sync_message' => $result['message'],
    ]);
}
    }

    private function getAccessToken()
    {
        $response = Http::withBasicAuth(
            env('MPESA_CONSUMER_KEY'),
            env('MPESA_CONSUMER_SECRET')
        )->get(
            'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        );

        return $response->json()['access_token'];
    }

    public function callback(Request $request)
    {
        \Log::info('M-Pesa Callback Received:', $request->all());

        file_put_contents(
            storage_path('logs/mpesa-callback.json'),
            json_encode($request->all(), JSON_PRETTY_PRINT)
        );

        $callback = $request->input('Body.stkCallback');

        if ($callback && isset($callback['CheckoutRequestID'])) {
            $payment = Payment::where('checkout_request_id', $callback['CheckoutRequestID'])->first();

            if ($payment) {
                if ($callback['ResultCode'] == 0) {
                    $this->activatePackage($payment);
                } else {
                    $payment->update([
                        'status' => 'failed',
                    ]);

                    NotificationHelper::create(
                        'Payment Failed',
                        'Payment from '.$payment->phone_number.' failed.',
                        'danger',
                        'admin',
                        $payment->user->admin_id ?? null
                    );
                }
            }
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Callback received successfully',
        ]);
    }
}