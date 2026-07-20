<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Receipts
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:1200px;margin:0 auto;background:white;padding:30px;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">

            <h3 style="font-size:26px;font-weight:800;margin-bottom:20px;">
                <i class="fas fa-receipt" style="color:#2563eb;"></i>
                Admin Subscription Receipts
            </h3>

            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="border:1px solid #e5e7eb;padding:12px;">Receipt</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Admin</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Months</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Amount</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Transaction</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Status</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Date</th>
                        <th style="border:1px solid #e5e7eb;padding:12px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($receipts as $receipt)
                        <tr>
                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                LIC-{{ str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                {{ $receipt->user->name ?? 'Admin' }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                {{ $receipt->months }} Month{{ $receipt->months > 1 ? 's' : '' }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;color:#16a34a;font-weight:800;">
                                KES {{ number_format($receipt->amount, 2) }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                {{ $receipt->transaction_code ?? 'N/A' }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                {{ ucfirst($receipt->status) }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                {{ $receipt->created_at->format('d M Y H:i') }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                <a href="{{ route('admin.license-receipts.show', $receipt) }}"
   style="background:#2563eb;color:white;padding:7px 12px;border-radius:8px;text-decoration:none;">
    View
</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:25px;text-align:center;color:#6b7280;">
                                No admin subscription receipts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:20px;">
                {{ $receipts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>