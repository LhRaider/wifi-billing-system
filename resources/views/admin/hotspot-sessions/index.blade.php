<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Session Monitoring
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="background:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <h3 style="font-size:22px;font-weight:bold;">Hotspot Sessions</h3>

                    <span style="background:#dbeafe;color:#1d4ed8;padding:8px 14px;border-radius:20px;font-weight:bold;">
                        {{ $sessions->total() }} Records
                    </span>
                </div>

                <form method="GET" action="{{ route('admin.hotspot-sessions.index') }}" style="margin-bottom:20px;display:flex;gap:10px;">
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Search customer, email, IP, or MAC..."
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
                            <th style="border:1px solid #e5e7eb;padding:12px;">IP Address</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">MAC Address</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Login Time</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Logout Time</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Status</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Data Used</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($sessions as $session)
                            <tr>
                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    <strong>{{ $session->user->name ?? 'N/A' }}</strong><br>
                                    <small style="color:#6b7280;">{{ $session->user->email ?? '' }}</small>
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $session->ip_address ?? 'N/A' }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $session->mac_address ?? 'N/A' }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $session->login_time ? $session->login_time->format('d M Y H:i') : 'N/A' }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $session->logout_time ? $session->logout_time->format('d M Y H:i') : 'Still Online' }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    @if(is_null($session->logout_time))
                                        <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:13px;">
                                            Online
                                        </span>
                                    @else
                                        <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:13px;">
                                            Offline
                                        </span>
                                    @endif
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ number_format($session->data_used / 1024 / 1024, 2) }} MB
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:20px;">
                    {{ $sessions->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>