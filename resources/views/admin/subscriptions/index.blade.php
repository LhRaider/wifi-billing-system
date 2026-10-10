<x-app-layout>
    <x-slot name="header">
        <div class="subs-header">
            <div class="subs-header-icon">
                <i class="fas fa-arrows-rotate"></i>
            </div>
            <div>
                <h2>Subscription Management</h2>
                <p>Manage customer subscription status and MikroTik access.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .subs-page {
            --bg:#f3f4f6;
            --card:#ffffff;
            --text:#111827;
            --muted:#64748b;
            --border:#e2e8f0;
            --thead:#f8fafc;
            --input:#ffffff;
            min-height:100vh;
            background:var(--bg);
            color:var(--text);
        }

        html.dark .subs-page {
            --bg:#091226;
            --card:#101c33;
            --text:#e8eeff;
            --muted:#94a3b8;
            --border:#293a59;
            --thead:#15233e;
            --input:#0b172b;
        }

        .subs-header {
            display:flex;
            align-items:center;
            gap:14px;
        }

        .subs-header-icon {
            width:46px;
            height:46px;
            display:grid;
            place-items:center;
            border-radius:13px;
            background:#2563eb;
            color:white;
        }

        .subs-header h2 {
            margin:0;
            color:#111827;
            font-size:20px;
            font-weight:800;
        }

        .subs-header p {
            margin:4px 0 0;
            color:#64748b;
            font-size:13px;
        }

        html.dark .subs-header h2 { color:#f8fafc; }
        html.dark .subs-header p { color:#94a3b8; }

        .subs-card {
            padding:25px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:16px;
            box-shadow:0 6px 20px rgba(15,23,42,.08);
        }

        .subs-top {
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            flex-wrap:wrap;
            margin-bottom:20px;
        }

        .subs-title {
            margin:0;
            color:var(--text);
            font-size:22px;
            font-weight:800;
        }

        .subs-subtitle {
            margin:6px 0 0;
            color:var(--muted);
            font-size:14px;
        }

        .subs-count {
            padding:8px 14px;
            border-radius:999px;
            background:#dbeafe;
            color:#1d4ed8;
            font-weight:800;
        }

        html.dark .subs-count {
            background:#172d55;
            color:#93c5fd;
        }

        .subs-search {
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            margin-bottom:20px;
        }

        .subs-search input {
            flex:1;
            min-width:240px;
            padding:12px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--input);
            color:var(--text);
            outline:none;
        }

        .subs-search input::placeholder {
            color:var(--muted);
        }

        .subs-search input:focus {
            border-color:#3b82f6;
            box-shadow:0 0 0 3px rgba(59,130,246,.14);
        }

        .subs-search-btn,
        .subs-clear-btn {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:12px 20px;
            border-radius:8px;
            border:0;
            text-decoration:none;
            cursor:pointer;
            font-weight:700;
        }

        .subs-search-btn {
            background:#2563eb;
            color:#fff;
        }

        .subs-clear-btn {
            background:#e5e7eb;
            color:#374151;
        }

        html.dark .subs-clear-btn {
            background:#263753;
            color:#e2e8f0;
        }

        .subs-table-wrap {
            overflow-x:auto;
        }

        .subs-table {
            width:100%;
            min-width:950px;
            border-collapse:collapse;
        }

        .subs-table th,
        .subs-table td {
            padding:12px;
            border:1px solid var(--border);
            text-align:left;
            vertical-align:top;
            color:var(--text);
        }

        .subs-table th {
            background:var(--thead);
            font-weight:800;
        }

        .subs-table tbody tr {
            background:var(--card);
        }

        .subs-table tbody tr:hover {
            background:rgba(59,130,246,.04);
        }

        html.dark .subs-table tbody tr:hover {
            background:#14223d;
        }

        .subs-muted {
            color:var(--muted);
            font-size:12px;
        }

        .subs-ended {
            color:#dc2626;
            font-size:12px;
            font-weight:700;
        }

        html.dark .subs-ended {
            color:#fda4af;
        }

        .subs-status {
            display:inline-block;
            padding:6px 12px;
            border-radius:999px;
            font-weight:700;
        }

        .subs-active {
            background:#dcfce7;
            color:#166534;
        }

        .subs-suspended {
            background:#fef3c7;
            color:#92400e;
        }

        .subs-expired {
            background:#fee2e2;
            color:#991b1b;
        }

        .subs-other {
            background:#e5e7eb;
            color:#374151;
        }

        html.dark .subs-active {
            background:#123c2a;
            color:#86efac;
        }

        html.dark .subs-suspended {
            background:#403414;
            color:#fde68a;
        }

        html.dark .subs-expired {
            background:#451d29;
            color:#fda4af;
        }

        html.dark .subs-other {
            background:#263753;
            color:#e2e8f0;
        }

        .subs-actions {
            display:flex;
            gap:8px;
            flex-wrap:wrap;
        }

        .subs-actions form {
            margin:0;
        }

        .subs-btn {
            padding:8px 14px;
            border:0;
            border-radius:8px;
            color:#fff;
            cursor:pointer;
            font-weight:700;
        }

        .subs-btn-expire { background:#dc2626; }
        .subs-btn-activate { background:#16a34a; }

        .subs-cannot {
            display:inline-block;
            padding:8px 12px;
            background:#f3f4f6;
            color:#6b7280;
            border-radius:8px;
            font-size:13px;
        }

        html.dark .subs-cannot {
            background:#263753;
            color:#cbd5e1;
        }

        .subs-note {
            margin-top:24px;
            padding:14px 16px;
            background:#eff6ff;
            color:#1e3a8a;
            border:1px solid #bfdbfe;
            border-radius:10px;
            font-size:14px;
            line-height:1.6;
        }

        html.dark .subs-note {
            background:#10264b;
            color:#bfdbfe;
            border-color:#264d83;
        }

        .subs-pagination {
            margin-top:20px;
            color:var(--text);
        }

        @media(max-width:640px) {
            .subs-page { padding-top:24px !important; }
            .subs-card { padding:16px; }
            .subs-search { flex-direction:column; }
            .subs-search input { min-width:0; width:100%; }
            .subs-search-btn,
            .subs-clear-btn { width:100%; }
        }
    </style>

    <div class="subs-page py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="subs-card">

                <div class="subs-top">
                    <div>
                        <h3 class="subs-title">All Subscriptions</h3>
                        <p class="subs-subtitle">
                            Manage customer subscription status and MikroTik access.
                        </p>
                    </div>

                    <span class="subs-count">
                        {{ $subscriptions->total() }} Records
                    </span>
                </div>

                <form
                    method="GET"
                    action="{{ route('admin.subscriptions.index') }}"
                    class="subs-search"
                >
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        maxlength="100"
                        placeholder="Search customer, email, package, or status..."
                    >

                    <button type="submit" class="subs-search-btn">
                        Search
                    </button>

                    @if($search ?? false)
                        <a
                            href="{{ route('admin.subscriptions.index') }}"
                            class="subs-clear-btn"
                        >
                            Clear
                        </a>
                    @endif
                </form>

                <div class="subs-table-wrap">
                    <table class="subs-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Package</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $subscription->user->name ?? 'Unknown Customer' }}
                                        </strong>

                                        <br>

                                        <span class="subs-muted">
                                            {{ $subscription->user->email ?? 'No email available' }}
                                        </span>
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $subscription->package->name ?? 'Unknown Package' }}
                                        </strong>

                                        @if($subscription->package)
                                            <br>

                                            <span class="subs-muted">
                                                {{ strtoupper($subscription->package->package_type ?? 'hotspot') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($subscription->start_time)
                                            {{ $subscription->start_time->format('d M Y, H:i') }}
                                        @else
                                            <span class="subs-muted">Not available</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($subscription->end_time)
                                            {{ $subscription->end_time->format('d M Y, H:i') }}

                                            @if(
                                                $subscription->status === 'active' &&
                                                $subscription->end_time->isPast()
                                            )
                                                <br>
                                                <span class="subs-ended">
                                                    End time has passed
                                                </span>
                                            @endif
                                        @else
                                            <span class="subs-muted">Not available</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($subscription->status === 'active')
                                            <span class="subs-status subs-active">
                                                Active
                                            </span>

                                        @elseif($subscription->status === 'suspended')
                                            <span class="subs-status subs-suspended">
                                                Suspended
                                            </span>

                                        @elseif($subscription->status === 'expired')
                                            <span class="subs-status subs-expired">
                                                Expired
                                            </span>

                                        @else
                                            <span class="subs-status subs-other">
                                                {{ ucfirst((string) $subscription->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="subs-actions">

                                            @if($subscription->status === 'active')

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.subscriptions.expire', $subscription) }}"
                                                    onsubmit="return confirm('Expire this subscription and disable its associated MikroTik access?');"
                                                >
                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="subs-btn subs-btn-expire"
                                                    >
                                                        Expire
                                                    </button>
                                                </form>

                                            @else

                                                @php
                                                    $canActivate =
                                                        $subscription->end_time &&
                                                        $subscription->end_time->isFuture() &&
                                                        (
                                                            !$subscription->start_time ||
                                                            $subscription->start_time->lte(now())
                                                        ) &&
                                                        $subscription->package &&
                                                        $subscription->package->is_active;
                                                @endphp

                                                @if($canActivate)
                                                    <form
                                                        method="POST"
                                                        action="{{ route('admin.subscriptions.activate', $subscription) }}"
                                                        onsubmit="return confirm('Activate this subscription and enable the customer MikroTik access?');"
                                                    >
                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="subs-btn subs-btn-activate"
                                                        >
                                                            Activate
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="subs-cannot">
                                                        Cannot activate
                                                    </span>
                                                @endif

                                            @endif

                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center;padding:30px;">
                                        No subscriptions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="subs-pagination">
                    {{ $subscriptions->links() }}
                </div>

                <div class="subs-note">
                    Expiring a subscription removes the customer's local service
                    entitlement immediately. If the router is temporarily unreachable,
                    the system records the synchronization failure so the MikroTik state
                    can be checked safely.
                </div>

            </div>
        </div>
    </div>
</x-app-layout>