<x-app-layout>
    <x-slot name="header">
        <div class="customer-profile-heading">
            <div class="customer-profile-heading__icon">
                <i class="fas fa-user"></i>
            </div>

            <div>
                <h2>Customer Profile</h2>
                <p>Review customer activity, subscription status, and network access.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .customer-profile-page {
            --page-bg: #f3f4f6;
            --card-bg: #ffffff;
            --text: #111827;
            --muted: #64748b;
            --border: #e2e8f0;
            --soft-blue: #eff6ff;
            --soft-blue-text: #1e3a8a;
            --soft-blue-border: #bfdbfe;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --warning-bg: #fef3c7;
            --warning-text: #92400e;
            --danger-bg: #fee2e2;
            --danger-text: #991b1b;

            min-height: 100vh;
            background: var(--page-bg);
            color: var(--text);
        }

        html.dark .customer-profile-page {
            --page-bg: #091226;
            --card-bg: #101c33;
            --text: #e8eeff;
            --muted: #94a3b8;
            --border: #293a59;
            --soft-blue: #10264b;
            --soft-blue-text: #bfdbfe;
            --soft-blue-border: #264d83;
            --success-bg: #123c2a;
            --success-text: #86efac;
            --warning-bg: #403414;
            --warning-text: #fde68a;
            --danger-bg: #451d29;
            --danger-text: #fda4af;
        }

        .customer-profile-heading {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .customer-profile-heading__icon {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: #2563eb;
            color: #ffffff;
        }

        .customer-profile-heading h2 {
            margin: 0;
            color: #111827;
            font-size: 20px;
            font-weight: 800;
        }

        .customer-profile-heading p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        html.dark .customer-profile-heading h2 {
            color: #f8fafc;
        }

        html.dark .customer-profile-heading p {
            color: #94a3b8;
        }

        .customer-profile-container {
            width: 100%;
            max-width: 80rem;
            margin: 0 auto;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .customer-profile-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .customer-profile-actions form {
            margin: 0;
        }

        .customer-action-btn {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.3;
            transition: opacity .18s ease, transform .18s ease;
        }

        .customer-action-btn:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .customer-action-btn:focus-visible {
            outline: 3px solid rgba(59, 130, 246, .28);
            outline-offset: 2px;
        }

        .customer-action-back {
            background: #475569;
        }

        .customer-action-activate {
            background: #16a34a;
        }

        .customer-action-suspend {
            background: #dc2626;
        }

        .customer-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .customer-summary-card {
            min-width: 0;
            padding: 25px;
            border-radius: 15px;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
            overflow: hidden;
        }

        .customer-summary-card--payments {
            background: #2563eb;
        }

        .customer-summary-card--spend {
            background: #16a34a;
        }

        .customer-summary-card--status {
            background: #7c3aed;
        }

        .customer-summary-card h3 {
            margin: 0 0 8px;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
        }

        .customer-summary-value {
            margin: 0;
            color: #ffffff !important;
            font-size: 32px;
            line-height: 1.15;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .customer-summary-value--status {
            font-size: 24px;
        }

        .customer-details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .customer-detail-card {
            min-width: 0;
            padding: 25px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 15px;
            color: var(--text);
            box-shadow: 0 4px 12px rgba(15, 23, 42, .08);
        }

        html.dark .customer-detail-card {
            box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
        }

        .customer-detail-card h3 {
            margin: 0 0 20px;
            color: var(--text) !important;
            font-size: 20px;
            font-weight: 800;
        }

        .customer-detail-list {
            display: grid;
            gap: 14px;
        }

        .customer-detail-row {
            min-width: 0;
            margin: 0;
            color: var(--text);
            line-height: 1.6;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .customer-detail-row strong {
            color: var(--text);
            font-weight: 800;
        }

        .customer-muted {
            color: var(--muted);
        }

        .customer-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 4px;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.3;
        }

        .customer-status--success {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .customer-status--warning {
            background: var(--warning-bg);
            color: var(--warning-text);
        }

        .customer-status--danger {
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .customer-sync-success {
            color: var(--success-text);
            font-weight: 800;
        }

        .customer-sync-danger {
            color: var(--danger-text);
            font-weight: 800;
        }

        .customer-provision-form {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .customer-provision-label {
            color: var(--text);
            font-size: 14px;
            font-weight: 800;
        }

        .customer-provision-select {
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text);
            font-size: 14px;
        }

        .customer-provision-select:focus {
            outline: 3px solid rgba(59, 130, 246, .18);
            border-color: #3b82f6;
        }

        .customer-action-provision {
            width: 100%;
            background: #2563eb;
        }

        .customer-validation-error {
            margin: 0;
            color: var(--danger-text);
            font-size: 13px;
            font-weight: 700;
        }

        .customer-info-notice,
        .customer-warning-notice {
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.6;
        }

        .customer-info-notice {
            margin-top: 25px;
            background: var(--soft-blue);
            color: var(--soft-blue-text);
            border: 1px solid var(--soft-blue-border);
        }

        .customer-warning-notice {
            margin-top: 15px;
            background: var(--warning-bg);
            color: var(--warning-text);
            border: 1px solid #fde68a;
            font-size: 13px;
        }

        html.dark .customer-warning-notice {
            border-color: #70561b;
        }

        @media (max-width: 900px) {
            .customer-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .customer-details-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 639px) {
            .customer-profile-page {
                padding-top: 24px !important;
                padding-bottom: 32px !important;
            }

            .customer-profile-container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .customer-profile-heading {
                align-items: flex-start;
            }

            .customer-profile-heading__icon {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
                border-radius: 11px;
            }

            .customer-profile-heading h2 {
                font-size: 18px;
            }

            .customer-profile-heading p {
                font-size: 12px;
            }

            .customer-profile-actions {
                align-items: stretch;
            }

            .customer-profile-actions,
            .customer-profile-actions form,
            .customer-action-btn {
                width: 100%;
            }

            .customer-summary-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .customer-summary-card {
                padding: 20px;
            }

            .customer-summary-value {
                font-size: 27px;
            }

            .customer-summary-value--status {
                font-size: 22px;
            }

            .customer-details-grid {
                gap: 14px;
                margin-top: 14px;
            }

            .customer-detail-card {
                padding: 18px;
                border-radius: 12px;
            }

            .customer-detail-card h3 {
                margin-bottom: 16px;
                font-size: 18px;
            }

            .customer-status {
                margin-top: 5px;
                margin-left: 0;
            }

            .customer-info-notice {
                margin-top: 14px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .customer-action-btn {
                transition: none;
            }
        }
    </style>

    <div class="customer-profile-page py-12">
        <div class="customer-profile-container">

            <div class="customer-profile-actions">
                <a
                    href="{{ route('admin.customers.index') }}"
                    class="customer-action-btn customer-action-back"
                >
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Back to Customers
                </a>

                @if($customer->is_suspended)
                    <form
                        method="POST"
                        action="{{ route('admin.customers.activate', $customer) }}"
                        onsubmit="return confirm('Reactivate this customer account? Internet access will not be restored automatically. A valid suspended subscription must be activated separately.');"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="customer-action-btn customer-action-activate"
                        >
                            <i class="fas fa-user-check" aria-hidden="true"></i>
                            Activate Customer
                        </button>
                    </form>
                @else
                    <form
                        method="POST"
                        action="{{ route('admin.customers.suspend', $customer) }}"
                        onsubmit="return confirm('Suspend this customer? Their active subscription will be suspended and associated MikroTik access will be disabled.');"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="customer-action-btn customer-action-suspend"
                        >
                            <i class="fas fa-user-slash" aria-hidden="true"></i>
                            Suspend Customer
                        </button>
                    </form>
                @endif
            </div>

            <div class="customer-summary-grid">
                <div class="customer-summary-card customer-summary-card--payments">
                    <h3>Total Payments</h3>

                    <p class="customer-summary-value">
                        {{ $totalPayments }}
                    </p>
                </div>

                <div class="customer-summary-card customer-summary-card--spend">
                    <h3>Total Completed Spend</h3>

                    <p class="customer-summary-value">
                        KES {{ number_format($totalSpent, 2) }}
                    </p>
                </div>

                <div class="customer-summary-card customer-summary-card--status">
                    <h3>Account Status</h3>

                    <p class="customer-summary-value customer-summary-value--status">
                        {{ $customer->is_suspended ? 'Suspended' : 'Active' }}
                    </p>
                </div>
            </div>

            <div class="customer-details-grid">

                <section class="customer-detail-card">
                    <h3>Customer Information</h3>

                    <div class="customer-detail-list">
                        <p class="customer-detail-row">
                            <strong>Name:</strong>
                            {{ $customer->name }}
                        </p>

                        <p class="customer-detail-row">
                            <strong>Email:</strong>
                            {{ $customer->email }}
                        </p>

                        <p class="customer-detail-row">
                            <strong>Status:</strong>

                            @if($customer->is_suspended)
                                <span class="customer-status customer-status--danger">
                                    Suspended
                                </span>
                            @else
                                <span class="customer-status customer-status--success">
                                    Active
                                </span>
                            @endif
                        </p>

                        <p class="customer-detail-row">
                            <strong>Joined:</strong>
                            {{ $customer->created_at?->format('d M Y') ?? 'N/A' }}
                        </p>
                    </div>
                </section>

                <section class="customer-detail-card">
                    <h3>Latest Payment</h3>

                    @if($lastPayment)
                        <div class="customer-detail-list">
                            <p class="customer-detail-row">
                                <strong>Amount:</strong>
                                KES {{ number_format($lastPayment->amount, 2) }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>Phone:</strong>
                                {{ $lastPayment->phone_number ?: 'N/A' }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>Status:</strong>

                                @if($lastPayment->status === 'completed')
                                    <span class="customer-status customer-status--success">
                                        Completed
                                    </span>
                                @elseif($lastPayment->status === 'pending')
                                    <span class="customer-status customer-status--warning">
                                        Pending
                                    </span>
                                @else
                                    <span class="customer-status customer-status--danger">
                                        Failed
                                    </span>
                                @endif
                            </p>

                            <p class="customer-detail-row">
                                <strong>Date:</strong>
                                {{ $lastPayment->created_at?->format('d M Y, H:i') ?? 'N/A' }}
                            </p>

                            @if($lastPayment->mpesa_receipt)
                                <p class="customer-detail-row">
                                    <strong>M-Pesa Receipt:</strong>
                                    {{ $lastPayment->mpesa_receipt }}
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="customer-detail-row customer-muted">
                            No payment found.
                        </p>
                    @endif
                </section>

                <section class="customer-detail-card">
                    <h3>Latest Subscription</h3>

                    @if($latestSubscription)
                        <div class="customer-detail-list">
                            <p class="customer-detail-row">
                                <strong>Package:</strong>
                                {{ $latestSubscription->package->name ?? 'N/A' }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>Price:</strong>
                                KES {{ number_format($latestSubscription->package->price ?? 0, 2) }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>Duration:</strong>
                                {{ $latestSubscription->package?->formatted_duration ?? 'N/A' }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>Status:</strong>

                                @if($latestSubscription->status === 'active')
                                    <span class="customer-status customer-status--success">
                                        Active
                                    </span>
                                @elseif($latestSubscription->status === 'suspended')
                                    <span class="customer-status customer-status--warning">
                                        Suspended
                                    </span>
                                @else
                                    <span class="customer-status customer-status--danger">
                                        Expired
                                    </span>
                                @endif
                            </p>

                            <p class="customer-detail-row">
                                <strong>Start:</strong>
                                {{ $latestSubscription->start_time?->format('d M Y, H:i') ?? 'N/A' }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>End:</strong>
                                {{ $latestSubscription->end_time?->format('d M Y, H:i') ?? 'N/A' }}
                            </p>
                        </div>

                        @if(
                            $latestSubscription->status === 'suspended' &&
                            $latestSubscription->end_time &&
                            $latestSubscription->end_time->isFuture()
                        )
                            <div class="customer-warning-notice">
                                This subscription is suspended but has not yet reached its end time.
                                After the customer account is reactivated, service must be restored
                                separately from Subscription Management.
                            </div>
                        @endif
                    @else
                        <p class="customer-detail-row customer-muted">
                            No subscription found.
                        </p>
                    @endif
                </section>

                <section class="customer-detail-card">
                    <h3>Network Access Account</h3>

                    @if($latestHotspotUser)
                        <div class="customer-detail-list">
                            <p class="customer-detail-row">
                                <strong>Username:</strong>
                                {{ $latestHotspotUser->username }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>Password:</strong>
                                <span class="customer-muted">Hidden for security</span>
                            </p>

                            <p class="customer-detail-row">
                                <strong>Profile:</strong>
                                {{ $latestHotspotUser->profile ?: 'Not Assigned' }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>Status:</strong>

                                @if($latestHotspotUser->status === 'active')
                                    <span class="customer-status customer-status--success">
                                        Active
                                    </span>
                                @else
                                    <span class="customer-status customer-status--danger">
                                        Inactive
                                    </span>
                                @endif
                            </p>

                            <p class="customer-detail-row">
                                <strong>Router:</strong>
                                {{ $latestHotspotUser->router->name ?? 'Not Assigned' }}
                            </p>

                            @if($latestHotspotUser->router)
                                <p class="customer-detail-row">
                                    <strong>Router IP:</strong>
                                    {{ $latestHotspotUser->router->ip_address }}
                                </p>
                            @endif

                            <p class="customer-detail-row">
                                <strong>Router Sync:</strong>

                                @if($latestHotspotUser->router_synced)
                                    <span class="customer-sync-success">
                                        Synchronized
                                    </span>
                                @else
                                    <span class="customer-sync-danger">
                                        Not Synchronized
                                    </span>
                                @endif
                            </p>

                            <p class="customer-detail-row">
                                <strong>Last Sync Status:</strong>
                                {{ $latestHotspotUser->last_sync_status ?? 'Pending' }}
                            </p>

                            <p class="customer-detail-row">
                                <strong>Sync Message:</strong>
                                {{ $latestHotspotUser->last_sync_message ?? 'Waiting for router synchronization.' }}
                            </p>
                        </div>
                    @else
                        <p class="customer-detail-row customer-muted">
                            No network access account found.
                        </p>

                        @php
                            $canProvisionNetworkAccess =
                                !$customer->is_suspended &&
                                $latestSubscription &&
                                $latestSubscription->status === 'active' &&
                                $latestSubscription->start_time &&
                                $latestSubscription->start_time->lte(now()) &&
                                $latestSubscription->end_time &&
                                $latestSubscription->end_time->isFuture() &&
                                $latestSubscription->package &&
                                $latestSubscription->package->is_active;
                        @endphp

                        @if($canProvisionNetworkAccess)
                            @if($activeRouters->isNotEmpty())
                                <form
                                    method="POST"
                                    action="{{ route('admin.customers.network-access', $customer) }}"
                                    class="customer-provision-form"
                                    onsubmit="return confirm('Provision this customer network access on the selected MikroTik router?');"
                                >
                                    @csrf

                                    <label
                                        for="router_id"
                                        class="customer-provision-label"
                                    >
                                        MikroTik Router
                                    </label>

                                    <select
                                        id="router_id"
                                        name="router_id"
                                        class="customer-provision-select"
                                        required
                                    >
                                        <option value="">Select an active router</option>

                                        @foreach($activeRouters as $router)
                                            <option
                                                value="{{ $router->id }}"
                                                @selected((string) old('router_id') === (string) $router->id)
                                            >
                                                {{ $router->name }} ({{ $router->ip_address }})
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('router_id')
                                        <p class="customer-validation-error">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                    <button
                                        type="submit"
                                        class="customer-action-btn customer-action-provision"
                                    >
                                        <i class="fas fa-network-wired" aria-hidden="true"></i>
                                        Provision Network Access
                                    </button>
                                </form>
                            @else
                                <div class="customer-warning-notice">
                                    No active MikroTik router is available. Add or activate a router before provisioning network access.
                                </div>
                            @endif
                        @elseif($customer->is_suspended)
                            <div class="customer-warning-notice">
                                Reactivate this customer before provisioning network access.
                            </div>
                        @else
                            <div class="customer-warning-notice">
                                A currently valid active subscription is required before network access can be provisioned.
                            </div>
                        @endif
                    @endif
                </section>

            </div>

            <div class="customer-info-notice">
                <strong>Security:</strong>
                Stored network passwords are never displayed on this page. If a customer
                requires new access credentials, use the secure password reset function
                from Hotspot User Management.
            </div>

        </div>
    </div>
</x-app-layout>