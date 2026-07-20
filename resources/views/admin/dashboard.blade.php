<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;justify-content:space-between;align-items:center;background:white;padding:14px 20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size:24px;font-weight:700;color:#111827;">
                @php $hour = now()->format('H'); @endphp

                @if($hour < 12)
                    ☀️ Good Morning, {{ auth()->user()->name }}
                @elseif($hour < 17)
                    🌤️ Good Afternoon, {{ auth()->user()->name }}
                @else
                    🌙 Good Evening, {{ auth()->user()->name }}
                @endif
            </div>

            @if(!$isSuperAdmin)
    <div>
        @if(auth()->user()->service_status === 'trial' && auth()->user()->trial_ends_at && auth()->user()->trial_ends_at->isFuture())
            <span style="background:#ecfdf5;border:1px solid #bbf7d0;padding:10px 16px;border-radius:10px;font-weight:600;color:#166534;">
                <i class="fas fa-clock"></i>
                Trial Ends: {{ auth()->user()->trial_ends_at->format('d M Y') }}
            </span>
        @elseif(auth()->user()->service_status === 'active' && auth()->user()->service_expires_at && auth()->user()->service_expires_at->isFuture())
            <span style="background:#f9fafb;border:1px solid #e5e7eb;padding:10px 16px;border-radius:10px;font-weight:600;color:#374151;">
                <i class="fas fa-calendar-alt"></i>
                Expiry: {{ auth()->user()->service_expires_at->format('d M Y') }}
            </span>
        @else
            <a href="{{ route('admin.license-payments.index') }}"
               style="background:#fee2e2;border:1px solid #fecaca;padding:10px 16px;border-radius:10px;color:#991b1b;font-weight:600;text-decoration:none;">
                <i class="fas fa-triangle-exclamation"></i>
                Subscription Required
            </a>
        @endif
    </div>
