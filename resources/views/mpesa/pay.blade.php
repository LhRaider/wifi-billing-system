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
                <i class="fas fa-credit-card" aria-hidden="true"></i>
            </span>

            <div>
                <h2 style="margin:0;font-size:25px;font-weight:800;color:#0f172a;">
                    Complete Payment
                </h2>

                <p style="margin:4px 0 0;color:#64748b;font-size:13px;">
                    Review your package and ISP payment instructions.
                </p>
            </div>
        </div>
    </x-slot>

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
                display:grid;
                grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
                gap:24px;
            "
        >

            {{-- Package Details --}}
            <section
                style="
                    background:#ffffff;
                    border:1px solid #e5eaf0;
                    border-radius:20px;
                    padding:28px;
                    box-shadow:0 8px 25px rgba(15,23,42,0.055);
                "
            >
                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:12px;
                        margin-bottom:25px;
                    "
                >
                    <span
                        style="
                            width:43px;
                            height:43px;
                            border-radius:13px;
                            background:#eff6ff;
                            color:#2563eb;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        "
                    >
                        <i class="fas fa-wifi" aria-hidden="true"></i>
                    </span>

                    <div>
                        <h2
                            style="
                                margin:0;
                                font-size:19px;
                                font-weight:800;
                                color:#0f172a;
                            "
                        >
                            Package Details
                        </h2>

                        <p
                            style="
                                margin:3px 0 0;
                                color:#64748b;
                                font-size:12px;
                            "
                        >
                            Internet package selected for purchase.
                        </p>
                    </div>
                </div>

                <div
                    style="
                        padding:20px;
                        background:#f8fafc;
                        border:1px solid #e8edf3;
                        border-radius:15px;
                    "
                >
                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:flex-start;
                            gap:15px;
                        "
                    >
                        <div>
                            <span
                                style="
                                    display:block;
                                    color:#64748b;
                                    font-size:11px;
                                    font-weight:700;
                                    margin-bottom:5px;
                                "
                            >
                                PACKAGE
                            </span>

                            <strong
                                style="
                                    color:#0f172a;
                                    font-size:19px;
                                "
                            >
                                {{ $package->name }}
                            </strong>
                        </div>

                        <span
                            style="
                                background:#dcfce7;
                                color:#166534;
                                padding:6px 10px;
                                border-radius:999px;
                                font-size:10px;
                                font-weight:800;
                            "
                        >
                            <i class="fas fa-circle-check" aria-hidden="true"></i>
                            Selected
                        </span>
                    </div>
                </div>

                <div style="margin-top:22px;display:grid;gap:17px;">
                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            gap:15px;
                            padding-bottom:15px;
                            border-bottom:1px solid #edf1f5;
                        "
                    >
                        <span style="color:#64748b;font-size:13px;">
                            <i
                                class="fas fa-money-bill-wave"
                                style="width:20px;color:#16a34a;"
                                aria-hidden="true"
                            ></i>
                            Price
                        </span>

                        <strong style="color:#15803d;font-size:17px;">
                            KES {{ number_format((float) $package->price, 2) }}
                        </strong>
                    </div>

                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            gap:15px;
                            padding-bottom:15px;
                            border-bottom:1px solid #edf1f5;
                        "
                    >
                        <span style="color:#64748b;font-size:13px;">
                            <i
                                class="fas fa-clock"
                                style="width:20px;color:#7c3aed;"
                                aria-hidden="true"
                            ></i>
                            Duration
                        </span>

                        <strong style="color:#0f172a;font-size:13px;">
                            {{ $package->formatted_duration }}
                        </strong>
                    </div>

                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            gap:15px;
                            padding-bottom:15px;
                            border-bottom:1px solid #edf1f5;
                        "
                    >
                        <span style="color:#64748b;font-size:13px;">
                            <i
                                class="{{ $package->is_speed_unlimited ? 'fas fa-infinity' : 'fas fa-gauge-high' }}"
                                style="width:20px;color:{{ $package->is_speed_unlimited ? '#15803d' : '#2563eb' }};"
                                aria-hidden="true"
                            ></i>
                            Speed
                        </span>

                        @if($package->is_speed_unlimited)
                            <strong
                                style="
                                    color:#15803d;
                                    font-size:13px;
                                    display:inline-flex;
                                    align-items:center;
                                    gap:6px;
                                "
                            >
                                <i
                                    class="fas fa-infinity"
                                    aria-hidden="true"
                                ></i>
                                Unlimited
                            </strong>
                        @else
                            <strong style="color:#0f172a;font-size:13px;">
                                {{ $package->formatted_speed }}
                            </strong>
                        @endif
                    </div>

                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            gap:15px;
                            padding-bottom:15px;
                            border-bottom:1px solid #edf1f5;
                        "
                    >
                        <span style="color:#64748b;font-size:13px;">
                            <i
                                class="{{ $package->package_type === 'pppoe' ? 'fas fa-network-wired' : 'fas fa-wifi' }}"
                                style="width:20px;color:#0f766e;"
                                aria-hidden="true"
                            ></i>
                            Access Type
                        </span>

                        <strong style="color:#0f172a;font-size:13px;">
                            {{ $package->package_type === 'pppoe' ? 'PPPoE Access' : 'Hotspot Access' }}
                        </strong>
                    </div>

                    @if($package->description)
                        <div>
                            <span
                                style="
                                    display:block;
                                    color:#64748b;
                                    font-size:11px;
                                    font-weight:700;
                                    margin-bottom:7px;
                                "
                            >
                                DESCRIPTION
                            </span>

                            <p
                                style="
                                    margin:0;
                                    color:#475569;
                                    font-size:13px;
                                    line-height:1.65;
                                "
                            >
                                {{ $package->description }}
                            </p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Payment Information --}}
            <section
                style="
                    background:#ffffff;
                    border:1px solid #e5eaf0;
                    border-radius:20px;
                    padding:28px;
                    box-shadow:0 8px 25px rgba(15,23,42,0.055);
                "
            >
                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:12px;
                        margin-bottom:25px;
                    "
                >
                    <span
                        style="
                            width:43px;
                            height:43px;
                            border-radius:13px;
                            background:#ecfdf5;
                            color:#059669;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        "
                    >
                        <i class="fas fa-building" aria-hidden="true"></i>
                    </span>

                    <div>
                        <h2
                            style="
                                margin:0;
                                font-size:19px;
                                font-weight:800;
                                color:#0f172a;
                            "
                        >
                            Payment Information
                        </h2>

                        <p
                            style="
                                margin:3px 0 0;
                                color:#64748b;
                                font-size:12px;
                            "
                        >
                            Pay directly using your ISP's configured details.
                        </p>
                    </div>
                </div>

                <div
                    style="
                        background:#f8fafc;
                        border:1px solid #e8edf3;
                        border-radius:15px;
                        padding:18px;
                        margin-bottom:20px;
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
                        INTERNET SERVICE PROVIDER
                    </span>

                    <strong style="color:#0f172a;font-size:16px;">
                        {{ $admin->business_name ?? $admin->name ?? 'Not Set' }}
                    </strong>

                    @if($admin->network_name)
                        <div
                            style="
                                margin-top:7px;
                                color:#64748b;
                                font-size:12px;
                            "
                        >
                            <i
                                class="fas fa-tower-broadcast"
                                aria-hidden="true"
                            ></i>
                            {{ $admin->network_name }}
                        </div>
                    @endif
                </div>

                @if($admin->payment_type === 'till')
                    @if($admin->business_till)
                        <div
                            style="
                                padding:18px;
                                border:1px solid #bfdbfe;
                                background:#eff6ff;
                                border-radius:14px;
                            "
                        >
                            <span
                                style="
                                    display:block;
                                    color:#1e40af;
                                    font-size:11px;
                                    font-weight:800;
                                    margin-bottom:7px;
                                "
                            >
                                M-PESA TILL NUMBER
                            </span>

                            <strong
                                style="
                                    font-size:24px;
                                    color:#1d4ed8;
                                    letter-spacing:.02em;
                                "
                            >
                                {{ $admin->business_till }}
                            </strong>
                        </div>
                    @else
                        <div
                            style="
                                padding:15px;
                                background:#fffbeb;
                                border:1px solid #fde68a;
                                color:#92400e;
                                border-radius:12px;
                                font-size:13px;
                            "
                        >
                            <i
                                class="fas fa-triangle-exclamation"
                                aria-hidden="true"
                            ></i>
                            Till payment details have not been configured.
                        </div>
                    @endif

                @elseif($admin->payment_type === 'paybill')
                    @if($admin->business_paybill)
                        <div
                            style="
                                padding:18px;
                                border:1px solid #bfdbfe;
                                background:#eff6ff;
                                border-radius:14px;
                            "
                        >
                            <span
                                style="
                                    display:block;
                                    color:#1e40af;
                                    font-size:11px;
                                    font-weight:800;
                                    margin-bottom:7px;
                                "
                            >
                                M-PESA PAYBILL NUMBER
                            </span>

                            <strong
                                style="
                                    font-size:24px;
                                    color:#1d4ed8;
                                "
                            >
                                {{ $admin->business_paybill }}
                            </strong>
                        </div>
                    @endif

                    @if($admin->business_account)
                        <div
                            style="
                                margin-top:12px;
                                padding:18px;
                                border:1px solid #bbf7d0;
                                background:#f0fdf4;
                                border-radius:14px;
                            "
                        >
                            <span
                                style="
                                    display:block;
                                    color:#166534;
                                    font-size:11px;
                                    font-weight:800;
                                    margin-bottom:7px;
                                "
                            >
                                ACCOUNT NUMBER
                            </span>

                            <strong
                                style="
                                    font-size:20px;
                                    color:#15803d;
                                "
                            >
                                {{ $admin->business_account }}
                            </strong>
                        </div>
                    @endif

                    @if(
                        !$admin->business_paybill
                        && !$admin->business_account
                    )
                        <div
                            style="
                                padding:15px;
                                background:#fffbeb;
                                border:1px solid #fde68a;
                                color:#92400e;
                                border-radius:12px;
                                font-size:13px;
                            "
                        >
                            <i
                                class="fas fa-triangle-exclamation"
                                aria-hidden="true"
                            ></i>
                            PayBill details have not been configured.
                        </div>
                    @endif

                @elseif($admin->payment_type === 'phone')
                    @if($admin->business_phone)
                        <div
                            style="
                                padding:18px;
                                border:1px solid #bfdbfe;
                                background:#eff6ff;
                                border-radius:14px;
                            "
                        >
                            <span
                                style="
                                    display:block;
                                    color:#1e40af;
                                    font-size:11px;
                                    font-weight:800;
                                    margin-bottom:7px;
                                "
                            >
                                M-PESA PHONE NUMBER
                            </span>

                            <strong
                                style="
                                    font-size:22px;
                                    color:#1d4ed8;
                                "
                            >
                                {{ $admin->business_phone }}
                            </strong>
                        </div>
                    @else
                        <div
                            style="
                                padding:15px;
                                background:#fffbeb;
                                border:1px solid #fde68a;
                                color:#92400e;
                                border-radius:12px;
                                font-size:13px;
                            "
                        >
                            <i
                                class="fas fa-triangle-exclamation"
                                aria-hidden="true"
                            ></i>
                            Payment phone number has not been configured.
                        </div>
                    @endif

                @else
                    <div
                        style="
                            padding:15px;
                            background:#fffbeb;
                            border:1px solid #fde68a;
                            color:#92400e;
                            border-radius:12px;
                            font-size:13px;
                        "
                    >
                        <i
                            class="fas fa-triangle-exclamation"
                            aria-hidden="true"
                        ></i>
                        Payment instructions have not been configured.
                    </div>
                @endif

                @if($admin->business_phone)
                    <div
                        style="
                            margin-top:18px;
                            display:flex;
                            align-items:center;
                            gap:11px;
                            padding:14px;
                            border:1px solid #e5e7eb;
                            border-radius:12px;
                        "
                    >
                        <span
                            style="
                                width:36px;
                                height:36px;
                                border-radius:10px;
                                background:#f1f5f9;
                                color:#475569;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                flex-shrink:0;
                            "
                        >
                            <i class="fas fa-phone" aria-hidden="true"></i>
                        </span>

                        <div>
                            <span
                                style="
                                    display:block;
                                    color:#64748b;
                                    font-size:10px;
                                    font-weight:700;
                                "
                            >
                                SUPPORT PHONE
                            </span>

                            <strong style="color:#0f172a;font-size:13px;">
                                {{ $admin->business_phone }}
                            </strong>
                        </div>
                    </div>
                @endif

                <div
                    style="
                        margin-top:22px;
                        background:#fff7ed;
                        border:1px solid #fed7aa;
                        border-radius:14px;
                        padding:17px;
                        display:flex;
                        gap:11px;
                        align-items:flex-start;
                    "
                >
                    <i
                        class="fas fa-circle-info"
                        style="color:#c2410c;margin-top:3px;"
                        aria-hidden="true"
                    ></i>

                    <div>
                        <strong
                            style="
                                display:block;
                                color:#9a3412;
                                font-size:13px;
                                margin-bottom:5px;
                            "
                        >
                            Automatic M-Pesa Payment Temporarily Unavailable
                        </strong>

                        <p
                            style="
                                margin:0;
                                color:#7c2d12;
                                font-size:12px;
                                line-height:1.6;
                            "
                        >
                            Please pay using the ISP payment details shown above.
                            Automatic package activation through M-Pesa is not
                            currently enabled.
                        </p>
                    </div>
                </div>

                <div
                    style="
                        margin-top:14px;
                        background:#f8fafc;
                        border:1px solid #e5eaf0;
                        border-radius:14px;
                        padding:17px;
                        display:flex;
                        gap:11px;
                        align-items:flex-start;
                    "
                >
                    <i
                        class="fas fa-headset"
                        style="color:#2563eb;margin-top:3px;"
                        aria-hidden="true"
                    ></i>

                    <div>
                        <strong
                            style="
                                display:block;
                                color:#0f172a;
                                font-size:13px;
                                margin-bottom:5px;
                            "
                        >
                            After Payment
                        </strong>

                        <p
                            style="
                                margin:0;
                                color:#475569;
                                font-size:12px;
                                line-height:1.6;
                            "
                        >
                            Contact your ISP using the support number shown above
                            so your payment can be confirmed and your internet
                            package activated.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>