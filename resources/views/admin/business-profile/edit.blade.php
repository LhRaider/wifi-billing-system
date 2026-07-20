<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            <i class="fas fa-building text-blue-600"></i>
            Business Profile
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-100 min-h-screen">

        <div class="max-w-6xl mx-auto">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-5 py-4 rounded-xl">
                    <i class="fas fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-300 text-red-700 px-5 py-4 rounded-xl">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.business-profile.update') }}">

                @csrf

                <div class="grid lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-2">

                        <div class="bg-white rounded-2xl shadow p-8">

                            <h3 class="text-xl font-bold mb-6 text-blue-700">
                                <i class="fas fa-building"></i>
                                Business Information
                            </h3>

                            <div class="mb-5">
                                <label class="font-semibold">
                                    Business Name
                                </label>

                                <input
                                    type="text"
                                    name="business_name"
                                    value="{{ old('business_name',$admin->business_name) }}"
                                    class="w-full mt-2 rounded-lg border-gray-300">
                            </div>

                            <div class="mb-5">
                                <label class="font-semibold">
                                    WiFi / Network Name
                                </label>

                                <input
                                    type="text"
                                    name="network_name"
                                    value="{{ old('network_name',$admin->network_name) }}"
                                    class="w-full mt-2 rounded-lg border-gray-300">

                                <small class="text-gray-500">
                                    Example:
                                    Korir WiFi
                                </small>
                            </div>

                            <div class="grid md:grid-cols-2 gap-5">

                                <div>

                                    <label class="font-semibold">
                                        Support Phone
                                    </label>

                                    <input
                                        type="text"
                                        name="business_phone"
                                        value="{{ old('business_phone',$admin->business_phone) }}"
                                        class="w-full mt-2 rounded-lg border-gray-300">

                                </div>

                                <div>

                                    <label class="font-semibold">
                                        Business Address
                                    </label>

                                    <input
                                        type="text"
                                        name="business_address"
                                        value="{{ old('business_address',$admin->business_address) }}"
                                        class="w-full mt-2 rounded-lg border-gray-300">

                                </div>

                            </div>

                        </div>

                    </div>

                    <div>

                        <div class="bg-white rounded-2xl shadow p-8">

                            <h3 class="text-xl font-bold text-green-700 mb-5">

                                <i class="fas fa-money-bill-wave"></i>

                                Payment Information

                            </h3>

                            <div class="mb-5">

                                <label class="font-semibold">

                                    Payment Type

                                </label>

                                <select
                                    name="payment_type"
                                    class="w-full mt-2 rounded-lg border-gray-300">

                                    <option value="till"
                                        {{ $admin->payment_type=="till" ? "selected":"" }}>
                                        Till Number
                                    </option>

                                    <option value="paybill"
                                        {{ $admin->payment_type=="paybill" ? "selected":"" }}>
                                        Paybill
                                    </option>

                                    <option value="phone"
                                        {{ $admin->payment_type=="phone" ? "selected":"" }}>
                                        Send Money
                                    </option>

                                </select>

                            </div>

                            <div class="mb-5">

                                <label class="font-semibold">

                                    Till Number

                                </label>

                                <input
                                    type="text"
                                    name="business_till"
                                    value="{{ old('business_till',$admin->business_till) }}"
                                    class="w-full mt-2 rounded-lg border-gray-300">

                            </div>

                            <div class="mb-5">

                                <label class="font-semibold">

                                    Paybill Number

                                </label>

                                <input
                                    type="text"
                                    name="business_paybill"
                                    value="{{ old('business_paybill',$admin->business_paybill) }}"
                                    class="w-full mt-2 rounded-lg border-gray-300">

                            </div>

                            <div class="mb-5">

                                <label class="font-semibold">

                                    Account Number

                                </label>

                                <input
                                    type="text"
                                    name="business_account"
                                    value="{{ old('business_account',$admin->business_account) }}"
                                    class="w-full mt-2 rounded-lg border-gray-300">

                            </div>

                            <div class="bg-yellow-100 border border-yellow-300 rounded-xl p-4">

                                <h4 class="font-bold text-yellow-800">

                                    <i class="fas fa-triangle-exclamation"></i>

                                    Important

                                </h4>

                                <p class="text-sm text-yellow-800 mt-2">

                                    Customers will pay using these payment
                                    details.

                                    They will appear on receipts,
                                    checkout page,
                                    captive portal,
                                    payment instructions
                                    and activation screen.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="mt-8">

                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold">

                        <i class="fas fa-floppy-disk"></i>

                        Save Business Profile

                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>