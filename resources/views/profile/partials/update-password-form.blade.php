<section>

    <form method="POST" action="{{ route('password.update') }}">

        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Current Password --}}
            <div>

                <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-200">

                    <i class="fas fa-lock text-blue-600 mr-2"></i>

                    Current Password

                </label>

                <input
                    type="password"
                    name="current_password"
                    autocomplete="current-password"
                    class="w-full rounded-xl border-gray-300 focus:ring-purple-500 focus:border-purple-500">

                @error('current_password','updatePassword')

                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>

                @enderror

            </div>

            {{-- New Password --}}
            <div>

                <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-200">

                    <i class="fas fa-key text-green-600 mr-2"></i>

                    New Password

                </label>

                <input
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    class="w-full rounded-xl border-gray-300 focus:ring-purple-500 focus:border-purple-500">

                @error('password','updatePassword')

                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>

                @enderror

            </div>

            {{-- Confirm Password --}}
            <div>

                <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-200">

                    <i class="fas fa-check-circle text-purple-600 mr-2"></i>

                    Confirm Password

                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="w-full rounded-xl border-gray-300 focus:ring-purple-500 focus:border-purple-500">

            </div>

        </div>

        <div class="mt-10 flex items-center gap-4">

            <button
                type="submit"
                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition">

                <i class="fas fa-key mr-2"></i>

                Change Password

            </button>

            @if(session('status') === 'password-updated')

                <div class="text-green-600 font-semibold">

                    <i class="fas fa-circle-check mr-2"></i>

                    Password updated successfully.

                </div>

            @endif

        </div>

    </form>

</section>