<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    /**
     * Display active internet packages for the authenticated customer.
     */
    public function index()
    {
        $customer = $this->currentCustomer();

        if (!$customer->admin_id) {
            abort(403);
        }

        $packages = Package::query()
            ->where('admin_id', $customer->admin_id)
            ->where('is_active', true)
            ->orderBy('price')
            ->orderBy('id')
            ->get();

        return view('packages.index', compact('packages'));
    }

    /**
     * Return the authenticated customer only.
     */
    private function currentCustomer(): User
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        if ($user->role !== 'customer') {
            abort(403);
        }

        return $user;
    }
}