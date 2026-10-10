<x-app-layout>
    <x-slot name="header">
        <div class="audit-page-header">
            <div class="audit-page-header__icon">
                <i class="fas fa-shield-halved" aria-hidden="true"></i>
            </div>

            <div>
                <h2>Audit Logs</h2>
                <p>Review security-sensitive and administrative activity.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .audit-page-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .audit-page-header__icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            box-shadow: 0 7px 18px rgba(37, 99, 235, .22);
            font-size: 15px;
        }

        .audit-page-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            line-height: 1.2;
            font-weight: 800;
        }

        .audit-page-header p {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 11px;
        }

        .audit-page {
            --audit-bg: #f4f7fb;
            --audit-surface: #ffffff;
            --audit-surface-soft: #f8fafc;
            --audit-text: #0f172a;
            --audit-muted: #64748b;
            --audit-border: #e2e8f0;
            --audit-input: #ffffff;
            --audit-table-head: #f8fafc;
            --audit-row-hover: #f8fbff;

            min-height: 100vh;
            padding: 32px 24px 50px;
            color: var(--audit-text);
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, .65),
                    transparent 30%
                ),
                var(--audit-bg);
        }

        html.dark .audit-page {
            --audit-bg: #091226;
            --audit-surface: #101b31;
            --audit-surface-soft: #14213a;
            --audit-text: #eaf1fc;
            --audit-muted: #93a5bf;
            --audit-border: #293a56;
            --audit-input: #0b1629;
            --audit-table-head: #152139;
            --audit-row-hover: #14223b;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, .10),
                    transparent 31%
                ),
                var(--audit-bg);
        }

        html.dark .audit-page-header h2 {
            color: #f8fafc !important;
        }

        html.dark .audit-page-header p {
            color: #94a3b8 !important;
        }

        .audit-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .audit-panel {
            overflow: hidden;
            border: 1px solid var(--audit-border);
            border-radius: 18px;
            background: var(--audit-surface);
            box-shadow: 0 8px 28px rgba(15, 23, 42, .07);
        }

        html.dark .audit-panel {
            box-shadow: 0 10px 30px rgba(0, 0, 0, .22);
        }

        .audit-panel__top {
            padding: 20px 22px 18px;
            border-bottom: 1px solid var(--audit-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .audit-heading {
            min-width: 0;
        }

        .audit-heading h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--audit-text);
            font-size: 18px;
            line-height: 1.3;
            font-weight: 800;
        }

        .audit-heading h3 i {
            color: #2563eb;
            font-size: 16px;
        }

        html.dark .audit-heading h3,
        html.dark .audit-heading h3 span {
            color: #f1f5f9 !important;
        }

        html.dark .audit-heading h3 i {
            color: #7da7ff !important;
        }

        .audit-heading p {
            margin: 4px 0 0;
            color: var(--audit-muted);
            font-size: 11px;
            line-height: 1.5;
        }

        html.dark .audit-heading p {
            color: #94a3b8 !important;
        }

        .audit-count {
            min-height: 32px;
            padding: 6px 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 999px;
            color: #1d4ed8;
            background: #dbeafe;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        html.dark .audit-count {
            color: #a8c7ff;
            background: #17315b;
        }

        .audit-search-wrap {
            padding: 17px 22px;
            border-bottom: 1px solid var(--audit-border);
        }

        .audit-search-form {
            display: flex;
            align-items: stretch;
            gap: 9px;
        }

        .audit-search-input-wrap {
            position: relative;
            flex: 1;
            min-width: 0;
        }

        .audit-search-input-wrap > i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--audit-muted);
            font-size: 11px;
            pointer-events: none;
        }

        .audit-search-input {
            width: 100%;
            min-height: 39px;
            padding: 8px 12px 8px 34px;
            border: 1px solid var(--audit-border);
            border-radius: 9px;
            outline: none;
            color: var(--audit-text);
            background: var(--audit-input);
            font-size: 11px;
            line-height: 1.4;
            transition:
                border-color .18s ease,
                box-shadow .18s ease;
        }

        .audit-search-input::placeholder {
            color: #94a3b8;
        }

        .audit-search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        html.dark .audit-search-input {
            background: #0b1629 !important;
            border-color: #33445f !important;
            color: #edf3ff !important;
        }

        html.dark .audit-search-input::placeholder {
            color: #71829d !important;
        }

        .audit-search-button,
        .audit-clear-button {
            min-height: 39px;
            padding: 8px 14px;
            border: 0;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            font-size: 10px;
            font-weight: 800;
            transition:
                transform .18s ease,
                background .18s ease;
        }

        .audit-search-button {
            color: #ffffff;
            background: #2563eb;
        }

        .audit-search-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .audit-clear-button {
            color: #475569;
            background: #e2e8f0;
        }

        .audit-clear-button:hover {
            color: #334155;
            background: #cbd5e1;
        }

        html.dark .audit-clear-button {
            color: #d5deeb;
            background: #293850;
        }

        html.dark .audit-clear-button:hover {
            color: #ffffff;
            background: #344660;
        }

        .audit-table-wrap {
            width: 100%;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: #64748b transparent;
        }

        .audit-table {
            width: 100%;
            min-width: 960px;
            border-collapse: collapse;
            table-layout: auto;
        }

        .audit-table th {
            padding: 10px 11px;
            border-bottom: 1px solid var(--audit-border);
            border-right: 1px solid var(--audit-border);
            color: #475569;
            background: var(--audit-table-head);
            text-align: left;
            vertical-align: middle;
            font-size: 9px;
            line-height: 1.35;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .045em;
            white-space: nowrap;
        }

        .audit-table th:last-child {
            border-right: 0;
        }

        .audit-table td {
            padding: 10px 11px;
            border-bottom: 1px solid var(--audit-border);
            border-right: 1px solid var(--audit-border);
            color: var(--audit-text);
            background: var(--audit-surface);
            vertical-align: top;
            font-size: 10.5px;
            line-height: 1.5;
        }

        .audit-table td:last-child {
            border-right: 0;
        }

        .audit-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .audit-table tbody tr:hover td {
            background: var(--audit-row-hover);
        }

        html.dark .audit-table th {
            color: #b9c8dc !important;
            background: #152139 !important;
            border-color: #293a56 !important;
        }

        html.dark .audit-table td {
            color: #dce6f5 !important;
            background: #101b31 !important;
            border-color: #293a56 !important;
        }

        html.dark .audit-table tbody tr:hover td {
            background: #14223b !important;
        }

        .audit-actor {
            min-width: 105px;
        }

        .audit-actor strong {
            display: block;
            color: var(--audit-text);
            font-size: 10.5px;
            line-height: 1.35;
            font-weight: 800;
        }

        html.dark .audit-actor strong {
            color: #f1f5f9 !important;
        }

        .audit-actor small {
            display: block;
            margin-top: 3px;
            color: var(--audit-muted);
            font-size: 8.5px;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        html.dark .audit-actor small {
            color: #8294af !important;
        }

        .audit-role,
        .audit-action {
            display: inline-flex;
            align-items: center;
            max-width: 125px;
            padding: 5px 8px;
            border-radius: 999px;
            font-size: 8.5px;
            line-height: 1.3;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .audit-role--super {
            color: #6d28d9;
            background: #ede9fe;
        }

        .audit-role--admin {
            color: #1d4ed8;
            background: #dbeafe;
        }

        .audit-role--customer {
            color: #166534;
            background: #dcfce7;
        }

        .audit-role--system {
            color: #475569;
            background: #e2e8f0;
        }

        .audit-action {
            color: #166534;
            background: #dcfce7;
        }

        html.dark .audit-role--super {
            color: #d8b4fe;
            background: rgba(126, 34, 206, .25);
        }

        html.dark .audit-role--admin {
            color: #a9c8ff;
            background: rgba(37, 99, 235, .22);
        }

        html.dark .audit-role--customer {
            color: #9ae6b4;
            background: rgba(22, 163, 74, .20);
        }

        html.dark .audit-role--system {
            color: #cbd5e1;
            background: #293850;
        }

        html.dark .audit-action {
            color: #9ae6b4;
            background: rgba(22, 163, 74, .20);
        }

        .audit-module {
            min-width: 90px;
            font-weight: 600;
        }

        .audit-description {
            min-width: 220px;
            max-width: 340px;
            overflow-wrap: anywhere;
        }

        .audit-ip {
            min-width: 88px;
            white-space: nowrap;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 9.5px;
        }

        .audit-date {
            min-width: 120px;
            white-space: nowrap;
            color: var(--audit-muted);
            font-size: 9.5px;
        }

        html.dark .audit-date {
            color: #aab8cc !important;
        }

        .audit-empty {
            padding: 40px 20px !important;
            text-align: center;
            color: var(--audit-muted) !important;
        }

        .audit-empty i {
            display: block;
            margin-bottom: 10px;
            color: #94a3b8;
            font-size: 24px;
        }

        .audit-pagination {
            padding: 17px 22px;
            border-top: 1px solid var(--audit-border);
        }

        html.dark .audit-pagination nav,
        html.dark .audit-pagination p,
        html.dark .audit-pagination span {
            color: #cbd5e1;
        }

        html.dark .audit-pagination a {
            border-color: #334155 !important;
            background: #17233a !important;
            color: #e2e8f0 !important;
        }

        html.dark .audit-pagination a:hover {
            background: #22314c !important;
        }

        html.dark .audit-pagination span[aria-current="page"] span {
            border-color: #3b82f6 !important;
            background: #2563eb !important;
            color: #ffffff !important;
        }

        .audit-note {
            margin: 0 22px 20px;
            padding: 11px 13px;
            border: 1px solid var(--audit-border);
            border-radius: 10px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: var(--audit-muted);
            background: var(--audit-surface-soft);
            font-size: 9.5px;
            line-height: 1.55;
        }

        .audit-note i {
            margin-top: 2px;
            color: #2563eb;
        }

        html.dark .audit-note {
            color: #a8b7cc !important;
            background: #14213a !important;
            border-color: #293a56 !important;
        }

        html.dark .audit-note i {
            color: #7da7ff !important;
        }

        @media (max-width: 768px) {
            .audit-page {
                padding: 24px 12px 40px;
            }

            .audit-panel__top {
                align-items: flex-start;
                flex-direction: column;
            }

            .audit-search-form {
                flex-wrap: wrap;
            }

            .audit-search-input-wrap {
                flex: 1 1 100%;
            }

            .audit-search-button,
            .audit-clear-button {
                flex: 1;
            }
        }

        @media (max-width: 480px) {
            .audit-page {
                padding-left: 8px;
                padding-right: 8px;
            }

            .audit-page-header__icon {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
            }

            .audit-page-header h2 {
                font-size: 18px;
            }

            .audit-page-header p {
                display: none;
            }

            .audit-panel {
                border-radius: 15px;
            }

            .audit-panel__top,
            .audit-search-wrap {
                padding-left: 15px;
                padding-right: 15px;
            }

            .audit-note {
                margin-left: 15px;
                margin-right: 15px;
            }
        }
    </style>

    <div class="audit-page">
        <div class="audit-container">
            <section class="audit-panel">

                <div class="audit-panel__top">
                    <div class="audit-heading">
                        <h3>
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>
                            <span>System Activity Logs</span>
                        </h3>

                        <p>
                            Review security-sensitive and administrative activity.
                        </p>
                    </div>

                    <span class="audit-count">
                        <i class="fas fa-database" aria-hidden="true"></i>
                        {{ number_format($logs->total()) }} Records
                    </span>
                </div>

                <div class="audit-search-wrap">
                    <form
                        method="GET"
                        action="{{ route('admin.audit-logs.index') }}"
                        class="audit-search-form"
                    >
                        <div class="audit-search-input-wrap">
                            <i class="fas fa-magnifying-glass" aria-hidden="true"></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ $search ?? '' }}"
                                maxlength="150"
                                class="audit-search-input"
                                placeholder="Search action, module, actor, email, IP, or description..."
                                aria-label="Search audit logs"
                            >
                        </div>

                        <button
                            type="submit"
                            class="audit-search-button"
                        >
                            <i class="fas fa-search" aria-hidden="true"></i>
                            Search
                        </button>

                        @if(!empty($search))
                            <a
                                href="{{ route('admin.audit-logs.index') }}"
                                class="audit-clear-button"
                            >
                                <i class="fas fa-xmark" aria-hidden="true"></i>
                                Clear
                            </a>
                        @endif
                    </form>
                </div>

                <div class="audit-table-wrap">
                    <table class="audit-table">
                        <thead>
                            <tr>
                                <th>Actor</th>
                                <th>Role</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($logs as $log)
                                @php
                                    $role = $log->actor_role ?? 'system';
                                @endphp

                                <tr>
                                    <td>
                                        <div class="audit-actor">
                                            <strong>
                                                {{ $log->actor_name ?? $log->user?->name ?? 'System' }}
                                            </strong>

                                            <small>
                                                {{ $log->actor_email ?? $log->user?->email ?? 'No email' }}
                                            </small>
                                        </div>
                                    </td>

                                    <td>
                                        @if($role === 'super_admin')
                                            <span class="audit-role audit-role--super">
                                                Super Admin
                                            </span>
                                        @elseif($role === 'admin')
                                            <span class="audit-role audit-role--admin">
                                                ISP Admin
                                            </span>
                                        @elseif($role === 'customer')
                                            <span class="audit-role audit-role--customer">
                                                Customer
                                            </span>
                                        @else
                                            <span class="audit-role audit-role--system">
                                                System
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="audit-action">
                                            {{ $log->action }}
                                        </span>
                                    </td>

                                    <td class="audit-module">
                                        {{ $log->module ?? 'General' }}
                                    </td>

                                    <td class="audit-description">
                                        {{ $log->description ?? 'No description' }}
                                    </td>

                                    <td class="audit-ip">
                                        {{ $log->ip_address ?? 'N/A' }}
                                    </td>

                                    <td class="audit-date">
                                        @if($log->created_at)
                                            {{ $log->created_at->format('d M Y, H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="audit-empty">
                                        <i class="fas fa-shield-halved" aria-hidden="true"></i>
                                        No audit logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="audit-pagination">
                        {{ $logs->links() }}
                    </div>
                @endif

                <div class="audit-note">
                    <i class="fas fa-circle-info" aria-hidden="true"></i>

                    <span>
                        Audit records preserve the actor's name, email, role,
                        tenant scope, IP address, action, and timestamp at the
                        time the event was recorded. This keeps historical
                        audit information available even if the related user
                        account later changes.
                    </span>
                </div>

            </section>
        </div>
    </div>
</x-app-layout>