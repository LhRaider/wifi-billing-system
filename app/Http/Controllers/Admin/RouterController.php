<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::where('admin_id', auth()->id())
            ->latest()
            ->get();

        return view('admin.routers.index', compact('routers'));
    }

    public function create()
    {
        return view('admin.routers.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'ip_address' => 'required|string|max:255',
        'username' => 'required|string|max:255',
        'password' => 'nullable|string|max:255',
        'api_port' => 'nullable|integer',
    ]);

    try {

        $router = Router::create([
            'admin_id' => auth()->id(),
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'username' => $request->username,
            'password' => $request->password,
            'api_port' => $request->api_port ?? 8728,
            'is_active' => true,
        ]);

        \Log::info('ROUTER CREATED', $router->toArray());

        return redirect()
            ->route('admin.routers.index')
            ->with('success', 'MikroTik added successfully.');

    } catch (\Exception $e) {

        \Log::error('ROUTER CREATE FAILED: '.$e->getMessage());

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

    public function edit(Router $router)
    {
        if ($router->admin_id !== auth()->id()) {
            abort(403);
        }

        return view('admin.routers.edit', compact('router'));
    }

    public function update(Request $request, Router $router)
{
    if ($router->admin_id !== auth()->id()) {
        abort(403);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'ip_address' => 'required|string|max:255',
        'username' => 'required|string|max:255',
        'password' => 'nullable|string|max:255',
        'api_port' => 'nullable|integer',
        'router_type' => 'required|string|max:255',
        'notes' => 'nullable|string',
    ]);

    $router->update([
        'name' => $request->name,
        'ip_address' => $request->ip_address,
        'username' => $request->username,
        'password' => $request->password,
        'api_port' => $request->api_port ?? 8728,
        'router_type' => $request->router_type,
        'notes' => $request->notes,
        'is_active' => $request->has('is_active'),
    ]);

    return redirect()
        ->route('admin.routers.index')
        ->with('success', 'Router updated successfully.');
}

    public function destroy(Router $router)
    {
        if ($router->admin_id !== auth()->id()) {
            abort(403);
        }

        $router->delete();

        return redirect()
            ->route('admin.routers.index')
            ->with('success', 'Router deleted successfully.');
    }

    public function testConnection(Router $router, MikrotikService $mikrotik)
    {
        if ($router->admin_id !== auth()->id()) {
            abort(403);
        }

        $result = $mikrotik->testConnection($router);

        return redirect()
            ->route('admin.routers.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function setupScript(Router $router)
{
    if ($router->admin_id !== auth()->id()) {
        abort(403);
    }

    $script = '
# Enable API
/ip service enable api

# API Port
/ip service set api port='.$router->api_port.'

# Router Name
/system identity set name="'.$router->name.'"

# Hotspot Profiles
/ip hotspot user profile add name=wifi-billing-hotspot-profile

# PPPoE Profiles
/ppp profile add name=default-pppoe only-one=yes

# Allow API Access
/ip firewall filter add chain=input protocol=tcp dst-port='.$router->api_port.' action=accept comment="WiFi Billing API"

# Save Configuration
/system backup save name=wifi-billing-backup
';

    return view('admin.routers.setup-script', compact('router', 'script'));
}
}