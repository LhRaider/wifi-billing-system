<x-app-layout>
    <x-slot name="header">
        <div class="sessions-header">
            <div class="sessions-header-icon">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <h2>Session Monitoring</h2>
                <p>Monitor customer Hotspot connection activity.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .sessions-page {
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

        html.dark .sessions-page {
            --bg:#091226;
            --card:#101c33;
            --text:#e8eeff;
            --muted:#94a3b8;
            --border:#293a59;
            --thead:#15233e;
            --input:#0b172b;
        }

        .sessions-header {
            display:flex;
            align-items:center;
            gap:14px;
        }

        .sessions-header-icon {
            width:46px;
            height:46px;
            display:grid;
            place-items:center;
            border-radius:13px;
            background:#2563eb;
            color:white;
        }

        .sessions-header h2 {
            margin:0;
            color:#111827;
            font-size:20px;
            font-weight:800;
        }

        .sessions-header p {
            margin:4px 0 0;
            color:#64748b;
            font-size:13px;
        }

        html.dark .sessions-header h2 { color:#f8fafc; }
        html.dark .sessions-header p { color:#94a3b8; }

        .sessions-card {
            padding:25px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:16px;
            box-shadow:0 6px 20px rgba(15,23,42,.08);
        }

        .sessions-top {
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            flex-wrap:wrap;
            margin-bottom:20px;
        }

        .sessions-title {
            margin:0;
            color:var(--text);
            font-size:22px;
            font-weight:800;
        }

        .sessions-count {
            padding:8px 14px;
            border-radius:999px;
            background:#dbeafe;
            color:#1d4ed8;
            font-weight:800;
        }

        html.dark .sessions-count {
            background:#172d55;
            color:#93c5fd;
        }

        .sessions-search {
            display:flex;
            gap:10px;
            margin-bottom:20px;
        }

        .sessions-search input {
            flex:1;
            min-width:0;
            padding:12px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--input);
            color:var(--text);
            outline:none;
        }

        .sessions-search input::placeholder {
            color:var(--muted);
        }

        .sessions-search input:focus {
            border-color:#3b82f6;
            box-shadow:0 0 0 3px rgba(59,130,246,.14);
        }

        .sessions-search button {
            padding:12px 20px;
            border:0;
            border-radius:8px;
            background:#2563eb;
            color:#fff;
            cursor:pointer;
            font-weight:700;
        }

        .sessions-table-wrap {
            overflow-x:auto;
        }

        .sessions-table {
            width:100%;
            min-width:1000px;
            border-collapse:collapse;
        }

        .sessions-table th,
        .sessions-table td {
            padding:12px;
            border:1px solid var(--border);
            color:var(--text);
            text-align:left;
            vertical-align:middle;
        }

        .sessions-table th {
            background:var(--thead);
            font-weight:800;
        }

        .sessions-table tbody tr {
            background:var(--card);
        }

        .sessions-table tbody tr:hover {
            background:rgba(59,130,246,.04);
        }

        html.dark .sessions-table tbody tr:hover {
            background:#14223d;
        }

        .session-muted {
            color:var(--muted);
            font-size:12px;
        }

        .session-status {
            display:inline-block;
            padding:6px 12px;
            border-radius:999px;
            font-size:13px;
            font-weight:800;
        }

        .session-online {
            background:#dcfce7;
            color:#166534;
        }

        .session-offline {
            background:#fee2e2;
            color:#991b1b;
        }

        html.dark .session-online {
            background:#123c2a;
            color:#86efac;
        }

        html.dark .session-offline {
            background:#451d29;
            color:#fda4af;
        }

        .sessions-pagination {
            margin-top:20px;
            color:var(--text);
        }

        @media(max-width:640px) {
            .sessions-page { padding-top:24px !important; }
            .sessions-card { padding:16px; }
            .sessions-search { flex-direction:column; }
            .sessions-search button { width:100%; }
        }
    </style>

    <div class="sessions-page py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="sessions-card">

                <div class="sessions-top">
                    <h3 class="sessions-title">Hotspot Sessions</h3>

                    <span class="sessions-count">
                        {{ $sessions->total() }} Records
                    </span>
                </div>

                <form
                    method="GET"
                    action="{{ route('admin.hotspot-sessions.index') }}"
                    class="sessions-search"
                >
                    <input
                        type="text"
                        name="search"
                        maxlength="100"
                        value="{{ $search ?? '' }}"
                        placeholder="Search customer, email, router, IP, or MAC..."
                    >

                    <button type="submit">Search</button>
                </form>

                <div class="sessions-table-wrap">
                    <table class="sessions-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Router</th>
                                <th>IP Address</th>
                                <th>MAC Address</th>
                                <th>Login Time</th>
                                <th>Logout Time</th>
                                <th>Status</th>
                                <th>Data Used</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td>
                                        <strong>{{ $session->user->name ?? 'N/A' }}</strong>
                                        <br>
                                        <span class="session-muted">
                                            {{ $session->user->email ?? '' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($session->router)
                                            <strong>{{ $session->router->name }}</strong>
                                            <br>
                                            <span class="session-muted">
                                                {{ $session->router->ip_address }}
                                            </span>
                                        @else
                                            <span class="session-muted">Not Recorded</span>
                                        @endif
                                    </td>

                                    <td>{{ $session->ip_address ?? 'N/A' }}</td>

                                    <td>{{ $session->mac_address ?? 'N/A' }}</td>

                                    <td>
                                        {{ $session->login_time
                                            ? $session->login_time->format('d M Y H:i')
                                            : 'N/A'
                                        }}
                                    </td>

                                    <td>
                                        {{ $session->logout_time
                                            ? $session->logout_time->format('d M Y H:i')
                                            : 'Still Online'
                                        }}
                                    </td>

                                    <td>
                                        @if(is_null($session->logout_time))
                                            <span class="session-status session-online">
                                                Online
                                            </span>
                                        @else
                                            <span class="session-status session-offline">
                                                Offline
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ number_format(($session->data_used ?? 0) / 1024 / 1024, 2) }} MB
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="8" style="text-align:center;padding:30px;">
                                        No hotspot sessions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="sessions-pagination">
                    {{ $sessions->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>