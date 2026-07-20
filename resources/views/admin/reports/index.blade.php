<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reports Dashboard
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">

                <div style="background:#16a34a;color:white;padding:25px;border-radius:15px;">
                    <h3>Total Revenue</h3>
                    <h2 style="font-size:32px;font-weight:bold;">
                        KES {{ number_format($totalRevenue, 2) }}
                    </h2>
                </div>

                <div style="background:#2563eb;color:white;padding:25px;border-radius:15px;">
                    <h3>Total Customers</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $totalCustomers }}</h2>
                </div>

                <div style="background:#7c3aed;color:white;padding:25px;border-radius:15px;">
                    <h3>Total Payments</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $totalPayments }}</h2>
                </div>

                <div style="background:#059669;color:white;padding:25px;border-radius:15px;">
                    <h3>Completed Payments</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $completedPayments }}</h2>
                </div>

                <div style="background:#ca8a04;color:white;padding:25px;border-radius:15px;">
                    <h3>Pending Payments</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $pendingPayments }}</h2>
                </div>

                <div style="background:#ea580c;color:white;padding:25px;border-radius:15px;">
                    <h3>Active Subscriptions</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $activeSubscriptions }}</h2>
                </div>

                <div style="background:#dc2626;color:white;padding:25px;border-radius:15px;">
                    <h3>Expired Subscriptions</h3>
                    <h2 style="font-size:32px;font-weight:bold;">{{ $expiredSubscriptions }}</h2>
                </div>

            </div>

            <div style="margin-top:30px;background:white;padding:25px;border-radius:15px;">
                <h3 style="margin-bottom:20px;font-size:20px;font-weight:bold;">
                    Export Reports
                </h3>

                <a href="{{ route('admin.reports.payments.csv') }}"
                   style="background:#2563eb;color:white;padding:12px 22px;text-decoration:none;border-radius:8px;margin-right:10px;display:inline-block;">
                    Export Payments CSV
                </a>

                <a href="{{ route('admin.reports.customers.csv') }}"
                   style="background:#16a34a;color:white;padding:12px 22px;text-decoration:none;border-radius:8px;margin-right:10px;display:inline-block;">
                    Export Customers CSV
                </a>

                <a href="{{ route('admin.reports.subscriptions.csv') }}"
                   style="background:#9333ea;color:white;padding:12px 22px;text-decoration:none;border-radius:8px;display:inline-block;">
                    Export Subscriptions CSV
                </a>
            </div>

        </div>
    </div>
</x-app-layout>