<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customer Profile
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;">
                <a href="{{ route('admin.customers.index') }}"
                   style="background:#475569;color:white;padding:10px 18px;text-decoration:none;border-radius:8px;">
                    ← Back to Customers
                </a>

                @if($customer->is_suspended)
                    <form method="POST" action="{{ route('admin.customers.activate', $customer) }}">
                        @csrf
                        <button type="submit" style="background:#16a34a;color:white;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;">
                            Activate Customer
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.customers.suspend', $customer) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Suspend this customer?')"
                                style="background:#dc2626;color:white;padding:10px 18px;border:none;border-radius:8px;cursor:pointer;">
                            Suspend Customer
                        </button>
                    </form>
                @endif
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">

                <div style="background:#2563eb;color:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                    <h3>Total Payments</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $totalPayments }}</h2>
                </div>

                <div style="background:#16a34a;color:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                    <h3>Total Spent</h3>
                    <h2 style="font-size:32px;font-weight:bold;">
                        KES {{ number_format($totalSpent, 2) }}
                    </h2>
                </div>

                <div style="background:#7c3aed;color:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                    <h3>Account Status</h3>
                    <h2 style="font-size:24px;font-weight:bold;">
                        {{ $customer->is_suspended ? 'Suspended' : 'Active' }}
                    </h2>
                </div>

            </div>

            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-top:25px;">

                <div style="background:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    <h3 style="font-size:20px;font-weight:bold;margin-bottom:20px;">
                        Customer Information
                    </h3>

                    <p><strong>Name:</strong> {{ $customer->name }}</p>
                    <p><strong>Email:</strong> {{ $customer->email }}</p>

                    <p>
                        <strong>Status:</strong>
                        @if($customer->is_suspended)
                            <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:bold;">
                                🔴 Suspended
                            </span>
                        @else
                            <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:bold;">
                                🟢 Active
                            </span>
                        @endif
                    </p>

                    <p><strong>Joined:</strong> {{ $customer->created_at->format('d M Y') }}</p>
                </div>

                <div style="background:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    <h3 style="font-size:20px;font-weight:bold;margin-bottom:20px;">
                        Last Payment
                    </h3>

                    @if($lastPayment)
                        <p><strong>Amount:</strong> KES {{ number_format($lastPayment->amount, 2) }}</p>
                        <p><strong>Phone:</strong> {{ $lastPayment->phone_number }}</p>

                        <p>
                            <strong>Status:</strong>
                            @if($lastPayment->status == 'completed')
                                <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:bold;">🟢 Completed</span>
                            @elseif($lastPayment->status == 'pending')
                                <span style="background:#fef3c7;color:#92400e;padding:6px 12px;border-radius:20px;font-weight:bold;">🟡 Pending</span>
                            @else
                                <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:bold;">🔴 Failed</span>
                            @endif
                        </p>

                        <p><strong>Date:</strong> {{ $lastPayment->created_at->format('d M Y H:i') }}</p>
                    @else
                        <p style="color:#6b7280;">No payment found.</p>
                    @endif
                </div>

                <div style="background:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    <h3 style="font-size:20px;font-weight:bold;margin-bottom:20px;">
                        Current Subscription
                    </h3>

                    @if($latestSubscription)
                        <p><strong>Package:</strong> {{ $latestSubscription->package->name ?? 'N/A' }}</p>
                        <p><strong>Price:</strong> KES {{ number_format($latestSubscription->package->price ?? 0, 2) }}</p>
                        <p><strong>Duration:</strong> {{ $latestSubscription->package->duration_hours ?? 'N/A' }} Hours</p>

                        <p>
                            <strong>Status:</strong>
                            @if($latestSubscription->status == 'active')
                                <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:bold;">
                                    🟢 Active
                                </span>
                            @else
                                <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:bold;">
                                    🔴 {{ ucfirst($latestSubscription->status) }}
                                </span>
                            @endif
                        </p>

                        <p><strong>Start:</strong> {{ $latestSubscription->start_time }}</p>
                        <p><strong>End:</strong> {{ $latestSubscription->end_time }}</p>
                    @else
                        <p style="color:#6b7280;">No subscription found.</p>
                    @endif
                </div>

                <div style="background:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
                    <h3 style="font-size:20px;font-weight:bold;margin-bottom:20px;">
                        Hotspot Account
                    </h3>

                    @if($latestHotspotUser)
                        <p><strong>Username:</strong> {{ $latestHotspotUser->username }}</p>
                        <p><strong>Password:</strong> {{ $latestHotspotUser->password }}</p>
                        <p><strong>Profile:</strong> {{ $latestHotspotUser->profile }}</p>

                        <p>
                            <strong>Status:</strong>
                            @if($latestHotspotUser->status == 'active')
                                <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:bold;">
                                    🟢 Active
                                </span>
                            @else
                                <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:bold;">
                                    🔴 {{ ucfirst($latestHotspotUser->status) }}
                                </span>
                            @endif
                        </p>

                        <p><strong>Router:</strong> {{ $latestHotspotUser->router->name ?? 'Not Assigned' }}</p>
                        <p><strong>Router Sync:</strong> {{ $latestHotspotUser->last_sync_status ?? 'Pending' }}</p>
                        <p><strong>Sync Message:</strong> {{ $latestHotspotUser->last_sync_message ?? 'Waiting for router synchronization.' }}</p>
                    @else
                        <p style="color:#6b7280;">No hotspot account found.</p>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>