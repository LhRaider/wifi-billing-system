<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Internet Package
        </h2>
    </x-slot>

    <div class="py-10" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-5xl mx-auto">
            <form method="POST" action="{{ route('admin.packages.store') }}">
                @csrf

                <div style="background:white;border-radius:16px;padding:30px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                    <h2 style="font-size:28px;font-weight:800;margin-bottom:10px;">
                        <i class="fas fa-box"></i> Create Package
                    </h2>

                    <p style="color:#6b7280;margin-bottom:30px;">
                        Create Hotspot or PPPoE packages with real upload/download speed limits.
                    </p>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div>
                            <label><strong>Package Name</strong></label>
                            <input type="text" name="name" required placeholder="10 Mbps Unlimited"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Package Type</strong></label>
                            <select name="package_type" required
                                    style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                                <option value="hotspot">Hotspot</option>
                                <option value="pppoe">PPPoE</option>
                            </select>
                        </div>

                        <div>
                            <label><strong>Download Speed</strong></label>
                            <input type="text" name="download_speed" required placeholder="10M"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Upload Speed</strong></label>
                            <input type="text" name="upload_speed" required placeholder="5M"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Price (KES)</strong></label>
                            <input type="number" name="price" required placeholder="100"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Duration (Hours)</strong></label>
                            <input type="number" name="duration_hours" placeholder="24"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div>
                            <label><strong>Router Profile</strong></label>
                            <input type="text" name="router_profile" required placeholder="10M_5M"
                                   style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;">
                        </div>

                        <div style="display:flex;align-items:center;gap:10px;margin-top:25px;">
                            <input type="checkbox" name="is_unlimited" id="is_unlimited">
                            <label for="is_unlimited"><strong>Unlimited Package</strong></label>
                        </div>
                    </div>

                    <div style="margin-top:20px;">
                        <label><strong>Description</strong></label>
                        <textarea name="description" rows="4" placeholder="Monthly unlimited PPPoE package"
                                  style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;"></textarea>
                    </div>

                    <div style="margin-top:20px;">
                        <label>
                            <input type="checkbox" name="is_active" checked>
                            Active Package
                        </label>
                    </div>

                    <hr style="margin:25px 0;">

                    <button type="submit"
                            style="background:#2563eb;color:white;padding:12px 24px;border:none;border-radius:8px;font-weight:700;cursor:pointer;">
                        <i class="fas fa-save"></i> Save Package
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>