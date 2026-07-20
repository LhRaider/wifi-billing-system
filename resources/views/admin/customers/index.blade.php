<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customer Management
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="background:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                <form method="GET" action="{{ route('admin.customers.index') }}" style="margin-bottom:20px;display:flex;gap:10px;">
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Search customer by name or email..."
                           style="flex:1;padding:12px;border:1px solid #ddd;border-radius:8px;">

                    <button type="submit"
                            style="background:#2563eb;color:white;padding:12px 20px;border:none;border-radius:8px;">
                        Search
                    </button>
                </form>

                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="border:1px solid #ddd;padding:12px;">Name</th>
                            <th style="border:1px solid #ddd;padding:12px;">Email</th>
                            <th style="border:1px solid #ddd;padding:12px;">Status</th>
                            <th style="border:1px solid #ddd;padding:12px;">Joined</th>
                            <th style="border:1px solid #ddd;padding:12px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td style="border:1px solid #ddd;padding:12px;font-weight:bold;">
                                    {{ $customer->name }}
                                </td>

                                <td style="border:1px solid #ddd;padding:12px;">
                                    {{ $customer->email }}
                                </td>

                                <td style="border:1px solid #ddd;padding:12px;">
                                    @if($customer->is_suspended)
                                        <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:13px;">
                                            🔴 Suspended
                                        </span>
                                    @else
                                        <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:13px;">
                                            🟢 Active
                                        </span>
                                    @endif
                                </td>

                                <td style="border:1px solid #ddd;padding:12px;">
                                    {{ $customer->created_at->format('d M Y') }}
                                </td>

                                <td style="border:1px solid #ddd;padding:12px;">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                       style="background:#2563eb;color:white;padding:7px 12px;text-decoration:none;border-radius:6px;margin-right:5px;">
                                        View
                                    </a>

                                    @if($customer->is_suspended)
                                        <form method="POST" action="{{ route('admin.customers.activate', $customer) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                    style="background:#16a34a;color:white;padding:7px 12px;border:none;border-radius:6px;">
                                                Activate
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.customers.suspend', $customer) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Suspend this customer?')"
                                                    style="background:#dc2626;color:white;padding:7px 12px;border:none;border-radius:6px;">
                                                Suspend
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:20px;">
                    {{ $customers->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>