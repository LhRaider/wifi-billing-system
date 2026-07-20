<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            All Payments
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="
                background:white;
                padding:25px;
                border-radius:15px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
            ">

                <div style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:20px;
                ">
                    <h3 style="font-size:20px;font-weight:bold;">
                        Payment History
                    </h3>

                    <span style="
                        background:#dbeafe;
                        color:#1d4ed8;
                        padding:8px 14px;
                        border-radius:20px;
                        font-weight:bold;
                    ">
                        {{ $payments->total() }} Records
                    </span>
                </div>

                <table style="
                    width:100%;
                    border-collapse:collapse;
                    overflow:hidden;
                    border-radius:10px;
                ">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:14px;border:1px solid #e5e7eb;">Amount</th>
                            <th style="padding:14px;border:1px solid #e5e7eb;">Phone Number</th>
                            <th style="padding:14px;border:1px solid #e5e7eb;">Status</th>
                            <th style="padding:14px;border:1px solid #e5e7eb;">Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($payments as $payment)
                            <tr style="transition:0.3s;">
                                
                                <td style="padding:14px;border:1px solid #e5e7eb;font-weight:bold;">
                                    KES {{ number_format($payment->amount, 2) }}
                                </td>

                                <td style="padding:14px;border:1px solid #e5e7eb;">
                                    {{ $payment->phone_number }}
                                </td>

                                <td style="padding:14px;border:1px solid #e5e7eb;">

                                    @if($payment->status == 'completed')
                                        <span style="
                                            background:#dcfce7;
                                            color:#166534;
                                            padding:6px 12px;
                                            border-radius:20px;
                                            font-size:13px;
                                            font-weight:bold;
                                        ">
                                            🟢 Completed
                                        </span>

                                    @elseif($payment->status == 'pending')
                                        <span style="
                                            background:#fef9c3;
                                            color:#854d0e;
                                            padding:6px 12px;
                                            border-radius:20px;
                                            font-size:13px;
                                            font-weight:bold;
                                        ">
                                            🟡 Pending
                                        </span>

                                    @else
                                        <span style="
                                            background:#fee2e2;
                                            color:#991b1b;
                                            padding:6px 12px;
                                            border-radius:20px;
                                            font-size:13px;
                                            font-weight:bold;
                                        ">
                                            🔴 Failed
                                        </span>
                                    @endif

                                </td>

                                <td style="padding:14px;border:1px solid #e5e7eb;">
                                    {{ $payment->created_at->format('d M Y H:i') }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:25px;">
                    {{ $payments->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>