<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Receipt
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div id="receiptArea" style="max-width:800px;margin:0 auto;background:white;padding:35px;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">

            <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #e5e7eb;padding-bottom:20px;margin-bottom:25px;">
                <div>
                    <h2 style="font-size:30px;font-weight:900;margin:0;color:#111827;">
                        WiFi Billing
                    </h2>
                    <p style="color:#6b7280;margin-top:5px;">Admin Subscription Receipt</p>
                </div>

                <div style="text-align:right;">
                    <h3 style="font-size:22px;font-weight:800;color:#2563eb;margin:0;">
                        LIC-{{ str_pad($licensePayment->id, 6, '0', STR_PAD_LEFT) }}
                    </h3>
                    <p style="color:#6b7280;margin-top:5px;">
                        {{ $licensePayment->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>

            <div style="margin-bottom:25px;">
                <h3 style="font-size:18px;font-weight:800;margin-bottom:10px;">Admin Details</h3>
                <p><strong>Name:</strong> {{ $licensePayment->user->name ?? 'Admin' }}</p>
                <p><strong>Email:</strong> {{ $licensePayment->user->email ?? 'N/A' }}</p>
            </div>

            <table style="width:100%;border-collapse:collapse;margin-bottom:25px;">
                <tr>
                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;background:#f8fafc;">Plan</th>
                    <td style="border:1px solid #e5e7eb;padding:12px;">
                        {{ $licensePayment->months }} Month{{ $licensePayment->months > 1 ? 's' : '' }} Subscription
                    </td>
                </tr>

                <tr>
                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;background:#f8fafc;">Amount</th>
                    <td style="border:1px solid #e5e7eb;padding:12px;color:#16a34a;font-weight:900;">
                        KES {{ number_format($licensePayment->amount, 2) }}
                    </td>
                </tr>

                <tr>
                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;background:#f8fafc;">Transaction Code</th>
                    <td style="border:1px solid #e5e7eb;padding:12px;">
                        {{ $licensePayment->transaction_code ?? 'N/A' }}
                    </td>
                </tr>

                <tr>
                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;background:#f8fafc;">Status</th>
                    <td style="border:1px solid #e5e7eb;padding:12px;">
                        {{ ucfirst($licensePayment->status) }}
                    </td>
                </tr>

                <tr>
                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;background:#f8fafc;">Start Date</th>
                    <td style="border:1px solid #e5e7eb;padding:12px;">
                        {{ $licensePayment->start_date ? $licensePayment->start_date->format('d M Y') : 'Pending' }}
                    </td>
                </tr>

                <tr>
                    <th style="border:1px solid #e5e7eb;padding:12px;text-align:left;background:#f8fafc;">Expiry Date</th>
                    <td style="border:1px solid #e5e7eb;padding:12px;">
                        {{ $licensePayment->expiry_date ? $licensePayment->expiry_date->format('d M Y') : 'Pending' }}
                    </td>
                </tr>
            </table>

            <p style="color:#6b7280;">
                This receipt confirms payment for software subscription service access.
            </p>
        </div>

        <div style="max-width:800px;margin:20px auto;text-align:right;">
            <button onclick="window.print()"
                    style="background:#2563eb;color:white;padding:10px 18px;border:none;border-radius:8px;font-weight:700;">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>
    </div>
</x-app-layout>