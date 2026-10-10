<x-app-layout>
    <x-slot name="header">
        <div class="hu-header">
            <div class="hu-header-icon">
                <i class="fas fa-wifi"></i>
            </div>
            <div>
                <h2>Hotspot Users</h2>
                <p>Manage customer MikroTik access credentials.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .hu-page {
            --bg: #f3f4f6;
            --card: #ffffff;
            --text: #111827;
            --muted: #64748b;
            --border: #e2e8f0;
            --thead: #f8fafc;
            --input: #ffffff;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
        }

        html.dark .hu-page {
            --bg: #091226;
            --card: #101c33;
            --text: #e8eeff;
            --muted: #94a3b8;
            --border: #293a59;
            --thead: #15233e;
            --input: #0b172b;
        }

        .hu-header {
            display:flex;
            align-items:center;
            gap:14px;
        }

        .hu-header-icon {
            width:46px;
            height:46px;
            display:grid;
            place-items:center;
            border-radius:13px;
            background:#2563eb;
            color:#fff;
        }

        .hu-header h2 {
            margin:0;
            color:#111827;
            font-size:20px;
            font-weight:800;
        }

        .hu-header p {
            margin:4px 0 0;
            color:#64748b;
            font-size:13px;
        }

        html.dark .hu-header h2 { color:#f8fafc; }
        html.dark .hu-header p { color:#94a3b8; }

        .hu-card {
            padding:25px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:16px;
            box-shadow:0 6px 20px rgba(15,23,42,.08);
        }

        .hu-top {
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            flex-wrap:wrap;
            margin-bottom:20px;
        }

        .hu-title {
            margin:0;
            color:var(--text);
            font-size:22px;
            font-weight:800;
        }

        .hu-records {
            padding:8px 14px;
            border-radius:999px;
            background:#dbeafe;
            color:#1d4ed8;
            font-weight:800;
        }

        html.dark .hu-records {
            background:#172d55;
            color:#93c5fd;
        }

        .hu-search {
            display:flex;
            gap:10px;
            margin-bottom:20px;
        }

        .hu-search input {
            flex:1;
            min-width:0;
            padding:12px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--input);
            color:var(--text);
            outline:none;
        }

        .hu-search input::placeholder {
            color:var(--muted);
        }

        .hu-search input:focus {
            border-color:#3b82f6;
            box-shadow:0 0 0 3px rgba(59,130,246,.14);
        }

        .hu-search button {
            padding:12px 20px;
            border:0;
            border-radius:8px;
            background:#2563eb;
            color:#fff;
            cursor:pointer;
            font-weight:700;
        }

        .hu-table-wrap {
            overflow-x:auto;
        }

        .hu-table {
            width:100%;
            min-width:1000px;
            border-collapse:collapse;
        }

        .hu-table th,
        .hu-table td {
            padding:12px;
            border:1px solid var(--border);
            text-align:left;
            color:var(--text);
            vertical-align:middle;
        }

        .hu-table th {
            background:var(--thead);
            font-weight:800;
        }

        .hu-table tbody tr {
            background:var(--card);
        }

        .hu-table tbody tr:hover {
            background:rgba(59,130,246,.04);
        }

        html.dark .hu-table tbody tr:hover {
            background:#14223d;
        }

        .hu-muted {
            color:var(--muted);
            font-size:12px;
        }

        .hu-badge {
            display:inline-block;
            padding:6px 12px;
            border-radius:999px;
            font-size:13px;
            font-weight:800;
        }

        .hu-active,
        .hu-synced {
            background:#dcfce7;
            color:#166534;
        }

        .hu-inactive {
            background:#fee2e2;
            color:#991b1b;
        }

        .hu-pending {
            background:#fef3c7;
            color:#92400e;
        }

        html.dark .hu-active,
        html.dark .hu-synced {
            background:#123c2a;
            color:#86efac;
        }

        html.dark .hu-inactive {
            background:#451d29;
            color:#fda4af;
        }

        html.dark .hu-pending {
            background:#403414;
            color:#fde68a;
        }

        .hu-actions {
            display:flex;
            gap:8px;
            flex-wrap:wrap;
        }

        .hu-actions form {
            margin:0;
        }

        .hu-btn {
            padding:8px 13px;
            border:0;
            border-radius:7px;
            color:#fff;
            cursor:pointer;
            font-weight:700;
        }

        .hu-green { background:#16a34a; }
        .hu-red { background:#dc2626; }
        .hu-blue { background:#2563eb; }
        .hu-purple { background:#7c3aed; }

        .hu-pagination {
            margin-top:20px;
            color:var(--text);
        }

        @media(max-width:640px) {
            .hu-page { padding-top:24px !important; }
            .hu-card { padding:16px; }
            .hu-search { flex-direction:column; }
            .hu-search button { width:100%; }
        }
    </style>

    <div class="hu-page py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="hu-card">

                <div class="hu-top">
                    <h3 class="hu-title">Registered Hotspot Users</h3>

                    <span class="hu-records">
                        {{ $hotspotUsers->total() }} Records
                    </span>
                </div>

                <form
                    method="GET"
                    action="{{ route('admin.hotspot-users.index') }}"
                    class="hu-search"
                >
                    <input
                        type="text"
                        name="search"
                        maxlength="100"
                        value="{{ $search ?? '' }}"
                        placeholder="Search username, customer, email, router, or IP..."
                    >

                    <button type="submit">Search</button>
                </form>

                <div class="hu-table-wrap">
                    <table class="hu-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Username</th>
                                <th>Package</th>
                                <th>Router</th>
                                <th>Status</th>
                                <th>Sync</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($hotspotUsers as $hotspotUser)
                                <tr>
                                    <td>
                                        <strong>{{ $hotspotUser->user->name ?? 'N/A' }}</strong>
                                        <br>
                                        <span class="hu-muted">
                                            {{ $hotspotUser->user->email ?? '' }}
                                        </span>
                                    </td>

                                    <td>
                                        <strong>{{ $hotspotUser->username }}</strong>
                                    </td>

                                    <td>
                                        {{ $hotspotUser->profile }}
                                    </td>

                                    <td>
                                        @if($hotspotUser->router)
                                            <strong>{{ $hotspotUser->router->name }}</strong>
                                            <br>
                                            <span class="hu-muted">
                                                {{ $hotspotUser->router->ip_address }}
                                            </span>
                                        @else
                                            <span class="hu-muted">Not Assigned</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($hotspotUser->status === 'active')
                                            <span class="hu-badge hu-active">Active</span>
                                        @else
                                            <span class="hu-badge hu-inactive">Inactive</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($hotspotUser->router_synced)
                                            <span class="hu-badge hu-synced">Synced</span>
                                        @else
                                            <span class="hu-badge hu-pending">Pending</span>
                                        @endif

                                        <br>

                                        <span class="hu-muted">
                                            {{ $hotspotUser->last_sync_status ?? 'Not synced yet' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="hu-actions">

                                            @if($hotspotUser->status === 'active')
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.hotspot-users.deactivate', $hotspotUser->id) }}"
                                                >
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="hu-btn hu-red"
                                                    >
                                                        Deactivate
                                                    </button>
                                                </form>
                                            @else
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.hotspot-users.activate', $hotspotUser->id) }}"
                                                >
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="hu-btn hu-green"
                                                    >
                                                        Activate
                                                    </button>
                                                </form>
                                            @endif

                                            <form
                                                method="POST"
                                                action="{{ route('admin.hotspot-users.reset-password', $hotspotUser->id) }}"
                                                onsubmit="return confirm('Reset this hotspot password?');"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="hu-btn hu-blue"
                                                >
                                                    Reset Password
                                                </button>
                                            </form>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.hotspot-users.sync-router', $hotspotUser->id) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="hu-btn hu-purple"
                                                >
                                                    Sync
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:30px;">
                                        No hotspot users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="hu-pagination">
                    {{ $hotspotUsers->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>