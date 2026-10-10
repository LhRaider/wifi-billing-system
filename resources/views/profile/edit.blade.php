<x-app-layout>

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
        padding:40px 20px;
    ">

        <div style="
            max-width:1100px;
            margin:auto;
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

            {{-- Personal Profile --}}
            <form
                method="POST"
                action="{{ route('profile.update') }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PATCH')

                <div style="
                    display:grid;
                    grid-template-columns:340px 1fr;
                    gap:35px;
                    align-items:start;
                ">

                    {{-- Profile Card --}}
                    <div style="
                        background:white;
                        border-radius:28px;
                        padding:35px;
                        box-shadow:0 18px 45px rgba(15,23,42,.08);
                        text-align:center;
                    ">

                        <div style="
                            position:relative;
                            width:170px;
                            height:170px;
                            margin:auto;
                        ">

                            @if(auth()->user()->profile_photo)

                                <img
                                    id="profilePreview"
                                    src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                                    alt="Profile Photo"
                                    style="
                                        width:170px;
                                        height:170px;
                                        border-radius:50%;
                                        object-fit:cover;
                                        border:6px solid #eff6ff;
                                        box-shadow:0 15px 35px rgba(37,99,235,.25);
                                    "
                                >

                                <div
                                    id="initialsAvatar"
                                    style="
                                        display:none;
                                        width:170px;
                                        height:170px;
                                        border-radius:50%;
                                        align-items:center;
                                        justify-content:center;
                                        background:#2563eb;
                                        color:white;
                                        font-size:58px;
                                        font-weight:700;
                                        border:6px solid #eff6ff;
                                        box-shadow:0 15px 35px rgba(37,99,235,.25);
                                        text-transform:uppercase;
                                    "
                                >
                                    {{ collect(preg_split('/\s+/', trim(auth()->user()->name)))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('') }}
                                </div>

                            @else

                                <div
                                    id="initialsAvatar"
                                    style="
                                        display:flex;
                                        width:170px;
                                        height:170px;
                                        border-radius:50%;
                                        align-items:center;
                                        justify-content:center;
                                        background:#2563eb;
                                        color:white;
                                        font-size:58px;
                                        font-weight:700;
                                        border:6px solid #eff6ff;
                                        box-shadow:0 15px 35px rgba(37,99,235,.25);
                                        text-transform:uppercase;
                                    "
                                >
                                    {{ collect(preg_split('/\s+/', trim(auth()->user()->name)))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('') }}
                                </div>

                                <img
                                    id="profilePreview"
                                    src=""
                                    alt="Profile Photo Preview"
                                    style="
                                        display:none;
                                        width:170px;
                                        height:170px;
                                        border-radius:50%;
                                        object-fit:cover;
                                        border:6px solid #eff6ff;
                                        box-shadow:0 15px 35px rgba(37,99,235,.25);
                                    "
                                >

                            @endif

                            <label
                                for="profile_photo"
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
                                "
                                aria-label="Choose profile photo"
                            >
                                <i class="fas fa-camera"></i>

                                <input
                                    id="profile_photo"
                                    type="file"
                                    name="profile_photo"
                                    accept="image/jpeg,image/png,image/webp"
                                    style="display:none;"
                                >
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

                        @if(auth()->user()->role === 'super_admin')

                            <div style="
                                margin-top:22px;
                                padding:10px;
                                background:#f3e8ff;
                                border-radius:40px;
                                font-weight:700;
                                color:#7c3aed;
                            ">
                                Super Administrator
                            </div>

                        @elseif(auth()->user()->role === 'admin')

                            <div style="
                                margin-top:22px;
                                padding:10px;
                                background:#eff6ff;
                                border-radius:40px;
                                font-weight:700;
                                color:#2563eb;
                            ">
                                ISP Administrator
                            </div>

                        @elseif(auth()->user()->role === 'customer')

                            <div style="
                                margin-top:22px;
                                padding:10px;
                                background:#ecfdf5;
                                border-radius:40px;
                                font-weight:700;
                                color:#059669;
                            ">
                                Customer
                            </div>

                        @endif

                    </div>

                    {{-- Personal Information --}}
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
                            color:#1e293b;
                        ">
                            Personal Information
                        </h2>

                        <div style="
                            display:grid;
                            grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));
                            gap:30px;
                        ">

                            <div>
                                <label
                                    for="name"
                                    style="
                                        display:block;
                                        font-weight:700;
                                        margin-bottom:10px;
                                    "
                                >
                                    Full Name
                                </label>

                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name', auth()->user()->name) }}"
                                    autocomplete="name"
                                    required
                                    style="
                                        width:100%;
                                        padding:18px;
                                        border:2px solid #dbeafe;
                                        border-radius:16px;
                                        background:#f8fbff;
                                    "
                                >

                                @error('name')
                                    <p style="
                                        color:#dc2626;
                                        margin-top:8px;
                                    ">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="email"
                                    style="
                                        display:block;
                                        font-weight:700;
                                        margin-bottom:10px;
                                    "
                                >
                                    Email Address
                                </label>

                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email', auth()->user()->email) }}"
                                    autocomplete="email"
                                    required
                                    style="
                                        width:100%;
                                        padding:18px;
                                        border:2px solid #dbeafe;
                                        border-radius:16px;
                                        background:#f8fbff;
                                    "
                                >

                                @error('email')
                                    <p style="
                                        color:#dc2626;
                                        margin-top:8px;
                                    ">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        @error('profile_photo')
                            <p style="
                                color:#dc2626;
                                margin-top:20px;
                            ">
                                {{ $message }}
                            </p>
                        @enderror

                        <div style="margin-top:35px;">
                            <button
                                type="submit"
                                style="
                                    background:linear-gradient(135deg,#2563eb,#4f46e5);
                                    color:white;
                                    padding:16px 40px;
                                    border:none;
                                    border-radius:16px;
                                    font-weight:700;
                                    cursor:pointer;
                                    box-shadow:0 12px 25px rgba(37,99,235,.25);
                                "
                            >
                                <i class="fas fa-save"></i>
                                Save Changes
                            </button>
                        </div>

                    </div>

                </div>

            </form>

            {{-- ISP Admin Business Profile Notice --}}
            @if(auth()->user()->role === 'admin')

                <div style="
                    background:white;
                    border-radius:28px;
                    padding:35px;
                    box-shadow:0 18px 45px rgba(15,23,42,.08);
                    margin-top:35px;
                ">

                    <div style="
                        display:flex;
                        align-items:center;
                        gap:15px;
                    ">
                        <div style="
                            width:52px;
                            height:52px;
                            border-radius:16px;
                            background:#ecfdf5;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:#059669;
                            font-size:22px;
                        ">
                            <i class="fas fa-building"></i>
                        </div>

                        <div>
                            <h2 style="
                                font-size:23px;
                                font-weight:700;
                                color:#1e293b;
                                margin:0;
                            ">
                                Business Profile
                            </h2>

                            <p style="
                                margin-top:5px;
                                color:#64748b;
                            ">
                                Business, network, and customer payment details are managed separately.
                            </p>
                        </div>
                    </div>

                    <div style="margin-top:25px;">
                        <a
                            href="{{ route('admin.business-profile.edit') }}"
                            style="
                                display:inline-block;
                                background:#059669;
                                color:white;
                                padding:14px 28px;
                                border-radius:14px;
                                font-weight:700;
                                text-decoration:none;
                            "
                        >
                            <i class="fas fa-building"></i>
                            Manage Business Profile
                        </a>
                    </div>

                </div>

            @endif

            {{-- Change Password --}}
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

                    <form
                        method="POST"
                        action="{{ route('password.update') }}"
                    >
                        @csrf
                        @method('PUT')

                        <div style="
                            display:grid;
                            gap:25px;
                        ">

                            <div>
                                <label
                                    for="current_password"
                                    style="
                                        display:block;
                                        font-weight:700;
                                        margin-bottom:10px;
                                    "
                                >
                                    Current Password
                                </label>

                                <input
                                    id="current_password"
                                    type="password"
                                    name="current_password"
                                    autocomplete="current-password"
                                    required
                                    style="
                                        width:100%;
                                        padding:18px;
                                        border:2px solid #e9d5ff;
                                        border-radius:16px;
                                        background:#faf5ff;
                                    "
                                >

                                @error('current_password', 'updatePassword')
                                    <p style="
                                        color:#dc2626;
                                        margin-top:8px;
                                    ">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="new_password"
                                    style="
                                        display:block;
                                        font-weight:700;
                                        margin-bottom:10px;
                                    "
                                >
                                    New Password
                                </label>

                                <input
                                    id="new_password"
                                    type="password"
                                    name="password"
                                    autocomplete="new-password"
                                    required
                                    style="
                                        width:100%;
                                        padding:18px;
                                        border:2px solid #e9d5ff;
                                        border-radius:16px;
                                        background:#faf5ff;
                                    "
                                >

                                @error('password', 'updatePassword')
                                    <p style="
                                        color:#dc2626;
                                        margin-top:8px;
                                    ">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="password_confirmation"
                                    style="
                                        display:block;
                                        font-weight:700;
                                        margin-bottom:10px;
                                    "
                                >
                                    Confirm Password
                                </label>

                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    autocomplete="new-password"
                                    required
                                    style="
                                        width:100%;
                                        padding:18px;
                                        border:2px solid #e9d5ff;
                                        border-radius:16px;
                                        background:#faf5ff;
                                    "
                                >
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
                                "
                            >
                                <i class="fas fa-key"></i>
                                Update Password
                            </button>
                        </div>

                    </form>

                </div>

            </div>

            {{-- Customer Account Deletion Only --}}
            @if(auth()->user()->role === 'customer')

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

                        <p style="
                            margin-top:8px;
                            opacity:.9;
                        ">
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

                            <p style="
                                margin-top:10px;
                                color:#7f1d1d;
                            ">
                                Deleting your account permanently removes your account access.
                            </p>

                        </div>

                        <button
                            type="button"
                            id="openDeleteModal"
                            style="
                                background:linear-gradient(135deg,#dc2626,#ef4444);
                                color:white;
                                padding:16px 40px;
                                border:none;
                                border-radius:16px;
                                font-weight:700;
                                cursor:pointer;
                                box-shadow:0 12px 25px rgba(220,38,38,.25);
                            "
                        >
                            <i class="fas fa-trash"></i>
                            Delete My Account
                        </button>

                    </div>

                </div>

                {{-- Delete Modal --}}
                <div
                    id="deleteModal"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="deleteModalTitle"
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
                        padding:20px;
                    "
                >

                    <div style="
                        width:500px;
                        max-width:100%;
                        background:white;
                        border-radius:25px;
                        padding:35px;
                        box-shadow:0 25px 60px rgba(0,0,0,.35);
                    ">

                        <h2
                            id="deleteModalTitle"
                            style="
                                font-size:26px;
                                font-weight:700;
                                color:#dc2626;
                                margin-bottom:20px;
                            "
                        >
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

                            <label
                                for="delete_account_password"
                                style="
                                    display:block;
                                    font-weight:700;
                                    margin-bottom:10px;
                                "
                            >
                                Enter Password
                            </label>

                            <input
                                id="delete_account_password"
                                type="password"
                                name="password"
                                autocomplete="current-password"
                                required
                                style="
                                    width:100%;
                                    padding:18px;
                                    border:2px solid #fecaca;
                                    border-radius:16px;
                                    background:#fff5f5;
                                    margin-bottom:20px;
                                "
                            >

                            @error('password', 'userDeletion')
                                <p style="
                                    color:#dc2626;
                                    margin-bottom:20px;
                                ">
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
                                    id="closeDeleteModal"
                                    style="
                                        padding:14px 25px;
                                        border:none;
                                        border-radius:14px;
                                        background:#e2e8f0;
                                        cursor:pointer;
                                        font-weight:600;
                                    "
                                >
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
                                        font-weight:700;
                                        cursor:pointer;
                                    "
                                >
                                    Delete
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            @endif

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const photoInput = document.querySelector('input[name="profile_photo"]');
            const profilePreview = document.getElementById('profilePreview');
            const initialsAvatar = document.getElementById('initialsAvatar');

            if (photoInput) {
                photoInput.addEventListener('change', function (event) {
                    const file = event.target.files && event.target.files[0];

                    if (!file || !profilePreview) {
                        return;
                    }

                    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                        photoInput.value = '';
                        return;
                    }

                    const reader = new FileReader();

                    reader.addEventListener('load', function (readerEvent) {
                        profilePreview.src = readerEvent.target.result;
                        profilePreview.style.display = 'block';

                        if (initialsAvatar) {
                            initialsAvatar.style.display = 'none';
                        }
                    });

                    reader.readAsDataURL(file);
                });
            }

            const modal = document.getElementById('deleteModal');
            const openModalButton = document.getElementById('openDeleteModal');
            const closeModalButton = document.getElementById('closeDeleteModal');
            const deletePassword = document.getElementById('delete_account_password');

            const openModal = function () {
                if (!modal) {
                    return;
                }

                modal.style.display = 'flex';

                if (deletePassword) {
                    deletePassword.focus();
                }
            };

            const closeModal = function () {
                if (!modal) {
                    return;
                }

                modal.style.display = 'none';
            };

            if (openModalButton) {
                openModalButton.addEventListener('click', openModal);
            }

            if (closeModalButton) {
                closeModalButton.addEventListener('click', closeModal);
            }

            if (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });
            }

            document.addEventListener('keydown', function (event) {
                if (
                    event.key === 'Escape' &&
                    modal &&
                    modal.style.display === 'flex'
                ) {
                    closeModal();
                }
            });

            @if($errors->userDeletion->isNotEmpty())
                openModal();
            @endif
        });
    </script>

</x-app-layout>