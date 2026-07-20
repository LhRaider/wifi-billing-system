<x-app-layout>

<x-slot name="header">

<h2 style="
font-size:28px;
font-weight:700;
color:#1e293b;
">

Complete Payment

</h2>

</x-slot>

<div style="
background:#eef3fb;
min-height:100vh;
padding:40px;
">

<div style="
max-width:900px;
margin:auto;
display:grid;
grid-template-columns:1fr 1fr;
gap:35px;
">

<div style="
background:white;
border-radius:20px;
padding:35px;
box-shadow:0 15px 35px rgba(0,0,0,.08);
">

<h2 style="
font-size:24px;
font-weight:bold;
margin-bottom:25px;
">

Package Details

</h2>

<p><strong>Package:</strong> {{ $package->name }}</p>

<p style="margin-top:15px;">
<strong>Price:</strong>
KES {{ number_format($package->price,2) }}
</p>

<p style="margin-top:15px;">
<strong>Duration:</strong>
{{ $package->duration_hours }} Hours
</p>

@if($package->speed)

<p style="margin-top:15px;">
<strong>Speed:</strong>
{{ $package->speed }}
</p>

@endif

</div>

<div style="
background:white;
border-radius:20px;
padding:35px;
box-shadow:0 15px 35px rgba(0,0,0,.08);
">

<h2 style="
font-size:24px;
font-weight:bold;
margin-bottom:25px;
">

Payment Information

</h2>

<p>
<strong>Business:</strong><br>
{{ $admin->business_name ?? 'Not Set' }}
</p>

@if($admin->network_name)

<p style="margin-top:15px;">
<strong>Network:</strong><br>
{{ $admin->network_name }}
</p>

@endif

@if($admin->payment_type=='till')

<p style="margin-top:20px;">
<strong>Till Number</strong><br>

<span style="
font-size:22px;
font-weight:bold;
color:#2563eb;
">

{{ $admin->business_till }}

</span>

</p>

@else

<p style="margin-top:20px;">
<strong>Paybill Number</strong><br>

<span style="
font-size:22px;
font-weight:bold;
color:#2563eb;
">

{{ $admin->business_paybill }}

</span>

</p>

<p style="margin-top:20px;">
<strong>Account Number</strong><br>

<span style="
font-size:20px;
font-weight:bold;
color:#059669;
">

{{ $admin->business_account }}

</span>

</p>

@endif

@if($admin->business_phone)

<p style="margin-top:20px;">
<strong>Business Phone</strong><br>

{{ $admin->business_phone }}

</p>

@endif

<hr style="margin:30px 0;">

<form
method="POST"
action="{{ route('mpesa.initiate',$package) }}">

@csrf

<label style="
font-weight:bold;
display:block;
margin-bottom:10px;
">

Safaricom Number

</label>

<input
type="text"
name="phone_number"
placeholder="2547XXXXXXXX"
required
style="
width:100%;
padding:15px;
border:2px solid #dbeafe;
border-radius:12px;
margin-bottom:25px;
">

<button
type="submit"
style="
width:100%;
padding:16px;
background:#16a34a;
color:white;
border:none;
border-radius:12px;
font-size:16px;
font-weight:bold;
cursor:pointer;
">

Pay KES {{ number_format($package->price,2) }}

</button>

</form>

</div>

</div>

</div>

</x-app-layout>