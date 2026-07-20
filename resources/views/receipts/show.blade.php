<x-app-layout>

<x-slot name="header">

<h2 style="
font-size:30px;
font-weight:700;
color:#1e293b;
display:flex;
align-items:center;
gap:12px;
">

<i class="fas fa-file-invoice-dollar" style="color:#2563eb;"></i>

Payment Receipt

</h2>

</x-slot>

@php

$admin = $payment->package->admin;

@endphp

<div style="
background:#eef3fb;
min-height:100vh;
padding:40px;
">

<div style="
max-width:1000px;
margin:auto;
background:white;
border-radius:24px;
overflow:hidden;
box-shadow:0 20px 45px rgba(15,23,42,.08);
">

<div style="
background:linear-gradient(135deg,#2563eb,#4f46e5);
padding:35px;
color:white;
display:flex;
justify-content:space-between;
align-items:flex-start;
flex-wrap:wrap;
gap:20px;
">

<div>

<h1 style="
font-size:36px;
font-weight:900;
margin:0;
">

<i class="fas fa-receipt"></i>

PAYMENT RECEIPT

</h1>

<p style="
margin-top:10px;
font-size:15px;
opacity:.95;
">

Receipt Number

</p>

<h2 style="
margin-top:5px;
font-size:24px;
font-weight:800;
">

REC-{{ str_pad($payment->id,6,'0',STR_PAD_LEFT) }}

</h2>

</div>

<div style="text-align:right;">

<h2 style="
font-size:28px;
font-weight:900;
margin:0;
">

{{ $admin->business_name ?? 'WiFi Billing' }}

</h2>

@if($admin->network_name)

<p style="
margin-top:8px;
font-size:16px;
">

<i class="fas fa-wifi"></i>

{{ $admin->network_name }}

</p>

@endif

@if($admin->business_phone)

<p style="
margin-top:8px;
font-size:15px;
">

<i class="fas fa-phone"></i>

{{ $admin->business_phone }}

</p>

@endif

@if($admin->business_address)

<p style="
margin-top:8px;
font-size:15px;
">

<i class="fas fa-location-dot"></i>

{{ $admin->business_address }}

</p>

@endif

<p style="
margin-top:10px;
font-size:15px;
">

<i class="fas fa-calendar-days"></i>

{{ $payment->created_at->format('d M Y • H:i') }}

</p>

</div>

</div>

<div style="padding:35px;">
    <div style="
display:grid;
grid-template-columns:1fr 1fr;
gap:40px;
margin-bottom:35px;
">

<div style="
background:#f8fbff;
border-radius:18px;
padding:25px;
border:1px solid #dbeafe;
">

<h2 style="
font-size:22px;
font-weight:800;
color:#2563eb;
margin-bottom:20px;
">

<i class="fas fa-user"></i>

Customer Information

</h2>

<p style="margin-bottom:12px;">
<strong>Full Name</strong><br>
{{ $payment->user->name }}
</p>

<p style="margin-bottom:12px;">
<strong>Email Address</strong><br>
{{ $payment->user->email }}
</p>

<p style="margin-bottom:12px;">
<strong>Phone Number</strong><br>
{{ $payment->phone_number }}
</p>

</div>

<div style="
background:#f8fbff;
border-radius:18px;
padding:25px;
border:1px solid #dbeafe;
">

<h2 style="
font-size:22px;
font-weight:800;
color:#2563eb;
margin-bottom:20px;
">

<i class="fas fa-money-check-dollar"></i>

Payment Details

</h2>

<p style="margin-bottom:12px;">
<strong>Payment Status</strong><br>

<span style="
display:inline-block;
background:#dcfce7;
color:#166534;
padding:8px 14px;
border-radius:30px;
font-weight:700;
">

<i class="fas fa-circle-check"></i>

{{ ucfirst($payment->status) }}

</span>

</p>

<p style="margin-bottom:12px;">
<strong>M-Pesa Receipt</strong><br>

{{ $payment->mpesa_receipt ?? 'Awaiting Confirmation' }}

</p>

<p style="margin-bottom:12px;">
<strong>Purchase Date</strong><br>

{{ $payment->created_at->format('d M Y H:i') }}

</p>

@if($payment->paid_at)

<p style="margin-bottom:12px;">
<strong>Paid On</strong><br>

{{ $payment->paid_at->format('d M Y H:i') }}

</p>

@endif

</div>

</div>

<table style="
width:100%;
border-collapse:collapse;
margin-top:10px;
border-radius:16px;
overflow:hidden;
">

<thead>

<tr style="
background:#2563eb;
color:white;
">

<th style="padding:18px;text-align:left;">
Package
</th>

<th style="padding:18px;text-align:center;">
Duration
</th>

<th style="padding:18px;text-align:center;">
Speed
</th>

<th style="padding:18px;text-align:right;">
Amount
</th>

</tr>

</thead>

<tbody>

<tr style="
background:white;
border-bottom:1px solid #e5e7eb;
">

<td style="padding:20px;">

<strong>

{{ $payment->package->name }}

</strong>

</td>

<td style="
padding:20px;
text-align:center;
">

{{ $payment->package->duration_hours }}

Hours

</td>

<td style="
padding:20px;
text-align:center;
">

{{ $payment->package->speed ?? 'Standard' }}

</td>

<td style="
padding:20px;
text-align:right;
font-weight:800;
font-size:18px;
color:#16a34a;
">

KES {{ number_format($payment->amount,2) }}

</td>

</tr>

</tbody>

</table>
<div style="
margin-top:40px;
display:flex;
justify-content:flex-end;
">

<div style="
width:340px;
background:#f8fbff;
border:1px solid #dbeafe;
border-radius:18px;
padding:25px;
">

<div style="
display:flex;
justify-content:space-between;
margin-bottom:15px;
font-size:17px;
">

<span>Subtotal</span>

<strong>

KES {{ number_format($payment->amount,2) }}

</strong>

</div>

<div style="
display:flex;
justify-content:space-between;
margin-bottom:15px;
font-size:17px;
">

<span>Tax</span>

<strong>

KES 0.00

</strong>

</div>

<hr style="
margin:18px 0;
">

<div style="
display:flex;
justify-content:space-between;
font-size:28px;
font-weight:900;
color:#16a34a;
">

<span>Total</span>

<span>

KES {{ number_format($payment->amount,2) }}

</span>

</div>

</div>

</div>

<div style="
margin-top:45px;
background:#f8fbff;
border:1px solid #dbeafe;
border-radius:18px;
padding:30px;
">

<h2 style="
font-size:22px;
font-weight:800;
color:#2563eb;
margin-bottom:20px;
">

Business Payment Information

</h2>

@if($admin->payment_type=='till')

<div style="
display:flex;
justify-content:space-between;
align-items:center;
padding:18px;
background:white;
border-radius:14px;
margin-bottom:15px;
">

<div>

<strong>Till Number</strong>

<br>

<span style="
font-size:28px;
font-weight:900;
color:#2563eb;
">

{{ $admin->business_till }}

</span>

</div>

<i class="fas fa-store fa-2x" style="color:#2563eb;"></i>

</div>

@else

<div style="
display:flex;
justify-content:space-between;
align-items:center;
padding:18px;
background:white;
border-radius:14px;
margin-bottom:15px;
">

<div>

<strong>Paybill Number</strong>

<br>

<span style="
font-size:28px;
font-weight:900;
color:#2563eb;
">

{{ $admin->business_paybill }}

</span>

</div>

<i class="fas fa-building-columns fa-2x" style="color:#2563eb;"></i>

</div>

<div style="
display:flex;
justify-content:space-between;
align-items:center;
padding:18px;
background:white;
border-radius:14px;
">

<div>

<strong>Account Number</strong>

<br>

<span style="
font-size:24px;
font-weight:900;
color:#059669;
">

{{ $admin->business_account }}

</span>

</div>

<i class="fas fa-hashtag fa-2x" style="color:#059669;"></i>

</div>

@endif

</div>

<hr style="
margin:45px 0;
">

<div style="
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:15px;
">

<div>

<h3 style="
margin:0;
font-size:20px;
font-weight:800;
">

Thank you for choosing

{{ $admin->business_name ?? 'our hotspot service' }}

</h3>

<p style="
margin-top:10px;
color:#64748b;
">

This receipt confirms successful payment for your selected internet package.

</p>

</div>

<div style="
display:flex;
gap:15px;
">

<button
onclick="window.print()"
style="
background:#16a34a;
color:white;
border:none;
padding:15px 30px;
border-radius:12px;
font-weight:700;
cursor:pointer;
">

<i class="fas fa-print"></i>

Print

</button>

<button
onclick="window.print()"
style="
background:#2563eb;
color:white;
border:none;
padding:15px 30px;
border-radius:12px;
font-weight:700;
cursor:pointer;
">

<i class="fas fa-download"></i>

Download PDF

</button>

<a
href="{{ route('receipts.index') }}"
style="
background:#475569;
color:white;
padding:15px 30px;
border-radius:12px;
font-weight:700;
text-decoration:none;
">

<i class="fas fa-arrow-left"></i>

Back

</a>

</div>

</div>

</div>

<div style="
text-align:center;
margin-top:25px;
color:#64748b;
font-size:15px;
">

This is a computer-generated receipt and does not require a signature.

</div>

</div>

</x-app-layout>