<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            MikroTik Setup Script
        </h2>
    </x-slot>

    <div style="padding:40px;background:#f3f4f6;min-height:100vh;">
        <div style="max-width:1000px;margin:0 auto;">

            <div style="background:white;padding:30px;border-radius:16px;box-shadow:0 6px 18px rgba(0,0,0,0.08);">
                <h2 style="font-size:26px;font-weight:900;margin-bottom:10px;">
                    Router Setup Script: {{ $router->name }}
                </h2>

                <p style="color:#6b7280;margin-bottom:20px;">
                    Copy this script and paste it into MikroTik Terminal. It enables API and creates basic Hotspot/PPPoE profiles.
                </p>

                <textarea id="scriptBox"
                          readonly
                          style="width:100%;height:350px;border:1px solid #d1d5db;border-radius:10px;padding:15px;font-family:monospace;">{{ $script }}</textarea>

                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button onclick="copyScript()"
                            style="background:#2563eb;color:white;border:none;padding:12px 20px;border-radius:8px;font-weight:800;">
                        Copy Script
                    </button>

                    <a href="{{ route('admin.routers.index') }}"
                       style="background:#64748b;color:white;padding:12px 20px;border-radius:8px;text-decoration:none;font-weight:800;">
                        Back
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function copyScript() {
            let script = document.getElementById('scriptBox');
            script.select();
            script.setSelectionRange(0, 99999);
            document.execCommand('copy');
            alert('Setup script copied.');
        }
    </script>
</x-app-layout>