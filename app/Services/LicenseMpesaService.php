<?php

namespace App\Services;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Models\LicensePayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseMpesaService
{
    public function queryTransactionStatus(string $checkoutRequestId): ?array
    {
        try {
            $accessToken = $this->getAccessToken();
            $shortcode = config('services.license_mpesa.shortcode');
            $passkey = config('services.license_mpesa.passkey');

            if (!$shortcode || !$passkey) {
                return null;
            }

            $timestamp = now()->format('YmdHis');
            $password = $this->generatePassword(
                (string) $shortcode,
                (string) $passkey,
                $timestamp
            );

            $response = Http::timeout($this->timeout())
                ->withToken($accessToken)
                ->acceptJson()
                ->post(
                    $this->mpesaBaseUrl().'/mpesa/stkpushquery/v1/query',
                    [
                        'BusinessShortCode' => $shortcode,
                        'Password' => $password,
                        'Timestamp' => $timestamp,
                        'CheckoutRequestID' => $checkoutRequestId,
                    ]
                );

            if (!$response->successful()) {
                Log::warning(
                    'License M-Pesa STK verification HTTP request failed.',
                    [
                        'checkout_request_id' => $checkoutRequestId,
                        'http_status' => $response->status(),
                    ]
                );

                return null;
            }

            $result = $response->json();

            return is_array($result) ? $result : null;
        } catch (\Throwable $e) {
            Log::error(
                'License M-Pesa STK verification failed.',
                [
                    'checkout_request_id' => $checkoutRequestId,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]
            );

            return null;
        }
    }

    public function queryConfirmsSuccess(
        ?array $result,
        string $checkoutRequestId
    ): bool {
        if (!is_array($result)) {
            return false;
        }

        if ((string) ($result['ResultCode'] ?? '') !== '0') {
            return false;
        }

        $queriedCheckoutRequestId = isset($result['CheckoutRequestID'])
            ? (string) $result['CheckoutRequestID']
            : '';

        return $queriedCheckoutRequestId !== ''
            && hash_equals($checkoutRequestId, $queriedCheckoutRequestId);
    }

    public function reconcilePayment(LicensePayment $payment): array
    {
        if ($payment->payment_method !== 'M-Pesa STK Push') {
            return ['status' => 'unsupported', 'activated' => false];
        }

        if ($payment->status === 'approved') {
            return [
                'status' => 'approved',
                'activated' => false,
                'already_approved' => true,
            ];
        }

        if ($payment->status !== 'pending') {
            return ['status' => 'not_pending', 'activated' => false];
        }

        if (!$payment->checkout_request_id) {
            return ['status' => 'missing_checkout_id', 'activated' => false];
        }

        $queryResult = $this->queryTransactionStatus(
            (string) $payment->checkout_request_id
        );

        if (!is_array($queryResult)) {
            return ['status' => 'verification_unavailable', 'activated' => false];
        }

        $queriedCheckoutRequestId = isset($queryResult['CheckoutRequestID'])
            ? (string) $queryResult['CheckoutRequestID']
            : '';

        if (
            $queriedCheckoutRequestId === ''
            || !hash_equals(
                (string) $payment->checkout_request_id,
                $queriedCheckoutRequestId
            )
        ) {
            Log::critical(
                'License M-Pesa reconciliation CheckoutRequestID mismatch.',
                ['payment_id' => $payment->id]
            );

            return ['status' => 'checkout_mismatch', 'activated' => false];
        }

        $queriedMerchantRequestId = isset($queryResult['MerchantRequestID'])
            ? (string) $queryResult['MerchantRequestID']
            : null;

        if (
            $payment->merchant_request_id
            && $queriedMerchantRequestId
            && !hash_equals(
                (string) $payment->merchant_request_id,
                $queriedMerchantRequestId
            )
        ) {
            Log::critical(
                'License M-Pesa reconciliation MerchantRequestID mismatch.',
                ['payment_id' => $payment->id]
            );

            return ['status' => 'merchant_mismatch', 'activated' => false];
        }

        if (!array_key_exists('ResultCode', $queryResult)) {
            return ['status' => 'not_final', 'activated' => false];
        }

        $resultCode = (string) $queryResult['ResultCode'];

        if ($resultCode !== '0') {
            $this->markVerifiedFailure(
                (string) $payment->checkout_request_id,
                $queriedMerchantRequestId,
                (int) $resultCode
            );

            return [
                'status' => 'failed',
                'activated' => false,
                'result_code' => $resultCode,
            ];
        }

        $activationResult = $this->activateVerifiedPayment(
            checkoutRequestId: (string) $payment->checkout_request_id,
            merchantRequestId: $queriedMerchantRequestId,
            receipt: null,
            paidAmount: null,
            normalizedPhone: null,
            verificationSource: 'stk_query'
        );

        $this->sendActivationEvents($activationResult, 'stk_query');

        return array_merge(
            [
                'status' => ($activationResult['activated'] ?? false)
                    ? 'approved'
                    : (($activationResult['already_approved'] ?? false)
                        ? 'approved'
                        : 'not_applied'),
            ],
            $activationResult
        );
    }

