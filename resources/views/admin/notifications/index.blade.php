<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Notifications
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:1100px;margin:0 auto;background:white;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,0.08);padding:25px;border:1px solid #e5e7eb;">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
                <div>
                    <h3 style="font-size:24px;font-weight:800;color:#111827;margin:0;">
                        <i class="fas fa-bell" style="color:#2563eb;"></i>
                        System Notifications
                    </h3>
                    <p style="color:#6b7280;margin-top:5px;">
                        Important billing, customer, subscription, and router alerts.
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                    @csrf
                    <button type="submit"
                            style="background:#2563eb;color:white;padding:10px 16px;border:none;border-radius:8px;font-weight:700;cursor:pointer;">
                        <i class="fas fa-check-double"></i>
                        Mark All Read
                    </button>
                </form>
            </div>

            @forelse($notifications as $notification)
                <div style="
                    display:flex;
                    justify-content:space-between;
                    gap:20px;
                    padding:18px;
                    border:1px solid #e5e7eb;
                    border-radius:12px;
                    margin-bottom:12px;
                    background:{{ $notification->is_read ? '#ffffff' : '#eff6ff' }};
                ">

                    <div>
                        <h4 style="font-size:17px;font-weight:800;color:#111827;margin:0;">
                            @if($notification->type === 'success')
                                <i class="fas fa-check-circle" style="color:#16a34a;"></i>
                            @elseif($notification->type === 'warning')
                                <i class="fas fa-triangle-exclamation" style="color:#f59e0b;"></i>
                            @elseif($notification->type === 'danger')
                                <i class="fas fa-circle-exclamation" style="color:#dc2626;"></i>
                            @else
                                <i class="fas fa-circle-info" style="color:#2563eb;"></i>
                            @endif

                            {{ $notification->title }}
                        </h4>

                        <p style="margin-top:8px;color:#4b5563;">
                            {{ $notification->message }}
                        </p>

                        <small style="color:#6b7280;">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>

                    <div>
                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                @csrf
                                <button type="submit"
                                        style="background:#16a34a;color:white;padding:8px 13px;border:none;border-radius:8px;font-weight:700;cursor:pointer;">
                                    Mark Read
                                </button>
                            </form>
                        @else
                            <span style="background:#e5e7eb;color:#374151;padding:7px 12px;border-radius:20px;font-weight:700;font-size:13px;">
                                Read
                            </span>
                        @endif
                    </div>

                </div>
            @empty
                <div style="text-align:center;padding:40px;color:#6b7280;">
                    <i class="fas fa-bell-slash" style="font-size:40px;margin-bottom:15px;"></i>
                    <p>No notifications found.</p>
                </div>
            @endforelse

            <div style="margin-top:20px;">
                {{ $notifications->links() }}
            </div>

        </div>
    </div>
</x-app-layout>