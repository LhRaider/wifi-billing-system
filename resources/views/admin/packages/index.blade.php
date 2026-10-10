<x-app-layout>
    <x-slot name="header">
        <div class="package-header">
            <div class="package-header-title">
                <span class="package-header-icon">
                    <i class="fas fa-boxes-stacked" aria-hidden="true"></i>
                </span>

                <div>
                    <h2>Package Management</h2>
                    <p>Manage the internet packages offered by your ISP.</p>
                </div>
            </div>

            <a
                href="{{ route('admin.packages.create') }}"
                class="add-package-button"
            >
                <i class="fas fa-plus" aria-hidden="true"></i>
                <span>Add Package</span>
            </a>
        </div>
    </x-slot>

    <style>
        .package-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .package-header-title {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
        }

        .package-header-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.22);
        }

        .package-header-title h2 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
        }

        .package-header-title p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        html.dark .package-header-title h2 {
            color: #f8fafc !important;
        }

        html.dark .package-header-title p {
            color: #94a3b8 !important;
        }

        .add-package-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 16px;
            border-radius: 12px;
            background: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            box-shadow: 0 7px 17px rgba(37, 99, 235, 0.18);
            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }

        .add-package-button:hover {
            background: #1d4ed8;
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        .package-page {
            --pkg-page-bg: #f4f7fb;
            --pkg-surface: #ffffff;
            --pkg-surface-soft: #f8fafc;
            --pkg-table-head: #f8fafc;
            --pkg-row-hover: #fbfdff;
            --pkg-text: #0f172a;
            --pkg-body-text: #334155;
            --pkg-muted: #64748b;
            --pkg-border: #e6ecf3;
            --pkg-border-soft: #edf1f5;

            min-height: 100vh;
            padding: 34px 25px 55px;
            color: var(--pkg-text);
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.65),
                    transparent 30%
                ),
                var(--pkg-page-bg);
        }

        html.dark .package-page {
            --pkg-page-bg: #091226;
            --pkg-surface: #101b31;
            --pkg-surface-soft: #14213a;
            --pkg-table-head: #152139;
            --pkg-row-hover: #14223b;
            --pkg-text: #f1f5f9;
            --pkg-body-text: #d7e1ef;
            --pkg-muted: #94a3b8;
            --pkg-border: #293a56;
            --pkg-border-soft: #293a56;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.11),
                    transparent 31%
                ),
                var(--pkg-page-bg);
        }

        .package-container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
        }

        .package-summary {
            margin-bottom: 22px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .summary-card {
            padding: 18px;
            border: 1px solid var(--pkg-border);
            border-radius: 17px;
            background: var(--pkg-surface);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.045);
            display: flex;
            align-items: center;
            gap: 14px;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-1px);
        }

        html.dark .summary-card {
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.16);
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .summary-icon.blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .summary-icon.green {
            background: #dcfce7;
            color: #16a34a;
        }

        .summary-icon.slate {
            background: #e2e8f0;
            color: #475569;
        }

        html.dark .summary-icon.blue {
            background: rgba(37, 99, 235, 0.20);
            color: #93c5fd;
        }

        html.dark .summary-icon.green {
            background: rgba(22, 163, 74, 0.18);
            color: #86efac;
        }

        html.dark .summary-icon.slate {
            background: #293850;
            color: #cbd5e1;
        }

        .summary-content span {
            display: block;
            margin-bottom: 3px;
            color: var(--pkg-muted);
            font-size: 10px;
            font-weight: 700;
        }

        .summary-content strong {
            color: var(--pkg-text);
            font-size: 20px;
            line-height: 1.2;
            font-weight: 800;
        }

        html.dark .summary-content span {
            color: #94a3b8 !important;
        }

        html.dark .summary-content strong {
            color: #f8fafc !important;
        }

        .package-panel {
            overflow: hidden;
            border: 1px solid var(--pkg-border);
            border-radius: 20px;
            background: var(--pkg-surface);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.055);
        }

        html.dark .package-panel {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.20);
        }

        .package-panel-header {
            padding: 21px 23px;
            border-bottom: 1px solid var(--pkg-border-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .package-panel-heading {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .package-panel-heading-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        html.dark .package-panel-heading-icon {
            background: #16294b;
            color: #93c5fd;
        }

        .package-panel-heading h3 {
            margin: 0;
            color: var(--pkg-text);
            font-size: 17px;
            font-weight: 800;
        }

        .package-panel-heading p {
            margin: 3px 0 0;
            color: var(--pkg-muted);
            font-size: 11px;
        }

        html.dark .package-panel-heading h3 {
            color: #f8fafc !important;
        }

        html.dark .package-panel-heading p {
            color: #94a3b8 !important;
        }

        .record-count {
            padding: 7px 11px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        html.dark .record-count {
            background: #26364e;
            color: #cbd5e1;
        }

        .package-table-wrapper {
            width: 100%;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: #64748b transparent;
        }

        .package-table {
            width: 100%;
            min-width: 1220px;
            border-collapse: collapse;
        }

        .package-table th {
            padding: 12px 14px;
            border-bottom: 1px solid var(--pkg-border);
            background: var(--pkg-table-head);
            color: var(--pkg-muted);
            text-align: left;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.045em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .package-table th:last-child {
            text-align: right;
        }

        .package-table td {
            padding: 14px;
            border-bottom: 1px solid var(--pkg-border-soft);
            color: var(--pkg-body-text);
            background: var(--pkg-surface);
            vertical-align: middle;
            font-size: 11px;
            transition: background 0.15s ease;
        }

        .package-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .package-table tbody tr:hover td {
            background: var(--pkg-row-hover);
        }

        html.dark .package-table th {
            background: #152139 !important;
            color: #aebed3 !important;
            border-color: #293a56 !important;
        }

        html.dark .package-table td {
            background: #101b31 !important;
            color: #d7e1ef !important;
            border-color: #293a56 !important;
        }

        html.dark .package-table tbody tr:hover td {
            background: #14223b !important;
        }

        .package-name {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 190px;
        }

        .package-name-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        html.dark .package-name-icon {
            background: #16294b;
            color: #93c5fd;
        }

        .package-name strong {
            display: block;
            color: var(--pkg-text);
            font-size: 11px;
            font-weight: 800;
        }

        .package-name small {
            display: block;
            max-width: 230px;
            margin-top: 3px;
            color: var(--pkg-muted);
            font-size: 9px;
            line-height: 1.45;
        }

        html.dark .package-name strong {
            color: #f1f5f9 !important;
        }

        html.dark .package-name small {
            color: #8fa1bb !important;
        }

        .package-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 9px;
            line-height: 1.3;
            font-weight: 800;
            white-space: nowrap;
        }

        .package-badge.hotspot {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .package-badge.pppoe {
            background: #fef3c7;
            color: #92400e;
        }

        .package-badge.active {
            background: #dcfce7;
            color: #166534;
        }

        .package-badge.disabled {
            background: #fee2e2;
            color: #991b1b;
        }

        .package-badge.duration {
            background: #f3e8ff;
            color: #7e22ce;
        }

        html.dark .package-badge.hotspot {
            background: rgba(37, 99, 235, 0.20);
            color: #a8c7ff;
        }

        html.dark .package-badge.pppoe {
            background: rgba(245, 158, 11, 0.18);
            color: #fde68a;
        }

        html.dark .package-badge.active {
            background: rgba(22, 163, 74, 0.19);
            color: #9ae6b4;
        }

        html.dark .package-badge.disabled {
            background: rgba(220, 38, 38, 0.18);
            color: #fca5a5;
        }

        html.dark .package-badge.duration {
            background: rgba(147, 51, 234, 0.20);
            color: #d8b4fe;
        }

        .speed-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 95px;
        }

        .speed-row {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--pkg-muted);
            font-size: 9.5px;
            white-space: nowrap;
        }

        .speed-row i {
            width: 12px;
            color: #64748b;
            text-align: center;
        }

        .speed-row strong {
            color: var(--pkg-text);
        }

        html.dark .speed-row {
            color: #aab8cc !important;
        }

        html.dark .speed-row i {
            color: #8fa1bb !important;
        }

        html.dark .speed-row strong {
            color: #e2e8f0 !important;
        }

        .speed-unlimited {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            width: fit-content;
            padding: 5px 9px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 9px;
            font-weight: 800;
            white-space: nowrap;
        }

        html.dark .speed-unlimited {
            background: rgba(5, 150, 105, 0.18);
            color: #6ee7b7;
        }

        .router-profile {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            max-width: 170px;
            padding: 5px 8px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #334155;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 9px;
            font-weight: 700;
            word-break: break-all;
        }

        html.dark .router-profile {
            background: #26364e;
            color: #d5dfec;
        }

        .package-price {
            color: #15803d;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }

        html.dark .package-price {
            color: #86efac;
        }

        .action-list {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 5px;
            flex-wrap: wrap;
            min-width: 280px;
        }

        .action-list form {
            margin: 0;
        }

        .action-button {
            min-height: 31px;
            padding: 6px 9px;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            text-decoration: none;
            font-size: 9px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }

        .action-button:hover {
            transform: translateY(-1px);
        }

        .action-button.edit {
            background: #dcfce7;
            color: #166534;
        }

        .action-button.sync {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .action-button.deactivate {
            background: #fef3c7;
            color: #92400e;
        }

        .action-button.activate {
            background: #dcfce7;
            color: #166534;
        }

        .action-button.delete {
            background: #fee2e2;
            color: #991b1b;
        }

        html.dark .action-button.edit,
        html.dark .action-button.activate {
            background: rgba(22, 163, 74, 0.19);
            color: #9ae6b4;
        }

        html.dark .action-button.sync {
            background: rgba(37, 99, 235, 0.20);
            color: #a8c7ff;
        }

        html.dark .action-button.deactivate {
            background: rgba(245, 158, 11, 0.18);
            color: #fde68a;
        }

        html.dark .action-button.delete {
            background: rgba(220, 38, 38, 0.18);
            color: #fca5a5;
        }

        .empty-state {
            padding: 55px 25px !important;
            text-align: center;
        }

        .empty-state-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            border-radius: 17px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .empty-state strong {
            display: block;
            color: var(--pkg-text);
            font-size: 14px;
        }

        .empty-state span {
            display: block;
            margin-top: 5px;
            color: var(--pkg-muted);
            font-size: 11px;
        }

        html.dark .empty-state-icon {
            background: #16294b;
            color: #93c5fd;
        }

        html.dark .empty-state strong {
            color: #f1f5f9 !important;
        }

        html.dark .empty-state span {
            color: #94a3b8 !important;
        }

        .history-notice {
            margin: 20px 23px 23px;
            padding: 13px 15px;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: #f8fbff;
            color: #475569;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 10px;
            line-height: 1.6;
        }

        .history-notice i {
            margin-top: 2px;
            color: #2563eb;
        }

        .history-notice strong {
            color: #1e3a8a;
        }

        html.dark .history-notice {
            border-color: #29466f;
            background: #12213a;
            color: #aebed3;
        }

        html.dark .history-notice i {
            color: #7da7ff;
        }

        html.dark .history-notice strong {
            color: #bfdbfe;
        }

        @media (max-width: 900px) {
            .package-summary {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .package-header-title h2 {
                font-size: 20px;
            }

            .package-header-title p {
                display: none;
            }

            .package-header-icon {
                width: 44px;
                height: 44px;
            }

            .package-page {
                padding: 25px 14px 45px;
            }

            .package-panel-header {
                padding: 19px;
            }

            .package-panel-heading p {
                display: none;
            }

            .history-notice {
                margin-left: 18px;
                margin-right: 18px;
            }
        }

        @media (max-width: 480px) {
            .package-page {
                padding-left: 11px;
                padding-right: 11px;
            }

            .package-header {
                gap: 10px;
            }

            .add-package-button span {
                display: none;
            }

            .add-package-button {
                width: 42px;
                padding: 0;
            }

            .package-panel {
                border-radius: 17px;
            }

            .package-panel-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    @php
        $activePackageCount = $packages->where('is_active', true)->count();
        $inactivePackageCount = $packages->where('is_active', false)->count();
    @endphp

    <div class="package-page">
        <div class="package-container">

            <div class="package-summary">
                <div class="summary-card">
                    <div class="summary-icon blue">
                        <i class="fas fa-boxes-stacked" aria-hidden="true"></i>
                    </div>

                    <div class="summary-content">
                        <span>Total Packages</span>
                        <strong>{{ $packages->count() }}</strong>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon green">
                        <i class="fas fa-circle-check" aria-hidden="true"></i>
                    </div>

                    <div class="summary-content">
                        <span>Active Packages</span>
                        <strong>{{ $activePackageCount }}</strong>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon slate">
                        <i class="fas fa-circle-pause" aria-hidden="true"></i>
                    </div>

                    <div class="summary-content">
                        <span>Disabled Packages</span>
                        <strong>{{ $inactivePackageCount }}</strong>
                    </div>
                </div>
            </div>

            <div class="package-panel">
                <div class="package-panel-header">
                    <div class="package-panel-heading">
                        <span class="package-panel-heading-icon">
                            <i class="fas fa-wifi" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h3>Internet Packages</h3>
                            <p>
                                Hotspot and PPPoE packages belonging to your ISP account.
                            </p>
                        </div>
                    </div>

                    <span class="record-count">
                        {{ $packages->count() }}
                        {{ $packages->count() === 1 ? 'Package' : 'Packages' }}
                    </span>
                </div>

                <div class="package-table-wrapper">
                    <table class="package-table">
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Type</th>
                                <th>Speed</th>
                                <th>Router Profile</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($packages as $package)
                                <tr>
                                    <td>
                                        <div class="package-name">
                                            <span class="package-name-icon">
                                                @if($package->package_type === 'pppoe')
                                                    <i
                                                        class="fas fa-network-wired"
                                                        aria-hidden="true"
                                                    ></i>
                                                @else
                                                    <i
                                                        class="fas fa-wifi"
                                                        aria-hidden="true"
                                                    ></i>
                                                @endif
                                            </span>

                                            <div>
                                                <strong>{{ $package->name }}</strong>

                                                <small>
                                                    {{ $package->description ?: 'No description provided.' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if($package->package_type === 'pppoe')
                                            <span class="package-badge pppoe">
                                                <i
                                                    class="fas fa-network-wired"
                                                    aria-hidden="true"
                                                ></i>
                                                PPPoE
                                            </span>
                                        @else
                                            <span class="package-badge hotspot">
                                                <i
                                                    class="fas fa-wifi"
                                                    aria-hidden="true"
                                                ></i>
                                                Hotspot
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($package->is_speed_unlimited)
                                            <span class="speed-unlimited">
                                                <i
                                                    class="fas fa-infinity"
                                                    aria-hidden="true"
                                                ></i>
                                                Unlimited
                                            </span>
                                        @else
                                            <div class="speed-list">
                                                <span class="speed-row">
                                                    <i
                                                        class="fas fa-arrow-down"
                                                        aria-hidden="true"
                                                    ></i>
                                                    <strong>Down</strong>
                                                    {{ $package->download_speed ?: 'Not Set' }}
                                                </span>

                                                <span class="speed-row">
                                                    <i
                                                        class="fas fa-arrow-up"
                                                        aria-hidden="true"
                                                    ></i>
                                                    <strong>Up</strong>
                                                    {{ $package->upload_speed ?: 'Not Set' }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="router-profile">
                                            <i
                                                class="fas fa-network-wired"
                                                aria-hidden="true"
                                            ></i>
                                            {{ $package->router_profile ?: 'Not Set' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="package-price">
                                            KES {{ number_format((float) $package->price, 2) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="package-badge duration">
                                            <i
                                                class="fas fa-clock"
                                                aria-hidden="true"
                                            ></i>
                                            {{ $package->formatted_duration }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($package->is_active)
                                            <span class="package-badge active">
                                                <i
                                                    class="fas fa-circle-check"
                                                    aria-hidden="true"
                                                ></i>
                                                Active
                                            </span>
                                        @else
                                            <span class="package-badge disabled">
                                                <i
                                                    class="fas fa-circle-xmark"
                                                    aria-hidden="true"
                                                ></i>
                                                Disabled
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="action-list">

                                            <a
                                                href="{{ route('admin.packages.edit', $package) }}"
                                                class="action-button edit"
                                                title="Edit package"
                                            >
                                                <i
                                                    class="fas fa-pen-to-square"
                                                    aria-hidden="true"
                                                ></i>
                                                Edit
                                            </a>

                                            @if($package->is_active)
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.packages.sync-profile', $package) }}"
                                                    onsubmit="return confirm('Synchronize this package profile with the active router?');"
                                                >
                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="action-button sync"
                                                        title="Synchronize router profile"
                                                    >
                                                        <i
                                                            class="fas fa-arrows-rotate"
                                                            aria-hidden="true"
                                                        ></i>
                                                        Sync
                                                    </button>
                                                </form>
                                            @endif

                                            <form
                                                method="POST"
                                                action="{{ route('admin.packages.toggle-status', $package) }}"
                                                onsubmit="return confirm('{{ $package->is_active ? 'Deactivate this package? Customers will no longer see or be able to select it, but its history will be preserved.' : 'Activate this package? It will become available to customers again.' }}');"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                @if($package->is_active)
                                                    <button
                                                        type="submit"
                                                        class="action-button deactivate"
                                                        title="Deactivate package"
                                                    >
                                                        <i
                                                            class="fas fa-circle-pause"
                                                            aria-hidden="true"
                                                        ></i>
                                                        Deactivate
                                                    </button>
                                                @else
                                                    <button
                                                        type="submit"
                                                        class="action-button activate"
                                                        title="Activate package"
                                                    >
                                                        <i
                                                            class="fas fa-circle-play"
                                                            aria-hidden="true"
                                                        ></i>
                                                        Activate
                                                    </button>
                                                @endif
                                            </form>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.packages.destroy', $package) }}"
                                                onsubmit="return confirm('Permanently delete this package? This action cannot be undone. Packages with payment or subscription history cannot be deleted.');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="action-button delete"
                                                    title="Permanently delete package"
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
                                    <td colspan="8" class="empty-state">
                                        <div class="empty-state-icon">
                                            <i
                                                class="fas fa-box-open"
                                                aria-hidden="true"
                                            ></i>
                                        </div>

                                        <strong>No packages created yet</strong>

                                        <span>
                                            Create your first internet package to start offering service to customers.
                                        </span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="history-notice">
                    <i
                        class="fas fa-shield-halved"
                        aria-hidden="true"
                    ></i>

                    <div>
                        <strong>Package history protection:</strong>
                        deactivate a package when you want to stop offering it
                        while preserving its billing and subscription history.
                        Permanent deletion is only allowed when the package has
                        no payment or subscription history.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>