<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Audit Logs
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:1200px;margin:0 auto;background:white;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,0.08);padding:25px;border:1px solid #e5e7eb;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
                <div>
                    <h3 style="font-size:24px;font-weight:800;color:#111827;margin:0;">
                        <i class="fas fa-shield-alt" style="color:#2563eb;"></i>
                        System Activity Logs
                    </h3>
                    <p style="color:#6b7280;margin-top:5px;">
                        Track important admin and system actions.
                    </p>
                </div>

                <span style="background:#dbeafe;color:#1d4ed8;padding:8px 14px;border-radius:20px;font-weight:bold;">
                    {{ $logs->total() }} Records
                </span>
            </div>

            <form method="GET" action="{{ route('admin.audit-logs.index') }}" style="display:flex;gap:10px;margin-bottom:25px;">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Search action, module, user, or description..."
                       style="flex:1;padding:12px;border:1px solid #d1d5db;border-radius:8px;">

                <button type="submit"
                        style="background:#2563eb;color:white;padding:12px 20px;border:none;border-radius:8px;font-weight:700;cursor:pointer;">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </form>

            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:12px;border:1px solid #e5e7eb;text-align:left;">User</th>
                        <th style="padding:12px;border:1px solid #e5e7eb;text-align:left;">Action</th>
                        <th style="padding:12px;border:1px solid #e5e7eb;text-align:left;">Module</th>
                        <th style="padding:12px;border:1px solid #e5e7eb;text-align:left;">Description</th>
                        <th style="padding:12px;border:1px solid #e5e7eb;text-align:left;">IP Address</th>
                        <th style="padding:12px;border:1px solid #e5e7eb;text-align:left;">Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="padding:12px;border:1px solid #e5e7eb;">
                                <strong>{{ $log->user->name ?? 'System' }}</strong><br>
                                <small style="color:#6b7280;">{{ $log->user->email ?? '' }}</small>
                            </td>

                            <td style="padding:12px;border:1px solid #e5e7eb;">
                                <span style="background:#dcfce7;color:#166534;padding:6px 10px;border-radius:20px;font-weight:700;font-size:13px;">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td style="padding:12px;border:1px solid #e5e7eb;">
                                {{ $log->module ?? 'General' }}
                            </td>

                            <td style="padding:12px;border:1px solid #e5e7eb;">
                                {{ $log->description ?? 'No description' }}
                            </td>

                            <td style="padding:12px;border:1px solid #e5e7eb;">
                                {{ $log->ip_address ?? 'N/A' }}
                            </td>

                            <td style="padding:12px;border:1px solid #e5e7eb;">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:20px;text-align:center;color:#6b7280;border:1px solid #e5e7eb;">
                                No audit logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:20px;">
                {{ $logs->links() }}
            </div>

        </div>
    </div>
</x-app-layout>