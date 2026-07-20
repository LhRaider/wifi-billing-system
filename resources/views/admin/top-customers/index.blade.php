<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Top Customers
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="
                background:white;
                padding:25px;
                border-radius:15px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
            ">

                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:12px;border:1px solid #ddd;">Customer</th>
                            <th style="padding:12px;border:1px solid #ddd;">Email</th>
                            <th style="padding:12px;border:1px solid #ddd;">Payments</th>
                            <th style="padding:12px;border:1px solid #ddd;">Total Spent</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($topCustomers as $customer)
                            <tr>
                                <td style="padding:12px;border:1px solid #ddd;">
                                    {{ $customer['name'] }}
                                </td>

                                <td style="padding:12px;border:1px solid #ddd;">
                                    {{ $customer['email'] }}
                                </td>

                                <td style="padding:12px;border:1px solid #ddd;">
                                    {{ $customer['payments'] }}
                                </td>

                                <td style="padding:12px;border:1px solid #ddd;">
                                    KES {{ number_format($customer['spent'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:20px;">
                    {{ $topCustomers->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>