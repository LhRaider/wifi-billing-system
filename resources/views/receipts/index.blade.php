<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Receipts
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <h3 class="text-2xl font-bold mb-6">
                    Receipt History
                </h3>

                <table class="w-full border-collapse">

                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 border">Receipt #</th>
                            <th class="p-3 border">Package</th>
                            <th class="p-3 border">Amount</th>
                            <th class="p-3 border">Date</th>
                            <th class="p-3 border">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($receipts as $receipt)

                            <tr>
                                <td class="p-3 border">
                                    REC-{{ str_pad($receipt->id, 6, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="p-3 border">
                                    {{ $receipt->package->name ?? 'Package' }}
                                </td>

                                <td class="p-3 border">
                                    KES {{ number_format($receipt->amount, 2) }}
                                </td>

                                <td class="p-3 border">
                                    {{ $receipt->created_at->format('d M Y H:i') }}
                                </td>

                                <td class="p-3 border text-center">
    <a href="{{ route('receipts.show', $receipt->id) }}"
       style="
            background:#2563eb;
            color:white;
            padding:8px 15px;
            border-radius:8px;
            text-decoration:none;
            display:inline-block;
       ">
        View Receipt
    </a>
</td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">
                                    No receipts found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="mt-6">
                    {{ $receipts->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>