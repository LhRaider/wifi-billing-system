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
                <i class="fas fa-bag-shopping" aria-hidden="true"></i>
            </span>

            <div>
                <h2 style="margin:0;font-size:25px;font-weight:800;color:#0f172a;">
                    Internet Packages
                </h2>

                <p style="margin:4px 0 0;color:#64748b;font-size:13px;">
                    Choose an active internet package from your service provider.
                </p>
            </div>
        </div>
    </x-slot>

    <style>
        .packages-page {
            min-height: 100vh;
            padding: 38px 20px 55px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.7),
                    transparent 30%
                ),
                #f4f7fb;
        }

        .packages-container {
            width: 100%;
            max-width: 1220px;
            margin: 0 auto;
        }

        .packages-alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 13px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        .packages-alert.success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .packages-alert.warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .packages-alert.error {
            background: #fff7f7;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .packages-intro {
            max-width: 650px;
            margin: 0 auto 32px;
            text-align: center;
        }

        .packages-intro-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 14px;
            border-radius: 18px;
            background: #ffffff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 7px 20px rgba(15, 23, 42, 0.06);
        }

        .packages-intro h1 {
            margin: 0;
            color: #0f172a;
            font-size: 31px;
            line-height: 1.2;
            font-weight: 900;
        }

        .packages-intro p {
            margin: 9px 0 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(285px, 1fr));
            gap: 20px;
            align-items: stretch;
        }

        .package-card {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 24px;
            border: 1px solid #e4eaf1;
            border-radius: 21px;
            background: #ffffff;
            box-shadow: 0 9px 26px rgba(15, 23, 42, 0.055);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease;
        }

        .package-card:hover {
            transform: translateY(-3px);
            border-color: #bfdbfe;
            box-shadow: 0 15px 34px rgba(15, 23, 42, 0.09);
        }

        .package-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 19px;
        }

        .package-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
            font-size:20px;
        }

        .available-badge {
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:6px 10px;
            border-radius:999px;
            background:#dcfce7;
            color:#166534;
            font-size:10px;
            font-weight:800;
            white-space:nowrap;
        }

        .package-card h3 {
            margin:0;
            color:#0f172a;
            font-size:20px;
            line-height:1.3;
            font-weight:900;
        }

        .package-description {
            min-height:42px;
            margin:7px 0 0;
            color:#64748b;
            font-size:12px;
            line-height:1.6;
        }

        .package-price {
            margin-top:20px;
            color:#15803d;
            font-size:31px;
            line-height:1;
            font-weight:900;
        }

        .package-price-label {
            margin-top:5px;
            color:#94a3b8;
            font-size:10px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:0.05em;
        }

        .package-details {
            margin-top:22px;
            display:grid;
            gap:10px;
        }

        .package-detail {
            min-height:47px;
            padding:11px 12px;
            border:1px solid #edf1f5;
            border-radius:12px;
            background:#f8fafc;
            display:flex;
            align-items:center;
            gap:10px;
        }

        .detail-icon {
            width:31px;
            height:31px;
            border-radius:9px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
            font-size:12px;
        }

        .detail-icon.duration {
            background:#f3e8ff;
            color:#7e22ce;
        }

        .detail-icon.speed {
            background:#dbeafe;
            color:#2563eb;
        }

        .detail-icon.speed-unlimited {
            background:#dcfce7;
            color:#15803d;
        }

        .detail-icon.access {
            background:#ccfbf1;
            color:#0f766e;
        }

        .detail-content {
            min-width:0;
        }

        .detail-content span {
            display:block;
            color:#94a3b8;
            font-size:9px;
            font-weight:800;
            letter-spacing:0.04em;
            text-transform:uppercase;
        }

        .detail-content strong {
            display:block;
            margin-top:2px;
            color:#334155;
            font-size:11px;
            line-height:1.4;
            font-weight:800;
        }

        .speed-values {
            display:flex;
            flex-wrap:wrap;
            gap:4px 10px;
        }

        .speed-values span {
            color:#334155;
            font-size:11px;
            letter-spacing:0;
            text-transform:none;
        }

        .unlimited-speed-value {
            display:inline-flex !important;
            align-items:center;
            gap:6px;
            color:#15803d !important;
            font-size:11px !important;
            font-weight:900 !important;
            letter-spacing:0 !important;
            text-transform:none !important;
        }

        .package-benefits {
            margin-top:18px;
            padding-top:17px;
            border-top:1px solid #edf1f5;
            display:grid;
            gap:8px;
        }

        .package-benefit {
            display:flex;
            align-items:center;
            gap:8px;
            color:#475569;
            font-size:10px;
            line-height:1.4;
        }

        .package-benefit i {
            color:#16a34a;
        }

        .package-action {
            margin-top:auto;
            padding-top:21px;
        }

        .payment-button {
            width:100%;
            min-height:45px;
            padding:11px 15px;
            border-radius:12px;
            background:#2563eb;
            color:#ffffff;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            text-align:center;
            text-decoration:none;
            font-size:12px;
            font-weight:900;
            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }

        .payment-button:hover {
            background:#1d4ed8;
            transform:translateY(-1px);
        }

        .packages-empty {
            padding:55px 25px;
            border:1px solid #e4eaf1;
            border-radius:20px;
            background:#ffffff;
            text-align:center;
            box-shadow:0 9px 26px rgba(15,23,42,0.055);
        }

        .packages-empty-icon {
            width:62px;
            height:62px;
            margin:0 auto 15px;
            border-radius:19px;
            background:#f1f5f9;
            color:#64748b;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:23px;
        }

        .packages-empty h3 {
            margin:0;
            color:#334155;
            font-size:18px;
            font-weight:800;
        }

        .packages-empty p {
            margin:7px 0 0;
            color:#64748b;
            font-size:12px;
            line-height:1.6;
        }

        .payment-notice {
            margin-top:25px;
            padding:15px 17px;
            border:1px solid #bfdbfe;
            border-radius:14px;
            background:#eff6ff;
            color:#1e3a8a;
            display:flex;
            align-items:flex-start;
            gap:11px;
            font-size:11px;
            line-height:1.65;
        }

        .payment-notice i {
            margin-top:3px;
            color:#2563eb;
        }

        @media (max-width:700px) {
            .packages-page {
                padding:28px 14px 45px;
            }

            .packages-intro {
                margin-bottom:25px;
            }

            .packages-intro h1 {
                font-size:25px;
            }

            .packages-intro p {
                font-size:12px;
            }

            .packages-grid {
                grid-template-columns:1fr;
            }
        }

        @media (max-width:400px) {
            .packages-page {
                padding-left:10px;
                padding-right:10px;
            }

            .package-card {
                padding:20px;
            }

            .package-price {
                font-size:27px;
            }
        }
    </style>

    <div class="packages-page">
        <div class="packages-container">

            @if(session('success'))
                <div class="packages-alert success" role="status">
                    <i class="fas fa-circle-check" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="packages-alert warning" role="status">
                    <i
                        class="fas fa-triangle-exclamation"
                        aria-hidden="true"
                    ></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="packages-alert error" role="alert">
                    <i
                        class="fas fa-circle-exclamation"
                        aria-hidden="true"
                    ></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="packages-intro">
                <div class="packages-intro-icon">
                    <i class="fas fa-wifi" aria-hidden="true"></i>
                </div>

                <h1>Choose Your Internet Package</h1>

                <p>
                    Select the package that best fits your internet needs.
                    Only packages currently offered by your ISP are shown here.
                </p>
            </div>

            @if($packages->isNotEmpty())
                <div class="packages-grid">
                    @foreach($packages as $package)
                        <article class="package-card">
                            <div class="package-card-top">
                                <div class="package-icon">
                                    @if($package->package_type === 'pppoe')
                                        <i
                                            class="fas fa-network-wired"
                                            aria-hidden="true"
                                        ></i>
                                    @else
                                        <i
                                            class="fas fa-wifi"
                                            aria-hidden="true"
                                        ></i>
                                    @endif
                                </div>

                                <span class="available-badge">
                                    <i
                                        class="fas fa-circle-check"
                                        aria-hidden="true"
                                    ></i>
                                    Available
                                </span>
                            </div>

                            <h3>{{ $package->name }}</h3>

                            <p class="package-description">
                                {{ $package->description ?: 'Internet access provided by your ISP.' }}
                            </p>

                            <div class="package-price">
                                KES {{ number_format((float) $package->price, 2) }}
                            </div>

                            <div class="package-price-label">
                                Package Price
                            </div>

                            <div class="package-details">
                                <div class="package-detail">
                                    <span class="detail-icon duration">
                                        <i
                                            class="fas fa-clock"
                                            aria-hidden="true"
                                        ></i>
                                    </span>

                                    <div class="detail-content">
                                        <span>Duration</span>

                                        <strong>
                                            {{ $package->formatted_duration }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="package-detail">
                                    <span
                                        class="detail-icon {{ $package->is_speed_unlimited ? 'speed-unlimited' : 'speed' }}"
                                    >
                                        @if($package->is_speed_unlimited)
                                            <i
                                                class="fas fa-infinity"
                                                aria-hidden="true"
                                            ></i>
                                        @else
                                            <i
                                                class="fas fa-gauge-high"
                                                aria-hidden="true"
                                            ></i>
                                        @endif
                                    </span>

                                    <div class="detail-content">
                                        <span>Internet Speed</span>

                                        <strong>
                                            @if($package->is_speed_unlimited)
                                                <span class="unlimited-speed-value">
                                                    <i
                                                        class="fas fa-infinity"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Unlimited
                                                </span>
                                            @else
                                                <span class="speed-values">
                                                    <span>
                                                        ↓
                                                        {{ $package->download_speed ?: 'Not Set' }}
                                                    </span>

                                                    <span>
                                                        ↑
                                                        {{ $package->upload_speed ?: 'Not Set' }}
                                                    </span>
                                                </span>
                                            @endif
                                        </strong>
                                    </div>
                                </div>

                                <div class="package-detail">
                                    <span class="detail-icon access">
                                        @if($package->package_type === 'pppoe')
                                            <i
                                                class="fas fa-network-wired"
                                                aria-hidden="true"
                                            ></i>
                                        @else
                                            <i
                                                class="fas fa-wifi"
                                                aria-hidden="true"
                                            ></i>
                                        @endif
                                    </span>

                                    <div class="detail-content">
                                        <span>Access Type</span>

                                        <strong>
                                            {{ $package->package_type === 'pppoe' ? 'PPPoE Access' : 'Hotspot Access' }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <div class="package-benefits">
                                <div class="package-benefit">
                                    <i
                                        class="fas fa-circle-check"
                                        aria-hidden="true"
                                    ></i>
                                    ISP managed internet package
                                </div>

                                <div class="package-benefit">
                                    <i
                                        class="fas fa-circle-check"
                                        aria-hidden="true"
                                    ></i>
                                    Secure customer access
                                </div>

                                <div class="package-benefit">
                                    <i
                                        class="fas fa-circle-check"
                                        aria-hidden="true"
                                    ></i>
                                    Activation after confirmed payment
                                </div>
                            </div>

                            <div class="package-action">
                                <a
                                    href="{{ route('mpesa.pay', $package) }}"
                                    class="payment-button"
                                >
                                    <i
                                        class="fas fa-credit-card"
                                        aria-hidden="true"
                                    ></i>
                                    View Payment Instructions
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="packages-empty">
                    <div class="packages-empty-icon">
                        <i class="fas fa-box-open" aria-hidden="true"></i>
                    </div>

                    <h3>No Packages Available</h3>

                    <p>
                        Your ISP has not published any active internet packages
                        yet. Please check again after your provider adds a package.
                    </p>
                </div>
            @endif

            <div class="payment-notice">
                <i class="fas fa-circle-info" aria-hidden="true"></i>

                <div>
                    Automatic customer-to-ISP M-Pesa processing is currently
                    paused. Selecting a package displays the payment instructions
                    configured by your ISP. Internet access should only be
                    activated after payment has been properly confirmed.
                </div>
            </div>

        </div>
    </div>
</x-app-layout>