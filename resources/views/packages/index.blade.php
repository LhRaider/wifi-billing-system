<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Internet Packages
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:1200px;margin:0 auto;">

            @if(session('success'))
                <div style="background:#d1fae5;color:#065f46;padding:15px;border-radius:10px;margin-bottom:20px;font-weight:700;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div style="text-align:center;margin-bottom:40px;">
                <h2 style="font-size:38px;font-weight:900;color:#111827;margin:0;">
                    Choose Your Internet Package
                </h2>

                <p style="color:#6b7280;margin-top:10px;font-size:17px;">
                    Fast, secure, and instant hotspot access.
                </p>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:25px;">

                @foreach($packages as $package)

                    <div style="background:white;border-radius:18px;padding:30px;box-shadow:0 8px 20px rgba(0,0,0,0.10);position:relative;border:1px solid #e5e7eb;">

                        @if($loop->iteration == 2)
                            <div style="position:absolute;top:15px;right:15px;background:#f97316;color:white;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:800;">
                                <i class="fas fa-star"></i> Best Value
                            </div>
                        @else
                            <div style="position:absolute;top:15px;right:15px;background:#2563eb;color:white;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:800;">
                                <i class="fas fa-wifi"></i> Available
                            </div>
                        @endif

                        <div style="width:65px;height:65px;background:#dbeafe;color:#2563eb;border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:28px;margin-bottom:20px;">
                            @if(str_contains(strtolower($package->name), 'unlimited'))
                                <i class="fas fa-infinity"></i>
                            @elseif(str_contains(strtolower($package->speed ?? ''), '30'))
                                <i class="fas fa-gauge-high"></i>
                            @elseif(str_contains(strtolower($package->speed ?? ''), '20'))
                                <i class="fas fa-gauge"></i>
                            @elseif(str_contains(strtolower($package->speed ?? ''), '10'))
                                <i class="fas fa-signal"></i>
                            @else
                                <i class="fas fa-box-open"></i>
                            @endif
                        </div>

                        <h3 style="font-size:24px;font-weight:900;color:#111827;margin-bottom:10px;">
                            {{ $package->name }}
                        </h3>

                        <p style="color:#6b7280;min-height:45px;">
                            {{ $package->description ?? 'Reliable internet access for browsing, streaming, and communication.' }}
                        </p>

                        <div style="font-size:40px;font-weight:900;color:#16a34a;margin:20px 0 5px;">
                            KES {{ number_format($package->price, 2) }}
                        </div>

                        <p style="color:#374151;font-weight:700;">
                            <i class="fas fa-clock" style="color:#2563eb;"></i>
                            {{ $package->duration_hours }} Hour(s)
                        </p>

                        <p style="color:#374151;font-weight:700;">
                            <i class="fas fa-gauge-high" style="color:#7c3aed;"></i>
                            {{ $package->speed ?? 'High Speed' }}
                        </p>

                        <hr style="margin:20px 0;border:none;border-top:1px solid #e5e7eb;">

                        <div style="display:grid;gap:10px;color:#374151;">
                            <div><i class="fas fa-circle-check" style="color:#16a34a;"></i> Instant Activation</div>
                            <div><i class="fas fa-circle-check" style="color:#16a34a;"></i> Hotspot Login Included</div>
                            <div><i class="fas fa-circle-check" style="color:#16a34a;"></i> Secure Connection</div>
                            <div><i class="fas fa-circle-check" style="color:#16a34a;"></i> Works on Phone & Laptop</div>
                        </div>

                        <a href="{{ route('mpesa.pay', $package) }}"
                           style="display:block;text-align:center;background:#2563eb;color:white;padding:14px;margin-top:25px;border-radius:10px;text-decoration:none;font-weight:900;">
                            <i class="fas fa-cart-shopping"></i>
                            Buy Package
                        </a>

                    </div>

                @endforeach

            </div>

        </div>
    </div>
</x-app-layout>