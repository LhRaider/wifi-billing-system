<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Recent Activity
        </h2>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="
                background:white;
                padding:25px;
                border-radius:15px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
            ">

                @foreach($recentActivities as $activity)
                    <div style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        padding:14px;
                        border-bottom:1px solid #e5e7eb;
                    ">
                        <div>
                            {{ $activity['icon'] }}
                            {{ $activity['message'] }}
                        </div>

                        <small style="color:#6b7280;">
                            {{ $activity['time']->diffForHumans() }}
                        </small>
                    </div>
                @endforeach

                <div style="margin-top:20px;">
                    {{ $recentActivities->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>