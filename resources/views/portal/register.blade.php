<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Register |
        {{ $admin->network_name
            ?: $admin->business_name
            ?: 'WiFi Internet' }}
    </title>

    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 30px 16px;
            font-family: Figtree, Arial, sans-serif;
            color: #0f172a;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.9),
                    transparent 35%
                ),
                #f4f7fb;
        }

        a {
            text-decoration: none;
        }

        .page {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .provider {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 11px;
        }

        .provider-icon {
            width: 43px;
            height: 43px;
            border-radius: 13px;
            background: #2563eb;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.2);
        }

        .provider-name {
            font-size: 16px;
            font-weight: 900;
        }

        .provider-label {
            margin-top: 2px;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
        }

        .card {
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08);
        }

        .heading {
            margin-bottom: 25px;
            text-align: center;
        }

        .heading-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 13px;
            border-radius: 17px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .heading h1 {
            margin: 0;
            font-size: 25px;
            font-weight: 900;
        }

        .heading p {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 11px;
            background: #fff7f7;
            border: 1px solid #fecaca;
            color: #991b1b;
            font-size: 11px;
            line-height: 1.6;
        }

        .field {
            margin-bottom: 17px;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: 11px;
            font-weight: 800;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            top: 50%;
            left: 14px;
            color: #94a3b8;
            font-size: 13px;
            transform: translateY(-50%);
        }

        .field input {
            width: 100%;
            min-height: 45px;
            padding: 10px 13px 10px 40px;
            border: 1px solid #cbd5e1;
            border-radius: 11px;
            outline: none;
            background: #ffffff;
            color: #0f172a;
            font: inherit;
            font-size: 12px;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .field input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .field-error {
            margin-top: 5px;
            color: #dc2626;
            font-size: 10px;
            font-weight: 700;
        }

        .security-note {
            margin: 4px 0 20px;
            padding: 12px 13px;
            border: 1px solid #bfdbfe;
            border-radius: 11px;
            background: #eff6ff;
            color: #1e3a8a;
            display: flex;
            align-items: flex-start;
            gap: 9px;
            font-size: 10px;
            line-height: 1.55;
        }

        .security-note i {
            margin-top: 2px;
            color: #2563eb;
        }

        .submit-button {
            width: 100%;
            min-height: 46px;
            padding: 11px 15px;
            border: 0;
            border-radius: 12px;
            background: #2563eb;
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font: inherit;
            font-size: 12px;
            font-weight: 900;
            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }

        .submit-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .back-link {
            margin-top: 18px;
            text-align: center;
        }

        .back-link a {
            color: #2563eb;
            font-size: 11px;
            font-weight: 800;
        }

        .footer {
            margin-top: 20px;
            color: #94a3b8;
            text-align: center;
            font-size: 9px;
            line-height: 1.6;
        }

        @media (max-width: 520px) {
            body {
                padding: 20px 11px;
            }

            .card {
                padding: 24px 18px;
            }

            .heading h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <div class="provider">
        <div class="provider-icon">
            <i class="fas fa-wifi" aria-hidden="true"></i>
        </div>

        <div>
            <div class="provider-name">
                {{ $admin->network_name
                    ?: $admin->business_name
                    ?: 'WiFi Internet' }}
            </div>

            <div class="provider-label">
                Customer Internet Portal
            </div>
        </div>
    </div>

    <div class="card">

        <div class="heading">
            <div class="heading-icon">
                <i class="fas fa-user-plus" aria-hidden="true"></i>
            </div>

            <h1>Create Customer Account</h1>

            <p>
                Register to purchase and manage internet packages
                from this service provider.
            </p>
        </div>

        @if(session('error'))
            <div class="alert" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route(
                'portal.register.store',
                $router->portal_token
            ) }}"
        >
            @csrf

            <div class="field">
                <label for="name">
                    Full Name
                </label>

                <div class="input-wrap">
                    <i class="fas fa-user" aria-hidden="true"></i>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                    >
                </div>

                @error('name')
                    <div class="field-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="field">
                <label for="email">
                    Email Address
                </label>

                <div class="input-wrap">
                    <i
                        class="fas fa-envelope"
                        aria-hidden="true"
                    ></i>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                    >
                </div>

                @error('email')
                    <div class="field-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="field">
                <label for="password">
                    Password
                </label>

                <div class="input-wrap">
                    <i class="fas fa-lock" aria-hidden="true"></i>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                    >
                </div>

                @error('password')
                    <div class="field-error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">
                    Confirm Password
                </label>

                <div class="input-wrap">
                    <i class="fas fa-lock" aria-hidden="true"></i>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                    >
                </div>
            </div>

            <div class="security-note">
                <i
                    class="fas fa-shield-halved"
                    aria-hidden="true"
                ></i>

                <div>
                    Your account will be securely registered with
                    <strong>
                        {{ $admin->network_name
                            ?: $admin->business_name
                            ?: $admin->name }}
                    </strong>.
                </div>
            </div>

            <button
                type="submit"
                class="submit-button"
            >
                <i class="fas fa-user-plus" aria-hidden="true"></i>
                Create Customer Account
            </button>
        </form>

        <div class="back-link">
            <a
                href="{{ route(
                    'portal.show',
                    $router->portal_token
                ) }}"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                Back to Internet Packages
            </a>
        </div>

    </div>

    <div class="footer">
        Secure registration portal for
        {{ $admin->network_name
            ?: $admin->business_name
            ?: $admin->name }}.
    </div>

</div>

</body>
</html>