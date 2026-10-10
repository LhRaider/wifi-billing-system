<x-app-layout>
    <x-slot name="header">
        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            flex-wrap:wrap;
        ">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $isSuperAdmin ? 'Platform License Payments' : 'Customer Payments' }}
                </h2>

                <p style="
                    margin-top:5px;
                    color:#6b7280;
                    font-size:14px;
                ">
                    @if($isSuperAdmin)
                        Payments made by ISP Admins for platform access.
                    @else
                        Payments made by customers belonging to your ISP account.
                    @endif
                </p>
            </div>

            <span style="
                background:#dbeafe;
                color:#1d4ed8;
                padding:8px 14px;
                border-radius:999px;
                font-weight:700;
                font-size:14px;
            ">
                {{ number_format($payments->total()) }}
                {{ $payments->total() === 1 ? 'Record' : 'Records' }}
            </span>
        </div>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="
                    background:#dcfce7;
                    color:#166534;
                    border:1px solid #bbf7d0;
                    padding:14px 16px;
                    border-radius:10px;
                    margin-bottom:20px;
                ">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div style="
                    background:#fef3c7;
                    color:#92400e;
                    border:1px solid #fde68a;
                    padding:14px 16px;
                    border-radius:10px;
                    margin-bottom:20px;
                ">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div style="
                    background:#fee2e2;
                    color:#991b1b;
                    border:1px solid #fecaca;
                    padding:14px 16px;
                    border-radius:10px;
                    margin-bottom:20px;
                ">
                    {{ session('error') }}
                </div>
            @endif

            <div style="
                background:white;
                padding:25px;
                border-radius:15px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
            ">

                @if($payments->count())

                    <div style="overflow-x:auto;">

                        <table style="
                            width:100%;
                            border-collapse:collapse;
                            min-width:950px;
                        ">
                            <thead>
                                <tr style="background:#f8fafc;">

                                    @if($isSuperAdmin)
                                        <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                            ISP Admin
                                        </th>

                                        <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                            Months
                                        </th>
                                    @else
                                        <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                            Customer
                                        </th>

                                        <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                            Package
                                        </th>
                                    @endif

                                    <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                        Amount
                                    </th>

                                    <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                        Phone
                                    </th>

                                    <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                        Receipt
                                    </th>

                                    <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                        Status
                                    </th>

                                    <th style="padding:14px;border:1px solid #e5e7eb;text-align:left;">
                                        Date
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>

                                        @if($isSuperAdmin)

                                            <td style="padding:14px;border:1px solid #e5e7eb;">
                                                <div style="font-weight:700;color:#111827;">
                                                    {{ $payment->user?->name ?? 'ISP Admin unavailable' }}
                                                </div>

                                                @if($payment->user?->email)
                                                    <div style="
                                                        color:#6b7280;
                                                        font-size:13px;
                                                        margin-top:3px;
                                                    ">
                                                        {{ $payment->user->email }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td style="padding:14px;border:1px solid #e5e7eb;">
                                                {{ number_format((int) $payment->months) }}
                                                {{ (int) $payment->months === 1 ? 'Month' : 'Months' }}
                                            </td>

                                        @else

                                            <td style="padding:14px;border:1px solid #e5e7eb;">
                                                <div style="font-weight:700;color:#111827;">
                                                    {{ $payment->user?->name ?? 'Customer unavailable' }}
                                                </div>

                                                @if($payment->user?->email)
                                                    <div style="
                                                        color:#6b7280;
                                                        font-size:13px;
                                                        margin-top:3px;
                                                    ">
                                                        {{ $payment->user->email }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td style="padding:14px;border:1px solid #e5e7eb;">
                                                {{ $payment->package?->name ?? 'Package unavailable' }}
                                            </td>

                                        @endif

                                        <td style="
                                            padding:14px;
                                            border:1px solid #e5e7eb;
                                            font-weight:700;
                                            white-space:nowrap;
                                        ">
                                            KES {{ number_format((float) $payment->amount, 2) }}
                                        </td>

                                        <td style="padding:14px;border:1px solid #e5e7eb;">
                                            {{ $payment->phone_number ?: '—' }}
                                        </td>

                                        <td style="padding:14px;border:1px solid #e5e7eb;">
                                            @php
                                                $receipt = $payment->mpesa_receipt ?? null;
                                            @endphp

                                            @if($receipt)
                                                <span style="
                                                    font-family:monospace;
                                                    font-weight:700;
                                                    color:#374151;
                                                ">
                                                    {{ $receipt }}
                                                </span>
                                            @else
                                                <span style="color:#9ca3af;">
                                                    —
                                                </span>
                                            @endif
                                        </td>

                                        <td style="padding:14px;border:1px solid #e5e7eb;">

                                            @php
                                                $status = strtolower((string) $payment->status);
                                            @endphp

                                            @if(in_array($status, ['approved', 'completed'], true))
                                                <span style="
                                                    display:inline-block;
                                                    background:#dcfce7;
                                                    color:#166534;
                                                    padding:6px 12px;
                                                    border-radius:999px;
                                                    font-size:13px;
                                                    font-weight:700;
                                                    white-space:nowrap;
                                                ">
                                                    {{ $status === 'approved' ? 'Approved' : 'Completed' }}
                                                </span>

                                            @elseif($status === 'pending')
                                                <span style="
                                                    display:inline-block;
                                                    background:#fef3c7;
                                                    color:#92400e;
                                                    padding:6px 12px;
                                                    border-radius:999px;
                                                    font-size:13px;
                                                    font-weight:700;
                                                    white-space:nowrap;
                                                ">
                                                    Pending
                                                </span>

                                            @elseif($status === 'failed')
                                                <span style="
                                                    display:inline-block;
                                                    background:#fee2e2;
                                                    color:#991b1b;
                                                    padding:6px 12px;
                                                    border-radius:999px;
                                                    font-size:13px;
                                                    font-weight:700;
                                                    white-space:nowrap;
                                                ">
                                                    Failed
                                                </span>

                                            @else
                                                <span style="
                                                    display:inline-block;
                                                    background:#e5e7eb;
                                                    color:#374151;
                                                    padding:6px 12px;
                                                    border-radius:999px;
                                                    font-size:13px;
                                                    font-weight:700;
                                                    white-space:nowrap;
                                                ">
                                                    {{ ucfirst($status ?: 'Unknown') }}
                                                </span>
                                            @endif

                                        </td>

                                        <td style="
                                            padding:14px;
                                            border:1px solid #e5e7eb;
                                            white-space:nowrap;
                                        ">
                                            @if($payment->paid_at)
                                                {{ $payment->paid_at->format('d M Y H:i') }}
                                            @elseif($payment->created_at)
                                                {{ $payment->created_at->format('d M Y H:i') }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <div style="margin-top:25px;">
                        {{ $payments->links() }}
                    </div>

                @else

                    <div style="
                        text-align:center;
                        padding:50px 20px;
                        color:#6b7280;
                    ">
                        <div style="
                            font-size:42px;
                            margin-bottom:15px;
                        ">
                            💳
                        </div>

                        <h3 style="
                            font-size:18px;
                            font-weight:700;
                            color:#374151;
                            margin-bottom:8px;
                        ">
                            No payment records found
                        </h3>

                        <p style="margin:0;">
                            @if($isSuperAdmin)
                                ISP Admin license payments will appear here when they are recorded.
                            @else
                                Customer payments for your ISP account will appear here when they are recorded.
                            @endif
                        </p>
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>