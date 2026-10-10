<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $this->ensureSuperAdmin();

        $settings = SystemSetting::firstOrCreate([]);

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->ensureSuperAdmin();

        $settings = SystemSetting::firstOrCreate([]);

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_phone' => 'nullable|string|max:50',
            'currency' => 'required|string|max:10',

            'license_payment_type' => 'required|in:till,paybill,phone',
            'license_till_number' => 'nullable|string|max:50',
            'license_paybill_number' => 'nullable|string|max:50',
            'license_account_number' => 'nullable|string|max:100',
            'license_phone_number' => 'nullable|string|max:50',
            'license_monthly_fee' => 'required|numeric|min:1',

            'support_message' => 'nullable|string',
        ]);

        $settings->update([
            'business_name' => $request->business_name,
            'business_phone' => $request->business_phone,
            'currency' => $request->currency,

            'license_payment_type' => $request->license_payment_type,
            'license_till_number' => $request->license_till_number,
            'license_paybill_number' => $request->license_paybill_number,
            'license_account_number' => $request->license_account_number,
            'license_phone_number' => $request->license_phone_number,
            'license_monthly_fee' => $request->license_monthly_fee,

            'support_message' => $request->support_message,
        ]);

        NotificationHelper::create(
            'Settings Updated',
            'Platform settings were updated.',
            'info',
            'super_admin'
        );

        return back()->with(
            'success',
            'Platform settings updated successfully.'
        );
    }

    private function ensureSuperAdmin(): void
    {
        if (
            !auth()->check() ||
            auth()->user()->role !== 'super_admin'
        ) {
            abort(
                403,
                'Only the Super Admin can manage platform settings.'
            );
        }
    }
}