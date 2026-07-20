<<x-app-layout>

<x-slot name="header">

<h2 style="
font-size:32px;
font-weight:700;
color:#1e293b;
display:flex;
align-items:center;
gap:12px;
">

<i class="fas fa-user-circle" style="color:#2563eb;"></i>

My Profile

</h2>

</x-slot>

<div style="
background:#eef3fb;
min-height:100vh;
padding:40px;
">

@if(session('success'))

<div style="
background:#dcfce7;
color:#166534;
padding:18px;
border-radius:14px;
margin-bottom:25px;
font-weight:600;
">

{{ session('success') }}

</div>

@endif

@if ($errors->any())

<div style="
background:#fee2e2;
color:#991b1b;
padding:18px;
border-radius:14px;
margin-bottom:25px;
">

<ul style="margin-left:20px;">

@foreach($errors->all() as $error)

<li>{{ $error }}</li>

@endforeach

</ul>

</div>

@endif

<form
method="POST"
action="{{ route('profile.update') }}"
enctype="multipart/form-data">

@csrf
@method('PATCH')

<div style="
display:grid;
grid-template-columns:340px 1fr;
gap:35px;
align-items:start;
">

<div style="
background:white;
border-radius:28px;
padding:35px;
box-shadow:0 18px 45px rgba(15,23,42,.08);
text-align:center;
">

<div
style="
position:relative;
width:170px;
height:170px;
margin:auto;
">

@if(auth()->user()->profile_photo)

<img
id="profilePreview"
src="{{ asset('storage/'.auth()->user()->profile_photo) }}"
style="
width:170px;
height:170px;
border-radius:50%;
object-fit:cover;
border:6px solid #eff6ff;
box-shadow:0 15px 35px rgba(37,99,235,.25);
">

@else

<img
id="profilePreview"
src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2563eb&color=ffffff&size=200"
style="
width:170px;
height:170px;
border-radius:50%;
object-fit:cover;
border:6px solid #eff6ff;
box-shadow:0 15px 35px rgba(37,99,235,.25);
">

@endif

<label
style="
position:absolute;
bottom:8px;
right:8px;
width:48px;
height:48px;
border-radius:50%;
background:#2563eb;
display:flex;
align-items:center;
justify-content:center;
cursor:pointer;
color:white;
box-shadow:0 10px 20px rgba(37,99,235,.35);
">

<i class="fas fa-camera"></i>

<input
type="file"
name="profile_photo"
accept="image/*"
style="display:none"
onchange="
const reader=new FileReader();
reader.onload=function(e){
document.getElementById('profilePreview').src=e.target.result;
}
reader.readAsDataURL(this.files[0]);
">

</label>

</div>

<h2 style="
margin-top:25px;
font-size:28px;
font-weight:700;
color:#1e293b;
">

{{ auth()->user()->name }}

</h2>

<p style="
margin-top:8px;
color:#64748b;
">

{{ auth()->user()->email }}

</p>

@if(auth()->user()->role=='admin')

<div style="
margin-top:22px;
padding:10px;
background:#eff6ff;
border-radius:40px;
font-weight:700;
color:#2563eb;
">

Administrator

</div>

@endif

</div>

<div style="
background:white;
border-radius:28px;
padding:40px;
box-shadow:0 18px 45px rgba(15,23,42,.08);
">

<h2 style="
font-size:28px;
font-weight:700;
margin-bottom:30px;
">

Personal Information

</h2>

<div style="
display:grid;
grid-template-columns:1fr 1fr;
gap:30px;
">

<div>

<label style="
display:block;
font-weight:700;
margin-bottom:10px;
">

Full Name

</label>

<input
type="text"
name="name"
value="{{ old('name',auth()->user()->name) }}"
style="
width:100%;
padding:18px;
border:2px solid #dbeafe;
border-radius:16px;
background:#f8fbff;
">

</div>

<div>

<label style="
display:block;
font-weight:700;
margin-bottom:10px;
">

