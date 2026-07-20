<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            System Settings
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div style="background:white;padding:30px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">Business Name</label>
                        <input type="text" name="business_name" value="{{ old('business_name', $settings->business_name) }}"
                               style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;">
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">Business Phone</label>
                        <input type="text" name="business_phone" value="{{ old('business_phone', $settings->business_phone) }}"
                               style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;">
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $settings->currency) }}"
                               style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;">
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:bold;">Default Router</label>
                        <select name="default_router_id"
                                style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;">
                            <option value="">No default router</option>

                            @foreach($routers as $router)
                                <option value="{{ $router->id }}"
                                    {{ $settings->default_router_id == $router->id ? 'selected' : '' }}>
                                    {{ $router->name }} - {{ $router->ip_address }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:10px;font-weight:bold;">
                            <input type="checkbox" name="mpesa_auto_activate" value="1"
                                {{ $settings->mpesa_auto_activate ? 'checked' : '' }}>
                            Enable M-Pesa Auto Activation Testing Mode
                        </label>
                        <small style="color:#6b7280;">
                            Keep enabled during testing. Disable later for real Safaricom callback-only activation.
                        </small>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:10px;font-weight:bold;">
                            <input type="checkbox" name="mikrotik_real_connection" value="1"
                                {{ $settings->mikrotik_real_connection ? 'checked' : '' }}>
                            Enable Real MikroTik Connection
                        </label>
                        <small style="color:#6b7280;">
                            Keep disabled until a real MikroTik router is connected and tested.
                        </small>
                    </div>

                    <div style="margin-bottom:25px;">
                        <label style="font-weight:bold;">Support Message</label>
                        <textarea name="support_message" rows="4"
                                  style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;margin-top:6px;">{{ old('support_message', $settings->support_message) }}</textarea>
                    </div>

                    <button type="submit"
                            style="background:#2563eb;color:white;padding:12px 22px;border:none;border-radius:8px;cursor:pointer;font-weight:bold;">
                        Save Settings
                    </button>
                </form>

            </div>

        </div>
    </div>
</x-app-layout>