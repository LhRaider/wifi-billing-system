<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Internet Package
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:900px;margin:0 auto;">
            <div style="background:white;border-radius:16px;padding:30px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">

                <h3 style="font-size:26px;font-weight:800;margin-bottom:10px;color:#111827;">
                    <i class="fas fa-edit" style="color:#2563eb;"></i>
                    Edit Package
                </h3>

                <p style="color:#6b7280;margin-bottom:25px;">
                    Update package type, real speed limits, pricing and duration.
                </p>

                <form method="POST" action="{{ route('admin.packages.update', $package) }}">
                    @csrf
                    @method('PUT')

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                        <div>
                            <label style="font-weight:700;">Package Name</label>
                            <input type="text" name="name" value="{{ $package->name }}" required
                                   style="width:100%;margin-top:8px;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label style="font-weight:700;">Package Type</label>
                            <select name="package_type"
                                    style="width:100%;margin-top:8px;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                                <option value="hotspot" {{ $package->package_type == 'hotspot' ? 'selected' : '' }}>Hotspot</option>
                                <option value="pppoe" {{ $package->package_type == 'pppoe' ? 'selected' : '' }}>PPPoE</option>
                            </select>
                        </div>

                        <div>
                            <label style="font-weight:700;">Download Speed</label>
                            <input type="text" name="download_speed" value="{{ $package->download_speed ?? '10M' }}" required
                                   placeholder="10M"
                                   style="width:100%;margin-top:8px;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label style="font-weight:700;">Upload Speed</label>
                            <input type="text" name="upload_speed" value="{{ $package->upload_speed ?? '5M' }}" required
                                   placeholder="5M"
                                   style="width:100%;margin-top:8px;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label style="font-weight:700;">Price (KES)</label>
                            <input type="number" step="0.01" name="price" value="{{ $package->price }}" required
                                   style="width:100%;margin-top:8px;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label style="font-weight:700;">Duration (Hours)</label>
                            <input type="number" name="duration_hours" value="{{ $package->duration_hours }}" placeholder="24"
                                   style="width:100%;margin-top:8px;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label style="font-weight:700;">Router Profile</label>
                            <input type="text" name="router_profile" value="{{ $package->router_profile }}" required
                                   placeholder="10M_5M"
                                   style="width:100%;margin-top:8px;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div style="display:flex;align-items:center;gap:10px;margin-top:35px;">
                            <input type="checkbox" name="is_unlimited" id="is_unlimited" {{ $package->is_unlimited ? 'checked' : '' }}>
                            <label for="is_unlimited" style="font-weight:700;">Unlimited Package</label>
                        </div>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-weight:700;">Description</label>
                        <textarea name="description" rows="4"
                                  style="width:100%;margin-top:8px;padding:12px;border:1px solid #d1d5db;border-radius:8px;">{{ $package->description }}</textarea>
                    </div>

                    <div style="background:#f8fafc;padding:15px;border-radius:10px;margin-bottom:25px;">
                        <label style="font-weight:700;">
                            <input type="checkbox" name="is_active" {{ $package->is_active ? 'checked' : '' }}>
                            Package Active
                        </label>
                    </div>

                    <div style="display:flex;gap:12px;">
                        <button type="submit"
                                style="background:#16a34a;color:white;padding:12px 22px;border:none;border-radius:8px;font-weight:700;cursor:pointer;">
                            <i class="fas fa-save"></i>
                            Update Package
                        </button>

                        <a href="{{ route('admin.packages.index') }}"
                           style="background:#64748b;color:white;padding:12px 22px;text-decoration:none;border-radius:8px;font-weight:700;">
                            <i class="fas fa-arrow-left"></i>
                            Back
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>