Email Address

</label>

<input
type="email"
name="email"
value="{{ old('email',auth()->user()->email) }}"
style="
width:100%;
padding:18px;
border:2px solid #dbeafe;
border-radius:16px;
background:#f8fbff;
">

</div>

</div>

<div style="margin-top:35px;">

<button
type="submit"
style="
background:linear-gradient(135deg,#2563eb,#4f46e5);
color:white;
padding:16px 40px;
border:none;
border-radius:16px;
font-weight:bold;
cursor:pointer;
">

<i class="fas fa-save"></i>

Save Changes

</button>

</div>

</div>

</div>

</form>

@if(auth()->user()->role=='admin')

<div style="
background:white;
border-radius:30px;
overflow:hidden;
box-shadow:0 18px 45px rgba(15,23,42,.08);
margin-top:35px;
">

<div style="
background:linear-gradient(135deg,#059669,#10b981);
padding:28px 35px;
color:white;
">

<h2 style="
font-size:26px;
font-weight:700;
margin:0;
display:flex;
align-items:center;
gap:12px;
">

<i class="fas fa-building"></i>

Business Profile

</h2>

<p style="
margin-top:8px;
opacity:.9;
">

This information is displayed to customers during payment.

</p>

</div>

<div style="padding:40px;">

<form method="POST" action="{{ route('profile.business.update') }}">

@csrf

<div style="
display:grid;
grid-template-columns:1fr 1fr;
gap:30px;
">

<div>

<label style="font-weight:700;display:block;margin-bottom:10px;">

Business Name

</label>

<input
type="text"
name="business_name"
value="{{ old('business_name',auth()->user()->business_name) }}"
style="
width:100%;
padding:18px;
border:2px solid #d1fae5;
border-radius:16px;
background:#f0fdf4;
outline:none;
">

</div>

<div>

<label style="font-weight:700;display:block;margin-bottom:10px;">

Network Name

</label>

<input
type="text"
name="network_name"
value="{{ old('network_name',auth()->user()->network_name) }}"
style="
width:100%;
padding:18px;
border:2px solid #d1fae5;
border-radius:16px;
background:#f0fdf4;
outline:none;
">

</div>

<div>

<label style="font-weight:700;display:block;margin-bottom:10px;">

Business Phone

</label>

<input
type="text"
name="business_phone"
value="{{ old('business_phone',auth()->user()->business_phone) }}"
style="
width:100%;
padding:18px;
border:2px solid #d1fae5;
border-radius:16px;
background:#f0fdf4;
outline:none;
">

</div>

<div>

<label style="font-weight:700;display:block;margin-bottom:10px;">

Business Address

</label>

<input
type="text"
name="business_address"
value="{{ old('business_address',auth()->user()->business_address) }}"
style="
width:100%;
padding:18px;
border:2px solid #d1fae5;
border-radius:16px;
background:#f0fdf4;
outline:none;
">

</div>

</div>

<div style="
margin-top:40px;
padding:25px;
background:#f8fafc;
border-radius:20px;
border:1px solid #e2e8f0;
">

<h3 style="
font-size:22px;
font-weight:700;
margin-bottom:25px;
color:#1e293b;
">

Payment Details

</h3>

<div style="
display:flex;
gap:35px;
margin-bottom:25px;
">

<label>

<input
type="radio"
name="payment_type"
value="till"
{{ auth()->user()->payment_type=='till'?'checked':'' }}>

Till Number

</label>

<label>

<input
type="radio"
name="payment_type"
value="paybill"
{{ auth()->user()->payment_type=='paybill'?'checked':'' }}>

Paybill Number

</label>

</div>

<div style="
display:grid;
grid-template-columns:repeat(3,1fr);
gap:25px;
">

<div>

<label style="font-weight:700;display:block;margin-bottom:10px;">

Till Number

</label>

<input
type="text"
name="business_till"
value="{{ old('business_till',auth()->user()->business_till) }}"
style="
width:100%;
padding:18px;
border:2px solid #dbeafe;
border-radius:16px;
background:white;
">

</div>

<div>

<label style="font-weight:700;display:block;margin-bottom:10px;">

Paybill Number

</label>

<input
type="text"
name="business_paybill"
value="{{ old('business_paybill',auth()->user()->business_paybill) }}"
style="
width:100%;
padding:18px;
border:2px solid #dbeafe;
border-radius:16px;
background:white;
">

</div>

<div>

<label style="font-weight:700;display:block;margin-bottom:10px;">

Account Number

</label>

<input
type="text"
name="business_account"
value="{{ old('business_account',auth()->user()->business_account) }}"
style="
width:100%;
padding:18px;
border:2px solid #dbeafe;
border-radius:16px;
background:white;
">

</div>

</div>

</div>

<div style="margin-top:35px;">

<button
type="submit"
style="
background:linear-gradient(135deg,#059669,#10b981);
color:white;
padding:16px 40px;
border:none;
border-radius:16px;
font-weight:700;
cursor:pointer;
box-shadow:0 12px 25px rgba(5,150,105,.25);
">

<i class="fas fa-save"></i>

Save Business Profile

</button>

</div>

</form>

</div>

</div>

@endif
<div style="
background:white;
border-radius:30px;
overflow:hidden;
box-shadow:0 18px 45px rgba(15,23,42,.08);
margin-top:35px;
">

<div style="
background:linear-gradient(135deg,#7c3aed,#9333ea);
padding:28px 35px;
color:white;
">

<h2 style="
font-size:26px;
font-weight:700;
margin:0;
display:flex;
align-items:center;
gap:12px;
">

<i class="fas fa-lock"></i>

Change Password

</h2>

<p style="
margin-top:8px;
opacity:.9;
">

Keep your account secure with a strong password.

</p>

</div>

<div style="padding:40px;">

<form method="POST" action="{{ route('password.update') }}">

@csrf
@method('PUT')

<div style="
display:grid;
gap:25px;
">

<div>

<label style="display:block;font-weight:700;margin-bottom:10px;">

Current Password

</label>

<input
type="password"
name="current_password"
autocomplete="current-password"
style="
width:100%;
padding:18px;
border:2px solid #e9d5ff;
border-radius:16px;
background:#faf5ff;
outline:none;
">

@error('current_password','updatePassword')

<p style="color:#dc2626;margin-top:8px;">

{{ $message }}

</p>

@enderror

</div>

<div>

<label style="display:block;font-weight:700;margin-bottom:10px;">

New Password

</label>

<input
type="password"
name="password"
autocomplete="new-password"
style="
width:100%;
padding:18px;
border:2px solid #e9d5ff;
border-radius:16px;
background:#faf5ff;
outline:none;
">

@error('password','updatePassword')

<p style="color:#dc2626;margin-top:8px;">

{{ $message }}

</p>

@enderror

</div>

<div>

<label style="display:block;font-weight:700;margin-bottom:10px;">

Confirm Password

</label>

<input
type="password"
name="password_confirmation"
autocomplete="new-password"
style="
width:100%;
padding:18px;
border:2px solid #e9d5ff;
border-radius:16px;
background:#faf5ff;
outline:none;
">

</div>

</div>

<div style="margin-top:35px;">

<button
type="submit"
style="
background:linear-gradient(135deg,#7c3aed,#9333ea);
color:white;
padding:16px 40px;
border:none;
border-radius:16px;
font-weight:700;
cursor:pointer;
box-shadow:0 12px 25px rgba(124,58,237,.25);
">

<i class="fas fa-key"></i>

Update Password

</button>

</div>

</form>

</div>

</div>
<div style="
background:white;
border-radius:30px;
overflow:hidden;
box-shadow:0 18px 45px rgba(15,23,42,.08);
margin-top:35px;
">

<div style="
background:linear-gradient(135deg,#dc2626,#ef4444);
padding:28px 35px;
color:white;
">

<h2 style="
font-size:26px;
font-weight:700;
margin:0;
display:flex;
align-items:center;
gap:12px;
">

<i class="fas fa-trash"></i>

Delete Account

</h2>

<p style="margin-top:8px;opacity:.9;">

This action is permanent and cannot be undone.

</p>

</div>

<div style="padding:40px;">

<div style="
background:#fef2f2;
border-left:6px solid #dc2626;
padding:20px;
border-radius:16px;
margin-bottom:30px;
">

<strong style="color:#991b1b;">

<i class="fas fa-triangle-exclamation"></i>

Warning

</strong>

<p style="margin-top:10px;color:#7f1d1d;">

Deleting your account permanently removes all your data.

</p>

</div>

<button
type="button"
onclick="document.getElementById('deleteModal').style.display='flex'"
style="
background:linear-gradient(135deg,#dc2626,#ef4444);
color:white;
padding:16px 40px;
border:none;
border-radius:16px;
font-weight:700;
cursor:pointer;
box-shadow:0 12px 25px rgba(220,38,38,.25);
">

<i class="fas fa-trash"></i>

Delete My Account

</button>

</div>

</div>

<div
id="deleteModal"
style="
display:none;
position:fixed;
left:0;
top:0;
width:100%;
height:100%;
background:rgba(0,0,0,.55);
justify-content:center;
align-items:center;
z-index:9999;
">

<div style="
width:500px;
background:white;
border-radius:25px;
padding:35px;
box-shadow:0 25px 60px rgba(0,0,0,.35);
">

<h2 style="
font-size:26px;
font-weight:700;
color:#dc2626;
margin-bottom:20px;
">

Delete Account

</h2>

<p style="
margin-bottom:25px;
color:#475569;
line-height:1.8;
">

Are you sure you want to permanently delete your account?

This action cannot be undone.

</p>

<form
method="POST"
action="{{ route('profile.destroy') }}"
>

@csrf

@method('DELETE')

<label style="
display:block;
font-weight:700;
margin-bottom:10px;
">

Enter Password

</label>

<input
type="password"
name="password"
autocomplete="current-password"
style="
width:100%;
padding:18px;
border:2px solid #fecaca;
border-radius:16px;
background:#fff5f5;
margin-bottom:20px;
">

@error('password','userDeletion')

<p style="color:#dc2626;margin-bottom:20px;">

{{ $message }}

</p>

@enderror

<div style="
display:flex;
justify-content:flex-end;
gap:15px;
">

<button
type="button"
onclick="document.getElementById('deleteModal').style.display='none'"
style="
padding:14px 25px;
border:none;
border-radius:14px;
background:#e2e8f0;
cursor:pointer;
font-weight:600;
">

Cancel

</button>

<button
type="submit"
style="
padding:14px 30px;
border:none;
border-radius:14px;
background:#dc2626;
color:white;
font-weight:bold;
cursor:pointer;
">

Delete

</button>

</div>

</form>

</div>

</div>
</div>

<script>

const photoInput=document.querySelector('input[name="profile_photo"]');

if(photoInput){

photoInput.addEventListener('change',function(e){

const file=e.target.files[0];

if(!file) return;

let reader=new FileReader();

reader.onload=function(ev){

let img=document.querySelector('#profilePreview');

if(img){

img.src=ev.target.result;

}else{

let holder=document.querySelector('.profile-image-holder');

if(holder){

holder.innerHTML='<img id="profilePreview" src="'+ev.target.result+'" style="width:150px;height:150px;border-radius:50%;object-fit:cover;border:6px solid #eff6ff;box-shadow:0 15px 35px rgba(37,99,235,.25);">';

}

}

};

reader.readAsDataURL(file);

});

}

window.onclick=function(event){

const modal=document.getElementById('deleteModal');

if(event.target===modal){

modal.style.display='none';

}

};

</script>

</x-app-layout>