<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotspotSession;
use Illuminate\Http\Request;

class HotspotSessionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        if (auth()->user()->role === 'super_admin') {
            $sessions = HotspotSession::whereRaw('1 = 0')->paginate(10);

            return view('admin.hotspot-sessions.index', compact('sessions', 'search'));
        }

        $sessions = HotspotSession::with('user')
            ->whereHas('user', function ($query) {
                $query->where('admin_id', auth()->id());
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ip_address', 'like', "%{$search}%")
                        ->orWhere('mac_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.hotspot-sessions.index', compact('sessions', 'search'));
    }
}