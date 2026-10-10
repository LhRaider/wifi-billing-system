<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $authenticatedUser = auth()->user();
        $themeEnabled = in_array($authenticatedUser?->role, ['admin', 'super_admin'], true);

        $pageTitle = config('app.name', 'WiFi Billing System');

        if ($authenticatedUser?->role === 'super_admin') {
            $pageTitle = 'Raiders';
        } elseif ($authenticatedUser?->role === 'admin') {
            $pageTitle = $authenticatedUser->network_name
                ?: $authenticatedUser->business_name
                ?: config('app.name', 'WiFi Billing System');
        }
    @endphp

    <title>{{ $pageTitle }}</title>

    <script>
        (() => {
            const themeEnabled = @json($themeEnabled);

            // Dark mode belongs only to ISP Admin and Super Admin accounts.
            // Customers and guests are always rendered in light mode, even if
            // this browser previously saved an administrator's dark preference.
            if (!themeEnabled) {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.colorScheme = 'light';
                return;
            }

            const storageKey = 'wifiBillingTheme';
            let savedTheme = null;

            try {
                savedTheme = window.localStorage.getItem(storageKey);
            } catch (error) {
                savedTheme = null;
            }

            const systemPrefersDark = window.matchMedia
                && window.matchMedia('(prefers-color-scheme: dark)').matches;

            const useDark = savedTheme === 'dark'
                || (savedTheme !== 'light' && systemPrefersDark);

            document.documentElement.classList.toggle('dark', useDark);
            document.documentElement.style.colorScheme = useDark ? 'dark' : 'light';
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-expanded: 258px;
            --sidebar-collapsed: 76px;
            --sidebar-bg-top: #101a3a;
            --sidebar-bg-mid: #101b35;
            --sidebar-bg-bottom: #091225;
            --sidebar-text: #e8efff;
            --sidebar-muted: #7f90b5;
            --sidebar-line: rgba(148, 163, 184, 0.13);
            --sidebar-blue: #3977ff;
        }

        .admin-sidebar {
            width: var(--sidebar-expanded);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            color: var(--sidebar-text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            padding: 18px 14px 16px;
            background:
                linear-gradient(180deg, #111d42 0%, #0e1937 48%, #091329 100%);
            border-right: 1px solid rgba(148, 163, 184, .10);
            box-shadow: 10px 0 34px rgba(9, 18, 37, .16);
            overflow: hidden;
            z-index: 1000;
            transition: width .28s ease, padding .28s ease;
        }

        .admin-sidebar::before,
        .admin-sidebar::after {
            content: "";
            position: absolute;
            left: -20%;
            width: 145%;
            pointer-events: none;
            z-index: 0;
            border-radius: 50%;
        }

        .admin-sidebar::before {
            height: 155px;
            top: 58px;
            bottom: auto;
            background: rgba(54, 102, 255, .075);
            transform: rotate(-7deg);
            border-bottom: 1px solid rgba(105, 145, 255, .08);
            animation: sidebarWaveOne 16s ease-in-out infinite alternate;
        }

        .admin-sidebar::after {
            height: 130px;
            top: 112px;
            bottom: auto;
            left: -32%;
            background: rgba(93, 73, 235, .055);
            transform: rotate(8deg);
            border-bottom: 1px solid rgba(132, 117, 255, .06);
            animation: sidebarWaveTwo 20s ease-in-out infinite alternate;
        }

        @keyframes sidebarWaveOne {
            0% {
                transform: translate3d(-8px, 0, 0) rotate(-7deg);
            }
            100% {
                transform: translate3d(18px, 10px, 0) rotate(-3deg);
            }
        }

        @keyframes sidebarWaveTwo {
            0% {
                transform: translate3d(12px, 0, 0) rotate(8deg);
            }
            100% {
                transform: translate3d(-16px, 8px, 0) rotate(4deg);
            }
        }

        .admin-sidebar.collapsed {
            width: var(--sidebar-collapsed);
            padding-left: 10px;
            padding-right: 10px;
        }

        .admin-content {
            margin-left: var(--sidebar-expanded);
            width: calc(100% - var(--sidebar-expanded));
            min-height: 100vh;
            background: #f3f4f6;
            transition: margin-left .28s ease, width .28s ease;
        }

        .admin-content.collapsed {
            margin-left: var(--sidebar-collapsed);
            width: calc(100% - var(--sidebar-collapsed));
        }

        .normal-content {
            width: 100%;
            min-height: 100vh;
            background: #f3f4f6;
        }

        .sidebar-brand {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 62px;
            padding: 3px 42px 10px 3px;
            margin-bottom: 8px;
            border-bottom: 1px solid var(--sidebar-line);
        }

        .sidebar-brand__mark {
            width: 43px;
            height: 43px;
            flex: 0 0 43px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #ffffff;
            font-size: 18px;
            background: linear-gradient(135deg, #3977ff, #6657f5);
            box-shadow: 0 9px 22px rgba(57, 119, 255, .28);
        }

        .sidebar-brand__copy {
            min-width: 0;
            margin-left: 11px;
            transition: opacity .18s ease;
        }

        .sidebar-title {
            margin: 0;
            color: #ffffff;
            font-size: 16px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-subtitle {
            margin-top: 4px;
            color: #91a2c7;
            font-size: 9px;
            line-height: 1.2;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .07em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-toggle {
            position: absolute;
            top: 12px;
            right: 10px;
            width: 31px;
            height: 31px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 10px;
            color: #c9d6f3;
            background: rgba(255, 255, 255, .07);
            cursor: pointer;
            font-size: 12px;
            transition: background .18s ease, transform .18s ease, color .18s ease;
        }

        .sidebar-toggle:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, .13);
            transform: translateY(-1px);
        }

        .sidebar-toggle:focus-visible,
        .sidebar-link:focus-visible,
        .logout-btn:focus-visible {
            outline: 2px solid #8db2ff;
            outline-offset: 2px;
        }

        .sidebar-scroll {
            position: relative;
            z-index: 1;
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 4px 2px 12px 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, .28) transparent;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, .25);
            border-radius: 999px;
        }

        .sidebar-section {
            color: #7183a8;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .11em;
            margin: 17px 0 7px 11px;
            white-space: nowrap;
        }

        .sidebar-link {
            --item-color: #8fa6d4;
            --item-soft: rgba(143, 166, 212, .12);
            position: relative;
            display: flex;
            align-items: center;
            min-height: 45px;
            color: #d6e0f5;
            text-decoration: none;
            padding: 6px 9px;
            margin-bottom: 5px;
            border: 1px solid transparent;
            border-radius: 13px;
            background: transparent;
            white-space: nowrap;
            overflow: hidden;
            transition:
                background .18s ease,
                border-color .18s ease,
                transform .18s ease,
                color .18s ease,
                box-shadow .18s ease;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, .065);
            border-color: rgba(255, 255, 255, .055);
            transform: translateX(2px);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: linear-gradient(105deg, rgba(57, 119, 255, .96), rgba(83, 74, 238, .92));
            border-color: rgba(130, 170, 255, .34);
            box-shadow: 0 8px 22px rgba(37, 99, 235, .24);
        }

        .sidebar-link.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 11px;
            bottom: 11px;
            width: 3px;
            border-radius: 0 4px 4px 0;
            background: #ffffff;
            opacity: .95;
        }

        .sidebar-icon {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: grid;
            place-items: center;
            margin-right: 10px;
            border-radius: 10px;
            color: var(--item-color);
            background: var(--item-soft);
            font-size: 14px;
            transition: transform .18s ease, background .18s ease, color .18s ease;
        }

        .sidebar-link:hover .sidebar-icon {
            transform: scale(1.04);
        }

        .sidebar-link.active .sidebar-icon {
            color: #ffffff;
            background: rgba(255, 255, 255, .16);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .08);
        }

        .sidebar-text {
            display: inline-block;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .01em;
        }

        .sidebar-blue   { --item-color: #65a2ff; --item-soft: rgba(59, 130, 246, .13); }
        .sidebar-cyan   { --item-color: #39d0e6; --item-soft: rgba(6, 182, 212, .13); }
        .sidebar-green  { --item-color: #4ade80; --item-soft: rgba(34, 197, 94, .13); }
        .sidebar-purple { --item-color: #b68cff; --item-soft: rgba(139, 92, 246, .14); }
        .sidebar-orange { --item-color: #ffad4d; --item-soft: rgba(249, 115, 22, .14); }
        .sidebar-pink   { --item-color: #ff7daf; --item-soft: rgba(236, 72, 153, .14); }
        .sidebar-yellow { --item-color: #facc4d; --item-soft: rgba(234, 179, 8, .13); }
        .sidebar-slate  { --item-color: #b4c2db; --item-soft: rgba(148, 163, 184, .13); }

        .notification-badge {
            min-width: 21px;
            height: 21px;
            display: inline-grid;
            place-items: center;
            margin-left: auto;
            padding: 0 6px;
            border-radius: 999px;
            color: #ffffff;
            background: #ef4444;
            border: 2px solid #111b34;
            font-size: 9px;
            font-weight: 900;
            line-height: 1;
        }

        .sidebar-footer {
            position: relative;
            z-index: 2;
            flex: 0 0 auto;
            padding-top: 11px;
            border-top: 1px solid var(--sidebar-line);
        }

        .sidebar-account {
            display: flex;
            align-items: center;
            min-width: 0;
            padding: 2px 3px 10px;
        }

        .sidebar-account__avatar {
            width: 35px;
            height: 35px;
            flex: 0 0 35px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            color: #dbe8ff;
            background: rgba(57, 119, 255, .15);
            border: 1px solid rgba(89, 143, 255, .18);
            font-size: 13px;
        }

        .sidebar-account__copy {
            min-width: 0;
            margin-left: 9px;
        }

        .sidebar-account__copy strong {
            display: block;
            overflow: hidden;
            color: #e9f0ff;
            font-size: 10px;
            font-weight: 900;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .sidebar-account__copy span {
            display: block;
            margin-top: 2px;
            color: #7183a8;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
        }

        .logout-form {
            margin: 0;
        }

        .logout-btn {
            width: 100%;
            min-height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 11px;
            border: 1px solid rgba(248, 113, 113, .14);
            border-radius: 12px;
            color: #fecaca;
            background: rgba(220, 38, 38, .10);
            cursor: pointer;
            font-size: 10px;
            font-weight: 900;
            transition: background .18s ease, color .18s ease, border-color .18s ease;
        }

        .logout-btn:hover {
            color: #ffffff;
            background: rgba(220, 38, 38, .24);
            border-color: rgba(248, 113, 113, .24);
        }

        .logout-btn .sidebar-icon {
            width: 25px;
            height: 25px;
            flex-basis: 25px;
            margin: 0;
            color: #fca5a5;
            background: rgba(220, 38, 38, .10);
            font-size: 12px;
        }

        .admin-sidebar.collapsed .sidebar-brand {
            justify-content: center;
            padding: 3px 0 10px;
        }

        .admin-sidebar.collapsed .sidebar-brand__mark {
            width: 42px;
            height: 42px;
            flex-basis: 42px;
        }

        .admin-sidebar.collapsed .sidebar-brand__copy,
        .admin-sidebar.collapsed .sidebar-section,
        .admin-sidebar.collapsed .sidebar-text,
        .admin-sidebar.collapsed .notification-badge,
        .admin-sidebar.collapsed .sidebar-account__copy,
        .admin-sidebar.collapsed .logout-text {
            display: none;
        }

        .admin-sidebar.collapsed .sidebar-toggle {
            top: 68px;
            right: 22px;
            width: 31px;
            height: 31px;
            z-index: 3;
        }

        .admin-sidebar.collapsed .sidebar-scroll {
            padding-top: 43px;
        }

        .admin-sidebar.collapsed .sidebar-link {
            justify-content: center;
            min-height: 47px;
            padding: 6px;
        }

        .admin-sidebar.collapsed .sidebar-link.active::before {
            left: -1px;
        }

        .admin-sidebar.collapsed .sidebar-icon {
            width: 35px;
            height: 35px;
            flex-basis: 35px;
            margin-right: 0;
            font-size: 15px;
        }

        .admin-sidebar.collapsed .sidebar-account {
            justify-content: center;
            padding-bottom: 9px;
        }

        .admin-sidebar.collapsed .logout-btn {
            padding: 7px;
        }

        .admin-sidebar.collapsed .logout-btn .sidebar-icon {
            margin: 0;
        }

        .flash-message {
            padding: 15px;
            margin: 20px auto;
            border-radius: 10px;
            max-width: 1200px;
        }

        .flash-success {
            background: #d1fae5;
            color: #065f46;
        }

        .flash-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .flash-warning {
            background: #fef3c7;
            color: #92400e;
        }


        /* Global light/dark theme */
        html {
            color-scheme: light;
        }

        html.dark {
            color-scheme: dark;
        }

        body,
        .admin-content,
        .normal-content {
            transition: background-color .24s ease, color .24s ease;
        }

        html.dark body,
        html.dark .admin-content,
        html.dark .normal-content {
            background: #091226;
            color: #e8eeff;
        }

        html.dark #mainContent > nav {
            background: #0f1a31 !important;
            border-color: #243451 !important;
        }

        html.dark #mainContent > header {
            background: #0f1a31 !important;
            border-color: #243451 !important;
            color: #e8eeff;
        }

        html.dark .flash-success {
            background: #123c2a;
            color: #8ce8b3;
        }

        html.dark .flash-error {
            background: #451d29;
            color: #ffabb8;
        }

        html.dark .flash-warning {
            background: #403414;
            color: #f6d978;
        }


        /*
         * Application-wide dark mode compatibility layer.
         *
         * Many existing Blade views pre-date the global theme switch and use
         * fixed light colours. These rules deliberately live in the shared
         * layout and use !important where necessary so those older page-level
         * styles cannot make text, forms, tables, or cards unreadable in dark
         * mode. Semantic buttons/badges keep their own colours.
         */
        html.dark main {
            color: #e5edf9;
        }

        html.dark main .bg-white,
        html.dark main .bg-gray-50,
        html.dark main .bg-gray-100,
        html.dark main .bg-slate-50,
        html.dark main .bg-slate-100 {
            background-color: #101b31 !important;
        }

        html.dark main .dark\:bg-gray-800,
        html.dark main .dark\:bg-gray-900 {
            background-color: #101b31 !important;
        }

        html.dark main .text-gray-900,
        html.dark main .text-gray-800,
        html.dark main .text-gray-700,
        html.dark main .text-slate-900,
        html.dark main .text-slate-800,
        html.dark main .text-slate-700 {
            color: #e8eefb !important;
        }

        html.dark main .text-gray-600,
        html.dark main .text-gray-500,
        html.dark main .text-gray-400,
        html.dark main .text-slate-600,
        html.dark main .text-slate-500,
        html.dark main .text-slate-400 {
            color: #a8b7cf !important;
        }

        html.dark main .border-gray-100,
        html.dark main .border-gray-200,
        html.dark main .border-gray-300,
        html.dark main .border-slate-100,
        html.dark main .border-slate-200,
        html.dark main .border-slate-300,
        html.dark main .divide-gray-100 > :not([hidden]) ~ :not([hidden]),
        html.dark main .divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #2a3a55 !important;
        }

        html.dark main input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        html.dark main select,
        html.dark main textarea {
            background-color: #0b1629 !important;
            color: #edf3ff !important;
            border-color: #33445f !important;
            caret-color: #ffffff;
        }

        html.dark main input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):focus,
        html.dark main select:focus,
        html.dark main textarea:focus {
            background-color: #0d1a30 !important;
            color: #ffffff !important;
            border-color: #5f8fff !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .16) !important;
            outline: none;
        }

        html.dark main input::placeholder,
        html.dark main textarea::placeholder {
            color: #71829d !important;
            opacity: 1;
        }

        html.dark main select option {
            background: #0b1629;
            color: #edf3ff;
        }

        html.dark main input:disabled,
        html.dark main select:disabled,
        html.dark main textarea:disabled,
        html.dark main input[readonly],
        html.dark main textarea[readonly] {
            background-color: #152138 !important;
            color: #899ab4 !important;
            border-color: #2b3b55 !important;
        }

        html.dark main label,
        html.dark main legend {
            color: #dce6f7;
        }

        html.dark main table {
            color: #dfe8f7;
        }

        html.dark main table thead,
        html.dark main table thead tr,
        html.dark main table th {
            background-color: #152139 !important;
            color: #b9c7dc !important;
            border-color: #2a3a55 !important;
        }

        html.dark main table tbody,
        html.dark main table tbody tr,
        html.dark main table td {
            background-color: #101b31 !important;
            color: #dce6f5 !important;
            border-color: #263750 !important;
        }

        html.dark main table tbody tr:hover,
        html.dark main table tbody tr:hover td {
            background-color: #14223b !important;
        }

        html.dark main hr {
            border-color: #293a54 !important;
        }

        /* Existing custom admin pages */
        html.dark main .package-page {
            background: #091426 !important;
        }

        html.dark main .summary-card,
        html.dark main .package-panel,
        html.dark main .package-panel-header,
        html.dark main .package-table-wrapper,
        html.dark main .history-notice,
        html.dark main .router-card,
        html.dark main .router-panel,
        html.dark main .hotspot-card,
        html.dark main .hotspot-panel,
        html.dark main .session-card,
        html.dark main .session-panel,
        html.dark main .subscription-card,
        html.dark main .subscription-panel,
        html.dark main .form-card,
        html.dark main .form-panel,
        html.dark main .content-card,
        html.dark main .settings-card,
        html.dark main .profile-card {
            background: #101b31 !important;
            border-color: #273852 !important;
            color: #e7eefb !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .18) !important;
        }

        html.dark main .package-panel-header,
        html.dark main .package-table th,
        html.dark main .package-table td {
            border-color: #273852 !important;
        }

        html.dark main .package-table th {
            background: #152139 !important;
            color: #aebdd3 !important;
        }

        html.dark main .package-table td,
        html.dark main .package-table tbody tr {
            background: #101b31 !important;
            color: #dce6f5 !important;
        }

        html.dark main .package-table tbody tr:hover,
        html.dark main .package-table tbody tr:hover td {
            background: #14223b !important;
        }

        html.dark main .package-header-title h2,
        html.dark main .package-panel-heading h3,
        html.dark main .package-name strong,
        html.dark main .summary-content strong,
        html.dark main .speed-row strong,
        html.dark main .empty-state strong {
            color: #f1f5ff !important;
        }

        html.dark main .package-header-title p,
        html.dark main .package-panel-heading p,
        html.dark main .package-name small,
        html.dark main .summary-content span,
        html.dark main .speed-row,
        html.dark main .empty-state span,
        html.dark main .history-notice {
            color: #a7b6cc !important;
        }

        html.dark main .record-count,
        html.dark main .router-profile {
            background: #1a2942 !important;
            color: #d9e5f7 !important;
        }

        html.dark main .history-notice {
            background: #10213c !important;
            border-color: #29476e !important;
        }

        html.dark main .history-notice strong {
            color: #9fc0ff !important;
        }

        /*
         * Final compatibility pass for legacy admin pages.
         * Several older views use custom headings/cards with hard-coded
         * light-mode colours rather than Tailwind text utilities.
         */
        html.dark main .package-header,
        html.dark main .page-header,
        html.dark main .router-header,
        html.dark main .hotspot-header,
        html.dark main .session-header,
        html.dark main .subscription-header,
        html.dark main .management-header {
            background: #0f1b33 !important;
            border-color: #273852 !important;
            color: #eef4ff !important;
        }

        html.dark main .package-header h1,
        html.dark main .package-header h2,
        html.dark main .package-header h3,
        html.dark main .page-header h1,
        html.dark main .page-header h2,
        html.dark main .page-header h3,
        html.dark main .router-header h1,
        html.dark main .router-header h2,
        html.dark main .hotspot-header h1,
        html.dark main .hotspot-header h2,
        html.dark main .session-header h1,
        html.dark main .session-header h2,
        html.dark main .subscription-header h1,
        html.dark main .subscription-header h2,
        html.dark main .management-header h1,
        html.dark main .management-header h2,
        html.dark main .management-header h3 {
            color: #f4f7ff !important;
        }

        html.dark main .package-header p,
        html.dark main .page-header p,
        html.dark main .router-header p,
        html.dark main .hotspot-header p,
        html.dark main .session-header p,
        html.dark main .subscription-header p,
        html.dark main .management-header p {
            color: #9fb0ca !important;
        }

        html.dark main .package-panel h1,
        html.dark main .package-panel h2,
        html.dark main .package-panel h3,
        html.dark main .router-panel h1,
        html.dark main .router-panel h2,
        html.dark main .router-panel h3,
        html.dark main .hotspot-panel h1,
        html.dark main .hotspot-panel h2,
        html.dark main .hotspot-panel h3,
        html.dark main .session-panel h1,
        html.dark main .session-panel h2,
        html.dark main .session-panel h3,
        html.dark main .subscription-panel h1,
        html.dark main .subscription-panel h2,
        html.dark main .subscription-panel h3,
        html.dark main .content-card h1,
        html.dark main .content-card h2,
        html.dark main .content-card h3,
        html.dark main .form-card h1,
        html.dark main .form-card h2,
        html.dark main .form-card h3 {
            color: #eef4ff !important;
        }

        html.dark main .package-panel p,
        html.dark main .router-panel p,
        html.dark main .hotspot-panel p,
        html.dark main .session-panel p,
        html.dark main .subscription-panel p,
        html.dark main .content-card p,
        html.dark main .form-card p {
            color: #a8b7cf !important;
        }

        /*
         * Catch legacy inline/custom dark text inside the application content.
         * Semantic status colours and buttons remain untouched.
         */
        html.dark main h1:not([class*="text-white"]):not([class*="text-blue"]):not([class*="text-green"]):not([class*="text-red"]):not([class*="text-yellow"]):not([class*="text-purple"]),
        html.dark main h2:not([class*="text-white"]):not([class*="text-blue"]):not([class*="text-green"]):not([class*="text-red"]):not([class*="text-yellow"]):not([class*="text-purple"]),
        html.dark main h3:not([class*="text-white"]):not([class*="text-blue"]):not([class*="text-green"]):not([class*="text-red"]):not([class*="text-yellow"]):not([class*="text-purple"]) {
            color: #eef4ff !important;
        }

        html.dark main .card,
        html.dark main .panel {
            background-color: #101b31 !important;
            border-color: #273852 !important;
            color: #e7eefb !important;
        }

        /* Generic legacy panels/cards used by older admin views. */
        html.dark main .rounded-lg.bg-white,
        html.dark main .rounded-xl.bg-white,
        html.dark main .rounded-2xl.bg-white,
        html.dark main .shadow.bg-white,
        html.dark main .shadow-sm.bg-white,
        html.dark main .shadow-md.bg-white,
        html.dark main .shadow-lg.bg-white {
            background-color: #101b31 !important;
            border-color: #273852 !important;
        }

        /* Preserve semantic colour surfaces while making their text readable. */
        html.dark main .bg-green-50,
        html.dark main .bg-green-100 {
            background-color: #123c2a !important;
            color: #9aebba !important;
        }

        html.dark main .bg-red-50,
        html.dark main .bg-red-100 {
            background-color: #451d29 !important;
            color: #ffb6c1 !important;
        }

        html.dark main .bg-yellow-50,
        html.dark main .bg-yellow-100,
        html.dark main .bg-amber-50,
        html.dark main .bg-amber-100 {
            background-color: #403414 !important;
            color: #f7dc82 !important;
        }

        html.dark main .bg-blue-50,
        html.dark main .bg-blue-100 {
            background-color: #102b50 !important;
            color: #a9c8ff !important;
        }

        /* Browser autofill otherwise creates bright fields in dark mode. */
        html.dark main input:-webkit-autofill,
        html.dark main input:-webkit-autofill:hover,
        html.dark main input:-webkit-autofill:focus,
        html.dark main textarea:-webkit-autofill,
        html.dark main select:-webkit-autofill {
            -webkit-text-fill-color: #edf3ff !important;
            -webkit-box-shadow: 0 0 0 1000px #0b1629 inset !important;
            box-shadow: 0 0 0 1000px #0b1629 inset !important;
            transition: background-color 9999s ease-out 0s;
        }

        .sidebar-theme {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 42px;
            margin-bottom: 9px;
            padding: 7px 9px;
            border: 1px solid rgba(148, 163, 184, .12);
            border-radius: 12px;
            background: rgba(255, 255, 255, .035);
        }

        .sidebar-theme__label {
            display: flex;
            align-items: center;
            min-width: 0;
            gap: 9px;
            color: #b8c5df;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .sidebar-theme__label i {
            width: 20px;
            text-align: center;
            color: #8fb5ff;
            font-size: 12px;
        }

        .theme-switch {
            position: relative;
            width: 38px;
            height: 21px;
            flex: 0 0 38px;
            padding: 0;
            border: 1px solid rgba(148, 163, 184, .24);
            border-radius: 999px;
            background: #33415f;
            cursor: pointer;
            transition: background .22s ease, border-color .22s ease;
        }

        .theme-switch__thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 15px;
            height: 15px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: #64748b;
            background: #ffffff;
            box-shadow: 0 2px 7px rgba(0, 0, 0, .24);
            font-size: 7px;
            transition: transform .22s ease, color .22s ease, background .22s ease;
        }

        html.dark .theme-switch {
            background: #3f6ff3;
            border-color: rgba(116, 158, 255, .55);
        }

        html.dark .theme-switch__thumb {
            transform: translateX(17px);
            color: #f59e0b;
            background: #fff7d6;
        }

        .theme-switch:focus-visible {
            outline: 2px solid #8db2ff;
            outline-offset: 3px;
        }

        .admin-sidebar.collapsed .sidebar-theme {
            justify-content: center;
            padding: 7px 4px;
        }

        .admin-sidebar.collapsed .sidebar-theme__label {
            display: none;
        }

        @media (max-width: 639px) {
            .admin-sidebar {
                display: none;
            }

            .admin-content,
            .admin-content.collapsed {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .admin-sidebar,
            .admin-content,
            .sidebar-link,
            .sidebar-icon,
            .sidebar-toggle,
            .theme-switch,
            .theme-switch__thumb {
                transition: none !important;
            }

            .admin-sidebar::before,
            .admin-sidebar::after {
                animation: none !important;
            }
        }


        /* FINAL GLOBAL BRAND + FLASH MESSAGE POLISH */
        .sidebar-brand__mark {
            position: relative;
            overflow: visible;
            background: linear-gradient(135deg, #2f80ff 0%, #5b5cf6 55%, #7c4dff 100%);
            box-shadow: 0 10px 25px rgba(57, 119, 255, .34), inset 0 0 0 1px rgba(255,255,255,.12);
        }

        .sidebar-brand__mark i {
            position: relative;
            z-index: 2;
            font-size: 19px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,.18));
        }

        .sidebar-brand__mark::after {
            content: "";
            position: absolute;
            right: 5px;
            bottom: 5px;
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #4ade80;
            border: 2px solid #172554;
            box-shadow: 0 0 0 2px rgba(74,222,128,.14);
        }

        .flash-message {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            width: calc(100% - 32px);
            max-width: 1200px;
            margin: 18px auto;
            padding: 14px 46px 14px 15px;
            border: 1px solid transparent;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.5;
            opacity: 1;
            transform: translateY(0);
            transition: opacity .22s ease, transform .22s ease, margin .22s ease, padding .22s ease;
        }

        .flash-message__icon {
            flex: 0 0 22px;
            width: 22px;
            height: 22px;
            display: grid;
            place-items: center;
            margin-top: 0;
            border-radius: 7px;
            font-size: 12px;
        }

        .flash-message__content {
            min-width: 0;
            flex: 1 1 auto;
        }

        .flash-message__close {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            padding: 0;
            border: 0;
            border-radius: 8px;
            color: currentColor;
            background: transparent;
            cursor: pointer;
            opacity: .72;
            transition: opacity .18s ease, background .18s ease;
        }

        .flash-message__close:hover {
            opacity: 1;
            background: rgba(15, 23, 42, .08);
        }

        .flash-message.is-hiding {
            opacity: 0;
            transform: translateY(-6px);
        }

        .flash-success {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .flash-success .flash-message__icon {
            color: #047857;
            background: #d1fae5;
        }

        .flash-error {
            background: #fff1f2;
            color: #9f1239;
            border-color: #fecdd3;
        }

        .flash-error .flash-message__icon {
            color: #be123c;
            background: #ffe4e6;
        }

        .flash-warning {
            background: #fffbeb;
            color: #92400e;
            border-color: #fde68a;
        }

        .flash-warning .flash-message__icon {
            color: #b45309;
            background: #fef3c7;
        }

        html.dark .flash-message {
            box-shadow: 0 10px 28px rgba(0, 0, 0, .24);
        }

        html.dark .flash-success {
            background: #0f2f27 !important;
            color: #a7f3d0 !important;
            border-color: #1f6b54 !important;
        }

        html.dark .flash-success .flash-message__icon {
            background: #134e3a;
            color: #6ee7b7;
        }

        html.dark .flash-error {
            background: #3a1721 !important;
            color: #fecdd3 !important;
            border-color: #7f1d3d !important;
        }

        html.dark .flash-error .flash-message__icon {
            background: #5b1d2d;
            color: #fda4af;
        }

        html.dark .flash-warning {
            background: #352b12 !important;
            color: #fde68a !important;
            border-color: #70561b !important;
        }

        html.dark .flash-warning .flash-message__icon {
            background: #4b3b14;
            color: #fcd34d;
        }

        html.dark .flash-message__close:hover {
            background: rgba(255, 255, 255, .08);
        }

        @media (max-width: 639px) {
            .flash-message {
                width: calc(100% - 24px);
                margin: 12px auto;
                padding: 13px 42px 13px 13px;
                font-size: 13px;
            }
        }

    </style>
</head>

<body class="font-sans antialiased">



@auth

    @php
        $currentUser = auth()->user();

        $isSuperAdmin = $currentUser->role === 'super_admin';
        $isAdmin = $currentUser->role === 'admin';

        if ($isSuperAdmin) {
            $sidebarBrand = 'Raiders';
            $sidebarSubtitle = 'Platform Administration';
        } elseif ($isAdmin) {
            $sidebarBrand = $currentUser->network_name
                ?: $currentUser->business_name
                ?: config('app.name', 'WiFi Billing System');

            $sidebarSubtitle = 'ISP Administration';
        } else {
            $sidebarBrand = config('app.name', 'WiFi Billing System');
            $sidebarSubtitle = null;
        }

        $sidebarUnreadNotifications = isset($unreadNotifications)
            ? max(0, (int) $unreadNotifications)
            : 0;
    @endphp

    @if($isAdmin || $isSuperAdmin)

        <aside
            id="sidebar"
            class="admin-sidebar"
            aria-label="Administration navigation"
        >
            <div class="sidebar-brand">
                <div class="sidebar-brand__mark" aria-hidden="true">
                    <i class="fas fa-wifi"></i>
                </div>

                <div class="sidebar-brand__copy">
                    <h2 class="sidebar-title">{{ $sidebarBrand }}</h2>

                    @if($sidebarSubtitle)
                        <div class="sidebar-subtitle">{{ $sidebarSubtitle }}</div>
                    @endif
                </div>

                <button
                    id="sidebarToggle"
                    class="sidebar-toggle"
                    type="button"
                    aria-label="Collapse sidebar"
                    aria-expanded="true"
                >
                    <i
                        id="sidebarToggleIcon"
                        class="fas fa-angle-left"
                        aria-hidden="true"
                    ></i>
                </button>
            </div>

            <div class="sidebar-scroll">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="sidebar-link sidebar-blue {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    @if(request()->routeIs('admin.dashboard'))
                        aria-current="page"
                    @endif
                >
                    <span class="sidebar-icon">
                        <i class="fas fa-gauge-high" aria-hidden="true"></i>
                    </span>
                    <span class="sidebar-text">Dashboard</span>
                </a>

                {{-- Super Admin menu --}}
                @if($isSuperAdmin)

                    <div class="sidebar-section">Platform</div>

                    <a
                        href="{{ route('admin.admin-users.index') }}"
                        class="sidebar-link sidebar-cyan {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.admin-users.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-user-tie" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">ISP Admins</span>
                    </a>

                    <a
                        href="{{ route('admin.license-payments.index') }}"
                        class="sidebar-link sidebar-green {{ request()->routeIs('admin.license-payments.*') || request()->routeIs('admin.license-receipts.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.license-payments.*') || request()->routeIs('admin.license-receipts.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-credit-card" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">License Payments</span>
                    </a>

                    <a
                        href="{{ route('admin.settings.edit') }}"
                        class="sidebar-link sidebar-purple {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.settings.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-sliders" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Platform Settings</span>
                    </a>

                    <div class="sidebar-section">Monitoring</div>

                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="sidebar-link sidebar-orange {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.reports.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-chart-column" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Reports</span>
                    </a>

                    <a
                        href="{{ route('admin.audit-logs.index') }}"
                        class="sidebar-link sidebar-slate {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.audit-logs.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Platform Logs</span>
                    </a>

                {{-- ISP Admin menu --}}
                @elseif($isAdmin)

                    <div class="sidebar-section">ISP Management</div>

                    <a
                        href="{{ route('admin.customers.index') }}"
                        class="sidebar-link sidebar-blue {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.customers.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-users" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Customers</span>
                    </a>

                    <a
                        href="{{ route('admin.packages.index') }}"
                        class="sidebar-link sidebar-purple {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.packages.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-cube" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Packages</span>
                    </a>

                    <a
                        href="{{ route('admin.routers.index') }}"
                        class="sidebar-link sidebar-cyan {{ request()->routeIs('admin.routers.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.routers.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-network-wired" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">MikroTik Routers</span>
                    </a>

                    <a
                        href="{{ route('admin.hotspot-users.index') }}"
                        class="sidebar-link sidebar-green {{ request()->routeIs('admin.hotspot-users.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.hotspot-users.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-wifi" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Hotspot Users</span>
                    </a>

                    <a
                        href="{{ route('admin.hotspot-sessions.index') }}"
                        class="sidebar-link sidebar-orange {{ request()->routeIs('admin.hotspot-sessions.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.hotspot-sessions.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Sessions</span>
                    </a>

                    <a
                        href="{{ route('admin.subscriptions.index') }}"
                        class="sidebar-link sidebar-pink {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.subscriptions.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-arrows-rotate" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Subscriptions</span>
                    </a>

                    <div class="sidebar-section">Business</div>

                    <a
                        href="{{ route('admin.business-profile.edit') }}"
                        class="sidebar-link sidebar-yellow {{ request()->routeIs('admin.business-profile.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.business-profile.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-building" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Business Profile</span>
                    </a>

                    <a
                        href="{{ route('admin.license-payments.index') }}"
                        class="sidebar-link sidebar-green {{ request()->routeIs('admin.license-payments.*') || request()->routeIs('admin.license-receipts.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.license-payments.*') || request()->routeIs('admin.license-receipts.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Platform License</span>
                    </a>

                    <a
                        href="{{ route('admin.reports.index') }}"
                        class="sidebar-link sidebar-orange {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.reports.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-chart-column" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Reports</span>
                    </a>

                    <a
                        href="{{ route('admin.audit-logs.index') }}"
                        class="sidebar-link sidebar-slate {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}"
                        @if(request()->routeIs('admin.audit-logs.*'))
                            aria-current="page"
                        @endif
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text">Audit Logs</span>
                    </a>

                @endif

                <div class="sidebar-section">Account</div>

                <a
                    href="{{ route('admin.notifications.index') }}"
                    class="sidebar-link sidebar-pink {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
                    @if(request()->routeIs('admin.notifications.*'))
                        aria-current="page"
                    @endif
                >
                    <span class="sidebar-icon">
                        <i class="fas fa-bell" aria-hidden="true"></i>
                    </span>

                    <span class="sidebar-text">Notifications</span>

                    @if($sidebarUnreadNotifications > 0)
                        <span
                            class="notification-badge"
                            aria-label="{{ $sidebarUnreadNotifications }} unread notifications"
                        >
                            {{ $sidebarUnreadNotifications > 99 ? '99+' : $sidebarUnreadNotifications }}
                        </span>
                    @endif
                </a>
            </div>

            <div class="sidebar-footer">
                <div class="sidebar-theme">
                    <div class="sidebar-theme__label">
                        <i id="themeLabelIcon" class="fas fa-moon" aria-hidden="true"></i>
                        <span id="themeLabelText">Dark Mode</span>
                    </div>

                    <button
                        id="themeToggle"
                        class="theme-switch"
                        type="button"
                        role="switch"
                        aria-label="Dark mode"
                        aria-checked="false"
                        title="Toggle light and dark mode"
                    >
                        <span class="theme-switch__thumb" aria-hidden="true">
                            <i id="themeToggleIcon" class="fas fa-moon"></i>
                        </span>
                    </button>
                </div>
                <div class="sidebar-account">
                    <div class="sidebar-account__avatar" aria-hidden="true">
                        <i class="{{ $isSuperAdmin ? 'fas fa-shield-halved' : 'fas fa-user-tie' }}"></i>
                    </div>

                    <div class="sidebar-account__copy">
                        <strong>{{ $currentUser->name }}</strong>
                        <span>{{ $isSuperAdmin ? 'Super Admin' : 'ISP Admin' }}</span>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="logout-btn"
                    >
                        <span class="sidebar-icon">
                            <i class="fas fa-right-from-bracket" aria-hidden="true"></i>
                        </span>

                        <span class="logout-text">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

    @endif

@endauth

<div
    id="mainContent"
    class="@auth{{ in_array(auth()->user()->role, ['admin', 'super_admin'], true) ? 'admin-content' : 'normal-content' }}@else normal-content @endauth"
>
    @include('layouts.navigation')

    @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        @if(session('success'))
            <div class="flash-message flash-success" role="status" data-flash-message>
                <span class="flash-message__icon" aria-hidden="true">
                    <i class="fas fa-check"></i>
                </span>
                <div class="flash-message__content">{{ session('success') }}</div>
                <button type="button" class="flash-message__close" aria-label="Dismiss message" data-flash-close>
                    <i class="fas fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="flash-message flash-error" role="alert" data-flash-message>
                <span class="flash-message__icon" aria-hidden="true">
                    <i class="fas fa-circle-exclamation"></i>
                </span>
                <div class="flash-message__content">{{ session('error') }}</div>
                <button type="button" class="flash-message__close" aria-label="Dismiss message" data-flash-close>
                    <i class="fas fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @if(session('warning'))
            <div class="flash-message flash-warning" role="status" data-flash-message>
                <span class="flash-message__icon" aria-hidden="true">
                    <i class="fas fa-triangle-exclamation"></i>
                </span>
                <div class="flash-message__content">{{ session('warning') }}</div>
                <button type="button" class="flash-message__close" aria-label="Dismiss message" data-flash-close>
                    <i class="fas fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        {{ $slot }}
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dismissFlashMessage = (message) => {
            if (!message || message.classList.contains('is-hiding')) {
                return;
            }

            message.classList.add('is-hiding');

            window.setTimeout(() => {
                message.remove();
            }, 240);
        };

        document.querySelectorAll('[data-flash-message]').forEach((message) => {
            const closeButton = message.querySelector('[data-flash-close]');

            if (closeButton) {
                closeButton.addEventListener('click', () => dismissFlashMessage(message));
            }

            window.setTimeout(() => dismissFlashMessage(message), 8000);
        });

        const themeStorageKey = 'wifiBillingTheme';
        const themeEnabled = @json($themeEnabled);
        const themeToggle = document.getElementById('themeToggle');
        const themeToggleIcon = document.getElementById('themeToggleIcon');

        if (!themeEnabled) {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        }

        const applyTheme = (theme, persist = false) => {
            const isDark = theme === 'dark';

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';

            if (themeToggle && themeToggleIcon) {
                const themeLabelIcon = document.getElementById('themeLabelIcon');
                const themeLabelText = document.getElementById('themeLabelText');

                themeToggleIcon.classList.toggle('fa-moon', !isDark);
                themeToggleIcon.classList.toggle('fa-sun', isDark);

                if (themeLabelIcon) {
                    themeLabelIcon.classList.toggle('fa-moon', !isDark);
                    themeLabelIcon.classList.toggle('fa-sun', isDark);
                }

                if (themeLabelText) {
                    themeLabelText.textContent = isDark ? 'Light Mode' : 'Dark Mode';
                }

                const nextLabel = isDark
                    ? 'Switch to light mode'
                    : 'Switch to dark mode';

                themeToggle.setAttribute('aria-label', nextLabel);
                themeToggle.setAttribute('title', nextLabel);
                themeToggle.setAttribute('aria-checked', isDark ? 'true' : 'false');
            }

            if (persist) {
                try {
                    window.localStorage.setItem(themeStorageKey, theme);
                } catch (error) {
                    // Theme still works when browser storage is unavailable.
                }
            }

            window.dispatchEvent(new CustomEvent('app-theme-changed', {
                detail: { theme }
            }));
        };

        let currentTheme = document.documentElement.classList.contains('dark')
            ? 'dark'
            : 'light';

        applyTheme(currentTheme);

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                currentTheme = document.documentElement.classList.contains('dark')
                    ? 'light'
                    : 'dark';

                /*
                 * Persist the selected theme before reloading.
                 * The early theme bootstrap in <head> reads this value before
                 * the page renders, so the refreshed page opens directly in
                 * the selected theme and theme-dependent charts/components
                 * initialize with the correct colours.
                 */
                try {
                    window.localStorage.setItem(themeStorageKey, currentTheme);
                } catch (error) {
                    /*
                     * If storage is unavailable, apply the theme normally
                     * instead of causing a reload that cannot remember it.
                     */
                    applyTheme(currentTheme);
                    return;
                }

                window.location.reload();
            });
        }

        const systemTheme = window.matchMedia
            ? window.matchMedia('(prefers-color-scheme: dark)')
            : null;

        if (themeEnabled && systemTheme) {
            const handleSystemThemeChange = (event) => {
                let savedTheme = null;

                try {
                    savedTheme = window.localStorage.getItem(themeStorageKey);
                } catch (error) {
                    savedTheme = null;
                }

                if (savedTheme !== 'light' && savedTheme !== 'dark') {
                    currentTheme = event.matches ? 'dark' : 'light';
                    applyTheme(currentTheme);
                }
            };

            if (typeof systemTheme.addEventListener === 'function') {
                systemTheme.addEventListener('change', handleSystemThemeChange);
            }
        }

        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('mainContent');
        const toggle = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('sidebarToggleIcon');

        if (!sidebar || !content || !toggle || !toggleIcon) {
            return;
        }

        const applySidebarState = (collapsed) => {
            sidebar.classList.toggle('collapsed', collapsed);
            content.classList.toggle('collapsed', collapsed);

            toggleIcon.classList.toggle('fa-angle-left', !collapsed);
            toggleIcon.classList.toggle('fa-angle-right', collapsed);

            toggle.setAttribute(
                'aria-label',
                collapsed ? 'Expand sidebar' : 'Collapse sidebar'
            );

            toggle.setAttribute(
                'aria-expanded',
                collapsed ? 'false' : 'true'
            );
        };

        let collapsed = false;

        try {
            collapsed = window.localStorage.getItem('adminSidebarCollapsed') === '1';
        } catch (error) {
            collapsed = false;
        }

        applySidebarState(collapsed);

        toggle.addEventListener('click', () => {
            collapsed = !sidebar.classList.contains('collapsed');
            applySidebarState(collapsed);

            try {
                window.localStorage.setItem(
                    'adminSidebarCollapsed',
                    collapsed ? '1' : '0'
                );
            } catch (error) {
                // Sidebar still works when browser storage is unavailable.
            }
        });
    });
</script>

</body>
</html>
