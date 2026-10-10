<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotspotSession;
use App\Models\HotspotUser;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RouterController extends Controller
{
    /**
     * Display routers belonging only to
     * the currently logged-in ISP Admin.
     */
    public function index()
    {
        $admin = $this->currentAdmin();

        $routers = Router::where('admin_id', $admin->id)
            ->latest()
            ->get();

        return view('admin.routers.index', compact('routers'));
    }

    /**
     * Show the router creation form.
     */
    public function create()
    {
        $this->currentAdmin();

        return view('admin.routers.create');
    }

    /**
     * Store a new MikroTik router.
     */
    public function store(Request $request)
    {
        $admin = $this->currentAdmin();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9._ -]+$/',
            ],
            'ip_address' => [
                'required',
                'ip',
            ],
            'username' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'max:255',
            ],
            'api_port' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'router_type' => [
                'nullable',
                Rule::in(['MikroTik']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        try {
            $router = Router::create([
                'admin_id' => $admin->id,
                'name' => $validated['name'],
                'ip_address' => $validated['ip_address'],
                'username' => $validated['username'],
                'password' => $validated['password'],
                'api_port' => $validated['api_port'] ?? 8728,
                'router_type' => $validated['router_type'] ?? 'MikroTik',
                'notes' => $validated['notes'] ?? null,
                'is_active' => true,
            ]);

            Log::info('Router created', [
                'router_id' => $router->id,
                'admin_id' => $router->admin_id,
                'name' => $router->name,
                'ip_address' => $router->ip_address,
                'api_port' => $router->api_port,
            ]);

            return redirect()
                ->route('admin.routers.index')
                ->with(
                    'success',
                    'MikroTik router added successfully.'
                );
        } catch (\Throwable $e) {
            Log::error('Router creation failed', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput(
                    $request->except('password')
                )
                ->with(
                    'error',
                    'Router could not be created. Please check the details and try again.'
                );
        }
    }

    /**
     * Show the router editing form.
     */
    public function edit(Router $router)
    {
        $this->ensureRouterBelongsToAdmin($router);

        return view('admin.routers.edit', compact('router'));
    }

    /**
     * Update an existing router.
     */
    public function update(Request $request, Router $router)
    {
        $this->ensureRouterBelongsToAdmin($router);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9._ -]+$/',
            ],
            'ip_address' => [
                'required',
                'ip',
            ],
            'username' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'nullable',
                'string',
                'max:255',
            ],
            'api_port' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'router_type' => [
                'required',
                Rule::in(['MikroTik']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $data = [
            'name' => $validated['name'],
            'ip_address' => $validated['ip_address'],
            'username' => $validated['username'],
            'api_port' => $validated['api_port'] ?? 8728,
            'router_type' => $validated['router_type'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        /*
         * Never overwrite the existing encrypted
         * router password with an empty value.
         */
        if ($request->filled('password')) {
            $data['password'] = $validated['password'];
        }

        try {
            $router->update($data);

            Log::info('Router updated', [
                'router_id' => $router->id,
                'admin_id' => $router->admin_id,
                'name' => $router->name,
                'ip_address' => $router->ip_address,
                'api_port' => $router->api_port,
                'is_active' => $router->is_active,
            ]);

            return redirect()
                ->route('admin.routers.index')
                ->with(
                    'success',
                    'Router updated successfully.'
                );
        } catch (\Throwable $e) {
            Log::error('Router update failed', [
                'router_id' => $router->id,
                'admin_id' => $router->admin_id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput(
                    $request->except('password')
                )
                ->with(
                    'error',
                    'Router could not be updated. Please try again.'
                );
        }
    }

    /**
     * Delete an unused router.
     *
     * Routers with customer credentials or session
     * history are retained so historical and tenant
     * relationships are not destroyed accidentally.
     */
    public function destroy(Router $router)
    {
        $this->ensureRouterBelongsToAdmin($router);

        $hasHotspotUsers = HotspotUser::where(
            'router_id',
            $router->id
        )->exists();

        $hasSessionHistory = HotspotSession::where(
            'router_id',
            $router->id
        )->exists();

        if ($hasHotspotUsers || $hasSessionHistory) {
            return back()->with(
                'error',
                'This router has customer or session records and cannot be deleted. Deactivate it instead.'
            );
        }

        $routerId = $router->id;
        $routerName = $router->name;
        $adminId = $router->admin_id;

        try {
            $router->delete();

            Log::info('Router deleted', [
                'router_id' => $routerId,
                'admin_id' => $adminId,
                'name' => $routerName,
            ]);

            return redirect()
                ->route('admin.routers.index')
                ->with(
                    'success',
                    'Router deleted successfully.'
                );
        } catch (\Throwable $e) {
            Log::error('Router deletion failed', [
                'router_id' => $routerId,
                'admin_id' => $adminId,
                'error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Router could not be deleted.'
            );
        }
    }

    /**
     * Test the MikroTik API connection.
     */
    public function testConnection(
        Router $router,
        MikrotikService $mikrotik
    ) {
        $this->ensureRouterBelongsToAdmin($router);

        $result = $mikrotik->testConnection($router);

        return redirect()
            ->route('admin.routers.index')
            ->with(
                $result['success'] ? 'success' : 'error',
                $result['message']
            );
    }

    /**
     * Generate the basic MikroTik setup script.
     */
    public function setupScript(Router $router)
    {
        $this->ensureRouterBelongsToAdmin($router);

        /*
         * Router names are restricted by validation
         * to characters that cannot break this script.
         */
        $script = '
# ============================================
# WiFi Billing System MikroTik Setup
# ============================================

# Enable MikroTik API
/ip service enable api

# Configure API Port
/ip service set api port='.$router->api_port.'

# Set Router Identity
/system identity set name="'.$router->name.'"

# ============================================
# Hotspot Profile
# ============================================

# Add the default WiFi Billing Hotspot profile
# if it does not already exist.
/ip hotspot user profile add name=wifi-billing-hotspot-profile

# ============================================
# PPPoE Profile
# ============================================

# Add the default PPPoE profile
# if it does not already exist.
/ppp profile add name=default-pppoe only-one=yes

# ============================================
# SECURITY NOTICE
# ============================================

# Never expose the MikroTik API port publicly.
#
# Production API access must be restricted to:
#
# 1. The WiFi Billing server public IP,
#    OR
# 2. A secure VPN/private network.
#
# The final firewall rule depends on the
# production server or VPN address.

# ============================================
# Backup Router Configuration
# ============================================

/system backup save name=wifi-billing-backup

# ============================================
# Setup Complete
# ============================================
';

        return view(
            'admin.routers.setup-script',
            compact('router', 'script')
        );
    }

    /**
     * Return the authenticated ISP Admin.
     */
    private function currentAdmin()
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->role === 'admin',
            403,
            'Only ISP Admins can manage routers.'
        );

        return $user;
    }

    /**
     * Protect every single-router operation
     * against cross-tenant access.
     */
    private function ensureRouterBelongsToAdmin(
        Router $router
    ): void {
        $admin = $this->currentAdmin();

        abort_unless(
            (int) $router->admin_id === (int) $admin->id,
            403
        );
    }
}