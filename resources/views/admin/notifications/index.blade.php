<x-app-layout>
    <x-slot name="header">
        <div class="notifications-header">
            <div class="notifications-header-icon">
                <i class="fas fa-bell" aria-hidden="true"></i>
            </div>

            <div>
                <h2>Notifications</h2>
                <p>Review important system and account alerts.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .notifications-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .notifications-header-icon {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.22);
        }

        .notifications-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
        }

        .notifications-header p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .notifications-page {
            min-height: 100vh;
            padding: 38px 24px 55px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.7),
                    transparent 30%
                ),
                #f4f7fb;
            transition:
                background-color 0.24s ease,
                color 0.24s ease;
        }

        .notifications-container {
            width: 100%;
            max-width: 1120px;
            margin: 0 auto;
        }

        .notifications-panel {
            overflow: hidden;
            border: 1px solid #e5eaf1;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .notifications-panel-header {
            padding: 23px 25px;
            border-bottom: 1px solid #e9eef5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .notifications-panel-heading {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0;
        }

        .notifications-panel-heading-icon {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            color: #2563eb;
            background: #eff6ff;
            font-size: 17px;
        }

        .notifications-panel-heading h3 {
            margin: 0;
            color: #111827;
            font-size: 20px;
            line-height: 1.3;
            font-weight: 800;
        }

        .notifications-panel-heading p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }

        .mark-all-button {
            min-height: 40px;
            padding: 9px 14px;
            border: 0;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            color: #ffffff;
            background: #2563eb;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition:
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .mark-all-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 7px 16px rgba(37, 99, 235, 0.2);
        }

        .notifications-list {
            padding: 20px 22px 10px;
        }

        .notification-card {
            margin-bottom: 12px;
            padding: 17px 18px;
            border: 1px solid #e5eaf1;
            border-radius: 14px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            background: #ffffff;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }

        .notification-card:hover {
            transform: translateY(-1px);
            border-color: #cbd5e1;
        }

        .notification-card.unread {
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .notification-main {
            min-width: 0;
            flex: 1;
        }

        .notification-title {
            margin: 0;
            display: flex;
            align-items: flex-start;
            gap: 9px;
            color: #111827;
            font-size: 15px;
            line-height: 1.45;
            font-weight: 800;
        }

        .notification-title-icon {
            width: 25px;
            height: 25px;
            flex: 0 0 25px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            margin-top: -2px;
            font-size: 11px;
        }

        .notification-title-icon.success {
            color: #15803d;
            background: #dcfce7;
        }

        .notification-title-icon.warning {
            color: #b45309;
            background: #fef3c7;
        }

        .notification-title-icon.danger {
            color: #b91c1c;
            background: #fee2e2;
        }

        .notification-title-icon.info {
            color: #1d4ed8;
            background: #dbeafe;
        }

        .notification-message {
            margin: 8px 0 0 34px;
            color: #475569;
            font-size: 12px;
            line-height: 1.65;
            overflow-wrap: anywhere;
        }

        .notification-time {
            margin: 8px 0 0 34px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
        }

        .notification-action {
            flex: 0 0 auto;
        }

        .mark-read-button {
            min-height: 34px;
            padding: 7px 11px;
            border: 0;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #ffffff;
            background: #16a34a;
            font-size: 10px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }

        .mark-read-button:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        .read-badge {
            min-height: 31px;
            padding: 6px 10px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: #475569;
            background: #e2e8f0;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .notifications-empty {
            padding: 65px 25px;
            text-align: center;
            color: #64748b;
        }

        .notifications-empty-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 15px;
            display: grid;
            place-items: center;
            border-radius: 20px;
            color: #2563eb;
            background: #eff6ff;
            font-size: 24px;
        }

        .notifications-empty strong {
            display: block;
            color: #1e293b;
            font-size: 15px;
            font-weight: 800;
        }

        .notifications-empty p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .notifications-pagination {
            padding: 5px 22px 22px;
        }

        /*
         * DARK MODE
         */

        html.dark .notifications-header h2 {
            color: #f8fafc;
        }

        html.dark .notifications-header p {
            color: #94a3b8;
        }

        html.dark .notifications-page {
            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.11),
                    transparent 31%
                ),
                #091226;
            color: #e5e7eb;
        }

        html.dark .notifications-panel {
            border-color: #263652;
            background: #101b31;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.22);
        }

        html.dark .notifications-panel-header {
            border-color: #263652;
        }

        html.dark .notifications-panel-heading-icon {
            color: #7fb0ff;
            background: #16294b;
        }

        html.dark .notifications-panel-heading h3 {
            color: #f8fafc;
        }

        html.dark .notifications-panel-heading p {
            color: #94a3b8;
        }

        html.dark .notification-card {
            border-color: #293954;
            background: #111d34;
        }

        html.dark .notification-card:hover {
            border-color: #3a4c6c;
            background: #14223c;
        }

        html.dark .notification-card.unread {
            border-color: #315fa5;
            background: #12284a;
        }

        html.dark .notification-title {
            color: #f1f5f9;
        }

        html.dark .notification-message {
            color: #cbd5e1;
        }

        html.dark .notification-time {
            color: #94a3b8;
        }

        html.dark .notification-title-icon.success {
            color: #86efac;
            background: rgba(22, 163, 74, 0.18);
        }

        html.dark .notification-title-icon.warning {
            color: #fde68a;
            background: rgba(245, 158, 11, 0.17);
        }

        html.dark .notification-title-icon.danger {
            color: #fca5a5;
            background: rgba(220, 38, 38, 0.18);
        }

        html.dark .notification-title-icon.info {
            color: #93c5fd;
            background: rgba(37, 99, 235, 0.2);
        }

        html.dark .read-badge {
            color: #cbd5e1;
            background: #27364d;
        }

        html.dark .notifications-empty {
            color: #94a3b8;
        }

        html.dark .notifications-empty-icon {
            color: #93c5fd;
            background: #16294b;
        }

        html.dark .notifications-empty strong {
            color: #f1f5f9;
        }

        html.dark .notifications-empty p {
            color: #94a3b8;
        }

        html.dark .notifications-pagination nav,
        html.dark .notifications-pagination span,
        html.dark .notifications-pagination p {
            color: #cbd5e1;
        }

        html.dark .notifications-pagination a {
            border-color: #334155 !important;
            background: #17233a !important;
            color: #e2e8f0 !important;
        }

        html.dark .notifications-pagination a:hover {
            background: #22314c !important;
        }

        html.dark .notifications-pagination span[aria-current="page"] span {
            border-color: #3b82f6 !important;
            background: #2563eb !important;
            color: #ffffff !important;
        }

        @media (max-width: 700px) {
            .notifications-page {
                padding: 26px 14px 45px;
            }

            .notifications-panel-header {
                padding: 19px;
                align-items: flex-start;
                flex-direction: column;
            }

            .notifications-list {
                padding: 16px;
            }

            .notification-card {
                flex-direction: column;
            }

            .notification-action {
                width: 100%;
            }

            .mark-read-button {
                width: 100%;
            }

            .notification-message,
            .notification-time {
                margin-left: 0;
            }

            .mark-all-button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .notifications-header-icon {
                width: 43px;
                height: 43px;
                flex-basis: 43px;
            }

            .notifications-header h2 {
                font-size: 20px;
            }

            .notifications-header p {
                display: none;
            }

            .notifications-page {
                padding-left: 10px;
                padding-right: 10px;
            }

            .notifications-panel {
                border-radius: 17px;
            }
        }
    </style>

    <div class="notifications-page">
        <div class="notifications-container">
            <section
                class="notifications-panel"
                aria-labelledby="system-notifications-heading"
            >
                <div class="notifications-panel-header">
                    <div class="notifications-panel-heading">
                        <div
                            class="notifications-panel-heading-icon"
                            aria-hidden="true"
                        >
                            <i class="fas fa-bell"></i>
                        </div>

                        <div>
                            <h3 id="system-notifications-heading">
                                System Notifications
                            </h3>

                            <p>
                                Important billing, customer, subscription,
                                license, and router alerts.
                            </p>
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.notifications.read-all') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="mark-all-button"
                        >
                            <i
                                class="fas fa-check-double"
                                aria-hidden="true"
                            ></i>

                            Mark All Read
                        </button>
                    </form>
                </div>

                <div class="notifications-list">
                    @forelse($notifications as $notification)
                        <article
                            class="notification-card {{ $notification->is_read ? '' : 'unread' }}"
                        >
                            <div class="notification-main">
                                <h4 class="notification-title">
                                    @if($notification->type === 'success')
                                        <span
                                            class="notification-title-icon success"
                                            aria-hidden="true"
                                        >
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @elseif($notification->type === 'warning')
                                        <span
                                            class="notification-title-icon warning"
                                            aria-hidden="true"
                                        >
                                            <i class="fas fa-triangle-exclamation"></i>
                                        </span>
                                    @elseif(in_array($notification->type, ['danger', 'error'], true))
                                        <span
                                            class="notification-title-icon danger"
                                            aria-hidden="true"
                                        >
                                            <i class="fas fa-circle-exclamation"></i>
                                        </span>
                                    @else
                                        <span
                                            class="notification-title-icon info"
                                            aria-hidden="true"
                                        >
                                            <i class="fas fa-circle-info"></i>
                                        </span>
                                    @endif

                                    <span>{{ $notification->title }}</span>
                                </h4>

                                <p class="notification-message">
                                    {{ $notification->message }}
                                </p>

                                <span class="notification-time">
                                    <i
                                        class="fas fa-clock"
                                        aria-hidden="true"
                                    ></i>

                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="notification-action">
                                @if(!$notification->is_read)
                                    <form
                                        method="POST"
                                        action="{{ route('admin.notifications.read', $notification) }}"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="mark-read-button"
                                        >
                                            <i
                                                class="fas fa-check"
                                                aria-hidden="true"
                                            ></i>

                                            Mark Read
                                        </button>
                                    </form>
                                @else
                                    <span class="read-badge">
                                        <i
                                            class="fas fa-check-double"
                                            aria-hidden="true"
                                        ></i>

                                        Read
                                    </span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="notifications-empty">
                            <div
                                class="notifications-empty-icon"
                                aria-hidden="true"
                            >
                                <i class="fas fa-bell-slash"></i>
                            </div>

                            <strong>No notifications</strong>

                            <p>
                                You do not currently have any system notifications.
                            </p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="notifications-pagination">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>