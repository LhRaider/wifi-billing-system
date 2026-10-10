<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'WiFi Billing System') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div style="
        min-height:100vh;
        background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 55%,#2563eb 100%);
        color:white;
        display:flex;
        flex-direction:column;
    ">

        {{-- Navigation --}}
        <header style="
            width:100%;
            padding:24px 30px;
        ">
            <div style="
                max-width:1200px;
                margin:auto;
                display:flex;
                justify-content:space-between;
                align-items:center;
                gap:20px;
            ">

                <a
                    href="{{ url('/') }}"
                    style="
                        display:flex;
                        align-items:center;
                        gap:12px;
                        color:white;
                        text-decoration:none;
                    "
                >
                    <div style="
                        width:48px;
                        height:48px;
                        border-radius:14px;
                        background:rgba(255,255,255,.12);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-size:22px;
                    ">
                        <i class="fas fa-wifi"></i>
                    </div>

                    <span style="
                        font-size:22px;
                        font-weight:800;
                    ">
                        {{ config('app.name', 'WiFi Billing System') }}
                    </span>
                </a>

                <nav style="
                    display:flex;
                    align-items:center;
                    gap:12px;
                    flex-wrap:wrap;
                    justify-content:flex-end;
                ">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            style="
                                background:white;
                                color:#1d4ed8;
                                padding:12px 22px;
                                border-radius:12px;
                                font-weight:700;
                                text-decoration:none;
                            "
                        >
                            Dashboard
                        </a>
                    @else
                        @if(Route::has('login'))
                            <a
                                href="{{ route('login') }}"
                                style="
                                    color:white;
                                    padding:12px 20px;
                                    border:1px solid rgba(255,255,255,.35);
                                    border-radius:12px;
                                    font-weight:700;
                                    text-decoration:none;
                                "
                            >
                                Log In
                            </a>
                        @endif

                        @if(Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                style="
                                    background:white;
                                    color:#1d4ed8;
                                    padding:12px 22px;
                                    border-radius:12px;
                                    font-weight:700;
                                    text-decoration:none;
                                "
                            >
                                Get Started
                            </a>
                        @endif
                    @endauth
                </nav>

            </div>
        </header>

        {{-- Main Content --}}
        <main style="
            flex:1;
            display:flex;
            align-items:center;
            padding:55px 30px 80px;
        ">
            <div style="
                width:100%;
                max-width:1200px;
                margin:auto;
                display:grid;
                grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
                gap:60px;
                align-items:center;
            ">

                {{-- Hero --}}
                <section>
                    <div style="
                        display:inline-flex;
                        align-items:center;
                        gap:9px;
                        padding:9px 15px;
                        border-radius:999px;
                        background:rgba(255,255,255,.12);
                        border:1px solid rgba(255,255,255,.15);
                        font-size:14px;
                        font-weight:700;
                        margin-bottom:24px;
                    ">
                        <i class="fas fa-shield-halved"></i>
                        WiFi Business Management Platform
                    </div>

                    <h1 style="
                        font-size:clamp(42px,6vw,72px);
                        line-height:1.05;
                        font-weight:800;
                        margin:0;
                        letter-spacing:-2px;
                    ">
                        Manage your WiFi business from one place.
                    </h1>

                    <p style="
                        margin-top:25px;
                        max-width:650px;
                        font-size:19px;
                        line-height:1.8;
                        color:#dbeafe;
                    ">
                        Manage customers, internet packages, subscriptions,
                        payments, routers, and network access through one
                        secure platform.
                    </p>

                    <div style="
                        margin-top:35px;
                        display:flex;
                        flex-wrap:wrap;
                        gap:15px;
                    ">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                style="
                                    background:white;
                                    color:#1d4ed8;
                                    padding:16px 28px;
                                    border-radius:14px;
                                    font-weight:800;
                                    text-decoration:none;
                                    box-shadow:0 14px 35px rgba(0,0,0,.18);
                                "
                            >
                                <i class="fas fa-gauge-high"></i>
                                Open Dashboard
                            </a>
                        @else
                            @if(Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    style="
                                        background:white;
                                        color:#1d4ed8;
                                        padding:16px 28px;
                                        border-radius:14px;
                                        font-weight:800;
                                        text-decoration:none;
                                        box-shadow:0 14px 35px rgba(0,0,0,.18);
                                    "
                                >
                                    Create ISP Account
                                </a>
                            @endif

                            @if(Route::has('login'))
                                <a
                                    href="{{ route('login') }}"
                                    style="
                                        color:white;
                                        padding:16px 28px;
                                        border-radius:14px;
                                        border:1px solid rgba(255,255,255,.35);
                                        font-weight:800;
                                        text-decoration:none;
                                    "
                                >
                                    Sign In
                                </a>
                            @endif
                        @endauth
                    </div>
                </section>

                {{-- Feature Card --}}
                <section style="
                    background:rgba(255,255,255,.10);
                    border:1px solid rgba(255,255,255,.16);
                    border-radius:30px;
                    padding:35px;
                    box-shadow:0 25px 60px rgba(0,0,0,.20);
                    backdrop-filter:blur(12px);
                ">

                    <h2 style="
                        font-size:27px;
                        font-weight:800;
                        margin:0 0 25px;
                    ">
                        Built for WiFi Providers
                    </h2>

                    <div style="
                        display:grid;
                        gap:18px;
                    ">

                        <div style="
                            background:rgba(255,255,255,.10);
                            padding:20px;
                            border-radius:18px;
                            display:flex;
                            gap:16px;
                            align-items:flex-start;
                        ">
                            <div style="
                                min-width:45px;
                                height:45px;
                                border-radius:13px;
                                background:#2563eb;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            ">
                                <i class="fas fa-users"></i>
                            </div>

                            <div>
                                <strong style="
                                    display:block;
                                    font-size:17px;
                                ">
                                    Customer Management
                                </strong>

                                <span style="
                                    display:block;
                                    margin-top:5px;
                                    color:#dbeafe;
                                    line-height:1.6;
                                ">
                                    Keep customer accounts, packages, and
                                    subscriptions organized.
                                </span>
                            </div>
                        </div>

                        <div style="
                            background:rgba(255,255,255,.10);
                            padding:20px;
                            border-radius:18px;
                            display:flex;
                            gap:16px;
                            align-items:flex-start;
                        ">
                            <div style="
                                min-width:45px;
                                height:45px;
                                border-radius:13px;
                                background:#059669;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            ">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>

                            <div>
                                <strong style="
                                    display:block;
                                    font-size:17px;
                                ">
                                    Billing and Payments
                                </strong>

                                <span style="
                                    display:block;
                                    margin-top:5px;
                                    color:#dbeafe;
                                    line-height:1.6;
                                ">
                                    Track billing activity and payment
                                    records from a central dashboard.
                                </span>
                            </div>
                        </div>

                        <div style="
                            background:rgba(255,255,255,.10);
                            padding:20px;
                            border-radius:18px;
                            display:flex;
                            gap:16px;
                            align-items:flex-start;
                        ">
                            <div style="
                                min-width:45px;
                                height:45px;
                                border-radius:13px;
                                background:#7c3aed;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            ">
                                <i class="fas fa-router"></i>
                            </div>

                            <div>
                                <strong style="
                                    display:block;
                                    font-size:17px;
                                ">
                                    Network Management
                                </strong>

                                <span style="
                                    display:block;
                                    margin-top:5px;
                                    color:#dbeafe;
                                    line-height:1.6;
                                ">
                                    Manage routers and customer network
                                    access from the same system.
                                </span>
                            </div>
                        </div>

                        <div style="
                            background:rgba(255,255,255,.10);
                            padding:20px;
                            border-radius:18px;
                            display:flex;
                            gap:16px;
                            align-items:flex-start;
                        ">
                            <div style="
                                min-width:45px;
                                height:45px;
                                border-radius:13px;
                                background:#ea580c;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                            ">
                                <i class="fas fa-chart-line"></i>
                            </div>

                            <div>
                                <strong style="
                                    display:block;
                                    font-size:17px;
                                ">
                                    Business Visibility
                                </strong>

                                <span style="
                                    display:block;
                                    margin-top:5px;
                                    color:#dbeafe;
                                    line-height:1.6;
                                ">
                                    View operational information and reports
                                    without mixing data between ISP accounts.
                                </span>
                            </div>
                        </div>

                    </div>

                </section>

            </div>
        </main>

        {{-- Footer --}}
        <footer style="
            padding:25px 30px;
            border-top:1px solid rgba(255,255,255,.12);
            color:#bfdbfe;
        ">
            <div style="
                max-width:1200px;
                margin:auto;
                display:flex;
                justify-content:space-between;
                gap:15px;
                flex-wrap:wrap;
                font-size:14px;
            ">
                <span>
                    &copy; {{ now()->year }}
                    {{ config('app.name', 'WiFi Billing System') }}.
                </span>

                <span>
                    Secure WiFi business management.
                </span>
            </div>
        </footer>

    </div>
</body>
</html>