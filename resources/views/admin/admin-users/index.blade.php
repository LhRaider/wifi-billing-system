<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Accounts
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:1100px;margin:0 auto;background:white;padding:28px;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">

            @if(session('success'))
                <div style="background:#dcfce7;color:#166534;padding:14px;border-radius:10px;margin-bottom:15px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background:#fee2e2;color:#991b1b;padding:14px;border-radius:10px;margin-bottom:15px;">
                    {{ session('error') }}
                </div>
            @endif

            <h3 style="font-size:26px;font-weight:800;margin-bottom:20px;">
                <i class="fas fa-user-shield" style="color:#2563eb;"></i>
                Admin Management
            </h3>

            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="border:1px solid #e5e7eb;padding:12px;">Name</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Email</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Role</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Status</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($admins as $admin)
                        <tr>
                            <td style="border:1px solid #e5e7eb;padding:12px;">{{ $admin->name }}</td>
                            <td style="border:1px solid #e5e7eb;padding:12px;">{{ $admin->email }}</td>
                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                @if($admin->role === 'super_admin')
                                    <span style="background:#fef3c7;color:#92400e;padding:6px 12px;border-radius:20px;font-weight:800;">
                                        Super Admin
                                    </span>
                                @else
                                    <span style="background:#dbeafe;color:#1d4ed8;padding:6px 12px;border-radius:20px;font-weight:800;">
                                        Admin
                                    </span>
                                @endif
                            </td>
                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                @if($admin->is_suspended)
                                    <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:800;">
                                        Deactivated
                                    </span>
                                @else
                                    <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:800;">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                @if($admin->role !== 'super_admin')
                                    @if($admin->is_suspended)
                                        <form method="POST" action="{{ route('admin.admin-users.activate', $admin) }}" style="display:inline;">
                                            @csrf
                                            <button style="background:#16a34a;color:white;padding:8px 12px;border:none;border-radius:8px;">
                                                Activate
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.admin-users.deactivate', $admin) }}" style="display:inline;">
                                            @csrf
                                            <button style="background:#dc2626;color:white;padding:8px 12px;border:none;border-radius:8px;">
                                                Deactivate
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    Protected
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:20px;">
                {{ $admins->links() }}
            </div>

        </div>
    </div>
</x-app-layout>