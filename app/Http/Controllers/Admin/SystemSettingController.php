<?php

namespace App\Http\Controllers\Admin;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $settings = SystemSetting::firstOrCreate([]);
        $routers = Router::where('is_active', true)->get();

        return view('admin.settings.edit', compact('settings', 'routers'));
    }

    public function update(Request $request)
    {
        $settings = SystemSetting::firstOrCreate([]);

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_phone' => 'nullable|string|max:50',
            'currency' => 'required|string|max:10',
            'default_router_id' => 'nullable|exists:routers,id',
            'support_message' => 'nullable|string',
        ]);

        $settings->update([
            'business_name' => $request->business_name,
            'business_phone' => $request->business_phone,
            'currency' => $request->currency,
            'mpesa_auto_activate' => $request->has('mpesa_auto_activate'),
            'mikrotik_real_connection' => $request->has('mikrotik_real_connection'),
            'default_router_id' => $request->default_router_id,
            'support_message' => $request->support_message,
        ]);
        NotificationHelper::create(
    'Settings Updated',
    'System settings were updated.',
    'info'
);

        return back()->with('success', 'System settings updated successfully.');
    }
}