<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            MikroTik Management
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- QUICK SETUP GUIDE --}}
            <div style="background:white;padding:20px;border-radius:15px;
                        box-shadow:0 4px 12px rgba(0,0,0,0.08);
                        margin-bottom:20px;">

                <details>
                    <summary style="cursor:pointer;
                                    font-size:18px;
                                    font-weight:bold;
                                    color:#2563eb;">
                        📘 How to Connect Your MikroTik Router
                    </summary>

                    <div style="margin-top:20px;
                                background:#f8fafc;
                                padding:20px;
                                border-radius:10px;
                                line-height:1.8;">

                        <h4 style="font-weight:bold;font-size:18px;">
                            Step 1: Enable API on MikroTik
                        </h4>

                        <p>
                            Login to your MikroTik using Winbox or WebFig and open:
                        </p>

                        <div style="background:black;
                                    color:#22c55e;
                                    padding:15px;
                                    border-radius:8px;
                                    margin:10px 0;
                                    font-family:monospace;">
                            /ip service enable api<br>
                            /ip service set api port=8728
                        </div>

                        <h4 style="font-weight:bold;font-size:18px;margin-top:20px;">
                            Step 2: Create an API User
                        </h4>

                        <div style="background:black;
                                    color:#22c55e;
                                    padding:15px;
                                    border-radius:8px;
                                    margin:10px 0;
                                    font-family:monospace;">
                            /user add name=wifibilling password=YourPassword group=full
                        </div>

                        <h4 style="font-weight:bold;font-size:18px;margin-top:20px;">
                            Step 3: Get Router IP Address
                        </h4>

                        <p>
                            Use your MikroTik LAN IP or Public IP.<br>
                            Example:
                        </p>

                        <ul style="margin-left:20px;list-style:disc;">
                            <li>192.168.88.1</li>
                            <li>10.0.0.1</li>
                            <li>41.xxx.xxx.xxx (Public IP)</li>
                        </ul>

                        <h4 style="font-weight:bold;font-size:18px;margin-top:20px;">
                            Step 4: Add Router in WiFi Billing
                        </h4>

                        <ul style="margin-left:20px;list-style:decimal;">
                            <li>Click <strong>Add MikroTik</strong>.</li>
                            <li>Enter Router Name.</li>
                            <li>Enter IP Address.</li>
                            <li>Enter API Username.</li>
                            <li>Enter Password.</li>
                            <li>Leave API Port as <strong>8728</strong>.</li>
                            <li>Save.</li>
                            <li>Click <strong>Test</strong> to verify connection.</li>
                        </ul>

                        <h4 style="font-weight:bold;font-size:18px;margin-top:20px;">
                            Step 5: Run Setup Script
                        </h4>

                        <p>
                            After connecting successfully, click
                            <strong>Setup Script</strong> and copy the generated commands
                            into your MikroTik terminal.
                        </p>

                    </div>
                </details>

            </div>

            {{-- ROUTERS TABLE --}}
            <div style="background:white;padding:25px;
                        border-radius:15px;
                        box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                <div style="display:flex;
                            justify-content:space-between;
                            align-items:center;
                            margin-bottom:20px;">

                    <div>
                        <h3 style="font-size:22px;font-weight:bold;">
                            Registered MikroTik Routers
                        </h3>

                        <p style="color:#6b7280;margin-top:5px;">
                            Manage MikroTik API connections, Hotspot profiles and PPPoE services.
                        </p>
                    </div>

                    <a href="{{ route('admin.routers.create') }}"
                       style="background:#2563eb;
                              color:white;
                              padding:10px 18px;
                              text-decoration:none;
                              border-radius:8px;
                              font-weight:bold;">
                        + Add MikroTik
                    </a>

                </div>

                <table style="width:100%;border-collapse:collapse;">

                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="border:1px solid #e5e7eb;padding:12px;">Name</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">IP Address</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Username</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">API Port</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Status</th>
                            <th style="border:1px solid #e5e7eb;padding:12px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($routers as $router)

                        <tr>

                            <td style="border:1px solid #e5e7eb;padding:12px;font-weight:bold;">
                                {{ $router->name }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                {{ $router->ip_address }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                {{ $router->username }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">
                                {{ $router->api_port }}
                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">

                                @if($router->is_active)

                                    <span style="background:#dcfce7;
                                                 color:#166534;
                                                 padding:6px 12px;
                                                 border-radius:20px;
                                                 font-weight:bold;
                                                 font-size:13px;">

                                        🟢 Active

                                    </span>

                                @else

                                    <span style="background:#fee2e2;
                                                 color:#991b1b;
                                                 padding:6px 12px;
                                                 border-radius:20px;
                                                 font-weight:bold;
                                                 font-size:13px;">

                                        🔴 Inactive

                                    </span>

                                @endif

                            </td>

                            <td style="border:1px solid #e5e7eb;padding:12px;">

                                <div style="display:flex;
                                            gap:8px;
                                            flex-wrap:wrap;">

                                    <a href="{{ route('admin.routers.edit',$router) }}"
                                       style="background:#16a34a;
                                              color:white;
                                              padding:8px 14px;
                                              border-radius:8px;
                                              text-decoration:none;">

                                        Edit

                                    </a>

                                    <a href="{{ route('admin.routers.test',$router) }}"
                                       style="background:#2563eb;
                                              color:white;
                                              padding:8px 14px;
                                              border-radius:8px;
                                              text-decoration:none;">

                                        Test

                                    </a>

                                    <a href="{{ route('admin.routers.setup-script',$router) }}"
                                       style="background:#7c3aed;
                                              color:white;
                                              padding:8px 14px;
                                              border-radius:8px;
                                              text-decoration:none;">

                                        Setup Script

                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.routers.destroy',$router) }}">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm('Delete this MikroTik router?')"
                                            style="background:#dc2626;
                                                   color:white;
                                                   padding:8px 14px;
                                                   border-radius:8px;
                                                   border:none;
                                                   cursor:pointer;">

                                            Delete

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                style="border:1px solid #e5e7eb;
                                       padding:25px;
                                       text-align:center;
                                       color:#6b7280;">

                                No MikroTik routers found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>
</x-app-layout>
