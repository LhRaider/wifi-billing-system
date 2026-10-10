<x-app-layout>
    <x-slot name="header">
        <div class="reports-header">
            <div class="reports-header-icon">
                <i class="fas fa-chart-column" aria-hidden="true"></i>
            </div>

            <div>
                <h2>Reports Dashboard</h2>
                <p>Review ISP performance, revenue, customers, payments, and subscriptions.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .reports-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .reports-header-icon {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #ffffff;
            font-size: 18px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.22);
        }

        .reports-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            line-height: 1.25;
            font-weight: 800;
        }

        .reports-header p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .reports-page {
            --page-bg: #f4f7fb;
            --surface: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --soft: #f8fafc;

            min-height: 100vh;
            padding: 38px 24px 55px;
            color: var(--text);
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.72),
                    transparent 30%
                ),
                var(--page-bg);
        }

        html.dark .reports-page {
            --page-bg: #091226;
            --surface: #101b31;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --border: #293954;
            --soft: #14213a;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.11),
                    transparent 31%
                ),
                var(--page-bg);
        }

        html.dark .reports-header h2 {
            color: #f8fafc;
        }

        html.dark .reports-header p {
            color: #94a3b8;
        }

        .reports-container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
        }

        .reports-intro {
            margin-bottom: 22px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
        }

        .reports-intro h3 {
            margin: 0;
            color: var(--text);
            font-size: 21px;
            font-weight: 800;
        }

        .reports-intro p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .report-stat {
            position: relative;
            min-height: 155px;
            overflow: hidden;
            padding: 22px;
            border-radius: 18px;
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .report-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.16);
        }

        .report-stat::after {
            content: "";
            position: absolute;
            width: 105px;
            height: 105px;
            right: -25px;
            bottom: -35px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.10);
        }

        .report-stat-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .report-stat-label {
            margin: 0;
            color: rgba(255, 255, 255, 0.90);
            font-size: 13px;
            font-weight: 700;
        }

        .report-stat-icon {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }

        .report-stat-value {
            position: relative;
            z-index: 1;
            margin: 25px 0 0;
            color: #ffffff;
            font-size: 28px;
            line-height: 1.15;
            font-weight: 900;
            letter-spacing: -0.5px;
            overflow-wrap: anywhere;
        }

        .report-revenue {
            background: linear-gradient(135deg, #15803d, #16a34a);
        }

        .report-customers {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
        }

        .report-payments {
            background: linear-gradient(135deg, #6d28d9, #7c3aed);
        }

        .report-completed {
            background: linear-gradient(135deg, #047857, #059669);
        }

        .report-pending {
            background: linear-gradient(135deg, #a16207, #ca8a04);
        }

        .report-active {
            background: linear-gradient(135deg, #c2410c, #ea580c);
        }

        .report-expired {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
        }

        .reports-export-panel {
            margin-top: 28px;
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: var(--surface);
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.06);
        }

        .reports-export-header {
            padding: 21px 23px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .reports-export-icon {
            width: 43px;
            height: 43px;
            flex: 0 0 43px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
        }

        html.dark .reports-export-icon {
            background: #16294b;
            color: #93c5fd;
        }

        .reports-export-header h3 {
            margin: 0;
            color: var(--text);
            font-size: 18px;
            font-weight: 800;
        }

        .reports-export-header p {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .reports-export-body {
            padding: 22px 23px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .report-export-button {
            min-height: 43px;
            padding: 10px 16px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #ffffff;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                opacity 0.2s ease;
        }

        .report-export-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            opacity: 0.94;
            box-shadow: 0 7px 16px rgba(15, 23, 42, 0.14);
        }

        .export-payments {
            background: #2563eb;
        }

        .export-customers {
            background: #16a34a;
        }

        .export-subscriptions {
            background: #9333ea;
        }

        .reports-note {
            margin-top: 18px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: var(--soft);
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .reports-note i {
            margin-top: 3px;
            color: #2563eb;
        }

        html.dark .reports-note i {
            color: #93c5fd;
        }

        @media (max-width: 1000px) {
            .reports-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 650px) {
            .reports-page {
                padding: 26px 14px 45px;
            }

            .reports-grid {
                grid-template-columns: 1fr;
            }

            .reports-intro {
                align-items: flex-start;
                flex-direction: column;
            }

            .report-stat {
                min-height: 140px;
            }

            .reports-export-body {
                flex-direction: column;
            }

            .report-export-button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .reports-header-icon {
                width: 43px;
                height: 43px;
                flex-basis: 43px;
            }

            .reports-header h2 {
                font-size: 20px;
            }

            .reports-header p {
                display: none;
            }

            .reports-page {
                padding-left: 10px;
                padding-right: 10px;
            }
        }
    </style>

    <div class="reports-page">
        <div class="reports-container">

            <div class="reports-intro">
                <div>
                    <h3>ISP Performance Overview</h3>
                    <p>
                        Current revenue, customer, payment, and subscription statistics.
                    </p>
                </div>
            </div>

            <div class="reports-grid">

                <div class="report-stat report-revenue">
                    <div class="report-stat-top">
                        <p class="report-stat-label">Total Revenue</p>

                        <div class="report-stat-icon">
                            <i class="fas fa-coins" aria-hidden="true"></i>
                        </div>
                    </div>

                    <h2 class="report-stat-value">
                        KES {{ number_format($totalRevenue, 2) }}
                    </h2>
                </div>

                <div class="report-stat report-customers">
                    <div class="report-stat-top">
                        <p class="report-stat-label">Total Customers</p>

                        <div class="report-stat-icon">
                            <i class="fas fa-users" aria-hidden="true"></i>
                        </div>
                    </div>

                    <h2 class="report-stat-value">
                        {{ number_format($totalCustomers) }}
                    </h2>
                </div>

                <div class="report-stat report-payments">
                    <div class="report-stat-top">
                        <p class="report-stat-label">Total Payments</p>

                        <div class="report-stat-icon">
                            <i class="fas fa-receipt" aria-hidden="true"></i>
                        </div>
                    </div>

                    <h2 class="report-stat-value">
                        {{ number_format($totalPayments) }}
                    </h2>
                </div>

                <div class="report-stat report-completed">
                    <div class="report-stat-top">
                        <p class="report-stat-label">Completed Payments</p>

                        <div class="report-stat-icon">
                            <i class="fas fa-circle-check" aria-hidden="true"></i>
                        </div>
                    </div>

                    <h2 class="report-stat-value">
                        {{ number_format($completedPayments) }}
                    </h2>
                </div>

                <div class="report-stat report-pending">
                    <div class="report-stat-top">
                        <p class="report-stat-label">Pending Payments</p>

                        <div class="report-stat-icon">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                        </div>
                    </div>

                    <h2 class="report-stat-value">
                        {{ number_format($pendingPayments) }}
                    </h2>
                </div>

                <div class="report-stat report-active">
                    <div class="report-stat-top">
                        <p class="report-stat-label">Active Subscriptions</p>

                        <div class="report-stat-icon">
                            <i class="fas fa-wifi" aria-hidden="true"></i>
                        </div>
                    </div>

                    <h2 class="report-stat-value">
                        {{ number_format($activeSubscriptions) }}
                    </h2>
                </div>

                <div class="report-stat report-expired">
                    <div class="report-stat-top">
                        <p class="report-stat-label">Expired Subscriptions</p>

                        <div class="report-stat-icon">
                            <i class="fas fa-ban" aria-hidden="true"></i>
                        </div>
                    </div>

                    <h2 class="report-stat-value">
                        {{ number_format($expiredSubscriptions) }}
                    </h2>
                </div>

            </div>

            <section class="reports-export-panel">
                <div class="reports-export-header">
                    <div class="reports-export-icon">
                        <i class="fas fa-file-export" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h3>Export Reports</h3>
                        <p>
                            Download detailed ISP records in CSV format.
                        </p>
                    </div>
                </div>

                <div class="reports-export-body">
                    <a
                        href="{{ route('admin.reports.payments.csv') }}"
                        class="report-export-button export-payments"
                    >
                        <i class="fas fa-file-csv" aria-hidden="true"></i>
                        Export Payments CSV
                    </a>

                    <a
                        href="{{ route('admin.reports.customers.csv') }}"
                        class="report-export-button export-customers"
                    >
                        <i class="fas fa-users" aria-hidden="true"></i>
                        Export Customers CSV
                    </a>

                    <a
                        href="{{ route('admin.reports.subscriptions.csv') }}"
                        class="report-export-button export-subscriptions"
                    >
                        <i class="fas fa-file-csv" aria-hidden="true"></i>
                        Export Subscriptions CSV
                    </a>
                </div>
            </section>

            <div class="reports-note">
                <i class="fas fa-circle-info" aria-hidden="true"></i>

                <span>
                    Report totals reflect the records available to your ISP account.
                    CSV exports provide the corresponding detailed records for
                    administrative reporting.
                </span>
            </div>

        </div>
    </div>
</x-app-layout>