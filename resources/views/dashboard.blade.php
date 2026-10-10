<x-app-layout>
    <x-slot name="header">
        <div class="customer-page-header">
            <div>
                <div class="customer-page-heading">
                    <span class="customer-page-heading-icon">
                        <i class="fas fa-gauge-high" aria-hidden="true"></i>
                    </span>

                    <div>
                        <h2>Customer Dashboard</h2>
                        <p>Manage your internet package, connection and account.</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('packages.index') }}" class="header-package-button">
                <i class="fas fa-bag-shopping" aria-hidden="true"></i>
                <span>Buy Package</span>
            </a>
        </div>
    </x-slot>

    @php
        $hasActiveSubscription = $activeSubscription !== null;
        $hasHotspotUser = $hotspotUser !== null;

        $hotspotStatus = $hasHotspotUser
            ? strtoupper((string) $hotspotUser->status)
            : 'INACTIVE';

        $hotspotIsActive = $hasHotspotUser
            && strtolower((string) $hotspotUser->status) === 'active';

        $packageName = $package?->name
            ?? $hotspotUser?->profile
            ?? 'No Active Package';
    @endphp

    <style>
        .customer-page-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .customer-page-heading {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .customer-page-heading-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.22);
            flex-shrink: 0;
        }

        .customer-page-heading h2 {
            margin: 0;
            color: #0f172a;
            font-size: 27px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .customer-page-heading p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        .header-package-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 12px 18px;
            border-radius: 13px;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.18);
            transition:
                transform 0.2s ease,
                background 0.2s ease,
                box-shadow 0.2s ease;
        }

        .header-package-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.25);
        }

        .header-package-button:focus-visible,
        .dashboard-action:focus-visible {
            outline: 3px solid #93c5fd;
            outline-offset: 3px;
        }

        .customer-dashboard {
            min-height: 100vh;
            padding: 34px 25px 55px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.7),
                    transparent 30%
                ),
                #f4f7fb;
        }

        .customer-dashboard-container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
        }

        .package-warning {
            margin-bottom: 24px;
            padding: 17px 20px;
            border: 1px solid #fecaca;
            border-radius: 18px;
            background: #fff7f7;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #991b1b;
        }

        .package-warning-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: #fee2e2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 17px;
        }

        .package-warning strong {
            display: block;
            margin-bottom: 2px;
            font-size: 14px;
            font-weight: 800;
        }

        .package-warning span {
            color: #b91c1c;
            font-size: 13px;
            line-height: 1.5;
        }

        .dashboard-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .summary-card {
            min-width: 0;
            min-height: 128px;
            padding: 21px;
            border: 1px solid #e7edf5;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.055);
            display: flex;
            align-items: center;
            gap: 16px;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 13px 30px rgba(15, 23, 42, 0.08);
        }

        .summary-card-icon {
            width: 55px;
            height: 55px;
            border-radius: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 21px;
        }

        .summary-card-icon.package {
            background: #dbeafe;
            color: #2563eb;
        }

        .summary-card-icon.time {
            background: #dcfce7;
            color: #16a34a;
        }

        .summary-card-icon.wallet {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .summary-card-icon.purchases {
            background: #ffedd5;
            color: #ea580c;
        }

        .summary-card-content {
            min-width: 0;
        }

        .summary-card-label {
            margin-bottom: 7px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.025em;
            text-transform: uppercase;
        }

        .summary-card-value {
            color: #0f172a;
            font-size: 18px;
            line-height: 1.25;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .summary-card-value.money {
            color: #7c3aed;
        }

        .dashboard-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.8fr);
            gap: 24px;
            align-items: start;
        }

        .dashboard-column {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .dashboard-card {
            overflow: hidden;
            border: 1px solid #e7edf5;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.055);
        }

        .dashboard-card-header {
            padding: 21px 23px;
            border-bottom: 1px solid #edf1f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .dashboard-card-heading {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .dashboard-card-heading-icon {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .dashboard-card-heading-icon.connection {
            background: #dbeafe;
            color: #2563eb;
        }

        .dashboard-card-heading-icon.credentials {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .dashboard-card-heading-icon.actions {
            background: #ffedd5;
            color: #ea580c;
        }

        .dashboard-card-heading-icon.support {
            background: #dcfce7;
            color: #16a34a;
        }

        .dashboard-card-heading h3 {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            font-weight: 800;
        }

        .dashboard-card-heading p {
            margin: 3px 0 0;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.4;
        }

        .dashboard-card-body {
            padding: 23px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }

        .status-pill.active {
            background: #dcfce7;
            color: #15803d;
        }

        .status-pill.inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        .connection-panel {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .connection-item {
            min-width: 0;
            padding: 18px;
            border: 1px solid #edf1f6;
            border-radius: 17px;
            background: #f8fafc;
        }

        .connection-item.package {
            background:
                linear-gradient(
                    135deg,
                    rgba(239, 246, 255, 1),
                    rgba(248, 250, 252, 1)
                );
            border-color: #dbeafe;
        }

        .connection-item-label {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 9px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .connection-item-label i {
            color: #94a3b8;
        }

        .connection-item.package .connection-item-label i {
            color: #2563eb;
        }

        .connection-item-value {
            color: #0f172a;
            font-size: 16px;
            line-height: 1.35;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .connection-item.package .connection-item-value {
            color: #1d4ed8;
        }

        .credentials-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .credential-item {
            min-width: 0;
            padding: 18px;
            border: 1px solid #edf1f6;
            border-radius: 17px;
            background: #f8fafc;
        }

        .credential-item-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .credential-item-header i {
            color: #7c3aed;
        }

        .credential-value {
            color: #0f172a;
            font-size: 17px;
            line-height: 1.4;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .credential-value.password {
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                "Liberation Mono",
                "Courier New",
                monospace;
        }

        .hotspot-status-row {
            margin-top: 15px;
            padding: 15px 17px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .hotspot-status-row.active {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
        }

        .hotspot-status-row.inactive {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .hotspot-status-label {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
        }

        .hotspot-status-label i {
            color: #64748b;
        }

        .hotspot-status-value {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 800;
        }

        .hotspot-status-value.active {
            color: #15803d;
        }

        .hotspot-status-value.inactive {
            color: #64748b;
        }

        .dashboard-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px;
        }

        .dashboard-action {
            min-height: 92px;
            padding: 16px;
            border: 1px solid #e7edf5;
            border-radius: 17px;
            background: #ffffff;
            color: #0f172a;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 13px;
            transition:
                transform 0.2s ease,
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .dashboard-action:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 9px 22px rgba(15, 23, 42, 0.07);
        }

        .dashboard-action.primary {
            border-color: #2563eb;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
        }

        .dashboard-action.primary:hover {
            border-color: #1d4ed8;
        }

        .dashboard-action-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 17px;
        }

        .dashboard-action.primary .dashboard-action-icon {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }

        .dashboard-action.receipts .dashboard-action-icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .dashboard-action.subscriptions .dashboard-action-icon {
            background: #dcfce7;
            color: #16a34a;
        }

        .dashboard-action.payments .dashboard-action-icon {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .dashboard-action-content {
            min-width: 0;
        }

        .dashboard-action-title {
            display: block;
            margin-bottom: 3px;
            font-size: 13px;
            font-weight: 800;
        }

        .dashboard-action-description {
            display: block;
            color: #94a3b8;
            font-size: 11px;
            line-height: 1.4;
        }

        .dashboard-action.primary .dashboard-action-description {
            color: #dbeafe;
        }

        .support-intro {
            margin: 0 0 19px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
        }

        .support-details {
            display: flex;
            flex-direction: column;
            gap: 11px;
        }

        .support-detail {
            padding: 14px 15px;
            border: 1px solid #edf1f6;
            border-radius: 15px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .support-detail-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .support-detail.phone .support-detail-icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .support-detail.network .support-detail-icon {
            background: #dcfce7;
            color: #16a34a;
        }

        .support-detail-content {
            min-width: 0;
        }

        .support-detail-label {
            display: block;
            margin-bottom: 2px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .support-detail-value {
            display: block;
            color: #0f172a;
            font-size: 13px;
            line-height: 1.4;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        @media (max-width: 1150px) {
            .dashboard-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-main-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .dashboard-column.secondary {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .customer-page-header {
                align-items: flex-start;
            }

            .customer-page-heading-icon {
                width: 44px;
                height: 44px;
                border-radius: 13px;
            }

            .customer-page-heading h2 {
                font-size: 22px;
            }

            .customer-page-heading p {
                display: none;
            }

            .header-package-button {
                padding: 11px 14px;
            }

            .header-package-button span {
                display: none;
            }

            .customer-dashboard {
                padding: 25px 15px 45px;
            }

            .dashboard-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 13px;
            }

            .summary-card {
                min-height: 115px;
                padding: 17px;
                align-items: flex-start;
                flex-direction: column;
                gap: 13px;
            }

            .summary-card-icon {
                width: 46px;
                height: 46px;
                border-radius: 14px;
                font-size: 18px;
            }

            .summary-card-value {
                font-size: 16px;
            }

            .connection-panel {
                grid-template-columns: 1fr;
            }

            .dashboard-column.secondary {
                display: flex;
            }

            .dashboard-card-header,
            .dashboard-card-body {
                padding: 19px;
            }
        }

        @media (max-width: 520px) {
            .customer-dashboard {
                padding-left: 12px;
                padding-right: 12px;
            }

            .customer-page-heading {
                gap: 10px;
            }

            .customer-page-heading h2 {
                font-size: 20px;
            }

            .dashboard-summary-grid {
                grid-template-columns: 1fr;
            }

            .summary-card {
                min-height: auto;
                flex-direction: row;
                align-items: center;
            }

            .credentials-grid,
            .dashboard-actions-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-card {
                border-radius: 19px;
            }

            .dashboard-card-header {
                align-items: flex-start;
            }

            .status-pill {
                padding: 7px 9px;
                font-size: 10px;
            }

            .hotspot-status-row {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <div class="customer-dashboard">
        <div class="customer-dashboard-container">

            @unless($hasActiveSubscription)
                <div class="package-warning" role="alert">
                    <div class="package-warning-icon">
                        <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                    </div>

                    <div>
                        <strong>No Active Internet Package</strong>
                        <span>
                            Purchase a package to restore internet access and continue browsing.
                        </span>
                    </div>
                </div>
            @endunless

            {{-- Account overview --}}
            <section
                class="dashboard-summary-grid"
                aria-label="Account overview"
            >
                <article class="summary-card">
                    <div class="summary-card-icon package">
                        <i class="fas fa-wifi" aria-hidden="true"></i>
                    </div>

                    <div class="summary-card-content">
                        <div class="summary-card-label">
                            Current Package
                        </div>

                        <div class="summary-card-value">
                            {{ $packageName }}
                        </div>
                    </div>
                </article>

                <article class="summary-card">
                    <div class="summary-card-icon time">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                    </div>

                    <div class="summary-card-content">
                        <div class="summary-card-label">
                            Remaining Time
                        </div>

                        <div class="summary-card-value">
                            {{ $remainingTime }}
                        </div>
                    </div>
                </article>

                <article class="summary-card">
                    <div class="summary-card-icon wallet">
                        <i class="fas fa-wallet" aria-hidden="true"></i>
                    </div>

                    <div class="summary-card-content">
                        <div class="summary-card-label">
                            Total Spent
                        </div>

                        <div class="summary-card-value money">
                            KES {{ number_format((float) $totalSpent, 2) }}
                        </div>
                    </div>
                </article>

                <article class="summary-card">
                    <div class="summary-card-icon purchases">
                        <i class="fas fa-box-open" aria-hidden="true"></i>
                    </div>

                    <div class="summary-card-content">
                        <div class="summary-card-label">
                            Packages Purchased
                        </div>

                        <div class="summary-card-value">
                            {{ number_format((int) $totalPurchases) }}
                        </div>
                    </div>
                </article>
            </section>

            <div class="dashboard-main-grid">

                {{-- Main content --}}
                <div class="dashboard-column">

                    {{-- Internet connection --}}
                    <section
                        class="dashboard-card"
                        aria-labelledby="connection-heading"
                    >
                        <div class="dashboard-card-header">
                            <div class="dashboard-card-heading">
                                <div class="dashboard-card-heading-icon connection">
                                    <i class="fas fa-signal" aria-hidden="true"></i>
                                </div>

                                <div>
                                    <h3 id="connection-heading">
                                        Internet Connection
                                    </h3>

                                    <p>
                                        Current package and subscription details
                                    </p>
                                </div>
                            </div>

                            @if($hasActiveSubscription)
                                <span class="status-pill active">
                                    <i class="fas fa-circle-check" aria-hidden="true"></i>
                                    ACTIVE
                                </span>
                            @else
                                <span class="status-pill inactive">
                                    <i class="fas fa-circle-xmark" aria-hidden="true"></i>
                                    INACTIVE
                                </span>
                            @endif
                        </div>

                        <div class="dashboard-card-body">
                            <div class="connection-panel">
                                <div class="connection-item package">
                                    <div class="connection-item-label">
                                        <i class="fas fa-wifi" aria-hidden="true"></i>
                                        Package
                                    </div>

                                    <div class="connection-item-value">
                                        {{ $packageName }}
                                    </div>
                                </div>

                                <div class="connection-item">
                                    <div class="connection-item-label">
                                        <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                                        Remaining
                                    </div>

                                    <div class="connection-item-value">
                                        {{ $remainingTime }}
                                    </div>
                                </div>

                                <div class="connection-item">
                                    <div class="connection-item-label">
                                        <i class="fas fa-calendar-check" aria-hidden="true"></i>
                                        Expiry
                                    </div>

                                    <div class="connection-item-value">
                                        {{ $expiryDate }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Hotspot credentials --}}
                    <section
                        class="dashboard-card"
                        aria-labelledby="credentials-heading"
                    >
                        <div class="dashboard-card-header">
                            <div class="dashboard-card-heading">
                                <div class="dashboard-card-heading-icon credentials">
                                    <i class="fas fa-user-lock" aria-hidden="true"></i>
                                </div>

                                <div>
                                    <h3 id="credentials-heading">
                                        Hotspot Credentials
                                    </h3>

                                    <p>
                                        Credentials assigned to your internet account
                                    </p>
                                </div>
                            </div>

                            <span class="status-pill {{ $hotspotIsActive ? 'active' : 'inactive' }}">
                                <i
                                    class="{{ $hotspotIsActive ? 'fas fa-circle-check' : 'fas fa-circle-xmark' }}"
                                    aria-hidden="true"
                                ></i>

                                {{ $hotspotStatus }}
                            </span>
                        </div>

                        <div class="dashboard-card-body">
                            <div class="credentials-grid">
                                <div class="credential-item">
                                    <div class="credential-item-header">
                                        <i class="fas fa-user" aria-hidden="true"></i>
                                        Username
                                    </div>

                                    <div class="credential-value">
                                        {{ $hotspotUser?->username ?? '---' }}
                                    </div>
                                </div>

                                <div class="credential-item">
                                    <div class="credential-item-header">
                                        <i class="fas fa-key" aria-hidden="true"></i>
                                        Password
                                    </div>

                                    <div class="credential-value password">
                                        {{ $hotspotUser?->password ?? '---' }}
                                    </div>
                                </div>
                            </div>

                            <div class="hotspot-status-row {{ $hotspotIsActive ? 'active' : 'inactive' }}">
                                <div class="hotspot-status-label">
                                    <i class="fas fa-tower-broadcast" aria-hidden="true"></i>
                                    Network Account Status
                                </div>

                                <div class="hotspot-status-value {{ $hotspotIsActive ? 'active' : 'inactive' }}">
                                    <i
                                        class="{{ $hotspotIsActive ? 'fas fa-circle-check' : 'fas fa-circle-xmark' }}"
                                        aria-hidden="true"
                                    ></i>

                                    {{ $hotspotStatus }}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Secondary column --}}
                <div class="dashboard-column secondary">

                    {{-- Quick actions --}}
                    <section
                        class="dashboard-card"
                        aria-labelledby="actions-heading"
                    >
                        <div class="dashboard-card-header">
                            <div class="dashboard-card-heading">
                                <div class="dashboard-card-heading-icon actions">
                                    <i class="fas fa-bolt" aria-hidden="true"></i>
                                </div>

                                <div>
                                    <h3 id="actions-heading">
                                        Quick Actions
                                    </h3>

                                    <p>
                                        Common account actions
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card-body">
                            <div class="dashboard-actions-grid">
                                <a
                                    href="{{ route('packages.index') }}"
                                    class="dashboard-action primary"
                                >
                                    <span class="dashboard-action-icon">
                                        <i class="fas fa-bag-shopping" aria-hidden="true"></i>
                                    </span>

                                    <span class="dashboard-action-content">
                                        <span class="dashboard-action-title">
                                            Buy Package
                                        </span>

                                        <span class="dashboard-action-description">
                                            Choose an internet plan
                                        </span>
                                    </span>
                                </a>

                                <a
                                    href="{{ route('receipts.index') }}"
                                    class="dashboard-action receipts"
                                >
                                    <span class="dashboard-action-icon">
                                        <i class="fas fa-receipt" aria-hidden="true"></i>
                                    </span>

                                    <span class="dashboard-action-content">
                                        <span class="dashboard-action-title">
                                            Receipts
                                        </span>

                                        <span class="dashboard-action-description">
                                            View payment receipts
                                        </span>
                                    </span>
                                </a>

                                <a
                                    href="{{ route('subscriptions.index') }}"
                                    class="dashboard-action subscriptions"
                                >
                                    <span class="dashboard-action-icon">
                                        <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                                    </span>

                                    <span class="dashboard-action-content">
                                        <span class="dashboard-action-title">
                                            Subscriptions
                                        </span>

                                        <span class="dashboard-action-description">
                                            View package history
                                        </span>
                                    </span>
                                </a>

                                <a
                                    href="{{ route('payments.index') }}"
                                    class="dashboard-action payments"
                                >
                                    <span class="dashboard-action-icon">
                                        <i class="fas fa-credit-card" aria-hidden="true"></i>
                                    </span>

                                    <span class="dashboard-action-content">
                                        <span class="dashboard-action-title">
                                            Payments
                                        </span>

                                        <span class="dashboard-action-description">
                                            Review transactions
                                        </span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </section>

                    {{-- ISP support --}}
                    <section
                        class="dashboard-card"
                        aria-labelledby="support-heading"
                    >
                        <div class="dashboard-card-header">
                            <div class="dashboard-card-heading">
                                <div class="dashboard-card-heading-icon support">
                                    <i class="fas fa-headset" aria-hidden="true"></i>
                                </div>

                                <div>
                                    <h3 id="support-heading">
                                        Need Help?
                                    </h3>

                                    <p>
                                        Contact your Internet Service Provider
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card-body">
                            <p class="support-intro">
                                Having connection or account problems? Use the
                                details below to contact your Internet Service Provider.
                            </p>

                            <div class="support-details">
                                <div class="support-detail phone">
                                    <div class="support-detail-icon">
                                        <i class="fas fa-phone" aria-hidden="true"></i>
                                    </div>

                                    <div class="support-detail-content">
                                        <span class="support-detail-label">
                                            Support Phone
                                        </span>

                                        <span class="support-detail-value">
                                            {{ $ispPhone }}
                                        </span>
                                    </div>
                                </div>

                                <div class="support-detail network">
                                    <div class="support-detail-icon">
                                        <i class="fas fa-tower-cell" aria-hidden="true"></i>
                                    </div>

                                    <div class="support-detail-content">
                                        <span class="support-detail-label">
                                            Network Provider
                                        </span>

                                        <span class="support-detail-value">
                                            {{ $ispName }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>