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
                    {{ $isSuperAdmin ? 'Top ISP Admins' : 'Top Customers' }}
                </h2>

                <p style="
                    margin-top:5px;
                    color:#6b7280;
                    font-size:14px;
                ">
                    @if($isSuperAdmin)
                        ISP Admins ranked by approved platform license payments.
                    @else
                        Customers ranked by completed payments within your ISP account.
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
                {{ number_format($topCustomers->total()) }}
                {{ $topCustomers->total() === 1 ? 'Record' : 'Records' }}
            </span>
        </div>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="
                background:white;
                padding:25px;
                border-radius:15px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
            ">

                @if($topCustomers->count())

                    <div style="overflow-x:auto;">
                        <table style="
                            width:100%;
                            border-collapse:collapse;
                            min-width:760px;
                        ">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="
                                        padding:14px;
                                        border:1px solid #e5e7eb;
                                        text-align:left;
                                    ">
                                        {{ $isSuperAdmin ? 'ISP Admin' : 'Customer' }}
                                    </th>

                                    <th style="
                                        padding:14px;
                                        border:1px solid #e5e7eb;
                                        text-align:left;
                                    ">
                                        Email
                                    </th>

                                    <th style="
                                        padding:14px;
                                        border:1px solid #e5e7eb;
                                        text-align:left;
                                    ">
                                        Successful Payments
                                    </th>

                                    <th style="
                                        padding:14px;
                                        border:1px solid #e5e7eb;
                                        text-align:left;
                                    ">
                                        Total Spent
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($topCustomers as $customer)
                                    <tr>
                                        <td style="
                                            padding:14px;
                                            border:1px solid #e5e7eb;
                                            font-weight:700;
                                            color:#111827;
                                        ">
                                            {{ $customer['name'] ?: 'Unnamed account' }}
                                        </td>

                                        <td style="
                                            padding:14px;
                                            border:1px solid #e5e7eb;
                                            color:#4b5563;
                                        ">
                                            {{ $customer['email'] ?: '—' }}
                                        </td>

                                        <td style="
                                            padding:14px;
                                            border:1px solid #e5e7eb;
                                        ">
                                            {{ number_format((int) $customer['payments']) }}
                                        </td>

                                        <td style="
                                            padding:14px;
                                            border:1px solid #e5e7eb;
                                            font-weight:700;
                                            white-space:nowrap;
                                        ">
                                            KES {{ number_format((float) $customer['spent'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:25px;">
                        {{ $topCustomers->links() }}
                    </div>

                @else

                    <div style="
                        text-align:center;
                        padding:50px 20px;
                        color:#6b7280;
                    ">
                        <div style="font-size:42px;margin-bottom:15px;">
                            👥
                        </div>

                        <h3 style="
                            font-size:18px;
                            font-weight:700;
                            color:#374151;
                            margin-bottom:8px;
                        ">
                            No ranking data available
                        </h3>

                        <p style="margin:0;">
                            @if($isSuperAdmin)
                                ISP Admin accounts will appear here as platform activity is recorded.
                            @else
                                Customers will appear here as completed payment activity is recorded.
                            @endif
                        </p>
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>