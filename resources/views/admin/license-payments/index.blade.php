<x-app-layout>
    <x-slot name="header">
        <h2 class="license-page-header">
            License Payments
        </h2>
    </x-slot>

    <style>
        .license-page {
            --lp-bg: #f3f4f6;
            --lp-card: #ffffff;
            --lp-card-soft: #f8fafc;
            --lp-text: #111827;
            --lp-muted: #64748b;
            --lp-border: #e5e7eb;
            --lp-input: #ffffff;
            --lp-blue-soft: #eff6ff;
            --lp-blue-text: #1e3a8a;
            --lp-shadow: 0 8px 24px rgba(15, 23, 42, .08);

            min-height: 100vh;
            padding: 40px;
            color: var(--lp-text);
            background: var(--lp-bg);
            transition: background-color .24s ease, color .24s ease;
        }

        html.dark .license-page {
            --lp-bg: #091226;
            --lp-card: #0f1a31;
            --lp-card-soft: #111f39;
            --lp-text: #e8eeff;
            --lp-muted: #9eabc4;
            --lp-border: #263754;
            --lp-input: #0b162b;
            --lp-blue-soft: #13284a;
            --lp-blue-text: #b9d3ff;
            --lp-shadow: 0 10px 30px rgba(0, 0, 0, .22);
        }

        .license-page-header {
            margin: 0;
            color: #1f2937;
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.75rem;
        }

        html.dark .license-page-header {
            color: #e8eeff;
        }

        .license-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .license-card {
            padding: 30px;
            margin-bottom: 25px;
            color: var(--lp-text);
            background: var(--lp-card);
            border: 1px solid var(--lp-border);
            border-radius: 16px;
            box-shadow: var(--lp-shadow);
        }

        .license-card:last-child {
            margin-bottom: 0;
        }

        .license-card h2 {
            margin: 0 0 15px;
            color: var(--lp-text);
            font-size: 26px;
            font-weight: 900;
            line-height: 1.25;
        }

        .license-card h3 {
            margin: 0;
            color: var(--lp-text);
            font-size: 22px;
            font-weight: 900;
        }

        .license-card p,
        .license-card label,
        .license-card td,
        .license-card th {
            color: var(--lp-text);
        }

        .license-muted {
            color: var(--lp-muted) !important;
        }

        .license-validation {
            padding: 15px 18px;
            margin-bottom: 20px;
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 10px;
        }

        html.dark .license-validation {
            color: #ffb4c0;
            background: #451d29;
            border-color: #713143;
        }

        .license-validation ul {
            margin: 0;
            padding-left: 20px;
        }

        .license-status-row {
            margin: 7px 0;
        }

        .license-expired-notice {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 12px 14px;
            margin-top: 16px;
            color: #991b1b !important;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            font-weight: 800;
        }

        html.dark .license-expired-notice {
            color: #ffb4c0 !important;
            background: #451d29;
            border-color: #713143;
        }

        .license-payment-details {
            padding: 18px;
            margin-bottom: 20px;
            color: var(--lp-blue-text);
            background: var(--lp-blue-soft);
            border: 1px solid var(--lp-border);
            border-radius: 12px;
        }

        .license-payment-details p {
            color: var(--lp-blue-text);
        }

        .license-payment-highlight {
            margin: 0;
            color: #2563eb !important;
            font-size: 24px;
            font-weight: 900;
        }

        html.dark .license-payment-highlight {
            color: #8db7ff !important;
        }

        .license-plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .license-plan {
            padding: 20px;
            background: var(--lp-card-soft);
            border: 1px solid var(--lp-border);
            border-radius: 14px;
        }

        .license-price {
            margin: 10px 0;
            color: #16a34a !important;
            font-size: 26px;
            font-weight: 900;
        }

        html.dark .license-price {
            color: #6ee7a0 !important;
        }

        .license-label {
            display: block;
            color: var(--lp-text);
            font-weight: 700;
        }

        .license-input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            color: var(--lp-text);
            background: var(--lp-input);
            border: 1px solid var(--lp-border);
            border-radius: 8px;
            outline: none;
        }

        .license-input::placeholder {
            color: #94a3b8;
        }

        .license-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .14);
        }

        .license-pay-button {
            width: 100%;
            padding: 11px 15px;
            color: #ffffff;
            background: #16a34a;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 800;
            transition: background .18s ease, transform .18s ease;
        }

        .license-pay-button:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        .license-pay-button:focus-visible,
        .license-action-button:focus-visible {
            outline: 3px solid rgba(59, 130, 246, .35);
            outline-offset: 2px;
        }

        .license-table-wrap {
            overflow-x: auto;
            border: 1px solid var(--lp-border);
            border-radius: 12px;
        }

        .license-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        .license-table thead tr {
            background: var(--lp-card-soft);
        }

        .license-table th,
        .license-table td {
            padding: 12px;
            color: var(--lp-text);
            border-right: 1px solid var(--lp-border);
            border-bottom: 1px solid var(--lp-border);
            text-align: left;
            vertical-align: middle;
        }

        .license-table th {
            font-size: 13px;
            font-weight: 800;
        }

        .license-table tbody tr {
            background: var(--lp-card);
        }

        .license-table tbody tr:hover {
            background: var(--lp-card-soft);
        }

        .license-table th:last-child,
        .license-table td:last-child {
            border-right: 0;
        }

        .license-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .license-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .license-status-approved {
            color: #166534;
            background: #dcfce7;
        }

        .license-status-rejected,
        .license-status-failed {
            color: #991b1b;
            background: #fee2e2;
        }

        .license-status-pending {
            color: #92400e;
            background: #fef3c7;
        }

        html.dark .license-status-approved {
            color: #8ce8b3;
            background: #123c2a;
        }

        html.dark .license-status-rejected,
        html.dark .license-status-failed {
            color: #ffabb8;
            background: #451d29;
        }

        html.dark .license-status-pending {
            color: #f6d978;
            background: #403414;
        }

        .license-action-button {
            display: inline-block;
            padding: 7px 12px;
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            font-weight: 700;
        }

        .license-action-approve {
            background: #16a34a;
        }

        .license-action-reject {
            background: #dc2626;
        }

        .license-empty {
            padding: 25px !important;
            color: var(--lp-muted) !important;
            text-align: center !important;
        }

        .license-pagination {
            margin-top: 20px;
        }

        html.dark .license-pagination nav,
        html.dark .license-pagination span,
        html.dark .license-pagination a {
            border-color: var(--lp-border) !important;
        }

        @media (max-width: 767px) {
            .license-page {
                padding: 22px 14px;
            }

            .license-card {
                padding: 20px;
            }

            .license-card h2 {
                font-size: 22px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .license-pay-button {
                transition: none;
            }
        }
    </style>

    <div class="license-page">
        <div class="license-container">

            {{-- Global success/error/warning flash messages are rendered once
                 by resources/views/layouts/app.blade.php. --}}

            @if($errors->any())
                <div class="license-validation" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(auth()->user()->role !== 'super_admin')

                <section class="license-card">
                    <h2>
                        <i class="fas fa-crown" style="color:#f59e0b;" aria-hidden="true"></i>
                        Current Subscription
                    </h2>

                    <p class="license-status-row">
                        <strong>Status:</strong>
                        {{ ucfirst(auth()->user()->service_status ?? 'inactive') }}
                    </p>

                    @if(auth()->user()->service_status === 'trial' && auth()->user()->trial_ends_at)
                        <p class="license-status-row">
                            <strong>Trial Ends:</strong>
                            {{ auth()->user()->trial_ends_at->format('d M Y') }}
                        </p>
                    @endif

                    @if(auth()->user()->service_status === 'active' && auth()->user()->service_expires_at)
                        <p class="license-status-row">
                            <strong>Expiry:</strong>
                            {{ auth()->user()->service_expires_at->format('d M Y') }}
                        </p>
                    @endif

                    @if(auth()->user()->service_status === 'expired')
                        <div class="license-expired-notice" role="status">
                            <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                            <span>
                                Your subscription is over. Please renew to continue providing services.
                            </span>
                        </div>
                    @endif
                </section>

                <section class="license-card">
                    <h2>Super Admin Payment Details</h2>

                    <p class="license-muted" style="margin-bottom:20px;">
                        Use the M-Pesa payment option below. After Safaricom confirms payment,
                        your subscription will be activated automatically.
                    </p>

                    @if($settings->license_payment_type === 'till')

                        <div class="license-payment-details">
                            <p style="margin:0 0 5px;">
                                <strong>Payment Method:</strong> Till Number
                            </p>

                            <p class="license-payment-highlight">
                                {{ $settings->license_till_number ?: 'Not configured' }}
                            </p>
                        </div>

                    @elseif($settings->license_payment_type === 'paybill')

                        <div class="license-payment-details">
                            <p style="margin:0 0 5px;">
                                <strong>Payment Method:</strong> PayBill
                            </p>

                            <p style="margin:5px 0;">
                                <strong>PayBill Number:</strong>
                                {{ $settings->license_paybill_number ?: 'Not configured' }}
                            </p>

                            <p style="margin:5px 0;">
                                <strong>Account Number:</strong>
                                {{ $settings->license_account_number ?: 'Not configured' }}
                            </p>
                        </div>

                    @elseif($settings->license_payment_type === 'phone')

                        <div class="license-payment-details">
                            <p style="margin:0 0 5px;">
                                <strong>Payment Method:</strong> Phone Number
                            </p>

                            <p class="license-payment-highlight">
                                {{ $settings->license_phone_number ?: 'Not configured' }}
                            </p>
                        </div>

                    @endif

                    <p style="margin-bottom:0;">
                        <strong>Monthly Fee:</strong>
                        KES {{ number_format($settings->license_monthly_fee ?? 400, 2) }}
                    </p>
                </section>

                <section class="license-card">
                    <h2>Renew Subscription</h2>

                    <p class="license-muted" style="margin-bottom:22px;">
                        Enter the Safaricom M-Pesa number that should receive the STK Push.
                        You can enter it as 07XXXXXXXX, 01XXXXXXXX, or 254XXXXXXXXX.
                    </p>

                    <div class="license-plan-grid">
                        @foreach($plans as $months => $amount)

                            <form
                                method="POST"
                                action="{{ route('admin.license-payments.mpesa.initiate') }}"
                                class="license-plan"
                            >
                                @csrf

                                <h3>
                                    {{ $months }}
                                    Month{{ $months > 1 ? 's' : '' }}
                                </h3>

                                <p class="license-price">
                                    KES {{ number_format($amount, 2) }}
                                </p>

                                <input
                                    type="hidden"
                                    name="months"
                                    value="{{ $months }}"
                                >

                                <label
                                    for="phone_number_{{ $months }}"
                                    class="license-label"
                                >
                                    M-Pesa Phone Number
                                </label>

                                <input
                                    id="phone_number_{{ $months }}"
                                    type="text"
                                    name="phone_number"
                                    value="{{ old('phone_number') }}"
                                    placeholder="Example: 0712345678"
                                    required
                                    inputmode="tel"
                                    autocomplete="tel"
                                    class="license-input"
                                >

                                <button
                                    type="submit"
                                    class="license-pay-button"
                                >
                                    <i class="fas fa-mobile-alt" aria-hidden="true"></i>
                                    Pay with M-Pesa
                                </button>

                                <p class="license-muted" style="font-size:12px;margin-top:10px;margin-bottom:0;">
                                    An STK Push will be sent to this phone. Enter your M-Pesa PIN
                                    on the phone to complete payment.
                                </p>
                            </form>

                        @endforeach
                    </div>
                </section>

            @endif

            <section class="license-card">
                <h2 style="margin-bottom:20px;">
                    <i class="fas fa-money-check-alt" style="color:#2563eb;" aria-hidden="true"></i>

                    {{ auth()->user()->role === 'super_admin'
                        ? 'Admin License Payment Requests'
                        : 'My License Payments'
                    }}
                </h2>

                <div class="license-table-wrap">
                    <table class="license-table">
                        <thead>
                            <tr>
                                @if(auth()->user()->role === 'super_admin')
                                    <th>Admin</th>
                                @endif

                                <th>Months</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Phone</th>
                                <th>Transaction</th>
                                <th>Status</th>
                                <th>Start</th>
                                <th>Expiry</th>

                                @if(auth()->user()->role === 'super_admin')
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    @if(auth()->user()->role === 'super_admin')
                                        <td>{{ $payment->user->name ?? 'Admin' }}</td>
                                    @endif

                                    <td>{{ $payment->months }}</td>

                                    <td>
                                        KES {{ number_format($payment->amount, 2) }}
                                    </td>

                                    <td>{{ $payment->payment_method ?? 'N/A' }}</td>

                                    <td>{{ $payment->phone_number ?? 'N/A' }}</td>

                                    <td>{{ $payment->transaction_code ?? 'N/A' }}</td>

                                    <td>
                                        @if($payment->status === 'approved')
                                            <span class="license-status license-status-approved">
                                                Approved
                                            </span>
                                        @elseif($payment->status === 'rejected')
                                            <span class="license-status license-status-rejected">
                                                Rejected
                                            </span>
                                        @elseif($payment->status === 'failed')
                                            <span class="license-status license-status-failed">
                                                Failed
                                            </span>
                                        @else
                                            <span class="license-status license-status-pending">
                                                Pending
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $payment->start_date
                                            ? $payment->start_date->format('d M Y')
                                            : 'Pending'
                                        }}
                                    </td>

                                    <td>
                                        {{ $payment->expiry_date
                                            ? $payment->expiry_date->format('d M Y')
                                            : 'Pending'
                                        }}
                                    </td>

                                    @if(auth()->user()->role === 'super_admin')
                                        <td>
                                            @if($payment->status === 'pending')
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.license-payments.approve', $payment) }}"
                                                    style="display:inline;"
                                                >
                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="license-action-button license-action-approve"
                                                    >
                                                        Approve
                                                    </button>
                                                </form>

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.license-payments.reject', $payment) }}"
                                                    style="display:inline;"
                                                >
                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="license-action-button license-action-reject"
                                                    >
                                                        Reject
                                                    </button>
                                                </form>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="{{ auth()->user()->role === 'super_admin' ? 10 : 9 }}"
                                        class="license-empty"
                                    >
                                        No license payments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="license-pagination">
                    {{ $payments->links() }}
                </div>
            </section>

        </div>
    </div>
</x-app-layout>