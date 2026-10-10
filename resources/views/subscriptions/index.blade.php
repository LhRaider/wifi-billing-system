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
                <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
            </span>

            <div>
                <h2 style="margin:0;font-size:25px;font-weight:800;color:#0f172a;">
                    My Subscriptions
                </h2>

                <p style="margin:4px 0 0;color:#64748b;font-size:13px;">
                    View your current and previous internet subscriptions.
                </p>
            </div>
        </div>
    </x-slot>

    <style>
        .subscriptions-page {
            min-height: 100vh;
            padding: 38px 20px 55px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.65),
                    transparent 30%
                ),
                #f4f7fb;
        }

        .subscriptions-container {
            width: 100%;
            max-width: 1250px;
            margin: 0 auto;
        }

        .subscription-alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 13px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            line-height: 1.5;
            font-weight: 700;
        }

        .subscription-alert.success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .subscription-alert.warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .subscription-alert.error {
            background: #fff7f7;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .subscription-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .summary-card {
            padding: 18px;
            background: #ffffff;
            border: 1px solid #e5eaf0;
            border-radius: 17px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.045);
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .summary-icon.total {
            background: #dbeafe;
            color: #2563eb;
        }

        .summary-icon.active {
            background: #dcfce7;
            color: #16a34a;
        }

        .summary-icon.suspended {
            background: #fef3c7;
            color: #d97706;
        }

        .summary-icon.expired {
            background: #fee2e2;
            color: #dc2626;
        }

        .summary-info span {
            display: block;
            margin-bottom: 2px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .035em;
        }

        .summary-info strong {
            display: block;
            color: #0f172a;
            font-size: 22px;
            line-height: 1.2;
            font-weight: 900;
        }

        .subscription-panel {
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e5eaf0;
            border-radius: 20px;
            box-shadow: 0 9px 28px rgba(15, 23, 42, 0.055);
        }

        .subscription-panel-header {
            padding: 22px 24px;
            border-bottom: 1px solid #edf1f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .panel-heading-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .panel-heading h3 {
            margin: 0;
            color: #0f172a;
            font-size: 17px;
            font-weight: 800;
        }

        .panel-heading p {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 11px;
        }

        .record-badge {
            padding: 7px 11px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .subscription-table-wrapper {
            overflow-x: auto;
        }

        .subscription-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        .subscription-table th {
            padding: 13px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e5eaf0;
            color: #64748b;
            text-align: left;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .045em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .subscription-table td {
            padding: 16px;
            border-bottom: 1px solid #edf1f5;
            color: #334155;
            vertical-align: middle;
            font-size: 12px;
        }

        .subscription-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .subscription-table tbody tr:hover {
            background: #fbfdff;
        }

        .package-cell {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 170px;
        }

        .package-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .package-cell strong {
            color: #0f172a;
            font-size: 12px;
            font-weight: 800;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .status-badge.active {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.suspended {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.expired {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.other {
            background: #f1f5f9;
            color: #475569;
        }

        .date-cell {
            display: flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        .date-cell i {
            color: #94a3b8;
        }

        .duration-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #f3e8ff;
            color: #7e22ce;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .pagination-wrapper {
            padding: 18px 22px;
            border-top: 1px solid #edf1f5;
        }

        .subscription-notice {
            margin: 0 22px 22px;
            padding: 14px 16px;
            border: 1px solid #bfdbfe;
            border-radius: 13px;
            background: #eff6ff;
            color: #1e3a8a;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 11px;
            line-height: 1.65;
        }

        .subscription-notice i {
            margin-top: 3px;
            color: #2563eb;
        }

        .empty-state {
            padding: 55px 25px;
            text-align: center;
        }

        .empty-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 14px;
            border-radius: 18px;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .empty-state h3 {
            margin: 0;
            color: #334155;
            font-size: 16px;
            font-weight: 800;
        }

        .empty-state p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 11px;
            line-height: 1.6;
        }

        @media (max-width: 950px) {
            .subscription-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .subscriptions-page {
                padding: 28px 13px 45px;
            }

            .subscription-summary {
                grid-template-columns: 1fr;
            }

            .subscription-panel-header {
                padding: 19px;
            }

            .subscription-notice {
                margin-left: 17px;
                margin-right: 17px;
            }
        }
    </style>

    <div class="subscriptions-page">
        <div class="subscriptions-container">

            @if(session('success'))
                <div class="subscription-alert success" role="status">
                    <i class="fas fa-circle-check" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="subscription-alert warning" role="status">
                    <i
                        class="fas fa-triangle-exclamation"
                        aria-hidden="true"
                    ></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="subscription-alert error" role="alert">
                    <i
                        class="fas fa-circle-exclamation"
                        aria-hidden="true"
                    ></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="subscription-summary">
                <div class="summary-card">
                    <span class="summary-icon total">
                        <i class="fas fa-layer-group" aria-hidden="true"></i>
                    </span>

                    <div class="summary-info">
                        <span>Total</span>
                        <strong>{{ $totalSubscriptions }}</strong>
                    </div>
                </div>

                <div class="summary-card">
                    <span class="summary-icon active">
                        <i class="fas fa-circle-check" aria-hidden="true"></i>
                    </span>

                    <div class="summary-info">
                        <span>Active</span>
                        <strong>{{ $activeSubscriptions }}</strong>
                    </div>
                </div>

                <div class="summary-card">
                    <span class="summary-icon suspended">
                        <i class="fas fa-circle-pause" aria-hidden="true"></i>
                    </span>

                    <div class="summary-info">
                        <span>Suspended</span>
                        <strong>{{ $suspendedSubscriptions }}</strong>
                    </div>
                </div>

                <div class="summary-card">
                    <span class="summary-icon expired">
                        <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                    </span>

                    <div class="summary-info">
                        <span>Expired</span>
                        <strong>{{ $expiredSubscriptions }}</strong>
                    </div>
                </div>
            </div>

            <section class="subscription-panel">
                <div class="subscription-panel-header">
                    <div class="panel-heading">
                        <span class="panel-heading-icon">
                            <i class="fas fa-list-check" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h3>Subscription History</h3>

                            <p>
                                Your internet service subscription records.
                            </p>
                        </div>
                    </div>

                    <span class="record-badge">
                        {{ $subscriptions->total() }}
                        {{ $subscriptions->total() === 1 ? 'Record' : 'Records' }}
                    </span>
                </div>

                @if($subscriptions->count() > 0)
                    <div class="subscription-table-wrapper">
                        <table class="subscription-table">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($subscriptions as $subscription)
                                    <tr>
                                        <td>
                                            <div class="package-cell">
                                                <span class="package-icon">
                                                    @if(
                                                        $subscription->package?->package_type
                                                        === 'pppoe'
                                                    )
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

                                                <strong>
                                                    {{ $subscription->package?->name ?? 'Package unavailable' }}
                                                </strong>
                                            </div>
                                        </td>

                                        <td>
                                            @if($subscription->status === 'active')
                                                <span class="status-badge active">
                                                    <i
                                                        class="fas fa-circle-check"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Active
                                                </span>

                                            @elseif(
                                                $subscription->status === 'suspended'
                                            )
                                                <span class="status-badge suspended">
                                                    <i
                                                        class="fas fa-circle-pause"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Suspended
                                                </span>

                                            @elseif(
                                                $subscription->status === 'expired'
                                            )
                                                <span class="status-badge expired">
                                                    <i
                                                        class="fas fa-clock"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Expired
                                                </span>

                                            @else
                                                <span class="status-badge other">
                                                    <i
                                                        class="fas fa-circle-info"
                                                        aria-hidden="true"
                                                    ></i>
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="date-cell">
                                                <i
                                                    class="fas fa-calendar-plus"
                                                    aria-hidden="true"
                                                ></i>

                                                {{ $subscription->start_time?->format('d M Y, H:i') ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="date-cell">
                                                <i
                                                    class="fas fa-calendar-check"
                                                    aria-hidden="true"
                                                ></i>

                                                {{ $subscription->end_time?->format('d M Y, H:i') ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($subscription->package)
                                                <span class="duration-badge">
                                                    <i
                                                        class="fas fa-clock"
                                                        aria-hidden="true"
                                                    ></i>

                                                    {{ $subscription->package->formatted_duration }}
                                                </span>
                                            @else
                                                <span style="color:#94a3b8;">
                                                    N/A
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        {{ $subscriptions->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i
                                class="fas fa-clock-rotate-left"
                                aria-hidden="true"
                            ></i>
                        </div>

                        <h3>No subscriptions found</h3>

                        <p>
                            Your subscription history will appear here after an
                            internet package has been activated.
                        </p>
                    </div>
                @endif

                <div class="subscription-notice">
                    <i class="fas fa-circle-info" aria-hidden="true"></i>

                    <div>
                        An <strong>active</strong> subscription means internet
                        access is currently authorized and remains subject to its
                        configured end time. A <strong>suspended</strong>
                        subscription remains within its original paid service
                        period, but access has been disabled. An
                        <strong>expired</strong> subscription has reached the end
                        of its service period.
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>