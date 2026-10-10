<x-app-layout>
    <x-slot name="header">
        <div class="router-page-header">
            <div class="router-page-header__icon">
                <i class="fas fa-network-wired" aria-hidden="true"></i>
            </div>

            <div>
                <h2>MikroTik Management</h2>
                <p>Manage your ISP router connections and network services.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .router-page {
            --router-bg: #f3f6fa;
            --router-card: #ffffff;
            --router-card-soft: #f8fafc;
            --router-text: #0f172a;
            --router-muted: #64748b;
            --router-border: #e2e8f0;
            --router-table-head: #f8fafc;
            --router-table-hover: #f8fbff;
            --router-input: #ffffff;
            --router-blue: #2563eb;

            min-height: 100vh;
            padding: 48px 24px 60px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.55),
                    transparent 30%
                ),
                var(--router-bg);
            color: var(--router-text);
            transition:
                background-color .22s ease,
                color .22s ease;
        }

        html.dark .router-page {
            --router-bg: #081225;
            --router-card: #0f1b31;
            --router-card-soft: #111f38;
            --router-text: #e8efff;
            --router-muted: #9baaca;
            --router-border: #263957;
            --router-table-head: #14243f;
            --router-table-hover: #142641;
            --router-input: #0b172b;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.12),
                    transparent 32%
                ),
                var(--router-bg);
        }

        .router-page-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .router-page-header__icon {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            box-shadow: 0 8px 20px rgba(37, 99, 235, .22);
        }

        .router-page-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
        }

        .router-page-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        html.dark .router-page-header h2 {
            color: #f8fafc !important;
        }

        html.dark .router-page-header p {
            color: #9baaca !important;
        }

        .router-container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
        }

        .router-card {
            border: 1px solid var(--router-border);
            border-radius: 20px;
            background: var(--router-card);
            box-shadow: 0 8px 26px rgba(15, 23, 42, .07);
        }

        html.dark .router-card {
            box-shadow: 0 12px 32px rgba(0, 0, 0, .22);
        }

        .router-guide {
            margin-bottom: 20px;
            padding: 20px;
        }

        .router-guide summary {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2563eb;
            cursor: pointer;
            font-size: 18px;
            font-weight: 800;
            list-style: none;
        }

        .router-guide summary::-webkit-details-marker {
            display: none;
        }

        .router-guide summary::before {
            content: "\f0da";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 15px;
            transition: transform .2s ease;
        }

        .router-guide details[open] summary::before {
            transform: rotate(90deg);
        }

        html.dark .router-guide summary {
            color: #78a6ff;
        }

        .router-guide-content {
            margin-top: 20px;
            padding: 22px;
            border: 1px solid var(--router-border);
            border-radius: 14px;
            background: var(--router-card-soft);
            color: var(--router-text);
            line-height: 1.8;
        }

        .router-guide-content h4 {
            margin: 0;
            color: var(--router-text);
            font-size: 17px;
            font-weight: 800;
        }

        .router-guide-content h4:not(:first-child) {
            margin-top: 25px;
        }

        .router-guide-content p {
            margin: 10px 0;
            color: var(--router-muted);
        }

        .router-guide-content ul,
        .router-guide-content ol {
            margin: 10px 0 10px 22px;
            padding: 0;
            color: var(--router-muted);
        }

        .router-guide-content ul {
            list-style: disc;
        }

        .router-guide-content ol {
            list-style: decimal;
        }

        .router-guide-content li {
            margin: 5px 0;
        }

        .router-guide-content strong {
            color: var(--router-text);
        }

        .router-command {
            margin: 12px 0;
            padding: 15px;
            overflow-x: auto;
            border: 1px solid #25334a;
            border-radius: 10px;
            background: #111827;
            color: #a7f3d0;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            line-height: 1.7;
        }

        html.dark .router-command {
            border-color: #334155;
            background: #050d1b;
            color: #86efac;
        }

        .router-security-notice {
            margin-top: 13px;
            padding: 14px 16px;
            border: 1px solid #fde68a;
            border-radius: 11px;
            background: #fffbeb;
            color: #92400e;
        }

        html.dark .router-security-notice {
            border-color: #6b5518;
            background: #30270f;
            color: #f9dc82;
        }

        html.dark .router-security-notice strong {
            color: #fde68a;
        }

        .router-table-card {
            padding: 25px;
        }

        .router-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .router-table-title {
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .router-table-title__icon {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: #dbeafe;
            color: #2563eb;
        }

        html.dark .router-table-title__icon {
            background: rgba(37, 99, 235, .17);
            color: #7eabff;
        }

        .router-table-title h3 {
            margin: 0;
            color: var(--router-text);
            font-size: 21px;
            font-weight: 800;
        }

        .router-table-title p {
            margin: 4px 0 0;
            color: var(--router-muted);
            font-size: 13px;
        }

        .router-add-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 10px 17px;
            border-radius: 11px;
            background: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            box-shadow: 0 7px 18px rgba(37, 99, 235, .18);
            transition:
                background .18s ease,
                transform .18s ease;
        }

        .router-add-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .router-table-wrapper {
            overflow-x: auto;
            border: 1px solid var(--router-border);
            border-radius: 13px;
        }

        .router-table {
            width: 100%;
            min-width: 920px;
            border-collapse: collapse;
            color: var(--router-text);
        }

        .router-table thead tr {
            background: var(--router-table-head);
        }

        .router-table th {
            padding: 13px;
            border-right: 1px solid var(--router-border);
            border-bottom: 1px solid var(--router-border);
            color: var(--router-muted);
            text-align: left;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .router-table th:last-child {
            border-right: 0;
        }

        .router-table td {
            padding: 13px;
            border-right: 1px solid var(--router-border);
            border-bottom: 1px solid var(--router-border);
            color: var(--router-text);
            font-size: 13px;
            vertical-align: middle;
        }

        .router-table td:last-child {
            border-right: 0;
        }

        .router-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .router-table tbody tr:hover {
            background: var(--router-table-hover);
        }

        .router-name {
            color: var(--router-text);
            font-weight: 800;
        }

        .router-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }

        .router-status.active {
            border: 1px solid #bbf7d0;
            background: #dcfce7;
            color: #166534;
        }

        .router-status.inactive {
            border: 1px solid #fecaca;
            background: #fee2e2;
            color: #991b1b;
        }

        html.dark .router-status.active {
            border-color: #245f42;
            background: #153b2a;
            color: #8ce8b3;
        }

        html.dark .router-status.inactive {
            border-color: #71313c;
            background: #421f28;
            color: #ffadb9;
        }

        .router-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }

        .router-actions form {
            margin: 0;
        }

        .router-action {
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 12px;
            border: 0;
            border-radius: 9px;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition:
                transform .18s ease,
                filter .18s ease;
        }

        .router-action:hover {
            transform: translateY(-1px);
            filter: brightness(.96);
        }

        .router-action.edit {
            background: #16a34a;
        }

        .router-action.test {
            background: #2563eb;
        }

        .router-action.script {
            background: #7c3aed;
        }

        .router-action.delete {
            background: #dc2626;
        }

        .router-empty {
            padding: 40px 20px !important;
            color: var(--router-muted) !important;
            text-align: center;
        }

        .router-empty i {
            display: block;
            margin-bottom: 10px;
            color: #64748b;
            font-size: 25px;
        }

        @media (max-width: 767px) {
            .router-page {
                padding: 30px 14px 45px;
            }

            .router-page-header h2 {
                font-size: 20px;
            }

            .router-page-header p {
                display: none;
            }

            .router-table-card {
                padding: 18px;
            }

            .router-table-title h3 {
                font-size: 18px;
            }

            .router-table-title p {
                font-size: 12px;
            }

            .router-table-header {
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .router-page {
                padding-left: 10px;
                padding-right: 10px;
            }

            .router-guide,
            .router-table-card {
                border-radius: 16px;
            }

            .router-add-button {
                width: 100%;
            }
        }
    </style>

    <div class="router-page">
        <div class="router-container">

            {{-- Global success/error/warning messages are rendered by layouts/app.blade.php --}}

            <div class="router-card router-guide">
                <details>
                    <summary>
                        How to Connect Your MikroTik Router
                    </summary>

                    <div class="router-guide-content">
                        <h4>Step 1: Enable the MikroTik API</h4>

                        <p>
                            Log in to the router using Winbox, WebFig, or the MikroTik terminal.
                            Enable the API service and use the same API port that you enter in this system.
                        </p>

                        <div class="router-command">
                            /ip service enable api<br>
                            /ip service set api port=8728
                        </div>

                        <div class="router-security-notice">
                            <strong>Security:</strong>
                            Do not expose the MikroTik API to the entire public internet.
                            Restrict access using your router firewall, trusted management
                            addresses, private networking, or a VPN.
                        </div>

                        <h4>Step 2: Create a Dedicated API Account</h4>

                        <p>
                            Use a separate MikroTik account for this billing system instead
                            of your normal router administrator account.
                        </p>

                        <div class="router-security-notice">
                            Do not reuse your main router administrator password.
                            Give the billing account only the RouterOS permissions required
                            for the features you use.
                        </div>

                        <p>
                            The exact minimum RouterOS policy will be finalized when real
                            MikroTik integration is tested, because the required permissions
                            depend on the Hotspot and PPPoE commands used by the connected router.
                        </p>

                        <h4>Step 3: Identify the Router Address</h4>

                        <p>
                            Enter an IP address that the billing server can actually reach.
                        </p>

                        <ul>
                            <li>192.168.88.1</li>
                            <li>10.0.0.1</li>
                            <li>A private VPN address</li>
                            <li>A secured public address with strict firewall restrictions</li>
                        </ul>

                        <h4>Step 4: Add the Router</h4>

                        <ol>
                            <li>Click <strong>Add MikroTik</strong>.</li>
                            <li>Enter a router name.</li>
                            <li>Enter the router IP address.</li>
                            <li>Enter the dedicated API username.</li>
                            <li>Enter the API account password.</li>
                            <li>Enter the configured API port.</li>
                            <li>Save the router.</li>
                            <li>Use <strong>Test Connection</strong> to verify connectivity.</li>
                        </ol>

                        <h4>Step 5: Review the Setup Script</h4>

                        <p>
                            Use the Setup Script page to review commands generated for this
                            router before applying them in the MikroTik terminal.
                        </p>
                    </div>
                </details>
            </div>

            <div class="router-card router-table-card">
                <div class="router-table-header">
                    <div class="router-table-title">
                        <div class="router-table-title__icon">
                            <i class="fas fa-network-wired" aria-hidden="true"></i>
                        </div>

                        <div>
                            <h3>Registered MikroTik Routers</h3>

                            <p>
                                Manage MikroTik connections, Hotspot profiles and PPPoE services.
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.routers.create') }}"
                        class="router-add-button"
                    >
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        Add MikroTik
                    </a>
                </div>

                <div class="router-table-wrapper">
                    <table class="router-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>IP Address</th>
                                <th>Username</th>
                                <th>API Port</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($routers as $router)
                                <tr>
                                    <td>
                                        <span class="router-name">
                                            {{ $router->name }}
                                        </span>
                                    </td>

                                    <td>{{ $router->ip_address }}</td>

                                    <td>{{ $router->username }}</td>

                                    <td>{{ $router->api_port }}</td>

                                    <td>{{ $router->router_type ?: 'MikroTik' }}</td>

                                    <td>
                                        @if($router->is_active)
                                            <span class="router-status active">
                                                <i
                                                    class="fas fa-circle-check"
                                                    aria-hidden="true"
                                                ></i>
                                                Active
                                            </span>
                                        @else
                                            <span class="router-status inactive">
                                                <i
                                                    class="fas fa-circle-xmark"
                                                    aria-hidden="true"
                                                ></i>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="router-actions">
                                            <a
                                                href="{{ route('admin.routers.edit', $router) }}"
                                                class="router-action edit"
                                            >
                                                <i
                                                    class="fas fa-pen-to-square"
                                                    aria-hidden="true"
                                                ></i>
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.routers.test', $router) }}"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="router-action test"
                                                >
                                                    <i
                                                        class="fas fa-plug-circle-check"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Test Connection
                                                </button>
                                            </form>

                                            <a
                                                href="{{ route('admin.routers.setup-script', $router) }}"
                                                class="router-action script"
                                            >
                                                <i
                                                    class="fas fa-terminal"
                                                    aria-hidden="true"
                                                ></i>
                                                Setup Script
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.routers.destroy', $router) }}"
                                                onsubmit="return confirm('Delete this MikroTik router? This is only allowed when the router has no linked customer credentials or session records.');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="router-action delete"
                                                >
                                                    <i
                                                        class="fas fa-trash-can"
                                                        aria-hidden="true"
                                                    ></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="7"
                                        class="router-empty"
                                    >
                                        <i
                                            class="fas fa-network-wired"
                                            aria-hidden="true"
                                        ></i>

                                        No MikroTik routers have been added yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>