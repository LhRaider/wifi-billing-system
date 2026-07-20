<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hotspot Users
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="background:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <h3 style="font-size:22px;font-weight:bold;">
                        Registered Hotspot Users
                    </h3>

                    <span style="background:#dbeafe;color:#1d4ed8;padding:8px 14px;border-radius:20px;font-weight:bold;">
                        {{ $hotspotUsers->total() }} Records
                    </span>
                </div>

                <form method="GET" action="{{ route('admin.hotspot-users.index') }}" style="margin-bottom:20px;display:flex;gap:10px;">
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Search username, customer, email, or package..."
                           style="flex:1;padding:12px;border:1px solid #ddd;border-radius:8px;">

                    <button type="submit"
                            style="background:#2563eb;color:white;padding:12px 20px;border:none;border-radius:8px;cursor:pointer;">
                        Search
                    </button>
                </form>

                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="border:1px solid #e5e7eb;padding:12px;">Customer</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Username</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Password</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Package</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Router</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Status</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Sync</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($hotspotUsers as $hotspotUser)
                            <tr>
                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    <strong>{{ $hotspotUser->user->name ?? 'N/A' }}</strong><br>
                                    <small style="color:#6b7280;">{{ $hotspotUser->user->email ?? '' }}</small>
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;font-weight:bold;">
                                    {{ $hotspotUser->username }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $hotspotUser->password }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $hotspotUser->profile }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $hotspotUser->router->name ?? 'Not Assigned' }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    @if($hotspotUser->status === 'active')
                                        <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:13px;">
                                            🟢 Active
                                        </span>
                                    @else
                                        <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:13px;">
                                            🔴 Inactive
                                        </span>
                                    @endif
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    @if($hotspotUser->router_synced)
                                        <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:13px;">
                                            Synced
                                        </span>
                                    @else
                                        <span style="background:#fef3c7;color:#92400e;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:13px;">
                                            Pending
                                        </span>
                                    @endif

                                    <br>
                                    <small style="color:#6b7280;">
                                        {{ $hotspotUser->last_sync_status ?? 'Not synced yet' }}
                                    </small>
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">

                                        @if($hotspotUser->status === 'active')
                                            <form method="POST" action="{{ route('admin.hotspot-users.deactivate', $hotspotUser->id) }}">
                                                @csrf
                                                <button type="submit"
                                                    style="background:#dc2626;color:white;padding:7px 12px;border:none;border-radius:6px;cursor:pointer;">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.hotspot-users.activate', $hotspotUser->id) }}">
                                                @csrf
                                                <button type="submit"
                                                    style="background:#16a34a;color:white;padding:7px 12px;border:none;border-radius:6px;cursor:pointer;">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.hotspot-users.reset-password', $hotspotUser->id) }}">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Reset hotspot password?')"
                                                style="background:#2563eb;color:white;padding:7px 12px;border:none;border-radius:6px;cursor:pointer;">
                                                Reset
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.hotspot-users.sync-router', $hotspotUser->id) }}">
                                            @csrf
                                            <button type="submit"
                                                style="background:#7c3aed;color:white;padding:7px 12px;border:none;border-radius:6px;cursor:pointer;">
                                                Sync
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:20px;">
                    {{ $hotspotUsers->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>