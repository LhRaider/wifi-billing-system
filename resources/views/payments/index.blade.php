<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment History
        </h2>
    </x-slot>

    <style>
        .payment-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.10);
        }

        .summary-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.10);
            text-align: center;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-block;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-block;
        }

        .status-failed {
            background: #fee2e2;
            color: #991b1b;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-block;
        }

        .table-header {
            background: #f8fafc;
        }
    </style>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom:20px;padding:14px 16px;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;border-radius:10px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div style="margin-bottom:20px;padding:14px 16px;background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:10px;">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div style="margin-bottom:20px;padding:14px 16px;background:#fee2e2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;">
                    {{ session('error') }}
                </div>
            @endif

            <div
                style="
                    display:grid;
                    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
                    gap:20px;
                    margin-bottom:25px;
                "
            >
                <div class="summary-box">
                    <h3 style="margin:0 0 8px;">
                        Total Payment Records
                    </h3>

                    <h2 style="font-size:32px;font-weight:bold;color:#2563eb;margin:0;">
                        {{ $totalPayments }}
                    </h2>
                </div>

                <div class="summary-box">
                    <h3 style="margin:0 0 8px;">
                        Total Completed Spend
                    </h3>

                    <h2 style="font-size:32px;font-weight:bold;color:#16a34a;margin:0;">
                        KES {{ number_format($totalSpent, 2) }}
                    </h2>
                </div>

                <div class="summary-box">
                    <h3 style="margin:0 0 8px;">
                        Successful Payments
                    </h3>

                    <h2 style="font-size:32px;font-weight:bold;color:#16a34a;margin:0;">
                        {{ $successfulPayments }}
                    </h2>
                </div>
            </div>

            <div class="payment-card">

                <div
                    style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        gap:15px;
                        flex-wrap:wrap;
                        margin-bottom:20px;
                    "
                >
                    <div>
                        <h3 style="font-size:22px;font-weight:800;margin:0;color:#111827;">
                            Transaction History
                        </h3>

                        <p style="margin:6px 0 0;color:#6b7280;font-size:14px;">
                            Your payment records are shown below.
                        </p>
                    </div>

                    <span
                        style="
                            background:#dbeafe;
                            color:#1d4ed8;
                            padding:8px 14px;
                            border-radius:20px;
                            font-weight:700;
                        "
                    >
                        {{ $payments->total() }} Records
                    </span>
                </div>

                @if($payments->count() > 0)

                    <div style="overflow-x:auto;">
                        <table
                            style="
                                width:100%;
                                border-collapse:collapse;
                                min-width:950px;
                            "
                        >
                            <thead>
                                <tr class="table-header">
                                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;">
                                        Date
                                    </th>

                                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;">
                                        Package
                                    </th>

                                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;">
                                        Amount
                                    </th>

                                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;">
                                        Phone
                                    </th>

                                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;">
                                        Receipt
                                    </th>

                                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;">
                                        Status
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td style="border:1px solid #e5e7eb;padding:12px;vertical-align:top;white-space:nowrap;">
                                            @if($payment->paid_at)
                                                {{ $payment->paid_at->format('d M Y, H:i') }}
                                            @elseif($payment->created_at)
                                                {{ $payment->created_at->format('d M Y, H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>

                                        <td style="border:1px solid #e5e7eb;padding:12px;vertical-align:top;">
                                            {{ $payment->package->name ?? 'Package unavailable' }}
                                        </td>

                                        <td style="border:1px solid #e5e7eb;padding:12px;vertical-align:top;white-space:nowrap;">
                                            KES {{ number_format($payment->amount, 2) }}
                                        </td>

                                        <td style="border:1px solid #e5e7eb;padding:12px;vertical-align:top;">
                                            {{ $payment->phone_number ?: 'N/A' }}
                                        </td>

                                        <td style="border:1px solid #e5e7eb;padding:12px;vertical-align:top;">
                                            @if($payment->mpesa_receipt)
                                                {{ $payment->mpesa_receipt }}
                                            @else
                                                <span style="color:#9ca3af;">
                                                    Not available
                                                </span>
                                            @endif
                                        </td>

                                        <td style="border:1px solid #e5e7eb;padding:12px;vertical-align:top;">
                                            @if($payment->status === 'completed')
                                                <span class="status-completed">
                                                    Completed
                                                </span>

                                            @elseif($payment->status === 'pending')
                                                <span class="status-pending">
                                                    Pending
                                                </span>

                                            @else
                                                <span class="status-failed">
                                                    Failed
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:20px;">
                        {{ $payments->links() }}
                    </div>

                @else

                    <div
                        style="
                            text-align:center;
                            padding:40px;
                            color:#6b7280;
                        "
                    >
                        <h3 style="margin:0;font-size:18px;font-weight:700;">
                            No payment history found.
                        </h3>

                        <p style="margin-top:10px;">
                            Completed customer-to-ISP Admin payments will appear here when the payment service is available.
                        </p>
                    </div>

                @endif

                <div
                    style="
                        margin-top:24px;
                        padding:14px 16px;
                        background:#eff6ff;
                        color:#1e3a8a;
                        border:1px solid #bfdbfe;
                        border-radius:10px;
                        font-size:14px;
                        line-height:1.6;
                    "
                >
                    Only completed payments are included in your total spend.
                    Pending or failed transactions do not count as successful payment.
                    Automatic customer-to-ISP Admin M-Pesa processing is currently paused until the final payment provider is implemented.
                </div>

            </div>

        </div>
    </div>
</x-app-layout>