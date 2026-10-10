<x-guest-layout>

    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Create ISP Admin Account
        </h1>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Register your ISP business account and start your 15 day platform trial.
        </p>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- ISP Admin Name -->
        <div>
            <x-input-label
                for="name"
                :value="__('Administrator Name')"
            />

            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error
                :messages="$errors->get('name')"
                class="mt-2"
            />
        </div>


        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label
                for="email"
                :value="__('Email Address')"
            />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="email"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>


        <!-- Password -->
        <div class="mt-4">
            <x-input-label
                for="password"
                :value="__('Password')"
            />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>


        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label
                for="password_confirmation"
                :value="__('Confirm Password')"
            />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="mt-2"
            />
        </div>


        <!-- Account Type Notice -->
        <div class="mt-5 rounded-md border border-blue-200 bg-blue-50 px-4 py-3">
            <p class="text-sm text-blue-800">
                This registration creates an ISP Admin account.
                Customer internet accounts are managed separately by their ISP Admin.
            </p>
        </div>


        <!-- Trial Notice -->
        <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
            <p class="text-sm text-gray-700">
                Your account starts with a 15 day trial.
                Platform access after the trial requires an active ISP Admin license.
            </p>
        </div>


        <div class="mt-6 flex items-center justify-between">

            <a
                class="text-sm underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}"
            >
                {{ __('Already have an account?') }}
            </a>

            <x-primary-button>
                {{ __('Create ISP Admin Account') }}
            </x-primary-button>

        </div>

    </form>

</x-guest-layout>