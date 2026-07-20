<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Router
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div style="background:white;padding:25px;border-radius:15px;">

                <form method="POST" action="{{ route('admin.routers.update', $router) }}">
                    @csrf
                    @method('PUT')

                    <p>Router Name</p>
                    <input type="text" name="name" value="{{ $router->name }}" required style="width:100%;margin-bottom:15px;">

                    <p>IP Address</p>
                    <input type="text" name="ip_address" value="{{ $router->ip_address }}" required style="width:100%;margin-bottom:15px;">

                    <p>Username</p>
                    <input type="text" name="username" value="{{ $router->username }}" required style="width:100%;margin-bottom:15px;">

                    <p>Password</p>
                    <input type="text" name="password" value="{{ $router->password }}" required style="width:100%;margin-bottom:15px;">

                    <p>API Port</p>
                    <input type="number" name="api_port" value="{{ $router->api_port }}" required style="width:100%;margin-bottom:15px;">

                    <label>
                        <input type="checkbox" name="is_active" {{ $router->is_active ? 'checked' : '' }}>
                        Active
                    </label>

                    <br><br>

                    <button type="submit" style="background:#2563eb;color:white;padding:12px 22px;border-radius:8px;">
                        Update Router
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>