@endif
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .stat-card {
            color:white;
            padding:25px;
            border-radius:15px;
            box-shadow:0 6px 15px rgba(0,0,0,0.15);
            transition:0.3s;
        }

        .stat-card:hover {
            transform:translateY(-5px);
            box-shadow:0 10px 25px rgba(0,0,0,0.25);
        }

        .panel-card {
            background:white;
            padding:24px;
            border-radius:15px;
            box-shadow:0 4px 12px rgba(0,0,0,0.08);
        }

        .action-btn {
            color:white;
            padding:12px 20px;
            text-decoration:none;
            border-radius:8px;
            margin-right:10px;
            margin-bottom:10px;
            display:inline-block;
            font-weight:700;
        }

        .section-grid {
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:20px;
            margin-top:30px;
            align-items:start;
        }

        .chart-box {
            height:260px;
            position:relative;
        }

        .eye-btn-light {
            float:right;
            background:none;
            border:none;
            color:#111827;
            cursor:pointer;
            font-size:18px;
        }
    </style>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="panel-card" style="margin-bottom:30px;">
                <h3 style="font-size:22px;font-weight:bold;margin-bottom:20px;">
                    <i class="fas fa-chart-line" style="color:#16a34a;"></i>
                    {{ $isSuperAdmin ? 'License Revenue Summary' : 'Revenue Summary' }}

                    <button onclick="toggleRevenueSummary(this)" class="eye-btn-light">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </h3>

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
                    <div style="background:#ecfdf5;padding:25px;border-radius:15px;">
                        <h4 style="font-weight:bold;color:#065f46;">Today</h4>
                        <h2 style="font-size:30px;font-weight:900;color:#16a34a;">
                            <span class="revenue-item" data-amount="KES {{ number_format($todayRevenue, 2) }}">
                                KES {{ number_format($todayRevenue, 2) }}
                            </span>
                        </h2>
                    </div>

                    <div style="background:#eff6ff;padding:25px;border-radius:15px;">
                        <h4 style="font-weight:bold;color:#1d4ed8;">This Month</h4>
                        <h2 style="font-size:30px;font-weight:900;color:#2563eb;">
                            <span class="revenue-item" data-amount="KES {{ number_format($monthRevenue, 2) }}">
                                KES {{ number_format($monthRevenue, 2) }}
                            </span>
                        </h2>
                    </div>

                    <div style="background:#faf5ff;padding:25px;border-radius:15px;">
                        <h4 style="font-weight:bold;color:#6b21a8;">All Time</h4>
                        <h2 style="font-size:30px;font-weight:900;color:#9333ea;">
                            <span class="revenue-item" data-amount="KES {{ number_format($totalRevenue, 2) }}">
                                KES {{ number_format($totalRevenue, 2) }}
                            </span>
                        </h2>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
                <div class="stat-card" style="background:#2563eb;">
                    <h3><i class="fas {{ $isSuperAdmin ? 'fa-user-shield' : 'fa-users' }}"></i> {{ $isSuperAdmin ? 'Total Admins' : 'Total Customers' }}</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $totalCustomers }}</h2>
                </div>

                <div class="stat-card" style="background:#7c3aed;">
                    <h3><i class="fas fa-credit-card"></i> {{ $isSuperAdmin ? 'License Payments' : 'Total Payments' }}</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $totalPayments }}</h2>
                </div>

                <div class="stat-card" style="background:#ea580c;">
                    <h3><i class="fas fa-file-invoice"></i> {{ $isSuperAdmin ? 'Active Admins' : 'Active Subscriptions' }}</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $activeSubscriptions }}</h2>
                </div>

                <div class="stat-card" style="background:#dc2626;">
                    <h3><i class="fas fa-circle-exclamation"></i> {{ $isSuperAdmin ? 'Expired Admins' : 'Expired Subscriptions' }}</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $expiredSubscriptions }}</h2>
                </div>

                @if(!$isSuperAdmin)
                    <div class="stat-card" style="background:#0891b2;">
                        <h3><i class="fas fa-box"></i> Packages</h3>
                        <h2 style="font-size:32px;font-weight:bold;">{{ $totalPackages }}</h2>
                    </div>

                    <div class="stat-card" style="background:#0f766e;">
                        <h3><i class="fas fa-wifi"></i> Total Hotspot Users</h3>
                        <h2 style="font-size:32px;font-weight:bold;">{{ $totalHotspotUsers }}</h2>
                    </div>

                    <div class="stat-card" style="background:#0ea5e9;">
                        <h3><i class="fas fa-signal"></i> Active Hotspots</h3>
                        <h2 style="font-size:32px;font-weight:bold;">{{ $activeHotspotUsers }}</h2>
                    </div>

                    <div class="stat-card" style="background:#475569;">
                        <h3><i class="fas fa-network-wired"></i> Routers</h3>
                        <h2 style="font-size:32px;font-weight:bold;">{{ $totalRouters }}</h2>
                    </div>
                @endif
            </div>

            <div class="panel-card" style="margin-top:30px;">
                <h3 style="font-size:20px;font-weight:bold;margin-bottom:18px;">
                    <i class="fas fa-bolt" style="color:#f59e0b;"></i> Quick Actions
                </h3>

                @if($isSuperAdmin)
                    <a href="{{ route('admin.admin-users.index') }}" class="action-btn" style="background:#2563eb;">
                        <i class="fas fa-user-shield"></i> Admin Accounts
                    </a>

                    <a href="{{ route('admin.customers.index') }}" class="action-btn" style="background:#16a34a;">
                        <i class="fas fa-building"></i> Admin Clients
                    </a>

                    <a href="{{ route('receipts.index') }}" class="action-btn" style="background:#0f766e;">
                        <i class="fas fa-receipt"></i> Receipts
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}" class="action-btn" style="background:#475569;">
                        <i class="fas fa-shield-alt"></i> Platform Logs
                    </a>

                    <a href="{{ route('admin.notifications.index') }}" class="action-btn" style="background:#f59e0b;">
                        <i class="fas fa-bell"></i> Notifications
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="action-btn" style="background:#9333ea;">
                        <i class="fas fa-chart-pie"></i> Reports
                    </a>
                @else
                    <a href="{{ route('admin.packages.index') }}" class="action-btn" style="background:#2563eb;">
                        <i class="fas fa-box"></i> Manage Packages
                    </a>

                    <a href="{{ route('admin.customers.index') }}" class="action-btn" style="background:#16a34a;">
                        <i class="fas fa-users"></i> Manage Customers
                    </a>

                    <a href="{{ route('admin.routers.index') }}" class="action-btn" style="background:#475569;">
                        <i class="fas fa-network-wired"></i> Manage Routers
                    </a>

                    <a href="{{ route('admin.hotspot-users.index') }}" class="action-btn" style="background:#0f766e;">
                        <i class="fas fa-wifi"></i> Hotspot Users
                    </a>

                    <a href="{{ route('admin.notifications.index') }}" class="action-btn" style="background:#f59e0b;">
    <i class="fas fa-bell"></i> Notifications
