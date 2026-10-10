<?php

namespace App\Console\Commands;

use App\Models\LicensePayment;
use App\Services\LicenseMpesaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcileLicenseMpesaPayments extends Command
{
    protected $signature = 'license-payments:reconcile-mpesa';

    protected $description =
        'Reconcile stale pending ISP Admin M-Pesa license payments with Safaricom.';

    public function handle(
        LicenseMpesaService $mpesaService
    ): int {
        $processed = 0;
        $approved = 0;
        $failed = 0;

        LicensePayment::query()
            ->where('payment_method', 'M-Pesa STK Push')
            ->where('status', 'pending')
            ->whereNotNull('checkout_request_id')
            ->where('updated_at', '<=', now()->subMinutes(2))
            ->orderBy('id')
            ->chunkById(
                50,
                function ($payments) use (
                    $mpesaService,
                    &$processed,
                    &$approved,
                    &$failed
                ) {
                    foreach ($payments as $payment) {
                        $processed++;

                        try {
                            $result =
                                $mpesaService->reconcilePayment(
                                    $payment
                                );

                            $status =
                                $result['status']
                                ?? 'unknown';

                            if ($status === 'approved') {
                                $approved++;
                                continue;
                            }

                            if ($status === 'failed') {
                                $failed++;
                                continue;
                            }

                            /*
                             * Pace retries for unresolved transactions.
                             * This prevents the scheduler from querying the
                             * same Safaricom CheckoutRequestID every minute.
                             */
                            if (
                                in_array(
                                    $status,
                                    [
                                        'verification_unavailable',
                                        'not_final',
                                        'not_applied',
                                    ],
                                    true
                                )
                            ) {
                                $payment->touch();
                            }
                        } catch (\Throwable $e) {
                            Log::error(
                                'Scheduled license M-Pesa reconciliation failed.',
                                [
                                    'payment_id' =>
                                        $payment->id,

                                    'exception' =>
                                        get_class($e),

                                    'message' =>
                                        $e->getMessage(),
                                ]
                            );

                            /*
                             * Delay the next automatic retry for this payment.
                             */
                            $payment->touch();
                        }
                    }
                }
            );

        if ($processed > 0) {
            Log::info(
                'Scheduled license M-Pesa reconciliation completed.',
                [
                    'processed' => $processed,
                    'approved' => $approved,
                    'failed' => $failed,
                ]
            );
        }

        $this->info(
            "Processed {$processed} pending M-Pesa license payment(s); "
            ."approved {$approved}; failed {$failed}."
        );

        return self::SUCCESS;
    }
}