    public function processSuccessfulCallback(
        string $checkoutRequestId,
        ?string $merchantRequestId,
        string $receipt,
        float $paidAmount,
        string $normalizedPhone
    ): array {
        $queryResult = $this->queryTransactionStatus($checkoutRequestId);

        if (!$this->queryConfirmsSuccess($queryResult, $checkoutRequestId)) {
            return ['status' => 'verification_failed', 'activated' => false];
        }

        $verifiedMerchantRequestId = isset($queryResult['MerchantRequestID'])
            ? (string) $queryResult['MerchantRequestID']
            : null;

        if (
            $merchantRequestId
            && $verifiedMerchantRequestId
            && !hash_equals($merchantRequestId, $verifiedMerchantRequestId)
        ) {
            Log::warning(
                'License M-Pesa callback MerchantRequestID did not match Safaricom query.',
                ['checkout_request_id' => $checkoutRequestId]
            );

            return ['status' => 'merchant_mismatch', 'activated' => false];
        }

        $activationResult = $this->activateVerifiedPayment(
            checkoutRequestId: $checkoutRequestId,
            merchantRequestId: $merchantRequestId ?? $verifiedMerchantRequestId,
            receipt: $receipt,
            paidAmount: $paidAmount,
            normalizedPhone: $normalizedPhone,
            verificationSource: 'callback'
        );

        $this->sendActivationEvents($activationResult, 'callback');

        return $activationResult;
    }

    public function verifyAndMarkFailure(
        string $checkoutRequestId,
        ?string $merchantRequestId,
        int $callbackResultCode
    ): bool {
        $queryResult = $this->queryTransactionStatus($checkoutRequestId);

        $verifiedResultCode = is_array($queryResult)
            ? (string) ($queryResult['ResultCode'] ?? '')
            : null;

        if (
            $verifiedResultCode === null
            || $verifiedResultCode === ''
            || $verifiedResultCode !== (string) $callbackResultCode
        ) {
            Log::warning(
                'License M-Pesa failed callback could not be independently verified.',
                [
                    'checkout_request_id' => $checkoutRequestId,
                    'callback_result_code' => $callbackResultCode,
                    'verified_result_code' => $verifiedResultCode,
                ]
            );

            return false;
        }

        $queriedMerchantRequestId = isset($queryResult['MerchantRequestID'])
            ? (string) $queryResult['MerchantRequestID']
            : null;

        if (
            $merchantRequestId
            && $queriedMerchantRequestId
            && !hash_equals($merchantRequestId, $queriedMerchantRequestId)
        ) {
            Log::warning(
                'License M-Pesa failed callback MerchantRequestID did not match Safaricom query.',
                ['checkout_request_id' => $checkoutRequestId]
            );

            return false;
        }

        $this->markVerifiedFailure(
            $checkoutRequestId,
            $merchantRequestId ?? $queriedMerchantRequestId,
            $callbackResultCode
        );

        return true;
    }

    public function normalizePhoneNumber(string $phoneNumber): ?string
    {
        $phone = preg_replace('/\D+/', '', $phoneNumber);

        if (!$phone) {
            return null;
        }

        if (str_starts_with($phone, '254')) {
            return preg_match('/^254[17]\d{8}$/', $phone)
                ? $phone
                : null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '254'.substr($phone, 1);
        } elseif (
            str_starts_with($phone, '7')
            || str_starts_with($phone, '1')
        ) {
            $phone = '254'.$phone;
        }

        return preg_match('/^254[17]\d{8}$/', $phone)
            ? $phone
            : null;
    }

