<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        $admin = auth()->user();

        return view('admin.business-profile.edit', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = auth()->user();

        $request->validate([
            'business_name'=>'required|max:255',
            'network_name'=>'required|max:255',
            'business_phone'=>'required|max:30',
            'business_address'=>'nullable|max:255',

            'payment_type'=>'required|in:till,paybill,phone',

            'business_till'=>'nullable|max:30',
            'business_paybill'=>'nullable|max:30',
            'business_account'=>'nullable|max:50',
        ]);

        $admin->update([

            'business_name'=>$request->business_name,

            'network_name'=>$request->network_name,

            'business_phone'=>$request->business_phone,

            'business_address'=>$request->business_address,

            'payment_type'=>$request->payment_type,

            'business_till'=>$request->business_till,

            'business_paybill'=>$request->business_paybill,

            'business_account'=>$request->business_account,

        ]);

        return back()->with('success','Business Profile Updated Successfully.');
    }
}