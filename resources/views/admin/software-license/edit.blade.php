<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Software License
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">

        <div style="max-width:1000px;margin:0 auto;">

            @if(session('success'))
                <div style="
                    background:#dcfce7;
                    color:#166534;
                    padding:15px;
                    border-radius:10px;
                    margin-bottom:20px;
                ">
                    {{ session('success') }}
                </div>
            @endif

            <div style="
                background:white;
                border-radius:16px;
                padding:30px;
                box-shadow:0 6px 18px rgba(0,0,0,0.08);
            ">

                <h3 style="font-size:28px;font-weight:800;margin-bottom:10px;">
                    <i class="fas fa-crown" style="color:#f59e0b;"></i>
                    Software License Management
                </h3>

                <p style="color:#6b7280;margin-bottom:25px;">
                    Manage subscription and licensing for this installation.
                </p>

                <form method="POST"
                      action="{{ route('admin.software-license.update') }}">

                    @csrf
                    @method('PUT')

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

                        <div>
                            <label><strong>Client Name</strong></label>

                            <input type="text"
                                   name="client_name"
                                   value="{{ $license->client_name }}"
                                   required
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Plan Name</strong></label>

                            <input type="text"
                                   name="plan_name"
                                   value="{{ $license->plan_name }}"
                                   required
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Monthly Fee</strong></label>

                            <input type="number"
                                   step="0.01"
                                   name="monthly_fee"
                                   value="{{ $license->monthly_fee }}"
                                   required
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Status</strong></label>

                            <select name="status"
                                    style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">

                                <option value="active" {{ $license->status=='active' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="grace" {{ $license->status=='grace' ? 'selected' : '' }}>
                                    Grace Period
                                </option>

                                <option value="expired" {{ $license->status=='expired' ? 'selected' : '' }}>
                                    Expired
                                </option>

                                <option value="suspended" {{ $license->status=='suspended' ? 'selected' : '' }}>
                                    Suspended
                                </option>

                            </select>
                        </div>

                        <div>
                            <label><strong>Start Date</strong></label>

                            <input type="date"
                                   name="start_date"
                                   value="{{ optional($license->start_date)->format('Y-m-d') }}"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Expiry Date</strong></label>

                            <input type="date"
                                   name="expiry_date"
                                   value="{{ optional($license->expiry_date)->format('Y-m-d') }}"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Grace Days</strong></label>

                            <input type="number"
                                   name="grace_days"
                                   value="{{ $license->grace_days }}"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                    </div>

                    <div style="margin-top:20px;">
                        <label><strong>Notes</strong></label>

                        <textarea name="notes"
                                  rows="4"
                                  style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">{{ $license->notes }}</textarea>
                    </div>

                    <button type="submit"
                            style="
                                margin-top:25px;
                                background:#2563eb;
                                color:white;
                                padding:12px 22px;
                                border:none;
                                border-radius:8px;
                                font-weight:700;
                                cursor:pointer;
                            ">
                        <i class="fas fa-save"></i>
                        Save License
                    </button>

                </form>

            </div>

        </div>

    </div>
</x-app-layout>