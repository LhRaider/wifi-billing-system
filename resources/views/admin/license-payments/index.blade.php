<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            License Payments
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:1100px;margin:0 auto;">

            @if(auth()->user()->role !== 'super_admin')
                <div style="background:white;padding:30px;border-radius:16px;margin-bottom:25px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                    <h2 style="font-size:26px;font-weight:900;margin-bottom:15px;">
                        <i class="fas fa-crown" style="color:#f59e0b;"></i>
                        Current Subscription
                    </h2>

                    <p><strong>Status:</strong> {{ ucfirst(auth()->user()->service_status ?? 'inactive') }}</p>

                    @if(auth()->user()->service_status === 'trial' && auth()->user()->trial_ends_at)
                        <p><strong>Trial Ends:</strong> {{ auth()->user()->trial_ends_at->format('d M Y') }}</p>
                    @endif

                    @if(auth()->user()->service_status === 'active' && auth()->user()->service_expires_at)
                        <p><strong>Expiry:</strong> {{ auth()->user()->service_expires_at->format('d M Y') }}</p>
                    @endif

                    @if(auth()->user()->service_status === 'expired')
                        <p style="color:#dc2626;font-weight:800;margin-top:10px;">
                            Your subscription is over. Please renew to continue providing services.
                        </p>
                    @endif
                </div>

                <div style="background:white;padding:30px;border-radius:16px;margin-bottom:25px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                    <h2 style="font-size:26px;font-weight:900;margin-bottom:20px;">
                        Renew Subscription
                    </h2>

                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
                        @foreach($plans as $months => $amount)
                            <form method="POST" action="{{ route('admin.license-payments.store') }}"
                                  style="border:1px solid #e5e7eb;padding:20px;border-radius:14px;">
                                @csrf

                                <h3 style="font-size:22px;font-weight:900;">
                                    {{ $months }} Month{{ $months > 1 ? 's' : '' }}
                                </h3>

                                <p style="font-size:26px;font-weight:900;color:#16a34a;margin:10px 0;">
                                    KES {{ number_format($amount, 2) }}
                                </p>

                                <input type="hidden" name="months" value="{{ $months }}">

                                <label style="font-weight:700;">Transaction Code</label>
                                <input type="text"
                                       name="transaction_code"
                                       placeholder="e.g. QWE123XYZ"
                                       style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:10px;margin:8px 0 15px;">

                                <button type="submit"
                                        style="background:#2563eb;color:white;padding:10px 15px;border:none;border-radius:8px;width:100%;font-weight:800;">
                                    Submit Payment
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="background:white;padding:30px;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                <h2 style="font-size:26px;font-weight:900;margin-bottom:20px;">
                    <i class="fas fa-money-check-alt" style="color:#2563eb;"></i>
                    {{ auth()->user()->role === 'super_admin' ? 'Admin License Payment Requests' : 'My License Payments' }}
                </h2>

                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            @if(auth()->user()->role === 'super_admin')
                                <th style="border:1px solid #e5e7eb;padding:12px;">Admin</th>
                            @endif
                            <th style="border:1px solid #e5e7eb;padding:12px;">Months</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Amount</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Transaction</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Status</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Start</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Expiry</th>
                            @if(auth()->user()->role === 'super_admin')
                                <th style="border:1px solid #e5e7eb;padding:12px;">Action</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                @if(auth()->user()->role === 'super_admin')
                                    <td style="border:1px solid #e5e7eb;padding:12px;">
                                        {{ $payment->user->name ?? 'Admin' }}
                                    </td>
                                @endif

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $payment->months }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    KES {{ number_format($payment->amount, 2) }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $payment->transaction_code ?? 'N/A' }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ ucfirst($payment->status) }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $payment->start_date ? $payment->start_date->format('d M Y') : 'Pending' }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:12px;">
                                    {{ $payment->expiry_date ? $payment->expiry_date->format('d M Y') : 'Pending' }}
                                </td>

                                @if(auth()->user()->role === 'super_admin')
                                    <td style="border:1px solid #e5e7eb;padding:12px;">
                                        @if($payment->status === 'pending')
                                            <form method="POST" action="{{ route('admin.license-payments.approve', $payment) }}" style="display:inline;">
                                                @csrf
                                                <button type="submit"
                                                        style="background:#16a34a;color:white;border:none;padding:7px 12px;border-radius:8px;font-weight:700;">
                                                    Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.license-payments.reject', $payment) }}" style="display:inline;">
                                                @csrf
                                                <button type="submit"
                                                        style="background:#dc2626;color:white;border:none;padding:7px 12px;border-radius:8px;font-weight:700;">
                                                    Reject
                                                </button>
                                            </form>
                                        @else
                                            —
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role === 'super_admin' ? 8 : 7 }}"
                                    style="border:1px solid #e5e7eb;padding:25px;text-align:center;color:#6b7280;">
                                    No license payments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div style="margin-top:20px;">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>