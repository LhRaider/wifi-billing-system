<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /*
        Customer → show packages belonging to their admin
        Admin → show their own packages
        */

        $adminId = $user->role === 'customer'
            ? $user->admin_id
            : $user->id;

        $packages = Package::where('admin_id', $adminId)
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('packages.index', compact('packages'));
    }
}