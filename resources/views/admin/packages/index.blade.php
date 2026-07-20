<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Package Management
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:1250px;margin:0 auto;">
            <div style="background:white;padding:28px;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
                    <div>
                        <h3 style="font-size:26px;font-weight:800;color:#111827;margin:0;">
                            <i class="fas fa-box" style="color:#2563eb;"></i>
                            Internet Packages
                        </h3>
                        <p style="color:#6b7280;margin-top:6px;">
                            Manage Hotspot, PPPoE, real speeds, unlimited options and router profiles.
                        </p>
                    </div>

                    <a href="{{ route('admin.packages.create') }}"
                       style="background:#2563eb;color:white;padding:12px 20px;text-decoration:none;border-radius:8px;font-weight:800;">
                        <i class="fas fa-plus"></i>
                        Add New Package
                    </a>
                </div>

                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="border:1px solid #e5e7eb;padding:13px;text-align:left;">Package</th>
                            <th style="border:1px solid #e5e7eb;padding:13px;text-align:left;">Type</th>
                            <th style="border:1px solid #e5e7eb;padding:13px;text-align:left;">Speed</th>
                            <th style="border:1px solid #e5e7eb;padding:13px;text-align:left;">Router Profile</th>
                            <th style="border:1px solid #e5e7eb;padding:13px;text-align:left;">Price</th>
                            <th style="border:1px solid #e5e7eb;padding:13px;text-align:left;">Duration</th>
                            <th style="border:1px solid #e5e7eb;padding:13px;text-align:left;">Status</th>
                            <th style="border:1px solid #e5e7eb;padding:13px;text-align:center;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td style="border:1px solid #e5e7eb;padding:13px;">
                                    <strong>{{ $package->name }}</strong><br>
                                    <small style="color:#6b7280;">
                                        {{ $package->description ?? 'No description' }}
                                    </small>
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:13px;">
                                    @if($package->package_type === 'pppoe')
                                        <span style="background:#fef3c7;color:#92400e;padding:6px 12px;border-radius:20px;font-weight:800;">
                                            <i class="fas fa-network-wired"></i> PPPoE
                                        </span>
                                    @else
                                        <span style="background:#dbeafe;color:#1d4ed8;padding:6px 12px;border-radius:20px;font-weight:800;">
                                            <i class="fas fa-wifi"></i> Hotspot
                                        </span>
                                    @endif
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:13px;">
                                    <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:800;">
                                        {{ $package->download_speed ?? '-' }}
                                    </span>
                                    /
                                    <span style="background:#e0f2fe;color:#075985;padding:6px 12px;border-radius:20px;font-weight:800;">
                                        {{ $package->upload_speed ?? '-' }}
                                    </span>
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:13px;">
                                    <code style="background:#f1f5f9;color:#334155;padding:6px 10px;border-radius:8px;font-weight:700;">
                                        {{ $package->router_profile ?? 'Not Set' }}
                                    </code>
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:13px;font-weight:800;color:#16a34a;">
                                    KES {{ number_format($package->price, 2) }}
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:13px;">
                                    @if($package->is_unlimited)
                                        <span style="background:#fef3c7;color:#92400e;padding:6px 12px;border-radius:20px;font-weight:800;">
                                            Unlimited
                                        </span>
                                    @else
                                        {{ $package->duration_hours }} Hours
                                    @endif
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:13px;">
                                    @if($package->is_active)
                                        <span style="background:#dcfce7;color:#166534;padding:6px 12px;border-radius:20px;font-weight:800;">
                                            Active
                                        </span>
                                    @else
                                        <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:20px;font-weight:800;">
                                            Disabled
                                        </span>
                                    @endif
                                </td>

                                <td style="border:1px solid #e5e7eb;padding:13px;text-align:center;white-space:nowrap;">
                                    <a href="{{ route('admin.packages.edit', $package) }}"
                                       style="background:#16a34a;color:white;padding:8px 13px;text-decoration:none;border-radius:8px;font-weight:700;display:inline-block;margin-bottom:5px;">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.packages.sync-profile', $package) }}"
                                          style="display:inline-block;margin:0;">
                                        @csrf
                                        <button type="submit"
                                                style="background:#2563eb;color:white;padding:8px 13px;border-radius:8px;border:none;font-weight:700;cursor:pointer;margin-bottom:5px;">
                                            Sync
                                        </button>
                                    </form>

                                    <form method="POST"
                                          action="{{ route('admin.packages.destroy', $package) }}"
                                          style="display:inline-block;margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Delete this package?')"
                                                style="background:#dc2626;color:white;padding:8px 13px;border-radius:8px;border:none;font-weight:700;cursor:pointer;">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding:30px;text-align:center;color:#6b7280;border:1px solid #e5e7eb;">
                                    No packages found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>