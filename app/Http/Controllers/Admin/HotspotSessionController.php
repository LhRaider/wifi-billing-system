<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotspotSession;
use Illuminate\Http\Request;

class HotspotSessionController extends Controller
{
    public function index(Request $request)
    {
        $admin = auth()->user();

        abort_unless(
            $admin && $admin->role === 'admin',
            403,
            'Only ISP Admins can access hotspot sessions.'
        );

        $search = trim((string) $request->input('search'));

        $sessions = HotspotSession::with([
                'user',
                'router',
            ])
            ->whereHas('user', function ($query) use ($admin) {
                $query->where('role', 'customer')
                    ->where('admin_id', $admin->id);
            })
            ->where(function ($query) use ($admin) {
                $query->whereNull('router_id')
                    ->orWhereHas('router', function ($routerQuery) use ($admin) {
                        $routerQuery->where('admin_id', $admin->id);
                    });
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('ip_address', 'like', "%{$search}%")
                        ->orWhere('mac_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('router', function ($routerQuery) use ($search) {
                            $routerQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('login_time')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.hotspot-sessions.index',
            compact('sessions', 'search')
        );
    }
}