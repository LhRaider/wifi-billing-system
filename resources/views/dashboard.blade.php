<x-app-layout>

<x-slot name="header">

<div style="
display:flex;
justify-content:space-between;
align-items:center;
">

<h2 style="
font-size:30px;
font-weight:700;
color:#1e293b;
display:flex;
align-items:center;
gap:12px;
">

<i class="fas fa-house-user" style="color:#2563eb;"></i>

Customer Dashboard

</h2>

</div>

</x-slot>

<div style="
background:#edf2fb;
min-height:100vh;
padding:45px 25px;
">

<div style="
max-width:1250px;
margin:auto;
">

@if(!$activeSubscription)

<div style="
background:#fee2e2;
color:#991b1b;
padding:18px;
border-radius:16px;
margin-bottom:25px;
font-weight:700;
">

<i class="fas fa-circle-exclamation"></i>

Your package has expired.
Purchase another package to continue browsing.

</div>

@endif

<div style="
display:grid;
grid-template-columns:300px 1fr 330px;
gap:25px;
align-items:start;
">
<!-- LEFT COLUMN -->

<div style="
background:white;
border-radius:28px;
padding:30px;
box-shadow:0 15px 35px rgba(15,23,42,.08);
">

<h2 style="
font-size:18px;
font-weight:700;
color:#0f172a;
margin-bottom:6px;
">

Current Internet Package

</h2>

<p style="
color:#64748b;
font-size:15px;
margin-bottom:25px;
">

Your current subscription information.

</p>

@if($activeSubscription)

<div style="
display:inline-flex;
align-items:center;
gap:8px;
background:#dcfce7;
padding:10px 18px;
border-radius:30px;
color:#15803d;
font-weight:700;
margin-bottom:28px;
">

<i class="fas fa-circle-check"></i>

ACTIVE

</div>

@else

<div style="
display:inline-flex;
align-items:center;
gap:8px;
background:#fee2e2;
padding:10px 18px;
border-radius:30px;
color:#dc2626;
font-weight:700;
margin-bottom:28px;
">

<i class="fas fa-circle-xmark"></i>

EXPIRED

</div>

@endif


<div style="
background:#f8fafc;
border-radius:18px;
padding:18px;
margin-bottom:18px;
">

<div style="
font-size:14px;
color:#64748b;
margin-bottom:8px;
">

Package

</div>

<div style="
font-size:18px;
font-weight:700;
color:#2563eb;
">

{{ $hotspotUser->profile ?? 'No Active Package' }}

</div>

</div>


<div style="
background:#f8fafc;
border-radius:18px;
padding:18px;
margin-bottom:18px;
">

<div style="
font-size:14px;
color:#64748b;
margin-bottom:8px;
">

Remaining Time

</div>

<div style="
font-size:18px;
font-weight:700;
color:#16a34a;
">

{{ $remainingTime }}

</div>

</div>


<div style="
background:#f8fafc;
border-radius:18px;
padding:18px;
">

<div style="
font-size:14px;
color:#64748b;
margin-bottom:8px;
">

Expiry Date

</div>

<div style="
font-size:18px;
font-weight:700;
color:#0f172a;
">

{{ $activeSubscription ? \Carbon\Carbon::parse($activeSubscription->end_time)->format('d M Y H:i') : 'Expired' }}

</div>

</div>

</div>
<!-- CENTER COLUMN -->

<div style="
display:flex;
flex-direction:column;
gap:25px;
">

<!-- HOTSPOT CARD -->

<div style="
background:white;
border-radius:28px;
padding:30px;
box-shadow:0 15px 35px rgba(15,23,42,.08);
">

<h2 style="
font-size:18px;
font-weight:700;
color:#0f172a;
display:flex;
align-items:center;
gap:10px;
margin-bottom:25px;
">

<i class="fas fa-key" style="color:#2563eb;"></i>

Hotspot Credentials

</h2>

<div style="
display:grid;
grid-template-columns:1fr 1fr;
gap:18px;
">

<div style="
background:#f8fafc;
padding:18px;
border-radius:16px;
">

<div style="
font-size:14px;
color:#64748b;
margin-bottom:8px;
">

Username

</div>

<div style="
font-size:18px;
font-weight:700;
color:#0f172a;
">

{{ $hotspotUser->username ?? '---' }}

</div>

</div>

<div style="
background:#f8fafc;
padding:18px;
border-radius:16px;
">

<div style="
font-size:14px;
color:#64748b;
margin-bottom:8px;
">

Password

</div>

<div style="
font-size:18px;
font-weight:700;
color:#0f172a;
">

{{ $hotspotUser->password ?? '---' }}

</div>

</div>

</div>

<div style="
margin-top:18px;
background:#ecfdf5;
padding:16px 18px;
border-radius:16px;
display:flex;
justify-content:space-between;
align-items:center;
">

