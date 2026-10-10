<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        {{ $admin->network_name ?: $admin->business_name ?: 'WiFi Internet' }}
    </title>

    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Figtree, Arial, sans-serif;
            color: #0f172a;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.85),
                    transparent 30%
                ),
                #f4f7fb;
        }

        a {
            text-decoration: none;
        }

        .portal-header {
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }

        .portal-header-inner {
            width: 100%;
            max-width: 1220px;
            margin: 0 auto;
            padding: 17px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .provider {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .provider-icon {
            width: 45px;
            height: 45px;
            flex-shrink: 0;
            border-radius: 14px;
            background: #2563eb;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.2);
        }

        .provider-name {
            overflow: hidden;
            color: #0f172a;
            font-size: 17px;
            font-weight: 900;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .provider-label {
            margin-top: 2px;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
        }

        .header-badge {
            flex-shrink: 0;
            padding: 7px 11px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 800;
        }

        .portal-page {
            min-height: calc(100vh - 80px);
            padding: 45px 20px 55px;
        }

        .portal-container {
            width: 100%;
            max-width: 1220px;
            margin: 0 auto;
        }

        .portal-intro {
            max-width: 690px;
            margin: 0 auto 34px;
            text-align: center;
        }

        .portal-intro-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 15px;
            border: 1px solid #dbeafe;
            border-radius: 20px;
            background: #ffffff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            box-shadow: 0 9px 25px rgba(15, 23, 42, 0.07);
        }

        .portal-intro h1 {
            margin: 0;
            color: #0f172a;
            font-size: 32px;
            line-height: 1.2;
            font-weight: 900;
        }

        .portal-intro p {
            margin: 10px auto 0;
            max-width: 610px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        .account-box {
            max-width: 720px;
            margin: 0 auto 35px;
            padding: 17px 18px;
            border: 1px solid #bfdbfe;
            border-radius: 16px;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .account-message {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            color: #1e3a8a;
            font-size: 12px;
            line-height: 1.55;
        }

        .account-message i {
            margin-top: 2px;
            color: #2563eb;
            font-size: 15px;
        }

        .account-message strong {
            display: block;
            margin-bottom: 2px;
            font-size: 13px;
        }

        .account-actions {
            flex-shrink: 0;
            display: flex;
            gap: 8px;
        }

        .account-button {
            min-height: 38px;
            padding: 9px 14px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 900;
            transition:
                transform 0.2s ease,
                background 0.2s ease;
        }

        .account-button:hover {
            transform: translateY(-1px);
        }

        .account-button.login {
            border: 1px solid #bfdbfe;
            background: #ffffff;
            color: #1d4ed8;
        }

        .account-button.register {
            border: 1px solid #2563eb;
            background: #2563eb;
            color: #ffffff;
        }

        .account-button.register:hover {
            background: #1d4ed8;
        }

        .packages-heading {
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .packages-heading h2 {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            font-weight: 900;
        }

        .packages-heading span {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(285px, 1fr)
            );
            gap: 20px;
            align-items: stretch;
        }

        .package-card {
            position: relative;
            overflow: hidden;
            padding: 24px;
            border: 1px solid #e4eaf1;
            border-radius: 21px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
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
            margin-bottom: 19px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .package-icon {
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            border-radius: 16px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .available-badge {
            padding: 6px 10px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .package-card h3 {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            line-height: 1.3;
            font-weight: 900;
        }

        .package-description {
            min-height: 42px;
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        .package-price {
            margin-top: 20px;
            color: #15803d;
            font-size: 31px;
            line-height: 1;
            font-weight: 900;
        }

        .package-price-label {
            margin-top: 5px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .package-details {
            margin-top: 22px;
            display: grid;
            gap: 10px;
        }

        .package-detail {
            min-height: 47px;
            padding: 11px 12px;
            border: 1px solid #edf1f5;
            border-radius: 12px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-icon {
            width: 31px;
            height: 31px;
            flex-shrink: 0;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .detail-icon.duration {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .detail-icon.speed {
            background: #dbeafe;
            color: #2563eb;
        }

        .detail-icon.speed-unlimited {
            background: #dcfce7;
            color: #15803d;
        }

        .detail-icon.access {
            background: #ccfbf1;
            color: #0f766e;
        }

        .detail-content {
            min-width: 0;
        }

        .detail-content > span {
            display: block;
            color: #94a3b8;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .detail-content strong {
            display: block;
            margin-top: 2px;
            color: #334155;
            font-size: 11px;
            line-height: 1.4;
            font-weight: 800;
        }

        .speed-values {
            display: flex;
            flex-wrap: wrap;
            gap: 4px 10px;
        }

        .speed-values span {
            color: #334155;
            font-size: 11px;
        }

        .unlimited-speed-value {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #15803d;
            font-weight: 900;
        }

        .package-benefits {
            margin-top: 18px;
            padding-top: 17px;
            border-top: 1px solid #edf1f5;
            display: grid;
            gap: 8px;
        }

        .package-benefit {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-size: 10px;
            line-height: 1.4;
        }

        .package-benefit i {
            color: #16a34a;
        }

        .package-action {
            margin-top: auto;
            padding-top: 21px;
        }

        .choose-button {
            width: 100%;
            min-height: 45px;
            padding: 11px 15px;
            border-radius: 12px;
            background: #2563eb;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 900;
            transition:
                transform 0.2s ease,
                background 0.2s ease;
        }

        .choose-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .packages-empty {
            padding: 55px 25px;
            border: 1px solid #e4eaf1;
            border-radius: 20px;
            background: #ffffff;
            text-align: center;
            box-shadow: 0 9px 26px rgba(15, 23, 42, 0.055);
        }

        .packages-empty-icon {
            width: 62px;
            height: 62px;
            margin: 0 auto 15px;
            border-radius: 19px;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .packages-empty h3 {
            margin: 0;
            color: #334155;
            font-size: 18px;
            font-weight: 800;
        }

        .packages-empty p {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        .portal-footer {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            text-align: center;
            font-size: 10px;
            line-height: 1.6;
        }

        @media (max-width: 720px) {
            .portal-header-inner {
                padding: 14px;
            }

            .header-badge {
                display: none;
            }

            .portal-page {
                padding: 32px 14px 45px;
            }

            .portal-intro h1 {
                font-size: 26px;
            }

            .account-box {
                align-items: stretch;
                flex-direction: column;
            }

            .account-actions {
                width: 100%;
            }

            .account-button {
                flex: 1;
            }

            .packages-heading {
                align-items: flex-start;
                flex-direction: column;
                gap: 4px;
            }

            .packages-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 400px) {
            .portal-page {
                padding-left: 10px;
                padding-right: 10px;
            }

            .package-card {
                padding: 20px;
            }

            .account-actions {
                flex-direction: column;
            }

            .package-price {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

<header class="portal-header">
    <div class="portal-header-inner">
        <div class="provider">
            <div class="provider-icon">
                <i class="fas fa-wifi" aria-hidden="true"></i>
            </div>

            <div style="min-width:0;">
                <div class="provider-name">
                    {{ $admin->network_name
                        ?: $admin->business_name
                        ?: 'WiFi Internet' }}
                </div>

                <div class="provider-label">
                    Internet Service Portal
                </div>
            </div>
        </div>

        <div class="header-badge">
            <i class="fas fa-shield-halved" aria-hidden="true"></i>
            Secure Portal
        </div>
    </div>
</header>

<main class="portal-page">
    <div class="portal-container">

        <section class="portal-intro">
            <div class="portal-intro-icon">
                <i class="fas fa-wifi" aria-hidden="true"></i>
            </div>

            <h1>Connect to the Internet</h1>

            <p>
                Choose an internet package below. New customers can create
                an account, while existing customers can sign in to continue.
            </p>
        </section>

        <section class="account-box">
            <div class="account-message">
                <i
                    class="fas fa-circle-user"
                    aria-hidden="true"
                ></i>

                <div>
                    <strong>Customer Account</strong>

                    Register if this is your first time using this network,
                    or sign in if you already have an account.
                </div>
            </div>

            <div class="account-actions">
                <a
                    href="{{ route(
                        'portal.login',
                        $router->portal_token
                    ) }}"
                    class="account-button login"
                >
                    <i
                        class="fas fa-right-to-bracket"
                        aria-hidden="true"
                    ></i>
                    Log In
                </a>

                <a
                    href="{{ route(
                        'portal.register',
                        $router->portal_token
                    ) }}"
                    class="account-button register"
                >
                    <i
                        class="fas fa-user-plus"
                        aria-hidden="true"
                    ></i>
                    Register
                </a>
            </div>
        </section>

        <div class="packages-heading">
            <h2>Available Internet Packages</h2>

            <span>
                {{ $packages->count() }}
                {{ $packages->count() === 1 ? 'package' : 'packages' }}
                available
            </span>
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
                            {{ $package->description
                                ?: 'Internet access provided by your ISP.' }}
                        </p>

                        <div class="package-price">
                            KES
                            {{ number_format(
                                (float) $package->price,
                                2
                            ) }}
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
                                            <span
                                                class="unlimited-speed-value"
                                            >
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
                                                    {{ $package->download_speed
                                                        ?: 'Not Set' }}
                                                </span>

                                                <span>
                                                    ↑
                                                    {{ $package->upload_speed
                                                        ?: 'Not Set' }}
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
                                        {{ $package->package_type === 'pppoe'
                                            ? 'PPPoE Access'
                                            : 'Hotspot Access' }}
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
                                Secure customer account
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
                                href="{{ route(
                                    'portal.login',
                                    $router->portal_token
                                ) }}"
                                class="choose-button"
                            >
                                <i
                                    class="fas fa-arrow-right"
                                    aria-hidden="true"
                                ></i>
                                Sign In or Register to Continue
                            </a>
                        </div>

                    </article>
                @endforeach
            </div>
        @else
            <div class="packages-empty">
                <div class="packages-empty-icon">
                    <i
                        class="fas fa-box-open"
                        aria-hidden="true"
                    ></i>
                </div>

                <h3>No Packages Available</h3>

                <p>
                    This internet provider has not published any active
                    packages yet. Please check again later.
                </p>
            </div>
        @endif

        <footer class="portal-footer">
            Internet service provided by
            <strong>
                {{ $admin->network_name
                    ?: $admin->business_name
                    ?: $admin->name }}
            </strong>.

            <br>

            Secure customer access portal.
        </footer>

    </div>
</main>

</body>
</html>