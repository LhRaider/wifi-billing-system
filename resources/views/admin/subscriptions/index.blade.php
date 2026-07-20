<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subscription Management
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="background:white;padding:25px;border-radius:15px;">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <h3 style="font-size:22px;font-weight:bold;">
                        All Subscriptions
                    </h3>

                    <span style="background:#dbeafe;color:#1d4ed8;padding:10px 18px;border-radius:20px;font-weight:bold;">
                        {{ $subscriptions->total() }} Records
                    </span>
                </div>

                <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search customer, email, package, or status..."
                        style="flex:1;padding:12px;border:1px solid #d1d5db;border-radius:8px;"
                    >

                    <button
                        type="submit"
                        style="background:#2563eb;color:white;padding:12px 20px;border:none;border-radius:8px;cursor:pointer;">
                        Search
                    </button>
                </form>

                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:12px;border:1px solid #ddd;">Customer</th>
                            <th style="padding:12px;border:1px solid #ddd;">Package</th>
                            <th style="padding:12px;border:1px solid #ddd;">Start</th>
                            <th style="padding:12px;border:1px solid #ddd;">End</th>
                            <th style="padding:12px;border:1px solid #ddd;">Status</th>
                            <th style="padding:12px;border:1px solid #ddd;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($subscriptions as $subscription)
                            <tr>
                                <td style="padding:12px;border:1px solid #ddd;">
                                    <strong>{{ $subscription->user->name ?? 'N/A' }}</strong><br>
                                    <small>{{ $subscription->user->email ?? '' }}</small>
                                </td>

                                <td style="padding:12px;border:1px solid #ddd;">
                                    {{ $subscription->package->name ?? 'N/A' }}
                                </td>

                                <td style="padding:12px;border:1px solid #ddd;">
                                    {{ $subscription->start_time }}
                                </td>

                                <td style="padding:12px;border:1px solid #ddd;">
                                    {{ $subscription->end_time }}
                                </td>

                                <td style="padding:12px;border:1px solid #ddd;">

                                    @if($subscription->status == 'active')
                                        <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;">
                                            Active
                                        </span>
                                    @else
                                        <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;">
                                            Expired
                                        </span>
                                    @endif

                                </td>

                                <td style="padding:12px;border:1px solid #ddd;">
                                    <div style="display:flex;gap:8px;">

                                        @if($subscription->status == 'active')

                                            <form method="POST" action="{{ route('admin.subscriptions.expire', $subscription) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    style="background:#dc2626;color:white;padding:8px 14px;border:none;border-radius:8px;">
                                                    Expire
                                                </button>
                                            </form>

                                        @else

                                            <form method="POST" action="{{ route('admin.subscriptions.activate', $subscription) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    style="background:#16a34a;color:white;padding:8px 14px;border:none;border-radius:8px;">
                                                    Activate
                                                </button>
                                            </form>

                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:20px;">
                    {{ $subscriptions->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>