<x-app-layout>
    <x-slot name="header">
        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            flex-wrap:wrap;
        ">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $isSuperAdmin ? 'Platform Activity' : 'Recent Activity' }}
                </h2>

                <p style="
                    margin-top:5px;
                    color:#6b7280;
                    font-size:14px;
                ">
                    @if($isSuperAdmin)
                        Recent ISP Admin registrations and platform license payment activity.
                    @else
                        Recent customer, payment, and subscription activity for your ISP account.
                    @endif
                </p>
            </div>

            <span style="
                background:#dbeafe;
                color:#1d4ed8;
                padding:8px 14px;
                border-radius:999px;
                font-weight:700;
                font-size:14px;
            ">
                {{ number_format($recentActivities->total()) }}
                {{ $recentActivities->total() === 1 ? 'Record' : 'Records' }}
            </span>
        </div>
    </x-slot>

    <div class="py-12" style="background:#f3f4f6;min-height:100vh;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div style="
                background:white;
                padding:25px;
                border-radius:15px;
                box-shadow:0 4px 12px rgba(0,0,0,0.08);
            ">

                @if($recentActivities->count())

                    <div style="
                        display:flex;
                        flex-direction:column;
                        gap:0;
                    ">

                        @foreach($recentActivities as $activity)

                            <div style="
                                display:flex;
                                justify-content:space-between;
                                align-items:flex-start;
                                gap:20px;
                                padding:16px 0;
                                border-bottom:1px solid #e5e7eb;
                            ">

                                <div style="
                                    display:flex;
                                    gap:12px;
                                    align-items:flex-start;
                                    min-width:0;
                                ">

                                    <div style="
                                        width:38px;
                                        height:38px;
                                        min-width:38px;
                                        border-radius:50%;
                                        background:{{ $activity['color'] }}18;
                                        color:{{ $activity['color'] }};
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        font-size:15px;
                                    ">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </div>

                                    <div style="min-width:0;">
                                        <div style="
                                            color:#111827;
                                            font-weight:600;
                                            line-height:1.5;
                                            word-break:break-word;
                                        ">
                                            {{ $activity['message'] }}
                                        </div>

                                        <div style="
                                            color:#9ca3af;
                                            font-size:12px;
                                            margin-top:4px;
                                        ">
                                            {{ $activity['time']->format('d M Y H:i') }}
                                        </div>
                                    </div>

                                </div>

                                <div style="
                                    color:#6b7280;
                                    font-size:13px;
                                    white-space:nowrap;
                                    padding-top:3px;
                                ">
                                    {{ $activity['time']->diffForHumans() }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                    <div style="margin-top:25px;">
                        {{ $recentActivities->links() }}
                    </div>

                @else

                    <div style="
                        text-align:center;
                        padding:50px 20px;
                        color:#6b7280;
                    ">

                        <div style="
                            font-size:42px;
                            margin-bottom:15px;
                        ">
                            🕘
                        </div>

                        <h3 style="
                            font-size:18px;
                            font-weight:700;
                            color:#374151;
                            margin-bottom:8px;
                        ">
                            No activity recorded
                        </h3>

                        <p style="margin:0;">
                            @if($isSuperAdmin)
                                Platform activity will appear here as ISP Admin accounts and license payments are recorded.
                            @else
                                Customer, payment, and subscription activity for your ISP account will appear here.
                            @endif
                        </p>

                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>