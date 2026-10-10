<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Platform Settings
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div style="background:white;padding:30px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                @if(session('success'))
                    <div style="margin-bottom:20px;padding:12px 15px;background:#dcfce7;color:#166534;border-radius:8px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="margin-bottom:20px;padding:12px 15px;background:#fee2e2;color:#991b1b;border-radius:8px;">
                        <strong>Please fix the following:</strong>

                        <ul style="margin-top:8px;padding-left:20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf

                    <h3 style="font-size:20px;font-weight:bold;margin-bottom:20px;">
                        Platform Information
                    </h3>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">
                            Platform Business Name
                        </label>

                        <input
                            type="text"
                            name="business_name"
                            value="{{ old('business_name', $settings->business_name) }}"
                            style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                            required
                        >
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">
                            Platform Business Phone
                        </label>

                        <input
                            type="text"
                            name="business_phone"
                            value="{{ old('business_phone', $settings->business_phone) }}"
                            style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                        >
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">
                            Currency
                        </label>

                        <input
                            type="text"
                            name="currency"
                            value="{{ old('currency', $settings->currency) }}"
                            placeholder="KES"
                            style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                            required
                        >
                    </div>

                    <div style="margin:30px 0;border-top:1px solid #e5e7eb;"></div>

                    <h3 style="font-size:20px;font-weight:bold;margin-bottom:8px;">
                        Admin Subscription Payment Settings
                    </h3>

                    <p style="color:#6b7280;margin-bottom:20px;">
                        These payment details are used when ISP Admins pay their monthly platform subscription.
                    </p>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">
                            Payment Type
                        </label>

                        <select
                            name="license_payment_type"
                            id="license_payment_type"
                            style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                            required
                        >
                            <option
                                value="till"
                                {{ old('license_payment_type', $settings->license_payment_type) === 'till' ? 'selected' : '' }}
                            >
                                Till Number
                            </option>

                            <option
                                value="paybill"
                                {{ old('license_payment_type', $settings->license_payment_type) === 'paybill' ? 'selected' : '' }}
                            >
                                PayBill
                            </option>

                            <option
                                value="phone"
                                {{ old('license_payment_type', $settings->license_payment_type) === 'phone' ? 'selected' : '' }}
                            >
                                Phone Number
                            </option>
                        </select>
                    </div>

                    <div id="till_fields" style="margin-bottom:20px;">
                        <label style="font-weight:bold;">
                            Till Number
                        </label>

                        <input
                            type="text"
                            name="license_till_number"
                            value="{{ old('license_till_number', $settings->license_till_number) }}"
                            placeholder="Example: 123456"
                            style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                        >
                    </div>

                    <div id="paybill_fields">

                        <div style="margin-bottom:20px;">
                            <label style="font-weight:bold;">
                                PayBill Number
                            </label>

                            <input
                                type="text"
                                name="license_paybill_number"
                                value="{{ old('license_paybill_number', $settings->license_paybill_number) }}"
                                placeholder="Example: 174379"
                                style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                            >
                        </div>

                        <div style="margin-bottom:20px;">
                            <label style="font-weight:bold;">
                                Account Number
                            </label>

                            <input
                                type="text"
                                name="license_account_number"
                                value="{{ old('license_account_number', $settings->license_account_number) }}"
                                placeholder="Example: WIFI-LICENSE"
                                style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                            >
                        </div>

                    </div>

                    <div id="phone_fields" style="margin-bottom:20px;">
                        <label style="font-weight:bold;">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            name="license_phone_number"
                            value="{{ old('license_phone_number', $settings->license_phone_number) }}"
                            placeholder="Example: 0712345678"
                            style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                        >
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">
                            Monthly Subscription Fee
                        </label>

                        <input
                            type="number"
                            name="license_monthly_fee"
                            min="1"
                            step="0.01"
                            value="{{ old('license_monthly_fee', $settings->license_monthly_fee ?? 400) }}"
                            style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                            required
                        >

                        <small style="color:#6b7280;">
                            This is the monthly fee charged to ISP Admin accounts.
                        </small>
                    </div>

                    <div style="margin:30px 0;border-top:1px solid #e5e7eb;"></div>

                    <h3 style="font-size:20px;font-weight:bold;margin-bottom:20px;">
                        Support Settings
                    </h3>

                    <div style="margin-bottom:25px;">
                        <label style="font-weight:bold;">
                            Platform Support Message
                        </label>

                        <textarea
                            name="support_message"
                            rows="4"
                            style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;"
                            placeholder="Enter the support message displayed to ISP Admins."
                        >{{ old('support_message', $settings->support_message) }}</textarea>
                    </div>

                    <button
                        type="submit"
                        style="background:#2563eb;color:white;padding:12px 22px;border:none;border-radius:8px;cursor:pointer;font-weight:bold;"
                    >
                        Save Platform Settings
                    </button>
                </form>

            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentType = document.getElementById('license_payment_type');
            const tillFields = document.getElementById('till_fields');
            const paybillFields = document.getElementById('paybill_fields');
            const phoneFields = document.getElementById('phone_fields');

            function updatePaymentFields() {
                const selected = paymentType.value;

                tillFields.style.display =
                    selected === 'till' ? 'block' : 'none';

                paybillFields.style.display =
                    selected === 'paybill' ? 'block' : 'none';

                phoneFields.style.display =
                    selected === 'phone' ? 'block' : 'none';
            }

            paymentType.addEventListener('change', updatePaymentFields);

            updatePaymentFields();
        });
    </script>
</x-app-layout>