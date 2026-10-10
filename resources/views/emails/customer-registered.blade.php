<x-mail::message>
# New Customer Registration

Hello {{ $admin->name }},

A new customer has registered on your internet network.

<x-mail::panel>
**Customer Name:** {{ $customer->name }}

**Email Address:** {{ $customer->email }}

**Network:** {{ $admin->network_name
    ?: $admin->business_name
    ?: 'Your Internet Network' }}

**Registration Date:** {{ $customer->created_at->format('d M Y, h:i A') }}
</x-mail::panel>

The customer account has been assigned to your ISP account and can now be managed from your customer dashboard.

<x-mail::button :url="$customersUrl">
View Customers
</x-mail::button>

For security, customer passwords are never included in registration notifications.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>