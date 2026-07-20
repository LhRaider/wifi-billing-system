<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Router
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div style="background:white;padding:25px;border-radius:15px;">

                <form method="POST" action="{{ route('admin.routers.store') }}">
                    @csrf

                    <p>Router Name</p>
                    <input type="text" name="name" required style="width:100%;margin-bottom:15px;">

                    <p>IP Address</p>
                    <input type="text" name="ip_address" placeholder="192.168.88.1" required style="width:100%;margin-bottom:15px;">

                    <p>Username</p>
                    <input type="text" name="username" required style="width:100%;margin-bottom:15px;">

                    <p>Password</p>
                    <input type="password" name="password" required style="width:100%;margin-bottom:15px;">

                    <p>API Port</p>
                    <input type="number" name="api_port" value="8728" required style="width:100%;margin-bottom:15px;">

                    <button type="submit" style="background:#2563eb;color:white;padding:12px 22px;border-radius:8px;">
                        Save Router
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>