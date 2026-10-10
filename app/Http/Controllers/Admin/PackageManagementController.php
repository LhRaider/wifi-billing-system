<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Router;
use App\Models\User;
use App\Services\MikrotikService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PackageManagementController extends Controller
{
    /**
     * Return the authenticated ISP Admin.
     */
    private function currentAdmin(): User
    {
        $admin = auth()->user();

        if (!$admin) {
            abort(401);
        }

        if ($admin->role !== 'admin') {
            abort(403);
        }

        return $admin;
    }

    /**
     * Ensure the ISP Admin still has platform access.
     */
    private function checkAdminService(): ?RedirectResponse
    {
        $admin = $this->currentAdmin();

        $trialActive = $admin->service_status === 'trial'
            && $admin->trial_ends_at
            && now()->lessThanOrEqualTo($admin->trial_ends_at);

        $subscriptionActive = $admin->service_status === 'active'
            && $admin->service_expires_at
            && now()->lessThanOrEqualTo($admin->service_expires_at);

        if ($trialActive || $subscriptionActive) {
            return null;
        }

        if ($admin->service_status !== 'expired') {
            $admin->update([
                'service_status' => 'expired',
            ]);

            NotificationHelper::create(
                'Admin Subscription Expired',
                'Admin '.$admin->name.' subscription is over.',
                'warning',
                'super_admin'
            );
        }

        return redirect()
            ->route('admin.license-payments.index')
            ->with(
                'error',
                'Your subscription is over. Please renew to continue providing services.'
            );
    }

    /**
     * Display packages belonging to the authenticated ISP Admin.
     */
    public function index(): View
    {
        $admin = $this->currentAdmin();

        $packages = Package::query()
            ->where('admin_id', $admin->id)
            ->latest('id')
            ->get();

        return view(
            'admin.packages.index',
            compact('packages')
        );
    }

    /**
     * Display the package creation form.
     */
    public function create(): View|RedirectResponse
    {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        return view('admin.packages.create');
    }

    /**
     * Store a new package for the authenticated ISP Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        $admin = $this->currentAdmin();

        $validated = $this->validatePackageRequest(
            $request,
            $admin->id
        );

        $isSpeedUnlimited = $request->boolean(
            'is_speed_unlimited'
        );

        $downloadSpeed = $isSpeedUnlimited
            ? null
            : strtoupper(trim($validated['download_speed']));

        $uploadSpeed = $isSpeedUnlimited
            ? null
            : strtoupper(trim($validated['upload_speed']));

        $speed = $isSpeedUnlimited
            ? null
            : $downloadSpeed.'/'.$uploadSpeed;

        $package = Package::create([
            'admin_id' => $admin->id,
            'name' => trim($validated['name']),
            'price' => $validated['price'],

            'duration_minutes' => (int) $validated['duration_minutes'],

            'is_speed_unlimited' => $isSpeedUnlimited,

            'speed' => $speed,
            'download_speed' => $downloadSpeed,
            'upload_speed' => $uploadSpeed,

            'package_type' => $validated['package_type'],

            'router_profile' => trim(
                $validated['router_profile']
            ),

            'description' => isset(
                $validated['description']
            ) && trim($validated['description']) !== ''
                ? trim($validated['description'])
                : null,

            'is_active' => $request->boolean(
                'is_active'
            ),
        ]);

        AuditHelper::log(
            'Create Package',
            'Packages',
            'Package '.$package->name
                .' created with duration '
                .$package->formatted_duration
                .' and speed '
                .$package->formatted_speed.'.'
        );

        NotificationHelper::create(
            'Package Created',
            'New '
                .strtoupper($package->package_type)
                .' package '
                .$package->name
                .' was created.',
            'success',
            'admin',
            $admin->id
        );

        return redirect()
            ->route('admin.packages.index')
            ->with(
                'success',
                'Package created successfully.'
            );
    }

    /**
     * Display the package edit form.
     */
    public function edit(
        Package $package
    ): View|RedirectResponse {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        $admin = $this->currentAdmin();

        $this->ensurePackageBelongsToAdmin(
            $package,
            $admin->id
        );

        return view(
            'admin.packages.edit',
            compact('package')
        );
    }

    /**
     * Update an existing package.
     */
    public function update(
        Request $request,
        Package $package
    ): RedirectResponse {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        $admin = $this->currentAdmin();

        $this->ensurePackageBelongsToAdmin(
            $package,
            $admin->id
        );

        $validated = $this->validatePackageRequest(
            $request,
            $admin->id,
            $package
        );

        $isSpeedUnlimited = $request->boolean(
            'is_speed_unlimited'
        );

        $downloadSpeed = $isSpeedUnlimited
            ? null
            : strtoupper(trim($validated['download_speed']));

        $uploadSpeed = $isSpeedUnlimited
            ? null
            : strtoupper(trim($validated['upload_speed']));

        $speed = $isSpeedUnlimited
            ? null
            : $downloadSpeed.'/'.$uploadSpeed;

        $package->update([
            'name' => trim($validated['name']),
            'price' => $validated['price'],

            'duration_minutes' => (int) $validated['duration_minutes'],

            'is_speed_unlimited' => $isSpeedUnlimited,

            'speed' => $speed,
            'download_speed' => $downloadSpeed,
            'upload_speed' => $uploadSpeed,

            'package_type' => $validated['package_type'],

            'router_profile' => trim(
                $validated['router_profile']
            ),

            'description' => isset(
                $validated['description']
            ) && trim($validated['description']) !== ''
                ? trim($validated['description'])
                : null,

            'is_active' => $request->boolean(
                'is_active'
            ),
        ]);

        AuditHelper::log(
            'Update Package',
            'Packages',
            'Package '.$package->name
                .' updated with duration '
                .$package->formatted_duration
                .' and speed '
                .$package->formatted_speed.'.'
        );

        NotificationHelper::create(
            'Package Updated',
            'Package '.$package->name.' was updated.',
            'info',
            'admin',
            $admin->id
        );

        return redirect()
            ->route('admin.packages.index')
            ->with(
                'success',
                'Package updated successfully.'
            );
    }

    /**
     * Activate or deactivate a package.
     *
     * Deactivation preserves all package, payment and subscription history.
     * An inactive package is no longer available for new customer purchases.
     */
    public function toggleStatus(
        Package $package
    ): RedirectResponse {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        $admin = $this->currentAdmin();

        $this->ensurePackageBelongsToAdmin(
            $package,
            $admin->id
        );

        $newStatus = !$package->is_active;

        $package->update([
            'is_active' => $newStatus,
        ]);

        $action = $newStatus
            ? 'Activate Package'
            : 'Deactivate Package';

        $statusWord = $newStatus
            ? 'activated'
            : 'deactivated';

        AuditHelper::log(
            $action,
            'Packages',
            'Package '
                .$package->name
                .' '.$statusWord.'.'
        );

        NotificationHelper::create(
            $newStatus
                ? 'Package Activated'
                : 'Package Deactivated',
            'Package '
                .$package->name
                .' was '.$statusWord.'.',
            $newStatus ? 'success' : 'warning',
            'admin',
            $admin->id
        );

        return redirect()
            ->route('admin.packages.index')
            ->with(
                'success',
                'Package '.$statusWord.' successfully.'
            );
    }

    /**
     * Delete a package that has no billing or subscription history.
     */
    public function destroy(
        Package $package
    ): RedirectResponse {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        $admin = $this->currentAdmin();

        $this->ensurePackageBelongsToAdmin(
            $package,
            $admin->id
        );

        if (
            $package->subscriptions()->exists()
            || $package->payments()->exists()
        ) {
            return redirect()
                ->route('admin.packages.index')
                ->with(
                    'error',
                    'This package has billing or subscription history and cannot be deleted. Deactivate it instead.'
                );
        }

        $packageName = $package->name;

        $package->delete();

        AuditHelper::log(
            'Delete Package',
            'Packages',
            'Package '.$packageName.' deleted.'
        );

        return redirect()
            ->route('admin.packages.index')
            ->with(
                'success',
                'Package deleted successfully.'
            );
    }

    /**
     * Synchronize the package profile with the ISP Admin's active router.
     */
    public function syncProfile(
        Package $package,
        MikrotikService $mikrotik
    ): RedirectResponse {
        if ($response = $this->checkAdminService()) {
            return $response;
        }

        $admin = $this->currentAdmin();

        $this->ensurePackageBelongsToAdmin(
            $package,
            $admin->id
        );

        if (!$package->is_active) {
            return back()->with(
                'error',
                'Inactive packages cannot be synchronized. Activate the package first.'
            );
        }

        $router = Router::query()
            ->where('admin_id', $admin->id)
            ->where('is_active', true)
            ->first();

        if (!$router) {
            return back()->with(
                'error',
                'No active router found. Please add and activate your router first.'
            );
        }

        $result = $mikrotik->syncPackageProfile(
            $package,
            $router
        );

        if (!empty($result['success'])) {
            AuditHelper::log(
                'Sync Package Profile',
                'Packages',
                'Package '
                    .$package->name
                    .' profile synced to '
                    .$router->name.'.'
            );
        }

        return back()->with(
            !empty($result['success'])
                ? 'success'
                : 'error',
            $result['message']
                ?? 'Router profile synchronization failed.'
        );
    }

    /**
     * Validate package creation and update requests.
     *
     * Every package must have a duration.
     * Unlimited applies only to internet speed.
     */
    private function validatePackageRequest(
        Request $request,
        int $adminId,
        ?Package $package = null
    ): array {
        $isSpeedUnlimited = $request->boolean(
            'is_speed_unlimited'
        );

        $nameRule = Rule::unique(
            'packages',
            'name'
        )->where(
            fn ($query) => $query->where(
                'admin_id',
                $adminId
            )
        );

        $routerProfileRule = Rule::unique(
            'packages',
            'router_profile'
        )->where(
            fn ($query) => $query->where(
                'admin_id',
                $adminId
            )
        );

        if ($package) {
            $nameRule->ignore($package->id);

            $routerProfileRule->ignore(
                $package->id
            );
        }

        return $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:100',
                    $nameRule,
                ],

                'price' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:99999999.99',
                ],

                'duration_minutes' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:5256000',
                ],

                'is_speed_unlimited' => [
                    'nullable',
                    'boolean',
                ],

                'download_speed' => [
                    Rule::requiredIf(!$isSpeedUnlimited),
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^[0-9]+(?:\.[0-9]+)?[KMG]$/i',
                ],

                'upload_speed' => [
                    Rule::requiredIf(!$isSpeedUnlimited),
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^[0-9]+(?:\.[0-9]+)?[KMG]$/i',
                ],

                'package_type' => [
                    'required',
                    Rule::in([
                        'hotspot',
                        'pppoe',
                    ]),
                ],

                'router_profile' => [
                    'required',
                    'string',
                    'max:100',
                    'regex:/^[A-Za-z0-9._-]+$/',
                    $routerProfileRule,
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

                'is_active' => [
                    'nullable',
                    'boolean',
                ],
            ],
            [
                'name.unique' =>
                    'You already have a package with this name.',

                'duration_minutes.required' =>
                    'Package duration is required.',

                'duration_minutes.integer' =>
                    'Package duration must be entered in whole minutes.',

                'duration_minutes.min' =>
                    'Package duration must be at least 1 minute.',

                'duration_minutes.max' =>
                    'Package duration is too large.',

                'download_speed.required' =>
                    'Download speed is required unless Unlimited Speed is enabled.',

                'download_speed.regex' =>
                    'Download speed must use a format such as 10M, 512K or 1G.',

                'upload_speed.required' =>
                    'Upload speed is required unless Unlimited Speed is enabled.',

                'upload_speed.regex' =>
                    'Upload speed must use a format such as 5M, 512K or 1G.',

                'router_profile.regex' =>
                    'Router profile may contain only letters, numbers, dots, underscores and hyphens.',

                'router_profile.unique' =>
                    'You already have a package using this router profile.',
            ]
        );
    }

    /**
     * Prevent cross-tenant package access.
     */
    private function ensurePackageBelongsToAdmin(
        Package $package,
        int $adminId
    ): void {
        if ((int) $package->admin_id !== $adminId) {
            abort(403);
        }
    }
}