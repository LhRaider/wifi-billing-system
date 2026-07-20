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
            box-shadow: 0 6px 15px rgba(0,0,0,0.10);
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }

        .status-failed {
            background: #fee2e2;
            color: #991b1b;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }

        .table-header {
            background: #f8fafc;
        }

        .summary-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.10);
            text-align: center;
        }
    </style>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:25px;">

                <div class="summary-box">
                    <h3>💳 Total Payments</h3>
                    <h2 style="font-size:32px;font-weight:bold;color:#2563eb;">
                        {{ $payments->count() }}
                    </h2>
                </div>

                <div class="summary-box">
                    <h3>💰 Total Spent</h3>
                    <h2 style="font-size:32px;font-weight:bold;color:#16a34a;">
                        KES {{ number_format($payments->sum('amount'), 2) }}
                    </h2>
                </div>

                <div class="summary-box">
                    <h3>✅ Successful Payments</h3>
                    <h2 style="font-size:32px;font-weight:bold;color:#16a34a;">
                        {{ $payments->where('status', 'completed')->count() }}
                    </h2>
                </div>

            </div>

            <div class="payment-card">

                @if($payments->count() > 0)

                    <table style="width:100%;border-collapse:collapse;">

                        <thead>
                            <tr class="table-header">
                                <th style="border:1px solid #ddd;padding:12px;">Date</th>
                                <th style="border:1px solid #ddd;padding:12px;">Amount</th>
                                <th style="border:1px solid #ddd;padding:12px;">Phone</th>
                                <th style="border:1px solid #ddd;padding:12px;">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($payments as $payment)

                                <tr>
                                    <td style="border:1px solid #ddd;padding:12px;">
                                        {{ $payment->created_at }}
                                    </td>

                                    <td style="border:1px solid #ddd;padding:12px;">
                                        KES {{ number_format($payment->amount, 2) }}
                                    </td>

                                    <td style="border:1px solid #ddd;padding:12px;">
                                        {{ $payment->phone_number }}
                                    </td>

                                    <td style="border:1px solid #ddd;padding:12px;">

                                        @if($payment->status === 'completed')
                                            <span class="status-completed">
                                                🟢 Completed
                                            </span>

                                        @elseif($payment->status === 'pending')
                                            <span class="status-pending">
                                                🟡 Pending
                                            </span>

                                        @else
                                            <span class="status-failed">
                                                🔴 Failed
                                            </span>
                                        @endif

                                    </td>
                                </tr>

                            @endforeach
                        </tbody>

                    </table>

                @else

                    <div style="
                        text-align:center;
                        padding:40px;
                        color:#6b7280;
                    ">
                        <h3>No payment history found.</h3>

                        <p style="margin-top:10px;">
                            Purchase a package to start using the service.
                        </p>
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>