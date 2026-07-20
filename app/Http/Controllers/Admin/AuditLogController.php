<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $logs = AuditLog::with('user');

        if (auth()->user()->role === 'super_admin') {
            $logs->whereIn('module', [
                'Admin Users',
                'Admin Accounts',
                'License',
                'License Payments',
                'Admin Subscription',
                'Software License',
            ]);
        } else {
            $logs->where('user_id', auth()->id())
                ->whereNotIn('module', [
                    'Admin Users',
                    'Admin Accounts',
                    'License',
                    'License Payments',
                    'Admin Subscription',
                    'Software License',
                ]);
        }

        $logs = $logs->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15);

        return view('admin.audit-logs.index', compact('logs', 'search'));
    }
}