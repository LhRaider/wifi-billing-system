<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicensePayment;
use App\Models\SystemSetting;
use App\Services\LicenseMpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseMpesaController extends Controller
{
    public function __construct(
        private readonly LicenseMpesaService $mpesaService
    ) {
    }

    public function initiate(Request $request)
    {
        $admin = auth()->user();

        if (!$admin || $admin->role !== 'admin') {
            abort(403, 'Only ISP Admins can make platform license payments.');
        }

        $validated = $request->validate([
            'months' => ['required', 'integer', 'in:1,2,3,4'],
            'phone_number' => ['required', 'string', 'max:30'],
        ]);

        $settings = SystemSetting::firstOrCreate([]);
        $monthlyFee = (float) ($settings->license_monthly_fee ?? 400);
        $months = (int) $validated['months'];
        $amount = (int) round($monthlyFee * $months);

        if ($amount < 1) {
            return back()
                ->withInput()
                ->with('error', 'The license payment amount is invalid.');
        }

        $phoneNumber = $this->mpesaService->normalizePhoneNumber(
            $validated['phone_number']
        );

        if (!$phoneNumber) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Please enter a valid Kenyan M-Pesa phone number.'
                );
        }

        $payment = LicensePayment::create([
            'user_id' => $admin->id,
            'months' => $months,
            'amount' => $amount,
            'payment_method' => 'M-Pesa STK Push',
            'phone_number' => $phoneNumber,
            'status' => 'pending',
        ]);

        try {
            $accessToken = $this->getAccessToken();
            $timestamp = now()->format('YmdHis');

            $shortcode = config('services.license_mpesa.shortcode');
            $passkey = config('services.license_mpesa.passkey');
            $callbackUrl = config('services.license_mpesa.callback_url');
            $transactionType = config(
                'services.license_mpesa.transaction_type',
                'CustomerPayBillOnline'
            );

            if (!$shortcode || !$passkey || !$callbackUrl) {
                throw new \RuntimeException(
                    'License M-Pesa configuration is incomplete.'
                );
            }

            $password = $this->generatePassword(
                (string) $shortcode,
                (string) $passkey,
                $timestamp
            );

            $response = Http::timeout($this->timeout())
                ->withToken($accessToken)
                ->acceptJson()
                ->post(
                    $this->mpesaBaseUrl()
                    .'/mpesa/stkpush/v1/processrequest',
                    [
                        'BusinessShortCode' => $shortcode,
                        'Password' => $password,
                        'Timestamp' => $timestamp,
                        'TransactionType' => $transactionType,
                        'Amount' => $amount,
                        'PartyA' => $phoneNumber,
                        'PartyB' => $shortcode,
                        'PhoneNumber' => $phoneNumber,
                        'CallBackURL' => $callbackUrl,
                        'AccountReference' => 'WIFI-LICENSE',
                        'TransactionDesc' => 'WiFi Billing License Payment',
                    ]
                );

            if (!$response->successful()) {
                Log::error(
                    'License M-Pesa STK HTTP error.',
                    [
                        'payment_id' => $payment->id,
                        'http_status' => $response->status(),
                    ]
                );

                $payment->update(['status' => 'failed']);

                return back()->with(
                    'error',
                    'M-Pesa payment request failed. Please try again.'
                );
            }

            $result = $response->json();

            if (
                !is_array($result)
                || empty($result['CheckoutRequestID'])
                || (string) ($result['ResponseCode'] ?? '') !== '0'
            ) {
                Log::error(
                    'License M-Pesa STK request rejected.',
                    [
                        'payment_id' => $payment->id,
                        'response_code' => is_array($result)
                            ? ($result['ResponseCode'] ?? null)
                            : null,
                    ]
                );

                $payment->update(['status' => 'failed']);

                return back()->with(
                    'error',
                    'M-Pesa rejected the payment request. Please try again.'
                );
            }

            $payment->update([
                'checkout_request_id' =>
                    (string) $result['CheckoutRequestID'],
                'merchant_request_id' =>
                    isset($result['MerchantRequestID'])
                        ? (string) $result['MerchantRequestID']
                        : null,
            ]);

            return back()->with(
                'success',
                'M-Pesa request sent. Enter your PIN to complete payment.'
            );
        } catch (\Throwable $e) {
            Log::error(
                'License M-Pesa STK initiation failed.',
                [
                    'payment_id' => $payment->id,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]
            );

            $payment->update(['status' => 'failed']);

            return back()->with(
                'error',
                'Unable to start M-Pesa payment. Please try again.'
            );
        }
    }

    public function callback(Request $request)
    {
        $callback = $request->input('Body.stkCallback');

        if (
            !is_array($callback)
            || empty($callback['CheckoutRequestID'])
        ) {
            Log::warning(
                'Invalid license M-Pesa callback received.'
            );

            return $this->callbackResponse();
        }

        $checkoutRequestId =
            (string) $callback['CheckoutRequestID'];

        $merchantRequestId =
            isset($callback['MerchantRequestID'])
                ? (string) $callback['MerchantRequestID']
                : null;

        $resultCode =
            (int) ($callback['ResultCode'] ?? -1);

        if ($resultCode !== 0) {
            $this->mpesaService->verifyAndMarkFailure(
                $checkoutRequestId,
                $merchantRequestId,
                $resultCode
            );

            return $this->callbackResponse();
        }

        $metadataItems =
            $callback['CallbackMetadata']['Item'] ?? [];

        if (!is_array($metadataItems)) {
            $metadataItems = [];
        }

        $metadata = collect($metadataItems)
            ->pluck('Value', 'Name');

        $receipt = $metadata->get('MpesaReceiptNumber');
        $paidAmount = $metadata->get('Amount');
        $callbackPhone = $metadata->get('PhoneNumber');

        if (
            !is_string($receipt)
            || trim($receipt) === ''
            || !is_numeric($paidAmount)
            || $callbackPhone === null
        ) {
            Log::error(
                'Successful license M-Pesa callback lacked required payment proof.',
                [
                    'checkout_request_id' =>
                        $checkoutRequestId,
                ]
            );

            return $this->callbackResponse();
        }

        $receipt = trim($receipt);

        $normalizedCallbackPhone =
            $this->mpesaService->normalizePhoneNumber(
                (string) $callbackPhone
            );

        if (!$normalizedCallbackPhone) {
            Log::error(
                'Successful license M-Pesa callback contained an invalid phone number.',
                [
                    'checkout_request_id' =>
                        $checkoutRequestId,
                ]
            );

            return $this->callbackResponse();
        }

        $result =
            $this->mpesaService->processSuccessfulCallback(
                checkoutRequestId: $checkoutRequestId,
                merchantRequestId: $merchantRequestId,
                receipt: $receipt,
                paidAmount: (float) $paidAmount,
                normalizedPhone: $normalizedCallbackPhone
            );

        if (
            ($result['status'] ?? null)
            === 'verification_failed'
        ) {
            Log::error(
                'License M-Pesa transaction could not be independently verified.',
                [
                    'checkout_request_id' =>
                        $checkoutRequestId,
                ]
            );
        }

        return $this->callbackResponse(
            'Callback received successfully'
        );
    }

    public function reconcile(LicensePayment $payment)
    {
        $admin = auth()->user();

        if (!$admin || $admin->role !== 'admin') {
            abort(
                403,
                'Only ISP Admins can reconcile license payments.'
            );
        }

        if (
            (int) $payment->user_id
            !== (int) $admin->id
        ) {
            abort(404);
        }

        $result =
            $this->mpesaService->reconcilePayment(
                $payment
            );

        return match ($result['status'] ?? null) {
            'approved' =>
                back()->with(
                    'success',
                    ($result['activated'] ?? false)
                        ? 'Payment verified directly with M-Pesa and your license has been activated.'
                        : 'This license payment is already approved.'
                ),

            'failed' =>
                back()->with(
                    'error',
                    'M-Pesa confirms that this payment was not completed.'
                ),

            'verification_unavailable' =>
                back()->with(
                    'error',
                    'M-Pesa could not be reached for verification. Please try again shortly.'
                ),

            'not_final' =>
                back()->with(
                    'error',
                    'M-Pesa has not returned a final status for this payment yet. Please try again shortly.'
                ),

            'checkout_mismatch',
            'merchant_mismatch' =>
                back()->with(
                    'error',
                    'The M-Pesa verification response did not match this payment.'
                ),

            'missing_checkout_id' =>
                back()->with(
                    'error',
                    'This payment cannot be verified because its M-Pesa request ID is missing.'
                ),

            'unsupported' =>
                back()->with(
                    'error',
                    'Only M-Pesa STK Push payments can be reconciled.'
                ),

            'not_pending' =>
                back()->with(
                    'error',
                    'This payment is no longer pending.'
                ),

            default =>
                back()->with(
                    'error',
                    'The payment was verified but could not be applied. Please contact support.'
                ),
        };
    }

    private function getAccessToken(): string
    {
        $consumerKey = config(
            'services.license_mpesa.consumer_key'
        );

        $consumerSecret = config(
            'services.license_mpesa.consumer_secret'
        );

        if (!$consumerKey || !$consumerSecret) {
            throw new \RuntimeException(
                'License M-Pesa consumer credentials are missing.'
            );
        }

        $response = Http::timeout($this->timeout())
            ->withBasicAuth(
                (string) $consumerKey,
                (string) $consumerSecret
            )
            ->acceptJson()
            ->get(
                $this->mpesaBaseUrl()
                .'/oauth/v1/generate',
                [
                    'grant_type' =>
                        'client_credentials',
                ]
            );

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Unable to obtain M-Pesa access token.'
            );
        }

        $accessToken = $response->json(
            'access_token'
        );

        if (
            !is_string($accessToken)
            || $accessToken === ''
        ) {
            throw new \RuntimeException(
                'M-Pesa access token was not returned.'
            );
        }

        return $accessToken;
    }

    private function generatePassword(
        string $shortcode,
        string $passkey,
        string $timestamp
    ): string {
        return base64_encode(
            $shortcode.
            $passkey.
            $timestamp
        );
    }

    private function mpesaBaseUrl(): string
    {
        $environment = strtolower(
            (string) config(
                'services.license_mpesa.environment',
                'sandbox'
            )
        );

        return $environment === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    private function timeout(): int
    {
        $timeout = (int) config(
            'services.license_mpesa.timeout',
            30
        );

        return max(
            5,
            min($timeout, 60)
        );
    }

    private function callbackResponse(
        string $description = 'Callback received'
    ) {
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => $description,
        ]);
    }
}
