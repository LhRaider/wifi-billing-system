<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageManagementController extends Controller
{
    private function checkAdminService()
    {
        $admin = auth()->user();

        $trialActive = $admin->service_status === 'trial'
            && $admin->trial_ends_at
            && now()->lessThanOrEqualTo($admin->trial_ends_at);

        $subscriptionActive = $admin->service_status === 'active'
            && $admin->service_expires_at
            && now()->lessThanOrEqualTo($admin->service_expires_at);

        if (!$trialActive && !$subscriptionActive) {
            if ($admin->service_status !== 'expired') {
                $admin->update(['service_status' => 'expired']);

                NotificationHelper::create(
                    'Admin Subscription Expired',
                    'Admin '.$admin->name.' subscription is over.',
                    'warning',
                    'super_admin'
                );
            }

            return redirect()
                ->route('admin.license-payments.index')
                ->with('error', 'Your subscription is over. Please renew to continue providing services.');
        }

        return null;
    }

    public function index()
    {
        $packages = Package::where('admin_id', auth()->id())
            ->latest()
            ->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:1',
            'is_unlimited' => 'nullable',
            'download_speed' => 'required|string|max:50',
            'upload_speed' => 'required|string|max:50',
            'package_type' => 'required|in:hotspot,pppoe',
            'router_profile' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $package = Package::create([
            'admin_id' => auth()->id(),
            'name' => $request->name,
            'price' => $request->price,
            'duration_hours' => $request->has('is_unlimited') ? 0 : $request->duration_hours,
            'is_unlimited' => $request->has('is_unlimited'),
            'speed' => $request->download_speed.'/'.$request->upload_speed,
            'download_speed' => $request->download_speed,
            'upload_speed' => $request->upload_speed,
            'package_type' => $request->package_type,
            'router_profile' => $request->router_profile,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        AuditHelper::log(
            'Create Package',
            'Packages',
            'Package '.$package->name.' created with '.$package->speed
        );

        NotificationHelper::create(
            'Package Created',
            'New '.strtoupper($package->package_type).' package '.$package->name.' was created.',
            'success',
            'admin',
            auth()->id()
        );

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        if ($package->admin_id !== auth()->id()) {
            abort(403);
        }

        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        if ($package->admin_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:1',
            'is_unlimited' => 'nullable',
            'download_speed' => 'required|string|max:50',
            'upload_speed' => 'required|string|max:50',
            'package_type' => 'required|in:hotspot,pppoe',
            'router_profile' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $package->update([
            'name' => $request->name,
            'price' => $request->price,
            'duration_hours' => $request->has('is_unlimited') ? 0 : $request->duration_hours,
            'is_unlimited' => $request->has('is_unlimited'),
            'speed' => $request->download_speed.'/'.$request->upload_speed,
            'download_speed' => $request->download_speed,
            'upload_speed' => $request->upload_speed,
            'package_type' => $request->package_type,
            'router_profile' => $request->router_profile,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        AuditHelper::log(
            'Update Package',
            'Packages',
            'Package '.$package->name.' updated with '.$package->speed
        );

        NotificationHelper::create(
            'Package Updated',
            'Package '.$package->name.' was updated.',
            'info',
            'admin',
            auth()->id()
        );

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        if ($package->admin_id !== auth()->id()) {
            abort(403);
        }

        $packageName = $package->name;

        $package->delete();

        AuditHelper::log(
            'Delete Package',
            'Packages',
            'Package '.$packageName.' deleted'
        );

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    public function syncProfile(Package $package, \App\Services\MikrotikService $mikrotik)
{
    if ($response = $this->checkAdminService()) {
        return $response;
    }

    if ($package->admin_id !== auth()->id()) {
        abort(403);
    }

    $router = \App\Models\Router::where('admin_id', auth()->id())
        ->where('is_active', true)
        ->first();

    if (!$router) {
        return back()->with('error', 'No active router found. Please add and activate your router first.');
    }

    $result = $mikrotik->syncPackageProfile($package, $router);

    if ($result['success']) {
        AuditHelper::log(
            'Sync Package Profile',
            'Packages',
            'Package '.$package->name.' profile synced to '.$router->name.'.'
        );
    }

    return back()->with($result['success'] ? 'success' : 'error', $result['message']);
}
}