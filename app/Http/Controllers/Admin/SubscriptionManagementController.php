<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        if (auth()->user()->role === 'super_admin') {

            $subscriptions = Subscription::whereRaw('1 = 0')
                ->paginate(10);

            return view('admin.subscriptions.index', compact(
                'subscriptions',
                'search'
            ));
        }

        $subscriptions = Subscription::with(['user', 'package'])
            ->whereHas('user', function ($query) {
                $query->where('admin_id', auth()->id());
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    $q->where('status', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('package', function ($p) use ($search) {
                            $p->where('name', 'like', "%{$search}%");
                        });

                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.subscriptions.index', compact(
            'subscriptions',
            'search'
        ));
    }

    public function activate(Subscription $subscription)
    {
        if (auth()->user()->role === 'super_admin') {
            abort(403);
        }

        if ($subscription->user->admin_id !== auth()->id()) {
            abort(403);
        }

        $subscription->update([
            'status' => 'active',
        ]);

        return back()->with(
            'success',
            'Subscription activated successfully.'
        );
    }

    public function expire(Subscription $subscription)
    {
        if (auth()->user()->role === 'super_admin') {
            abort(403);
        }

        if ($subscription->user->admin_id !== auth()->id()) {
            abort(403);
        }

        $subscription->update([
            'status' => 'expired',
        ]);

        return back()->with(
            'success',
            'Subscription expired successfully.'
        );
    }
}