</a>

<a href="{{ route('admin.business-profile.edit') }}" class="action-btn" style="background:#0ea5e9;">
    <i class="fas fa-building"></i> Business Profile
</a>

<a href="{{ route('admin.reports.index') }}" class="action-btn" style="background:#9333ea;">
    <i class="fas fa-chart-pie"></i> Reports
</a>
                @endif
            </div>

            <div class="section-grid">
                <div class="panel-card">
                    <h3 style="margin-bottom:15px;font-size:18px;font-weight:bold;">
                        <i class="fas fa-chart-column" style="color:#2563eb;"></i>
                        {{ $isSuperAdmin ? 'Monthly License Revenue' : 'Monthly Revenue' }}
                    </h3>
                    <div class="chart-box">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                @if(!$isSuperAdmin)
                    <div class="panel-card">
                        <h3 style="margin-bottom:15px;font-size:18px;font-weight:bold;">
                            <i class="fas fa-chart-pie" style="color:#9333ea;"></i> Package Distribution
                        </h3>
                        <div class="chart-box">
                            <canvas id="packageDistributionChart"></canvas>
                        </div>
                    </div>
                @endif

                <div class="panel-card">
                    <h3 style="margin-bottom:15px;font-size:18px;font-weight:bold;">
                        <i class="fas fa-user-plus" style="color:#16a34a;"></i>
                        {{ $isSuperAdmin ? 'Admin Registration Trend' : 'Customer Registration Trend' }}
                    </h3>
                    <div class="chart-box">
                        <canvas id="customerChart"></canvas>
                    </div>
                </div>

                <div class="panel-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
                        <h3 style="font-size:18px;font-weight:bold;">
                            <i class="fas fa-clock-rotate-left" style="color:#f59e0b;"></i> Recent Activity
                        </h3>

                        <a href="{{ route('admin.recent-activity') }}" style="color:#2563eb;font-weight:bold;text-decoration:none;">
                            View All →
                        </a>
                    </div>

                    @forelse($recentActivities as $activity)
                        <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid #e5e7eb;">
                            <div>
                                <i class="{{ $activity['icon'] }}" style="color:{{ $activity['color'] }};margin-right:8px;"></i>
                                {{ $activity['message'] }}
                            </div>

                            <small style="color:#6b7280;">
                                {{ $activity['time']->diffForHumans() }}
                            </small>
                        </div>
                    @empty
                        <p style="color:#6b7280;">No recent activity.</p>
                    @endforelse
                </div>
            </div>

            <div class="section-grid">
                <div class="panel-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="font-size:20px;font-weight:bold;">
                            <i class="fas fa-credit-card" style="color:#16a34a;"></i>
                            {{ $isSuperAdmin ? 'Recent License Payments' : 'Recent Payments' }}
                        </h3>

                        <a href="{{ route('admin.payments') }}" style="color:#2563eb;font-weight:bold;text-decoration:none;">
                            View All →
                        </a>
                    </div>

                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f9fafb;">
                                <th style="border:1px solid #ddd;padding:10px;">{{ $isSuperAdmin ? 'Admin' : 'Customer' }}</th>
                                <th style="border:1px solid #ddd;padding:10px;">Amount</th>
                                <th style="border:1px solid #ddd;padding:10px;">Status</th>
                                <th style="border:1px solid #ddd;padding:10px;">Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td style="border:1px solid #ddd;padding:10px;">
                                        {{ $payment->user->name ?? $payment->phone_number ?? 'Admin' }}
                                    </td>

                                    <td style="border:1px solid #ddd;padding:10px;">
                                        KES {{ number_format($payment->amount, 2) }}
                                    </td>

                                    <td style="border:1px solid #ddd;padding:10px;">
                                        @if(in_array($payment->status, ['completed', 'approved']))
                                            <span style="background:#dcfce7;color:#166534;padding:5px 10px;border-radius:20px;font-weight:bold;">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span style="background:#fef3c7;color:#92400e;padding:5px 10px;border-radius:20px;font-weight:bold;">
                                                Pending
                                            </span>
                                        @else
                                            <span style="background:#fee2e2;color:#991b1b;padding:5px 10px;border-radius:20px;font-weight:bold;">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td style="border:1px solid #ddd;padding:10px;">
                                        {{ $payment->created_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="border:1px solid #ddd;padding:20px;text-align:center;color:#6b7280;">
                                        No recent payments.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="panel-card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="font-size:20px;font-weight:bold;">
                            <i class="fas fa-ranking-star" style="color:#f59e0b;"></i>
                            {{ $isSuperAdmin ? 'Top Paying Admins' : 'Top Customers' }}
                        </h3>

                        <a href="{{ route('admin.top-customers') }}" style="color:#2563eb;text-decoration:none;font-weight:bold;">
                            View All →
                        </a>
                    </div>

                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="border:1px solid #ddd;padding:12px;">{{ $isSuperAdmin ? 'Admin' : 'Customer' }}</th>
                                <th style="border:1px solid #ddd;padding:12px;">Payments</th>
                                <th style="border:1px solid #ddd;padding:12px;">Total Spent</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($topCustomers as $customer)
                                <tr>
                                    <td style="border:1px solid #ddd;padding:12px;">{{ $customer['name'] }}</td>
                                    <td style="border:1px solid #ddd;padding:12px;">{{ $customer['payments'] }}</td>
                                    <td style="border:1px solid #ddd;padding:12px;">KES {{ number_format($customer['spent'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="border:1px solid #ddd;padding:20px;text-align:center;color:#6b7280;">
                                        No records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(!$isSuperAdmin)
                <div class="panel-card" style="margin-top:30px;">
                    <h3 style="margin-bottom:10px;font-size:20px;font-weight:bold;">
                        <i class="fas fa-chart-simple" style="color:#2563eb;"></i> Package Performance Comparison
                    </h3>

                    <p style="color:#6b7280;margin-bottom:20px;">
                        Performance of each internet package based on active users and completed payment revenue.
                    </p>

                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="border:1px solid #ddd;padding:12px;">Package Name</th>
                                <th style="border:1px solid #ddd;padding:12px;">Price</th>
                                <th style="border:1px solid #ddd;padding:12px;">Active Users</th>
                                <th style="border:1px solid #ddd;padding:12px;">Revenue</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($packagePerformance as $package)
                                <tr>
                                    <td style="border:1px solid #ddd;padding:12px;font-weight:bold;">{{ $package['name'] }}</td>
                                    <td style="border:1px solid #ddd;padding:12px;">KES {{ number_format($package['price'], 2) }}</td>
                                    <td style="border:1px solid #ddd;padding:12px;">
                                        <span style="background:#dbeafe;color:#1d4ed8;padding:6px 12px;border-radius:15px;font-weight:bold;">
                                            {{ $package['active_users'] }}
                                        </span>
                                    </td>
                                    <td style="border:1px solid #ddd;padding:12px;">KES {{ number_format($package['monthly_revenue'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="border:1px solid #ddd;padding:20px;text-align:center;color:#6b7280;">
                                        No package performance data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>

    <script>
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: '{{ $isSuperAdmin ? "License Revenue" : "Revenue" }}',
                    data: @json($chartData),
                    backgroundColor: '#2563eb',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        @if(!$isSuperAdmin)
            new Chart(document.getElementById('packageDistributionChart'), {
                type: 'pie',
                data: {
                    labels: @json($packageDistributionLabels),
                    datasets: [{
                        data: @json($packageDistributionData),
                        backgroundColor: ['#2563eb','#16a34a','#dc2626','#ea580c','#7c3aed','#0891b2','#0f766e','#f59e0b'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        @endif

        new Chart(document.getElementById('customerChart'), {
            type: 'line',
            data: {
                labels: @json($customerChartLabels),
                datasets: [{
                    label: '{{ $isSuperAdmin ? "New Admins" : "New Customers" }}',
                    data: @json($customerChartData),
                    borderColor: '#16a34a',
                    backgroundColor: '#16a34a',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.revenue-item').forEach(function (item) {
                item.dataset.amount = item.innerText.trim();
                item.innerText = '********';
            });
        });

        function toggleRevenueSummary(btn) {
            const items = document.querySelectorAll('.revenue-item');
            const firstItem = items[0];

            if (firstItem.innerText.trim() === '********') {
                items.forEach(function (item) {
                    item.innerText = item.dataset.amount;
                });
                btn.innerHTML = '<i class="fas fa-eye"></i>';
            } else {
                items.forEach(function (item) {
                    item.innerText = '********';
                });
                btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
            }
        }
    </script>
</x-app-layout>