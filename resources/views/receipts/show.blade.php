<x-app-layout>

    <x-slot name="header">
        <div style="display:flex;align-items:center;gap:14px;">
            <span
                style="
                    width:48px;
                    height:48px;
                    border-radius:14px;
                    background:#dbeafe;
                    color:#2563eb;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:19px;
                "
            >
                <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
            </span>

            <div>
                <h2 style="margin:0;font-size:25px;font-weight:800;color:#0f172a;">
                    Payment Receipt
                </h2>

                <p style="margin:4px 0 0;color:#64748b;font-size:13px;">
                    Completed internet package payment record.
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $admin = $payment->package?->admin;
        $package = $payment->package;
        $customer = $payment->user;
    @endphp

    <div
        style="
            background:#f4f7fb;
            min-height:100vh;
            padding:38px 20px 55px;
        "
    >
        <div
            style="
                max-width:1000px;
                margin:0 auto;
                background:#ffffff;
                border:1px solid #e5eaf0;
                border-radius:22px;
                overflow:hidden;
                box-shadow:0 12px 35px rgba(15,23,42,.07);
            "
        >

            {{-- Receipt Header --}}
            <div
                style="
                    background:linear-gradient(135deg,#1d4ed8,#4338ca);
                    padding:32px;
                    color:#ffffff;
                    display:flex;
                    justify-content:space-between;
                    align-items:flex-start;
                    flex-wrap:wrap;
                    gap:25px;
                "
            >
                <div>
                    <div
                        style="
                            width:52px;
                            height:52px;
                            border-radius:16px;
                            background:rgba(255,255,255,.14);
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:21px;
                            margin-bottom:16px;
                        "
                    >
                        <i class="fas fa-receipt" aria-hidden="true"></i>
                    </div>

                    <h1
                        style="
                            margin:0;
                            font-size:28px;
                            line-height:1.2;
                            font-weight:900;
                        "
                    >
                        PAYMENT RECEIPT
                    </h1>

                    <p
                        style="
                            margin:12px 0 4px;
                            font-size:11px;
                            font-weight:700;
                            opacity:.8;
                            letter-spacing:.05em;
                        "
                    >
                        RECEIPT NUMBER
                    </p>

                    <strong style="font-size:18px;">
                        REC-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                    </strong>
                </div>

                <div style="text-align:right;max-width:390px;">
                    <h2
                        style="
                            margin:0;
                            font-size:22px;
                            font-weight:900;
                        "
                    >
                        {{ $admin?->business_name ?? 'WiFi Billing' }}
                    </h2>

                    @if($admin?->network_name)
                        <p style="margin:9px 0 0;font-size:13px;">
                            <i
                                class="fas fa-tower-broadcast"
                                style="width:18px;"
                                aria-hidden="true"
                            ></i>
                            {{ $admin->network_name }}
                        </p>
                    @endif

                    @if($admin?->business_phone)
                        <p style="margin:7px 0 0;font-size:13px;">
                            <i
                                class="fas fa-phone"
                                style="width:18px;"
                                aria-hidden="true"
                            ></i>
                            {{ $admin->business_phone }}
                        </p>
                    @endif

                    @if($admin?->business_address)
                        <p style="margin:7px 0 0;font-size:13px;">
                            <i
                                class="fas fa-location-dot"
                                style="width:18px;"
                                aria-hidden="true"
                            ></i>
                            {{ $admin->business_address }}
                        </p>
                    @endif

                    <p style="margin:12px 0 0;font-size:13px;">
                        <i
                            class="fas fa-calendar-days"
                            style="width:18px;"
                            aria-hidden="true"
                        ></i>

                        @if($payment->paid_at)
                            {{ $payment->paid_at->format('d M Y • H:i') }}
                        @else
                            {{ $payment->created_at?->format('d M Y • H:i') ?? 'N/A' }}
                        @endif
                    </p>
                </div>
            </div>

            <div style="padding:30px;">

                {{-- Information Cards --}}
                <div
                    style="
                        display:grid;
                        grid-template-columns:repeat(auto-fit,minmax(290px,1fr));
                        gap:20px;
                        margin-bottom:28px;
                    "
                >
                    <section
                        style="
                            background:#f8fafc;
                            border:1px solid #e5eaf0;
                            border-radius:16px;
                            padding:22px;
                        "
                    >
                        <div
                            style="
                                display:flex;
                                align-items:center;
                                gap:11px;
                                margin-bottom:20px;
                            "
                        >
                            <span
                                style="
                                    width:38px;
                                    height:38px;
                                    border-radius:11px;
                                    background:#dbeafe;
                                    color:#2563eb;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                "
                            >
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </span>

                            <h2
                                style="
                                    margin:0;
                                    font-size:16px;
                                    font-weight:800;
                                    color:#0f172a;
                                "
                            >
                                Customer Information
                            </h2>
                        </div>

                        <div style="display:grid;gap:15px;">
                            <div>
                                <span
                                    style="
                                        display:block;
                                        color:#94a3b8;
                                        font-size:10px;
                                        font-weight:800;
                                        margin-bottom:3px;
                                    "
                                >
                                    FULL NAME
                                </span>

                                <strong style="color:#334155;font-size:13px;">
                                    {{ $customer?->name ?? 'N/A' }}
                                </strong>
                            </div>

                            <div>
                                <span
                                    style="
                                        display:block;
                                        color:#94a3b8;
                                        font-size:10px;
                                        font-weight:800;
                                        margin-bottom:3px;
                                    "
                                >
                                    EMAIL ADDRESS
                                </span>

                                <strong
                                    style="
                                        color:#334155;
                                        font-size:13px;
                                        overflow-wrap:anywhere;
                                    "
                                >
                                    {{ $customer?->email ?? 'N/A' }}
                                </strong>
                            </div>

                            <div>
                                <span
                                    style="
                                        display:block;
                                        color:#94a3b8;
                                        font-size:10px;
                                        font-weight:800;
                                        margin-bottom:3px;
                                    "
                                >
                                    PHONE NUMBER
                                </span>

                                <strong style="color:#334155;font-size:13px;">
                                    {{ $payment->phone_number ?: 'N/A' }}
                                </strong>
                            </div>
                        </div>
                    </section>

                    <section
                        style="
                            background:#f8fafc;
                            border:1px solid #e5eaf0;
                            border-radius:16px;
                            padding:22px;
                        "
                    >
                        <div
                            style="
                                display:flex;
                                align-items:center;
                                gap:11px;
                                margin-bottom:20px;
                            "
                        >
                            <span
                                style="
                                    width:38px;
                                    height:38px;
                                    border-radius:11px;
                                    background:#dcfce7;
                                    color:#16a34a;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                "
                            >
                                <i
                                    class="fas fa-money-check-dollar"
                                    aria-hidden="true"
                                ></i>
                            </span>

                            <h2
                                style="
                                    margin:0;
                                    font-size:16px;
                                    font-weight:800;
                                    color:#0f172a;
                                "
                            >
                                Payment Details
                            </h2>
                        </div>

                        <div style="display:grid;gap:15px;">
                            <div>
                                <span
                                    style="
                                        display:block;
                                        color:#94a3b8;
                                        font-size:10px;
                                        font-weight:800;
                                        margin-bottom:5px;
                                    "
                                >
                                    PAYMENT STATUS
                                </span>

                                <span
                                    style="
                                        display:inline-flex;
                                        align-items:center;
                                        gap:6px;
                                        background:#dcfce7;
                                        color:#166534;
                                        padding:6px 10px;
                                        border-radius:999px;
                                        font-size:11px;
                                        font-weight:800;
                                    "
                                >
                                    <i
                                        class="fas fa-circle-check"
                                        aria-hidden="true"
                                    ></i>
                                    Completed
                                </span>
                            </div>

                            <div>
                                <span
                                    style="
                                        display:block;
                                        color:#94a3b8;
                                        font-size:10px;
                                        font-weight:800;
                                        margin-bottom:3px;
                                    "
                                >
                                    M-PESA RECEIPT
                                </span>

                                <strong style="color:#334155;font-size:13px;">
                                    {{ $payment->mpesa_receipt ?: 'Not available' }}
                                </strong>
                            </div>

                            <div>
                                <span
                                    style="
                                        display:block;
                                        color:#94a3b8;
                                        font-size:10px;
                                        font-weight:800;
                                        margin-bottom:3px;
                                    "
                                >
                                    TRANSACTION CREATED
                                </span>

                                <strong style="color:#334155;font-size:13px;">
                                    {{ $payment->created_at?->format('d M Y, H:i') ?? 'N/A' }}
                                </strong>
                            </div>

                            @if($payment->paid_at)
                                <div>
                                    <span
                                        style="
                                            display:block;
                                            color:#94a3b8;
                                            font-size:10px;
                                            font-weight:800;
                                            margin-bottom:3px;
                                        "
                                    >
                                        PAID ON
                                    </span>

                                    <strong style="color:#334155;font-size:13px;">
                                        {{ $payment->paid_at->format('d M Y, H:i') }}
                                    </strong>
                                </div>
                            @endif
                        </div>
                    </section>
                </div>

                {{-- Package --}}
                <section
                    style="
                        border:1px solid #e5eaf0;
                        border-radius:16px;
                        overflow:hidden;
                    "
                >
                    <div
                        style="
                            padding:16px 20px;
                            background:#f8fafc;
                            border-bottom:1px solid #e5eaf0;
                            display:flex;
                            align-items:center;
                            gap:10px;
                        "
                    >
                        <i
                            class="fas fa-wifi"
                            style="color:#2563eb;"
                            aria-hidden="true"
                        ></i>

                        <strong style="color:#0f172a;font-size:15px;">
                            Internet Package
                        </strong>
                    </div>

                    <div style="overflow-x:auto;">
                        <table
                            style="
                                width:100%;
                                border-collapse:collapse;
                                min-width:650px;
                            "
                        >
                            <thead>
                                <tr
                                    style="
                                        color:#64748b;
                                        background:#ffffff;
                                        font-size:10px;
                                        text-transform:uppercase;
                                    "
                                >
                                    <th
                                        style="
                                            padding:14px 18px;
                                            text-align:left;
                                            border-bottom:1px solid #edf1f5;
                                        "
                                    >
                                        Package
                                    </th>

                                    <th
                                        style="
                                            padding:14px 18px;
                                            text-align:center;
                                            border-bottom:1px solid #edf1f5;
                                        "
                                    >
                                        Duration
                                    </th>

                                    <th
                                        style="
                                            padding:14px 18px;
                                            text-align:center;
                                            border-bottom:1px solid #edf1f5;
                                        "
                                    >
                                        Speed
                                    </th>

                                    <th
                                        style="
                                            padding:14px 18px;
                                            text-align:right;
                                            border-bottom:1px solid #edf1f5;
                                        "
                                    >
                                        Amount
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td
                                        style="
                                            padding:18px;
                                            color:#0f172a;
                                            font-size:13px;
                                        "
                                    >
                                        <strong>
                                            {{ $package?->name ?? 'Package unavailable' }}
                                        </strong>
                                    </td>

                                    <td
                                        style="
                                            padding:18px;
                                            text-align:center;
                                            color:#334155;
                                            font-size:13px;
                                            font-weight:700;
                                        "
                                    >
                                        {{ $package?->formatted_duration ?? 'N/A' }}
                                    </td>

                                    <td
                                        style="
                                            padding:18px;
                                            text-align:center;
                                            color:#334155;
                                            font-size:13px;
                                            font-weight:700;
                                        "
                                    >
                                        {{ $package?->formatted_speed ?? 'N/A' }}
                                    </td>

                                    <td
                                        style="
                                            padding:18px;
                                            text-align:right;
                                            color:#15803d;
                                            font-size:15px;
                                            font-weight:900;
                                        "
                                    >
                                        KES {{ number_format((float) $payment->amount, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Totals --}}
                <div
                    style="
                        margin-top:25px;
                        display:flex;
                        justify-content:flex-end;
                    "
                >
                    <div
                        style="
                            width:350px;
                            max-width:100%;
                            background:#f8fafc;
                            border:1px solid #e5eaf0;
                            border-radius:16px;
                            padding:21px;
                        "
                    >
                        <div
                            style="
                                display:flex;
                                justify-content:space-between;
                                gap:15px;
                                margin-bottom:12px;
                                color:#64748b;
                                font-size:13px;
                            "
                        >
                            <span>Subtotal</span>

                            <strong style="color:#334155;">
                                KES {{ number_format((float) $payment->amount, 2) }}
                            </strong>
                        </div>

                        <div
                            style="
                                display:flex;
                                justify-content:space-between;
                                gap:15px;
                                color:#64748b;
                                font-size:13px;
                            "
                        >
                            <span>Tax</span>
                            <strong style="color:#334155;">KES 0.00</strong>
                        </div>

                        <div
                            style="
                                height:1px;
                                background:#e2e8f0;
                                margin:17px 0;
                            "
                        ></div>

                        <div
                            style="
                                display:flex;
                                justify-content:space-between;
                                align-items:center;
                                gap:15px;
                            "
                        >
                            <strong style="color:#0f172a;font-size:17px;">
                                Total
                            </strong>

                            <strong style="color:#15803d;font-size:21px;">
                                KES {{ number_format((float) $payment->amount, 2) }}
                            </strong>
                        </div>
                    </div>
                </div>

                {{-- ISP Payment Information --}}
                @if($admin)
                    <section
                        style="
                            margin-top:28px;
                            background:#f8fafc;
                            border:1px solid #e5eaf0;
                            border-radius:16px;
                            padding:22px;
                        "
                    >
                        <div
                            style="
                                display:flex;
                                align-items:center;
                                gap:11px;
                                margin-bottom:18px;
                            "
                        >
                            <span
                                style="
                                    width:38px;
                                    height:38px;
                                    border-radius:11px;
                                    background:#dbeafe;
                                    color:#2563eb;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                "
                            >
                                <i
                                    class="fas fa-building-columns"
                                    aria-hidden="true"
                                ></i>
                            </span>

                            <div>
                                <h2
                                    style="
                                        margin:0;
                                        color:#0f172a;
                                        font-size:16px;
                                        font-weight:800;
                                    "
                                >
                                    Business Payment Information
                                </h2>

                                <p
                                    style="
                                        margin:3px 0 0;
                                        color:#64748b;
                                        font-size:11px;
                                    "
                                >
                                    Payment destination configured by your ISP.
                                </p>
                            </div>
                        </div>

                        @if(
                            $admin->payment_type === 'till'
                            && $admin->business_till
                        )
                            <div
                                style="
                                    display:flex;
                                    justify-content:space-between;
                                    align-items:center;
                                    gap:15px;
                                    padding:17px;
                                    background:#ffffff;
                                    border:1px solid #e5e7eb;
                                    border-radius:13px;
                                "
                            >
                                <div>
                                    <span
                                        style="
                                            display:block;
                                            color:#64748b;
                                            font-size:10px;
                                            font-weight:800;
                                            margin-bottom:5px;
                                        "
                                    >
                                        TILL NUMBER
                                    </span>

                                    <strong
                                        style="
                                            color:#2563eb;
                                            font-size:20px;
                                        "
                                    >
                                        {{ $admin->business_till }}
                                    </strong>
                                </div>

                                <i
                                    class="fas fa-store"
                                    style="color:#2563eb;font-size:20px;"
                                    aria-hidden="true"
                                ></i>
                            </div>

                        @elseif(
                            $admin->payment_type === 'paybill'
                            && $admin->business_paybill
                        )
                            <div
                                style="
                                    display:grid;
                                    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
                                    gap:12px;
                                "
                            >
                                <div
                                    style="
                                        padding:17px;
                                        background:#ffffff;
                                        border:1px solid #e5e7eb;
                                        border-radius:13px;
                                    "
                                >
                                    <span
                                        style="
                                            display:block;
                                            color:#64748b;
                                            font-size:10px;
                                            font-weight:800;
                                            margin-bottom:5px;
                                        "
                                    >
                                        PAYBILL NUMBER
                                    </span>

                                    <strong
                                        style="
                                            color:#2563eb;
                                            font-size:20px;
                                        "
                                    >
                                        {{ $admin->business_paybill }}
                                    </strong>
                                </div>

                                @if($admin->business_account)
                                    <div
                                        style="
                                            padding:17px;
                                            background:#ffffff;
                                            border:1px solid #e5e7eb;
                                            border-radius:13px;
                                        "
                                    >
                                        <span
                                            style="
                                                display:block;
                                                color:#64748b;
                                                font-size:10px;
                                                font-weight:800;
                                                margin-bottom:5px;
                                            "
                                        >
                                            ACCOUNT NUMBER
                                        </span>

                                        <strong
                                            style="
                                                color:#059669;
                                                font-size:18px;
                                            "
                                        >
                                            {{ $admin->business_account }}
                                        </strong>
                                    </div>
                                @endif
                            </div>

                        @elseif($admin->business_phone)
                            <div
                                style="
                                    display:flex;
                                    justify-content:space-between;
                                    align-items:center;
                                    gap:15px;
                                    padding:17px;
                                    background:#ffffff;
                                    border:1px solid #e5e7eb;
                                    border-radius:13px;
                                "
                            >
                                <div>
                                    <span
                                        style="
                                            display:block;
                                            color:#64748b;
                                            font-size:10px;
                                            font-weight:800;
                                            margin-bottom:5px;
                                        "
                                    >
                                        PAYMENT PHONE NUMBER
                                    </span>

                                    <strong
                                        style="
                                            color:#2563eb;
                                            font-size:18px;
                                        "
                                    >
                                        {{ $admin->business_phone }}
                                    </strong>
                                </div>

                                <i
                                    class="fas fa-phone"
                                    style="color:#2563eb;font-size:20px;"
                                    aria-hidden="true"
                                ></i>
                            </div>

                        @else
                            <p
                                style="
                                    margin:0;
                                    color:#64748b;
                                    font-size:12px;
                                "
                            >
                                No business payment information is currently
                                configured.
                            </p>
                        @endif
                    </section>
                @endif

                {{-- Footer --}}
                <div
                    style="
                        margin-top:30px;
                        padding-top:25px;
                        border-top:1px solid #e5eaf0;
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        flex-wrap:wrap;
                        gap:20px;
                    "
                >
                    <div style="max-width:530px;">
                        <h3
                            style="
                                margin:0;
                                color:#0f172a;
                                font-size:16px;
                                font-weight:800;
                            "
                        >
                            Thank you for choosing
                            {{ $admin?->business_name ?? 'our internet service' }}
                        </h3>

                        <p
                            style="
                                margin:7px 0 0;
                                color:#64748b;
                                font-size:11px;
                                line-height:1.6;
                            "
                        >
                            This receipt confirms a completed payment for the
                            selected internet package.
                        </p>
                    </div>

                    <div
                        class="receipt-actions"
                        style="
                            display:flex;
                            gap:10px;
                            flex-wrap:wrap;
                        "
                    >
                        <button
                            type="button"
                            onclick="window.print()"
                            style="
                                background:#16a34a;
                                color:#ffffff;
                                border:none;
                                padding:11px 17px;
                                border-radius:10px;
                                font-size:12px;
                                font-weight:800;
                                cursor:pointer;
                            "
                        >
                            <i class="fas fa-print" aria-hidden="true"></i>
                            Print / Save as PDF
                        </button>

                        <a
                            href="{{ route('receipts.index') }}"
                            style="
                                background:#475569;
                                color:#ffffff;
                                padding:11px 17px;
                                border-radius:10px;
                                font-size:12px;
                                font-weight:800;
                                text-decoration:none;
                            "
                        >
                            <i
                                class="fas fa-arrow-left"
                                aria-hidden="true"
                            ></i>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <p
            style="
                margin:20px auto 0;
                text-align:center;
                color:#94a3b8;
                font-size:10px;
            "
        >
            This is a computer-generated receipt and does not require a signature.
        </p>
    </div>

    <style>
        @media print {
            nav,
            header,
            .receipt-actions {
                display: none !important;
            }

            body,
            .min-h-screen {
                background: #ffffff !important;
            }

            @page {
                margin: 12mm;
            }
        }

        @media (max-width: 640px) {
            .receipt-actions {
                width: 100%;
            }

            .receipt-actions button,
            .receipt-actions a {
                flex: 1;
                text-align: center;
            }
        }
    </style>

</x-app-layout>