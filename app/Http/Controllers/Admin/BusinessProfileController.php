<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusinessProfileController extends Controller
{
    public function edit()
    {
        $admin = auth()->user();

        return view(
            'admin.business-profile.edit',
            compact('admin')
        );
    }

    public function update(Request $request)
    {
        $admin = auth()->user();

        $validated = $request->validate([
            'business_name' => [
                'required',
                'string',
                'max:255',
            ],

            'network_name' => [
                'required',
                'string',
                'max:255',
            ],

            'business_phone' => [
                'required',
                'string',
                'max:30',
            ],

            'business_address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'payment_type' => [
                'required',
                'in:till,paybill,phone',
            ],

            'business_till' => [
                'nullable',
                'string',
                'max:30',
            ],

            'business_paybill' => [
                'nullable',
                'string',
                'max:30',
            ],

            'business_account' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Payment Method Validation
        |--------------------------------------------------------------------------
        |
        | Only require the payment details that belong to the payment method
        | selected by this ISP Admin.
        |
        */

        if (
            $validated['payment_type'] === 'till' &&
            empty($validated['business_till'])
        ) {
            return back()
                ->withErrors([
                    'business_till' =>
                        'A Till Number is required when Till Number is selected.',
                ])
                ->withInput();
        }

        if (
            $validated['payment_type'] === 'paybill' &&
            empty($validated['business_paybill'])
        ) {
            return back()
                ->withErrors([
                    'business_paybill' =>
                        'A PayBill Number is required when PayBill is selected.',
                ])
                ->withInput();
        }

        if (
            $validated['payment_type'] === 'paybill' &&
            empty($validated['business_account'])
        ) {
            return back()
                ->withErrors([
                    'business_account' =>
                        'An Account Number is required when PayBill is selected.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Update This ISP Admin Only
        |--------------------------------------------------------------------------
        |
        | The current authenticated ISP Admin is the only account updated.
        | No Admin ID is accepted from the request.
        |
        */

        $admin->update([
            'business_name' => $validated['business_name'],
            'network_name' => $validated['network_name'],
            'business_phone' => $validated['business_phone'],
            'business_address' =>
                $validated['business_address'] ?? null,

            'payment_type' => $validated['payment_type'],

            'business_till' =>
                $validated['business_till'] ?? null,

            'business_paybill' =>
                $validated['business_paybill'] ?? null,

            'business_account' =>
                $validated['business_account'] ?? null,
        ]);

        return back()->with(
            'success',
            'Business profile updated successfully.'
        );
    }
}