    private function markVerifiedFailure(
        string $checkoutRequestId,
        ?string $merchantRequestId,
        int $resultCode
    ): void {
        DB::transaction(function () use (
            $checkoutRequestId,
            $merchantRequestId,
            $resultCode
        ) {
            $payment = LicensePayment::where(
                'checkout_request_id',
                $checkoutRequestId
            )
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                Log::warning(
                    'Verified unsuccessful license M-Pesa transaction did not match a payment.',
                    ['checkout_request_id' => $checkoutRequestId]
                );

                return;
            }

            if (
                $payment->merchant_request_id
                && $merchantRequestId
                && !hash_equals(
                    (string) $payment->merchant_request_id,
                    $merchantRequestId
                )
            ) {
                Log::warning(
                    'Verified unsuccessful license M-Pesa MerchantRequestID mismatch.',
                    ['payment_id' => $payment->id]
                );

                return;
            }

            if ($payment->status === 'pending') {
                $payment->update(['status' => 'failed']);
            }

            Log::info(
                'License M-Pesa transaction was independently verified as unsuccessful.',
                [
                    'payment_id' => $payment->id,
                    'result_code' => $resultCode,
                ]
            );
        });
    }

    private function activateVerifiedPayment(
        string $checkoutRequestId,
        ?string $merchantRequestId,
        ?string $receipt,
        ?float $paidAmount,
        ?string $normalizedPhone,
        string $verificationSource
    ): array {
        try {
            return DB::transaction(function () use (
                $checkoutRequestId,
                $merchantRequestId,
                $receipt,
                $paidAmount,
                $normalizedPhone,
                $verificationSource
            ) {
                $payment = LicensePayment::where(
                    'checkout_request_id',
                    $checkoutRequestId
                )
                    ->lockForUpdate()
                    ->first();

                if (!$payment) {
                    Log::warning(
                        'Verified license M-Pesa transaction did not match a payment.',
                        ['checkout_request_id' => $checkoutRequestId]
                    );

                    return ['activated' => false];
                }

                if (
                    $payment->merchant_request_id
                    && $merchantRequestId
                    && !hash_equals(
                        (string) $payment->merchant_request_id,
                        $merchantRequestId
                    )
                ) {
                    Log::warning(
                        'Verified license M-Pesa MerchantRequestID mismatch.',
                        ['payment_id' => $payment->id]
                    );

                    return ['activated' => false];
                }

                if ($payment->status === 'approved') {
                    if (
                        $receipt
                        && !$payment->mpesa_receipt
                        && $this->callbackProofMatchesPayment(
                            $payment,
                            $paidAmount,
                            $normalizedPhone
                        )
                    ) {
                        $receiptAlreadyUsed = LicensePayment::where(
                            'mpesa_receipt',
                            $receipt
                        )
                            ->where('id', '!=', $payment->id)
                            ->exists();

                        if (!$receiptAlreadyUsed) {
                            $payment->update([
                                'transaction_code' => $receipt,
                                'mpesa_receipt' => $receipt,
                            ]);
                        }
                    }

                    return [
                        'activated' => false,
                        'already_approved' => true,
                    ];
                }

                if ($payment->status !== 'pending') {
                    Log::warning(
                        'Verified license M-Pesa transaction targeted a non-pending payment.',
                        [
                            'payment_id' => $payment->id,
                            'status' => $payment->status,
                        ]
                    );

                    return ['activated' => false];
                }

                if ($verificationSource === 'callback') {
                    if (
                        !$receipt
                        || $paidAmount === null
                        || !$normalizedPhone
                    ) {
                        Log::error(
                            'License M-Pesa callback proof was incomplete.',
                            ['payment_id' => $payment->id]
                        );

                        return ['activated' => false];
                    }

                    if (!$this->callbackProofMatchesPayment(
                        $payment,
                        $paidAmount,
                        $normalizedPhone
                    )) {
                        return ['activated' => false];
                    }
                } else {
                    if (
                        !$payment->phone_number
                        || !$this->normalizePhoneNumber(
                            (string) $payment->phone_number
                        )
                        || (float) $payment->amount <= 0
                    ) {
                        Log::error(
                            'License M-Pesa reconciliation found invalid stored payment proof.',
                            ['payment_id' => $payment->id]
                        );

                        return ['activated' => false];
                    }
                }

                if ($receipt) {
                    $receiptAlreadyUsed = LicensePayment::where(
                        'mpesa_receipt',
                        $receipt
                    )
                        ->where('id', '!=', $payment->id)
                        ->exists();

                    if ($receiptAlreadyUsed) {
                        Log::critical(
                            'Duplicate license M-Pesa receipt detected.',
                            ['payment_id' => $payment->id]
                        );

                        return ['activated' => false];
                    }
                }

                $admin = User::whereKey($payment->user_id)
                    ->lockForUpdate()
                    ->first();

                if (!$admin || $admin->role !== 'admin') {
                    Log::error(
                        'License M-Pesa payment is not linked to a valid ISP Admin.',
                        ['payment_id' => $payment->id]
                    );

                    return ['activated' => false];
                }

                $existingExpiry = $admin->service_expires_at
                    ? Carbon::parse($admin->service_expires_at)
                    : null;

                $licenseStart = $existingExpiry && $existingExpiry->isFuture()
                    ? $existingExpiry
                    : now();

                $expiryDate = $licenseStart
                    ->copy()
                    ->addMonths((int) $payment->months);

                $payment->update([
                    'transaction_code' => $receipt,
                    'mpesa_receipt' => $receipt,
                    'paid_at' => now(),
                    'start_date' => now(),
                    'expiry_date' => $expiryDate,
                    'status' => 'approved',
                ]);

                $admin->update([
                    'service_status' => 'active',
                    'service_expires_at' => $expiryDate,
                    'is_suspended' => false,
                ]);

                return [
                    'activated' => true,
                    'admin' => $admin,
                    'payment' => $payment,
                    'expiry_date' => $expiryDate,
                ];
            });
        } catch (\Throwable $e) {
            Log::error(
                'Verified license M-Pesa payment processing failed.',
                [
                    'checkout_request_id' => $checkoutRequestId,
                    'verification_source' => $verificationSource,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                ]
            );

            return ['activated' => false];
        }
    }

    private function callbackProofMatchesPayment(
        LicensePayment $payment,
        ?float $paidAmount,
        ?string $normalizedPhone
    ): bool {
        if (
            !$normalizedPhone
            || !$payment->phone_number
            || !hash_equals(
                (string) $payment->phone_number,
                $normalizedPhone
            )
        ) {
            Log::error(
                'License M-Pesa callback phone number mismatch.',
                ['payment_id' => $payment->id]
            );

            return false;
        }

        if (
            $paidAmount === null
            || round($paidAmount, 2)
                !== round((float) $payment->amount, 2)
        ) {
            Log::error(
                'License M-Pesa amount mismatch.',
                [
                    'payment_id' => $payment->id,
                    'expected' => $payment->amount,
                    'received' => $paidAmount,
                ]
            );

            return false;
        }

        return true;
    }

    private function sendActivationEvents(
        array $activationResult,
        string $verificationSource
    ): void {
        if (!($activationResult['activated'] ?? false)) {
            return;
        }

        $admin = $activationResult['admin'];
        $payment = $activationResult['payment'];
        $expiryDate = $activationResult['expiry_date'];

        AuditHelper::log(
            'Automatic License Activation',
            'License Payments',
            'License payment ID '.
            $payment->id.
            ' for '.
            $admin->name.
            ' automatically approved after verified M-Pesa confirmation.'
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

        Log::info(
            'ISP Admin license activated through verified M-Pesa payment.',
            [
                'payment_id' => $payment->id,
                'admin_id' => $admin->id,
                'expiry_date' => $expiryDate->toDateTimeString(),
                'verification_source' => $verificationSource,
            ]
        );
    }

    private function getAccessToken(): string
    {
        $consumerKey = config('services.license_mpesa.consumer_key');
        $consumerSecret = config('services.license_mpesa.consumer_secret');

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
                $this->mpesaBaseUrl().'/oauth/v1/generate',
                ['grant_type' => 'client_credentials']
            );

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Unable to obtain M-Pesa access token.'
            );
        }

        $accessToken = $response->json('access_token');

        if (!is_string($accessToken) || $accessToken === '') {
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
        return base64_encode($shortcode.$passkey.$timestamp);
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

        return max(5, min($timeout, 60));
    }
}
