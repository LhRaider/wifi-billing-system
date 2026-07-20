<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareLicense;
use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class SoftwareLicenseController extends Controller
{
    public function edit()
    {
        $license = SoftwareLicense::firstOrCreate([]);

        return view('admin.software-license.edit', compact('license'));
    }

    public function update(Request $request)
    {
        $license = SoftwareLicense::firstOrCreate([]);

        $request->validate([
            'client_name' => 'required|string|max:255',
            'plan_name' => 'required|string|max:255',
            'monthly_fee' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'status' => 'required|string|in:active,expired,suspended,grace',
            'grace_days' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $license->update([
            'client_name' => $request->client_name,
            'plan_name' => $request->plan_name,
            'monthly_fee' => $request->monthly_fee,
            'start_date' => $request->start_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
            'grace_days' => $request->grace_days,
            'notes' => $request->notes,
        ]);

        AuditHelper::log(
            'Update Software License',
            'License',
            'Software license settings were updated.'
        );

        NotificationHelper::create(
            'Software License Updated',
            'Software license details were updated.',
            'info'
        );

        return back()->with('success', 'Software license updated successfully.');
    }
}