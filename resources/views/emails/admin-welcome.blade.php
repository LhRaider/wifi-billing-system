 <x-mail::message>
<div style="text-align: center; margin-bottom: 28px;">
    <div style="
        display: inline-block;
        background: #111827;
        color: #ffffff;
        padding: 12px 22px;
        border-radius: 8px;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 0.3px;
    ">
        {{ config('app.name') }}
    </div>
</div>

# Welcome, {{ $user->name }}!

Thank you for joining **{{ config('app.name') }}**. Your ISP Admin account has been created successfully and is ready to use.

<x-mail::panel>
**Your Account**

**Email:** {{ $user->email }}

**Account Type:** ISP Admin

**Service Status:** Free Trial

**Trial Period:** 15 Days
</x-mail::panel>

During your trial, you can sign in to your dashboard and begin configuring your internet service operations, packages, routers, customers, and other available platform features.

<x-mail::button :url="$loginUrl">
Sign In to Your Dashboard
</x-mail::button>

## Need Help?

If you experience any issues while using the platform, please contact your service provider using any of the options below.

**Support Email:**

support@example.com

**WhatsApp Support:**

+254700000000

### Security Reminder

For your security, **{{ config('app.name') }}** will never include your account password in an email. If you forget your password, use the **Forgot Password** option on the sign-in page to securely create a new one.

We are glad to have you on board and wish you success managing your internet service operations.

Thanks,
**{{ config('app.name') }} Team**

<small>
This is an automated account message. You received it because an ISP Admin account was registered using this email address.
</small>
</x-mail::message>