<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WiFi Billing System') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .admin-sidebar {
            width:250px;height:100vh;position:fixed;left:0;top:0;
            background:linear-gradient(180deg,#0f172a,#111827);
            color:white;padding:25px;box-shadow:4px 0 15px rgba(0,0,0,0.2);
            overflow-y:auto;z-index:1000;transition:0.3s;
        }
        .admin-sidebar.collapsed { width:70px;padding:25px 12px; }
        .admin-content { margin-left:250px;width:calc(100% - 250px);min-height:100vh;background:#f3f4f6;transition:0.3s; }
        .admin-content.collapsed { margin-left:70px;width:calc(100% - 70px); }
        .normal-content { width:100%;min-height:100vh;background:#f3f4f6; }
        .sidebar-title { font-size:22px;font-weight:bold;margin-bottom:30px;white-space:nowrap;overflow:hidden;display:flex;align-items:center;gap:10px; }
        .admin-sidebar.collapsed .sidebar-title { justify-content:center;font-size:0;margin-top:45px; }
        .admin-sidebar.collapsed .sidebar-title i { font-size:24px; }
        .sidebar-toggle { position:absolute;top:15px;right:15px;background:rgba(255,255,255,0.1);color:white;border:none;width:35px;height:35px;border-radius:8px;cursor:pointer;font-size:16px; }
        .sidebar-link { display:flex;align-items:center;color:white;text-decoration:none;padding:12px 15px;margin-bottom:8px;border-radius:10px;transition:0.3s;background:rgba(255,255,255,0.03);white-space:nowrap;overflow:hidden; }
        .sidebar-link:hover { background:rgba(255,255,255,0.15);transform:translateX(5px); }
        .sidebar-link.active { background:#2563eb;box-shadow:0 4px 12px rgba(37,99,235,0.4); }
        .sidebar-icon { display:inline-block;width:24px;text-align:center;margin-right:8px;font-size:16px; }
        .sidebar-text { display:inline-block; }
        .admin-sidebar.collapsed .sidebar-text { display:none; }
        .admin-sidebar.collapsed .sidebar-link { justify-content:center;padding:12px 0; }
        .admin-sidebar.collapsed .sidebar-icon { margin-right:0;font-size:19px; }
        .logout-btn { background:#dc2626;color:white;padding:12px 15px;border:none;border-radius:10px;width:100%;cursor:pointer;display:flex;align-items:center;justify-content:center; }
        .logout-btn:hover { background:#b91c1c; }
        .admin-sidebar.collapsed .logout-text { display:none; }
    </style>
</head>

<body class="font-sans antialiased">

@auth
    @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
        <aside id="sidebar" class="admin-sidebar">

            <button id="sidebarToggle" onclick="toggleSidebar()" class="sidebar-toggle">
                <i class="fas fa-angle-left"></i>
            </button>

            <h2 class="sidebar-title">
                <i class="fas fa-wifi"></i>
                <span>WiFi Billing</span>
            </h2>

            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fas fa-chart-line"></i></span>
                <span class="sidebar-text">Dashboard</span>
            </a>

            @if(auth()->user()->role === 'super_admin')

                <a href="{{ route('admin.admin-users.index') }}" class="sidebar-link {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-user-shield"></i></span>
                    <span class="sidebar-text">Admin Accounts</span>
                </a>

                <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-building"></i></span>
                    <span class="sidebar-text">Admin Clients</span>
                </a>

                <a href="{{ route('admin.license-payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.license-payments.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-money-check-alt"></i></span>
                    <span class="sidebar-text">License Payments</span>
                </a>

                <a href="{{ route('receipts.index') }}" class="sidebar-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-receipt"></i></span>
                    <span class="sidebar-text">License Receipts</span>
                </a>

                <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-chart-pie"></i></span>
                    <span class="sidebar-text">License Reports</span>
                </a>

                <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-shield-alt"></i></span>
                    <span class="sidebar-text">Platform Logs</span>
                </a>

            @else

                <a href="{{ route('admin.packages.index') }}" class="sidebar-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-box"></i></span>
                    <span class="sidebar-text">Packages</span>
                </a>

                <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-users"></i></span>
                    <span class="sidebar-text">Customers</span>
                </a>

                <a href="{{ route('admin.routers.index') }}"
class="sidebar-link {{ request()->routeIs('admin.routers.*') ? 'active' : '' }}">

    <span class="sidebar-icon">
        <i class="fas fa-network-wired"></i>
    </span>

    <span class="sidebar-text">
        MikroTik
    </span>

</a>

                <a href="{{ route('admin.hotspot-users.index') }}" class="sidebar-link {{ request()->routeIs('admin.hotspot-users.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-signal"></i></span>
                    <span class="sidebar-text">Hotspot Users</span>
                </a>

                <a href="{{ route('admin.hotspot-sessions.index') }}" class="sidebar-link {{ request()->routeIs('admin.hotspot-sessions.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-clock"></i></span>
                    <span class="sidebar-text">Sessions</span>
                </a>

                <a href="{{ route('admin.subscriptions.index') }}" class="sidebar-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-file-invoice"></i></span>
                    <span class="sidebar-text">Subscriptions</span>
                </a>

                <a href="{{ route('receipts.index') }}" class="sidebar-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-receipt"></i></span>
                    <span class="sidebar-text">Receipts</span>
                </a>

                <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-shield-alt"></i></span>
                    <span class="sidebar-text">Audit Logs</span>
                </a>

                <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fas fa-chart-pie"></i></span>
                    <span class="sidebar-text">Reports</span>
                </a>

            @endif

          @php
    if (auth()->user()->role === 'super_admin') {
        $unreadNotifications = \App\Models\Notification::where('is_read', false)
            ->where('audience', 'super_admin')
            ->count();
    } else {
        $unreadNotifications = \App\Models\Notification::where('is_read', false)
            ->where('audience', 'admin')
            ->where('user_id', auth()->id())
            ->count();
    }
@endphp

            <a href="{{ route('admin.notifications.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
               style="position:relative;">
                <span class="sidebar-icon"><i class="fas fa-bell"></i></span>
                <span class="sidebar-text">Notifications</span>

                @if($unreadNotifications > 0)
                    <span style="background:#dc2626;color:white;font-size:12px;font-weight:bold;padding:2px 8px;border-radius:999px;margin-left:auto;">
                        {{ $unreadNotifications }}
                    </span>
                @endif
            </a>
          
            <a href="{{ route('admin.settings.edit') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fas fa-gear"></i></span>
                <span class="sidebar-text">Settings</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" style="margin-top:30px;">
                @csrf
                <button type="submit" class="logout-btn">
                    <span class="sidebar-icon"><i class="fas fa-right-from-bracket"></i></span>
                    <span class="logout-text">Logout</span>
                </button>
            </form>

        </aside>
    @endif
@endauth

<div id="mainContent"
     class="@auth {{ in_array(Auth::user()->role, ['admin', 'super_admin']) ? 'admin-content' : 'normal-content' }} @else normal-content @endauth">

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
            <div style="background:#d1fae5;color:#065f46;padding:15px;margin:20px auto;border-radius:10px;max-width:1200px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background:#fee2e2;color:#991b1b;padding:15px;margin:20px auto;border-radius:10px;max-width:1200px;">
                {{ session('error') }}
            </div>
        @endif

        {{ $slot }}
    </main>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('mainContent');
        const toggle = document.getElementById('sidebarToggle');

        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');

        if (sidebar.classList.contains('collapsed')) {
            toggle.innerHTML = '<i class="fas fa-angle-right"></i>';
        } else {
            toggle.innerHTML = '<i class="fas fa-angle-left"></i>';
        }
    }
</script>

</body>
</html>