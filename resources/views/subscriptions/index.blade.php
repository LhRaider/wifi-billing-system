<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Subscriptions
        </h2>
    </x-slot>

    <style>
        .summary-box {
            background:white;
            padding:20px;
            border-radius:15px;
            box-shadow:0 6px 15px rgba(0,0,0,0.10);
            text-align:center;
        }

        .sub-card {
            background:white;
            padding:25px;
            border-radius:15px;
            box-shadow:0 6px 15px rgba(0,0,0,0.10);
        }

        .status-active {
            background:#dcfce7;
            color:#166534;
            padding:6px 12px;
            border-radius:20px;
            font-weight:bold;
        }

        .status-expired {
            background:#fee2e2;
            color:#991b1b;
            padding:6px 12px;
            border-radius:20px;
            font-weight:bold;
        }
    </style>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:25px;">
                <div class="summary-box">
                    <h3>📅 Total Subscriptions</h3>
                    <h2 style="font-size:32px;font-weight:bold;color:#2563eb;">
                        {{ $subscriptions->count() }}
                    </h2>
                </div>

                <div class="summary-box">
                    <h3>🟢 Active</h3>
                    <h2 style="font-size:32px;font-weight:bold;color:#16a34a;">
                        {{ $subscriptions->where('status', 'active')->count() }}
                    </h2>
                </div>

                <div class="summary-box">
                    <h3>🔴 Expired</h3>
                    <h2 style="font-size:32px;font-weight:bold;color:#dc2626;">
                        {{ $subscriptions->where('status', 'expired')->count() }}
                    </h2>
                </div>
            </div>

            <div class="sub-card">
                @if($subscriptions->count() > 0)
                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="border:1px solid #ddd;padding:12px;">Status</th>
                                <th style="border:1px solid #ddd;padding:12px;">Start Time</th>
                                <th style="border:1px solid #ddd;padding:12px;">End Time</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td style="border:1px solid #ddd;padding:12px;">
                                        @if($subscription->status === 'active')
                                            <span class="status-active">🟢 Active</span>
                                        @else
                                            <span class="status-expired">🔴 Expired</span>
                                        @endif
                                    </td>

                                    <td style="border:1px solid #ddd;padding:12px;">
                                        {{ $subscription->start_time }}
                                    </td>

                                    <td style="border:1px solid #ddd;padding:12px;">
                                        {{ $subscription->end_time }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align:center;padding:40px;color:#6b7280;">
                        <h3>No subscriptions found.</h3>
                        <p style="margin-top:10px;">Buy a package to start your first subscription.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>