<div style="
font-weight:700;
color:#0f172a;
">

Status

</div>

<div style="
font-weight:700;
color:#16a34a;
display:flex;
align-items:center;
gap:8px;
">

<i class="fas fa-circle-check"></i>

{{ $hotspotUser ? strtoupper($hotspotUser->status) : 'INACTIVE' }}

</div>

</div>

</div>

<!-- QUICK ACTIONS -->

<div style="
background:white;
border-radius:28px;
padding:30px;
box-shadow:0 15px 35px rgba(15,23,42,.08);
">

<h2 style="
font-size:18px;
font-weight:700;
color:#0f172a;
margin-bottom:25px;
">

Quick Actions

</h2>

<div style="
display:grid;
grid-template-columns:1fr 1fr;
gap:15px;
">

<a href="{{ route('packages.index') }}"
style="
background:#2563eb;
color:white;
padding:18px;
border-radius:16px;
text-decoration:none;
font-weight:700;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
">

<i class="fas fa-cart-shopping"></i>

Buy New Package

</a>

<a href="{{ route('receipts.index') }}"
style="
background:#f8fafc;
color:#0f172a;
padding:18px;
border-radius:16px;
text-decoration:none;
font-weight:700;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
">

<i class="fas fa-receipt" style="color:#2563eb;"></i>

Receipts

</a>

<a href="{{ route('subscriptions.index') }}"
style="
background:#f8fafc;
color:#0f172a;
padding:18px;
border-radius:16px;
text-decoration:none;
font-weight:700;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
">

<i class="fas fa-file-lines" style="color:#16a34a;"></i>

Subscriptions

</a>

<a href="{{ route('payments.index') }}"
style="
background:#f8fafc;
color:#0f172a;
padding:18px;
border-radius:16px;
text-decoration:none;
font-weight:700;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
">

<i class="fas fa-credit-card" style="color:#7c3aed;"></i>

Payments

</a>

</div>

</div>

</div>
<!-- RIGHT COLUMN -->

<div style="
display:flex;
flex-direction:column;
gap:25px;
">

<!-- ACCOUNT SUMMARY -->

<div style="
background:white;
border-radius:28px;
padding:30px;
box-shadow:0 15px 35px rgba(15,23,42,.08);
">

<div style="
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
">

<div>

<div style="
font-size:15px;
color:#64748b;
">

Total Spent

</div>

<div style="
font-size:44px;
font-weight:800;
color:#16a34a;
margin-top:8px;
">

KES {{ number_format($totalSpent,2) }}

</div>

</div>

<div style="
width:74px;
height:74px;
border-radius:22px;
background:#dcfce7;
display:flex;
align-items:center;
justify-content:center;
font-size:30px;
color:#16a34a;
">

<i class="fas fa-wallet"></i>

</div>

</div>

<hr style="
border:none;
border-top:1px solid #e2e8f0;
margin:20px 0;
">

<div style="
display:flex;
justify-content:space-between;
align-items:center;
">

<div>

<div style="
font-size:15px;
color:#64748b;
">

Packages Purchased

</div>

<div style="
font-size:42px;
font-weight:800;
color:#2563eb;
margin-top:8px;
">

{{ $totalPurchases }}

</div>

</div>

<div style="
width:74px;
height:74px;
border-radius:22px;
background:#dbeafe;
display:flex;
align-items:center;
justify-content:center;
font-size:30px;
color:#2563eb;
">

<i class="fas fa-box-open"></i>

</div>

</div>

</div>


<!-- HELP CARD -->

<div style="
background:white;
border-radius:28px;
padding:30px;
box-shadow:0 15px 35px rgba(15,23,42,.08);
text-align:center;
">

<h2 style="
font-size:18px;
font-weight:700;
color:#0f172a;
margin-bottom:20px;
">

Need Help?

</h2>

<p style="
color:#64748b;
line-height:1.8;
margin-bottom:28px;
">

If you experience connection issues or require assistance with your account,
please contact your Internet Service Provider.

</p>

<div style="
display:flex;
flex-direction:column;
gap:15px;
">

@if(auth()->user()->admin)

<div style="
background:#f8fafc;
padding:16px;
border-radius:16px;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
font-weight:700;
">

<i class="fas fa-phone" style="color:#2563eb;"></i>

{{ auth()->user()->admin->business_phone ?? 'Not Available' }}

</div>

<div style="
background:#f8fafc;
padding:16px;
border-radius:16px;
display:flex;
align-items:center;
justify-content:center;
gap:10px;
font-weight:700;
">

<i class="fas fa-wifi" style="color:#16a34a;"></i>

{{ auth()->user()->admin->network_name ?? auth()->user()->admin->business_name }}

</div>

@endif

</div>

</div>

</div>

</div>

</div>

</x-app-layout>