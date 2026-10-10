<nav
    x-data="{ open: false }"
    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700"
>
    @php
        $user = Auth::user();

        $isSuperAdmin = $user?->role === 'super_admin';
        $isAdmin = $user?->role === 'admin';
        $isCustomer = $user?->role === 'customer';

        $homeRoute = ($isSuperAdmin || $isAdmin)
            ? route('admin.dashboard')
            : route('dashboard');
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Logo --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a
                        href="{{ $homeRoute }}"
                        aria-label="Go to dashboard"
                    >
                        <x-application-logo
                            class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
                        />
                    </a>
                </div>

                {{-- Customer desktop navigation.
                     Admin and Super Admin primary navigation is provided
                     by the tenant-safe sidebar in app.blade.php. --}}
                @if($isCustomer)
                    <div class="hidden sm:flex sm:items-center sm:ms-8 sm:space-x-6">
                        <a
                            href="{{ route('dashboard') }}"
                            class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}"
                        >
                            Dashboard
                        </a>

                        <a
                            href="{{ route('packages.index') }}"
                            class="text-sm font-medium {{ request()->routeIs('packages.index') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}"
                        >
                            Packages
                        </a>

                        <a
                            href="{{ route('payments.index') }}"
                            class="text-sm font-medium {{ request()->routeIs('payments.index') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}"
                        >
                            Payments
                        </a>

                        <a
                            href="{{ route('subscriptions.index') }}"
                            class="text-sm font-medium {{ request()->routeIs('subscriptions.index') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}"
                        >
                            Subscriptions
                        </a>

                        <a
                            href="{{ route('receipts.index') }}"
                            class="text-sm font-medium {{ request()->routeIs('receipts.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}"
                        >
                            Receipts
                        </a>
                    </div>
                @endif
            </div>

            {{-- Desktop account dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                            aria-label="Open account menu"
                        >
                            <span>{{ $user->name }}</span>

                            <svg
                                class="ms-1 fill-current h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if($isCustomer)
                            <x-dropdown-link :href="route('profile.edit')">
                                Profile
                            </x-dropdown-link>
                        @elseif($isAdmin)
                            <x-dropdown-link :href="route('admin.business-profile.edit')">
                                Business Profile
                            </x-dropdown-link>
                        @elseif($isSuperAdmin)
                            <x-dropdown-link :href="route('admin.settings.edit')">
                                Platform Settings
                            </x-dropdown-link>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                            >
                                Log Out
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile menu button --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button
                    type="button"
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition"
                    :aria-expanded="open.toString()"
                    aria-label="Toggle navigation menu"
                >
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            :class="{ 'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                        <path
                            :class="{ 'hidden': ! open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile navigation --}}
    <div
        :class="{ 'block': open, 'hidden': ! open }"
        class="hidden sm:hidden"
    >
        <div class="pt-2 pb-3 space-y-1">

            @if($isSuperAdmin)
                <x-responsive-nav-link
                    :href="route('admin.dashboard')"
                    :active="request()->routeIs('admin.dashboard')"
                >
                    Dashboard
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.admin-users.index')"
                    :active="request()->routeIs('admin.admin-users.*')"
                >
                    ISP Admins
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.license-payments.index')"
                    :active="request()->routeIs('admin.license-payments.*') || request()->routeIs('admin.license-receipts.*')"
                >
                    License Payments
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.settings.edit')"
                    :active="request()->routeIs('admin.settings.*')"
                >
                    Platform Settings
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.reports.index')"
                    :active="request()->routeIs('admin.reports.*')"
                >
                    Reports
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.audit-logs.index')"
                    :active="request()->routeIs('admin.audit-logs.*')"
                >
                    Platform Logs
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.notifications.index')"
                    :active="request()->routeIs('admin.notifications.*')"
                >
                    Notifications
                </x-responsive-nav-link>
            @elseif($isAdmin)
                <x-responsive-nav-link
                    :href="route('admin.dashboard')"
                    :active="request()->routeIs('admin.dashboard')"
                >
                    Dashboard
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.customers.index')"
                    :active="request()->routeIs('admin.customers.*')"
                >
                    Customers
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.packages.index')"
                    :active="request()->routeIs('admin.packages.*')"
                >
                    Packages
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.routers.index')"
                    :active="request()->routeIs('admin.routers.*')"
                >
                    MikroTik Routers
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.hotspot-users.index')"
                    :active="request()->routeIs('admin.hotspot-users.*')"
                >
                    Hotspot Users
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.hotspot-sessions.index')"
                    :active="request()->routeIs('admin.hotspot-sessions.*')"
                >
                    Sessions
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.subscriptions.index')"
                    :active="request()->routeIs('admin.subscriptions.*')"
                >
                    Subscriptions
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.business-profile.edit')"
                    :active="request()->routeIs('admin.business-profile.*')"
                >
                    Business Profile
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.license-payments.index')"
                    :active="request()->routeIs('admin.license-payments.*')"
                >
                    Platform License
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.reports.index')"
                    :active="request()->routeIs('admin.reports.*')"
                >
                    Reports
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.audit-logs.index')"
                    :active="request()->routeIs('admin.audit-logs.*')"
                >
                    Audit Logs
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('admin.notifications.index')"
                    :active="request()->routeIs('admin.notifications.*')"
                >
                    Notifications
                </x-responsive-nav-link>
            @elseif($isCustomer)
                <x-responsive-nav-link
                    :href="route('dashboard')"
                    :active="request()->routeIs('dashboard')"
                >
                    Dashboard
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('packages.index')"
                    :active="request()->routeIs('packages.index')"
                >
                    Packages
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('payments.index')"
                    :active="request()->routeIs('payments.index')"
                >
                    Payments
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('subscriptions.index')"
                    :active="request()->routeIs('subscriptions.index')"
                >
                    Subscriptions
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('receipts.index')"
                    :active="request()->routeIs('receipts.*')"
                >
                    Receipts
                </x-responsive-nav-link>
            @endif
        </div>

        {{-- Mobile account section --}}
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    {{ $user->name }}
                </div>

                <div class="font-medium text-sm text-gray-500">
                    {{ $user->email }}
                </div>

                <div class="text-xs text-gray-400 mt-1">
                    @if($isSuperAdmin)
                        Super Admin
                    @elseif($isAdmin)
                        ISP Admin
                    @elseif($isCustomer)
                        Customer
                    @else
                        Account
                    @endif
                </div>
            </div>

            <div class="mt-3 space-y-1">
                @if($isCustomer)
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Profile
                    </x-responsive-nav-link>
                @elseif($isAdmin)
                    <x-responsive-nav-link :href="route('admin.business-profile.edit')">
                        Business Profile
                    </x-responsive-nav-link>
                @elseif($isSuperAdmin)
                    <x-responsive-nav-link :href="route('admin.settings.edit')">
                        Platform Settings
                    </x-responsive-nav-link>
                @endif

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="w-full text-start block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:text-gray-800 focus:bg-gray-100 transition duration-150 ease-in-out"
                    >
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>