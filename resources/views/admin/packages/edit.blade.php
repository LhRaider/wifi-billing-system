<x-app-layout>
    <x-slot name="header">
        <div class="package-page-header">
            <div class="package-page-heading">
                <span class="package-page-heading-icon">
                    <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                </span>

                <div>
                    <h2>Edit Internet Package</h2>
                    <p>
                        Update pricing, access time, internet speeds and router settings.
                    </p>
                </div>
            </div>

            <a
                href="{{ route('admin.packages.index') }}"
                class="package-back-button"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                <span>Back to Packages</span>
            </a>
        </div>
    </x-slot>

    @php
        $currentDurationMinutes = (int) old(
            'duration_minutes',
            $package->duration_minutes ?? 0
        );

        $currentDurationHours = intdiv(
            $currentDurationMinutes,
            60
        );

        $currentRemainingMinutes =
            $currentDurationMinutes % 60;

        $currentSpeedUnlimited =
            old(
                'is_speed_unlimited',
                $package->is_speed_unlimited ? '1' : '0'
            ) == '1';

        $currentActive =
            old(
                'is_active',
                $package->is_active ? '1' : '0'
            ) == '1';
    @endphp

    <style>
        .package-page-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .package-page-heading {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .package-page-heading-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.22);
        }

        .package-page-heading h2 {
            margin: 0;
            color: #0f172a;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 800;
        }

        .package-page-heading p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .package-back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border: 1px solid #dbe3ed;
            border-radius: 13px;
            background: #ffffff;
            color: #334155;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }

        .package-back-button:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .package-edit-page {
            min-height: 100vh;
            padding: 34px 25px 55px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, 0.65),
                    transparent 30%
                ),
                #f4f7fb;
        }

        .package-edit-container {
            width: 100%;
            max-width: 1080px;
            margin: 0 auto;
        }

        .validation-alert {
            margin-bottom: 22px;
            padding: 17px 19px;
            border: 1px solid #fecaca;
            border-radius: 17px;
            background: #fff7f7;
            color: #991b1b;
            display: flex;
            gap: 13px;
            align-items: flex-start;
        }

        .validation-alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #fee2e2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .validation-alert strong {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .validation-alert ul {
            margin: 0;
            padding-left: 18px;
            font-size: 13px;
            line-height: 1.7;
        }

        .package-form-card {
            overflow: hidden;
            border: 1px solid #e7edf5;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.065);
        }

        .package-form-intro {
            padding: 26px 28px;
            border-bottom: 1px solid #edf1f6;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .package-form-intro-icon {
            width: 48px;
            height: 48px;
            border-radius: 15px;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 19px;
        }

        .package-form-intro h3 {
            margin: 0;
            color: #0f172a;
            font-size: 19px;
            font-weight: 800;
        }

        .package-form-intro p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
        }

        .package-form-body {
            padding: 28px;
        }

        .form-section {
            margin-bottom: 32px;
        }

        .form-section:last-of-type {
            margin-bottom: 0;
        }

        .form-section-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 17px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .form-section-heading i {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-field {
            min-width: 0;
        }

        .form-field.full {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 800;
        }

        .form-required {
            color: #dc2626;
        }

        .form-control {
            width: 100%;
            min-height: 47px;
            padding: 11px 13px;
            border: 1px solid #cfd8e3;
            border-radius: 12px;
            background: #ffffff;
            color: #0f172a;
            font-size: 14px;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        textarea.form-control {
            min-height: 110px;
            resize: vertical;
        }

        .form-control:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.11);
        }

        .form-control:disabled {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .form-help {
            display: block;
            margin-top: 7px;
            color: #64748b;
            font-size: 11px;
            line-height: 1.55;
        }

        .form-example {
            color: #2563eb;
            font-weight: 700;
        }

        .guidance-card {
            grid-column: 1 / -1;
            padding: 16px 17px;
            border: 1px solid #bfdbfe;
            border-radius: 15px;
            background: #eff6ff;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: #1e3a8a;
            font-size: 11px;
            line-height: 1.65;
        }

        .guidance-card > i {
            margin-top: 3px;
            color: #2563eb;
            font-size: 15px;
        }

        .guidance-card strong {
            color: #1e40af;
        }

        .duration-card {
            grid-column: 1 / -1;
            padding: 20px;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: #f8fbff;
        }

        .duration-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 17px;
        }

        .duration-header h4 {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
        }

        .duration-header p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }

        .duration-header-icon {
            width: 39px;
            height: 39px;
            border-radius: 12px;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .duration-input-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .duration-input-wrap {
            position: relative;
        }

        .duration-input-wrap .form-control {
            padding-right: 78px;
        }

        .duration-unit {
            position: absolute;
            top: 50%;
            right: 13px;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            pointer-events: none;
        }

        .duration-presets {
            margin-top: 17px;
        }

        .duration-presets-label {
            display: block;
            margin-bottom: 9px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .duration-preset-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .duration-preset {
            border: 1px solid #dbe3ed;
            border-radius: 999px;
            padding: 8px 12px;
            background: #ffffff;
            color: #475569;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                color 0.2s ease;
        }

        .duration-preset:hover {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .duration-summary {
            margin-top: 15px;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            color: #475569;
            font-size: 12px;
        }

        .duration-summary strong {
            color: #2563eb;
        }

        .speed-guidance {
            grid-column: 1 / -1;
            padding: 17px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #f8fafc;
        }

        .speed-guidance-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 8px;
            color: #0f172a;
            font-size: 12px;
            font-weight: 800;
        }

        .speed-guidance-title i {
            color: #2563eb;
        }

        .speed-guidance p {
            margin: 0;
            color: #64748b;
            font-size: 11px;
            line-height: 1.65;
        }

        .speed-examples {
            margin-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .speed-example {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #ffffff;
            color: #475569;
            font-size: 10px;
            font-weight: 700;
        }

        .speed-example strong {
            color: #0f172a;
        }

        .toggle-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 17px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            background: #f8fafc;
        }

        .toggle-copy {
            min-width: 0;
        }

        .toggle-copy strong {
            display: block;
            color: #0f172a;
            font-size: 13px;
        }

        .toggle-copy span {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
            line-height: 1.55;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 27px;
            flex-shrink: 0;
        }

        .switch input {
            width: 1px;
            height: 1px;
            opacity: 0;
            position: absolute;
        }

        .switch-slider {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: #cbd5e1;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .switch-slider::before {
            content: "";
            position: absolute;
            width: 21px;
            height: 21px;
            left: 3px;
            top: 3px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(15, 23, 42, 0.2);
            transition: transform 0.2s ease;
        }

        .switch input:checked + .switch-slider {
            background: #2563eb;
        }

        .switch input:checked + .switch-slider::before {
            transform: translateX(21px);
        }

        .switch input:focus-visible + .switch-slider {
            outline: 3px solid #93c5fd;
            outline-offset: 2px;
        }

        .package-form-footer {
            padding: 21px 28px;
            border-top: 1px solid #edf1f6;
            background: #fbfcfe;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 11px;
        }

        .form-button {
            min-height: 43px;
            padding: 10px 18px;
            border-radius: 12px;
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        .form-button.primary {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 7px 17px rgba(37, 99, 235, 0.18);
        }

        .form-button.primary:hover {
            background: #1d4ed8;
        }

        .form-button.secondary {
            border: 1px solid #dbe3ed;
            background: #ffffff;
            color: #475569;
        }

        .form-button.secondary:hover {
            background: #f8fafc;
        }


        /* Complete page-level dark theme */
        html.dark .package-page-heading h2,
        html.dark .package-form-intro h3,
        html.dark .form-section-heading,
        html.dark .duration-header h4,
        html.dark .speed-guidance-title,
        html.dark .toggle-copy strong {
            color: #f8fafc;
        }

        html.dark .package-page-heading p,
        html.dark .package-form-intro p,
        html.dark .form-help,
        html.dark .duration-header p,
        html.dark .duration-presets-label,
        html.dark .speed-guidance p,
        html.dark .toggle-copy span,
        html.dark .duration-unit {
            color: #aebbd0;
        }

        html.dark .package-back-button,
        html.dark .form-button.secondary,
        html.dark .duration-preset,
        html.dark .duration-summary,
        html.dark .speed-example {
            background: #111c31;
            border-color: #2b3b56;
            color: #dce6f5;
        }

        html.dark .package-back-button:hover,
        html.dark .form-button.secondary:hover,
        html.dark .duration-preset:hover {
            background: #17243b;
            border-color: #42669c;
            color: #ffffff;
        }

        html.dark .package-edit-page {
            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.14),
                    transparent 32%
                ),
                #091226;
        }

        html.dark .validation-alert {
            border-color: #713142;
            background: #351823;
            color: #fecdd3;
        }

        html.dark .validation-alert-icon {
            background: #4c1d2b;
            color: #fda4af;
        }

        html.dark .package-form-card {
            background: #0f1a2e;
            border-color: #243451;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.24);
        }

        html.dark .package-form-intro {
            border-color: #243451;
            background: #101c32;
        }

        html.dark .package-form-intro-icon,
        html.dark .form-section-heading i,
        html.dark .duration-header-icon {
            background: #162b50;
            color: #6ea8ff;
        }

        html.dark .package-form-body {
            background: #0f1a2e;
        }

        html.dark .form-label {
            color: #e5edf9;
        }

        html.dark .form-required {
            color: #fb7185;
        }

        html.dark .form-control {
            background: #09162a;
            border-color: #31425f;
            color: #f8fafc;
            caret-color: #ffffff;
        }

        html.dark .form-control::placeholder {
            color: #70819d;
            opacity: 1;
        }

        html.dark select.form-control {
            color-scheme: dark;
        }

        html.dark .form-control:focus {
            background: #0b1930;
            border-color: #4f8cff;
            box-shadow: 0 0 0 3px rgba(79, 140, 255, 0.18);
        }

        html.dark .form-control:disabled {
            background: #111a2a;
            border-color: #27364e;
            color: #71819a;
        }

        html.dark .form-example {
            color: #73a8ff;
        }

        html.dark .guidance-card {
            background: #102343;
            border-color: #28548a;
            color: #c9ddff;
        }

        html.dark .guidance-card > i {
            color: #6ea8ff;
        }

        html.dark .guidance-card strong {
            color: #8db8ff;
        }

        html.dark .duration-card {
            background: #0b172a;
            border-color: #263a59;
        }

        html.dark .duration-summary {
            background: #111d32;
        }

        html.dark .duration-summary strong {
            color: #76a9ff;
        }

        html.dark .duration-preset.selected {
            background: #1d4ed8;
            border-color: #60a5fa;
            color: #ffffff;
        }

        html.dark .speed-guidance,
        html.dark .toggle-panel {
            background: #0b172a;
            border-color: #293a54;
        }

        html.dark .speed-example strong {
            color: #f8fafc;
        }

        html.dark .switch-slider {
            background: #475569;
        }

        html.dark .switch input:checked + .switch-slider {
            background: #3977ff;
        }

        html.dark .package-form-footer {
            background: #0b1628;
            border-color: #243451;
        }

        html.dark .package-form-card input,
        html.dark .package-form-card select,
        html.dark .package-form-card textarea {
            color: #f8fafc;
        }

        @media (max-width: 767px) {
            .package-page-heading p {
                display: none;
            }

            .package-page-heading h2 {
                font-size: 21px;
            }

            .package-page-heading-icon {
                width: 44px;
                height: 44px;
            }

            .package-back-button span {
                display: none;
            }

            .package-edit-page {
                padding: 25px 14px 45px;
            }

            .package-form-body,
            .package-form-intro,
            .package-form-footer {
                padding-left: 20px;
                padding-right: 20px;
            }

            .form-grid,
            .duration-input-grid {
                grid-template-columns: 1fr;
            }

            .duration-card,
            .guidance-card,
            .speed-guidance {
                grid-column: auto;
            }
        }

        @media (max-width: 480px) {
            .package-edit-page {
                padding-left: 11px;
                padding-right: 11px;
            }

            .package-form-card {
                border-radius: 19px;
            }

            .package-form-footer {
                align-items: stretch;
                flex-direction: column-reverse;
            }

            .form-button {
                width: 100%;
            }

            .duration-header {
                flex-direction: column;
            }
        }
    </style>

    <div class="package-edit-page">
        <div class="package-edit-container">

            @if($errors->any())
                <div class="validation-alert" role="alert">
                    <div class="validation-alert-icon">
                        <i
                            class="fas fa-triangle-exclamation"
                            aria-hidden="true"
                        ></i>
                    </div>

                    <div>
                        <strong>Please correct the following:</strong>

                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('admin.packages.update', $package) }}"
                id="package-form"
            >
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="duration_minutes"
                    id="duration_minutes"
                    value="{{ old('duration_minutes', $package->duration_minutes) }}"
                >

                <div class="package-form-card">

                    <div class="package-form-intro">
                        <div class="package-form-intro-icon">
                            <i
                                class="fas fa-pen-to-square"
                                aria-hidden="true"
                            ></i>
                        </div>

                        <div>
                            <h3>{{ $package->name }}</h3>

                            <p>
                                Changes apply only to this package in your ISP account.
                                Review the settings carefully before saving.
                            </p>
                        </div>
                    </div>

                    <div class="package-form-body">

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i class="fas fa-tag" aria-hidden="true"></i>
                                Package Details
                            </div>

                            <div class="form-grid">
                                <div class="form-field">
                                    <label
                                        for="name"
                                        class="form-label"
                                    >
                                        Package Name
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name', $package->name) }}"
                                        required
                                        maxlength="100"
                                        autocomplete="off"
                                        placeholder="Example: 2 Hours"
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        Use a short customer-friendly name such as
                                        <span class="form-example">1 Hour</span>,
                                        <span class="form-example">2 Hours</span> or
                                        <span class="form-example">1 Day</span>.
                                    </span>
                                </div>

                                <div class="form-field">
                                    <label
                                        for="package_type"
                                        class="form-label"
                                    >
                                        Package Type
                                        <span class="form-required">*</span>
                                    </label>

                                    <select
                                        id="package_type"
                                        name="package_type"
                                        required
                                        class="form-control"
                                    >
                                        <option
                                            value="hotspot"
                                            {{ old('package_type', $package->package_type) === 'hotspot' ? 'selected' : '' }}
                                        >
                                            Hotspot
                                        </option>

                                        <option
                                            value="pppoe"
                                            {{ old('package_type', $package->package_type) === 'pppoe' ? 'selected' : '' }}
                                        >
                                            PPPoE
                                        </option>
                                    </select>

                                    <span class="form-help">
                                        Hotspot is normally used for WiFi login access.
                                        PPPoE is used for router or fixed connection accounts.
                                    </span>
                                </div>

                                <div class="form-field">
                                    <label
                                        for="price"
                                        class="form-label"
                                    >
                                        Price (KES)
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="price"
                                        type="number"
                                        name="price"
                                        value="{{ old('price', $package->price) }}"
                                        required
                                        min="0"
                                        max="99999999.99"
                                        step="0.01"
                                        inputmode="decimal"
                                        placeholder="Example: 20.00"
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        Enter the amount the customer pays for one
                                        activation of this package.
                                    </span>
                                </div>

                                <div class="form-field">
                                    <label
                                        for="router_profile"
                                        class="form-label"
                                    >
                                        Router Profile
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="router_profile"
                                        type="text"
                                        name="router_profile"
                                        value="{{ old('router_profile', $package->router_profile) }}"
                                        required
                                        maxlength="100"
                                        autocomplete="off"
                                        placeholder="Example: 2hours"
                                        pattern="[A-Za-z0-9._-]+"
                                        title="Use only letters, numbers, dots, underscores and hyphens."
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        Router identifier for this package. Use a simple
                                        unique value such as
                                        <span class="form-example">1hour</span>,
                                        <span class="form-example">2hours</span> or
                                        <span class="form-example">daily-10m</span>.
                                    </span>
                                </div>

                                <div class="guidance-card">
                                    <i
                                        class="fas fa-circle-info"
                                        aria-hidden="true"
                                    ></i>

                                    <div>
                                        <strong>Package setup tip:</strong>
                                        the customer sees the package name, price,
                                        duration and speeds. The router profile is a
                                        technical identifier used to match the package
                                        with its router configuration.
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i class="fas fa-stopwatch" aria-hidden="true"></i>
                                Access Duration
                            </div>

                            <div class="form-grid">
                                <div class="duration-card">
                                    <div class="duration-header">
                                        <div>
                                            <h4>How Long Should Access Last?</h4>

                                            <p>
                                                Enter hours and minutes, or choose a
                                                common duration below.
                                            </p>
                                        </div>

                                        <span class="duration-header-icon">
                                            <i
                                                class="fas fa-hourglass-half"
                                                aria-hidden="true"
                                            ></i>
                                        </span>
                                    </div>

                                    <div class="duration-input-grid">
                                        <div class="form-field">
                                            <label
                                                for="duration_hours_ui"
                                                class="form-label"
                                            >
                                                Hours
                                            </label>

                                            <div class="duration-input-wrap">
                                                <input
                                                    id="duration_hours_ui"
                                                    type="number"
                                                    min="0"
                                                    max="87600"
                                                    step="1"
                                                    value="{{ $currentDurationHours }}"
                                                    inputmode="numeric"
                                                    placeholder="2"
                                                    class="form-control"
                                                >

                                                <span class="duration-unit">
                                                    HOURS
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-field">
                                            <label
                                                for="duration_minutes_ui"
                                                class="form-label"
                                            >
                                                Minutes
                                            </label>

                                            <div class="duration-input-wrap">
                                                <input
                                                    id="duration_minutes_ui"
                                                    type="number"
                                                    min="0"
                                                    max="59"
                                                    step="1"
                                                    value="{{ $currentRemainingMinutes }}"
                                                    inputmode="numeric"
                                                    placeholder="0"
                                                    class="form-control"
                                                >

                                                <span class="duration-unit">
                                                    MINUTES
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="duration-presets">
                                        <span class="duration-presets-label">
                                            Quick duration
                                        </span>

                                        <div class="duration-preset-list">
                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="60"
                                            >
                                                <i
                                                    class="fas fa-clock"
                                                    aria-hidden="true"
                                                ></i>
                                                1 Hour
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="120"
                                            >
                                                <i
                                                    class="fas fa-clock"
                                                    aria-hidden="true"
                                                ></i>
                                                2 Hours
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="180"
                                            >
                                                <i
                                                    class="fas fa-clock"
                                                    aria-hidden="true"
                                                ></i>
                                                3 Hours
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="360"
                                            >
                                                <i
                                                    class="fas fa-clock"
                                                    aria-hidden="true"
                                                ></i>
                                                6 Hours
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="1440"
                                            >
                                                <i
                                                    class="fas fa-calendar-day"
                                                    aria-hidden="true"
                                                ></i>
                                                1 Day
                                            </button>
                                        </div>
                                    </div>

                                    <div
                                        class="duration-summary"
                                        aria-live="polite"
                                    >
                                        Selected duration:
                                        <strong id="duration_summary_value">
                                            {{ $package->formatted_duration }}
                                        </strong>
                                    </div>
                                </div>

                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i
                                    class="fas fa-gauge-high"
                                    aria-hidden="true"
                                ></i>
                                Internet Speed Limits
                            </div>

                            <div class="form-grid">
                                <div class="form-field full">
                                    <div class="toggle-panel">
                                        <div class="toggle-copy">
                                            <strong>
                                                <i class="fas fa-infinity" aria-hidden="true"></i>
                                                Unlimited Speed
                                            </strong>

                                            <span>
                                                Enable this when the package should have no configured
                                                download or upload speed cap. Package duration is still required.
                                            </span>
                                        </div>

                                        <label class="switch" for="is_speed_unlimited">
                                            <input
                                                type="hidden"
                                                name="is_speed_unlimited"
                                                value="0"
                                            >

                                            <input
                                                id="is_speed_unlimited"
                                                type="checkbox"
                                                name="is_speed_unlimited"
                                                value="1"
                                                {{ $currentSpeedUnlimited ? 'checked' : '' }}
                                            >

                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-field">
                                    <label
                                        for="download_speed"
                                        class="form-label"
                                    >
                                        Download Speed
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="download_speed"
                                        type="text"
                                        name="download_speed"
                                        value="{{ old('download_speed', $package->download_speed) }}"
                                        required
                                        maxlength="20"
                                        autocomplete="off"
                                        placeholder="Example: 10M"
                                        pattern="[0-9]+(\.[0-9]+)?[KkMmGg]"
                                        title="Use a value such as 512K, 10M, 20M or 1G."
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        Maximum speed received by the customer.
                                        Example:
                                        <span class="form-example">10M</span>
                                        means 10 Mbps.
                                    </span>
                                </div>

                                <div class="form-field">
                                    <label
                                        for="upload_speed"
                                        class="form-label"
                                    >
                                        Upload Speed
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="upload_speed"
                                        type="text"
                                        name="upload_speed"
                                        value="{{ old('upload_speed', $package->upload_speed) }}"
                                        required
                                        maxlength="20"
                                        autocomplete="off"
                                        placeholder="Example: 5M"
                                        pattern="[0-9]+(\.[0-9]+)?[KkMmGg]"
                                        title="Use a value such as 512K, 5M, 10M or 1G."
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        Maximum speed used when the customer sends
                                        data. It may be the same as or lower than
                                        the download speed.
                                    </span>
                                </div>

                                <div class="speed-guidance">
                                    <div class="speed-guidance-title">
                                        <i
                                            class="fas fa-circle-info"
                                            aria-hidden="true"
                                        ></i>
                                        Should download and upload speeds be the same?
                                    </div>

                                    <p>
                                        Not necessarily. Use the same values for a
                                        symmetrical package, or use different download
                                        and upload limits when appropriate. If Unlimited
                                        Speed is enabled, these two limits are disabled
                                        because no application-level speed cap is stored.
                                    </p>

                                    <div class="speed-examples">
                                        <span class="speed-example">
                                            <strong>Balanced:</strong>
                                            5M ↓ / 5M ↑
                                        </span>

                                        <span class="speed-example">
                                            <strong>Balanced:</strong>
                                            10M ↓ / 10M ↑
                                        </span>

                                        <span class="speed-example">
                                            <strong>Asymmetric:</strong>
                                            10M ↓ / 5M ↑
                                        </span>

                                        <span class="speed-example">
                                            <strong>Faster:</strong>
                                            20M ↓ / 10M ↑
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i
                                    class="fas fa-file-lines"
                                    aria-hidden="true"
                                ></i>
                                Customer Description
                            </div>

                            <div class="form-grid">
                                <div class="form-field full">
                                    <label
                                        for="description"
                                        class="form-label"
                                    >
                                        Package Description
                                    </label>

                                    <textarea
                                        id="description"
                                        name="description"
                                        maxlength="2000"
                                        class="form-control"
                                        placeholder="Example: Affordable 2-hour WiFi access with reliable browsing and streaming speeds."
                                    >{{ old('description', $package->description) }}</textarea>

                                    <span class="form-help">
                                        Optional. Write a short description that helps
                                        customers understand what this package offers.
                                    </span>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i
                                    class="fas fa-toggle-on"
                                    aria-hidden="true"
                                ></i>
                                Package Availability
                            </div>

                            <div class="form-grid">
                                <div class="form-field full">
                                    <div class="toggle-panel">
                                        <div class="toggle-copy">
                                            <strong>
                                                <i
                                                    class="fas fa-circle-check"
                                                    aria-hidden="true"
                                                ></i>
                                                Active Package
                                            </strong>

                                            <span>
                                                When active, customers can see and
                                                select this package. Turn it off when
                                                you no longer want new customers to
                                                purchase it. Existing billing and
                                                subscription history is preserved.
                                            </span>
                                        </div>

                                        <label
                                            class="switch"
                                            for="is_active"
                                        >
                                            <input
                                                type="hidden"
                                                name="is_active"
                                                value="0"
                                            >

                                            <input
                                                id="is_active"
                                                type="checkbox"
                                                name="is_active"
                                                value="1"
                                                {{ $currentActive ? 'checked' : '' }}
                                            >

                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="package-form-footer">
                        <a
                            href="{{ route('admin.packages.index') }}"
                            class="form-button secondary"
                        >
                            <i
                                class="fas fa-xmark"
                                aria-hidden="true"
                            ></i>
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="form-button primary"
                        >
                            <i
                                class="fas fa-floppy-disk"
                                aria-hidden="true"
                            ></i>
                            Save Package Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('package-form');
            const hoursInput = document.getElementById('duration_hours_ui');
            const minutesInput = document.getElementById('duration_minutes_ui');
            const totalMinutesInput = document.getElementById('duration_minutes');
            const summaryValue = document.getElementById('duration_summary_value');
            const presetButtons = document.querySelectorAll('.duration-preset');
            const speedUnlimitedCheckbox =
                document.getElementById('is_speed_unlimited');
            const downloadSpeedInput =
                document.getElementById('download_speed');
            const uploadSpeedInput =
                document.getElementById('upload_speed');

            function safeInteger(value) {
                const parsed = Number.parseInt(value, 10);

                if (!Number.isFinite(parsed) || parsed < 0) {
                    return 0;
                }

                return parsed;
            }

            function formatDuration(totalMinutes) {
                if (totalMinutes <= 0) {
                    return 'Not set';
                }

                const days = Math.floor(totalMinutes / 1440);
                const remainder = totalMinutes % 1440;
                const hours = Math.floor(remainder / 60);
                const minutes = remainder % 60;
                const parts = [];

                if (days > 0) {
                    parts.push(days + ' ' + (days === 1 ? 'Day' : 'Days'));
                }

                if (hours > 0) {
                    parts.push(hours + ' ' + (hours === 1 ? 'Hour' : 'Hours'));
                }

                if (minutes > 0) {
                    parts.push(
                        minutes + ' ' + (minutes === 1 ? 'Minute' : 'Minutes')
                    );
                }

                return parts.join(' ');
            }

            function updatePresetState(totalMinutes) {
                presetButtons.forEach(function (button) {
                    const presetMinutes =
                        safeInteger(button.dataset.duration);

                    button.classList.toggle(
                        'selected',
                        totalMinutes > 0 && presetMinutes === totalMinutes
                    );
                });
            }

            function calculateDuration() {
                const hours = safeInteger(hoursInput.value);
                const minutes = safeInteger(minutesInput.value);
                const totalMinutes = (hours * 60) + minutes;

                totalMinutesInput.value =
                    totalMinutes > 0 ? String(totalMinutes) : '';

                summaryValue.textContent =
                    formatDuration(totalMinutes);

                updatePresetState(totalMinutes);
            }

            function setPreset(totalMinutes) {
                hoursInput.value =
                    String(Math.floor(totalMinutes / 60));
                minutesInput.value =
                    String(totalMinutes % 60);

                calculateDuration();
            }

            function updateSpeedState() {
                const unlimited =
                    speedUnlimitedCheckbox.checked;

                downloadSpeedInput.disabled = unlimited;
                uploadSpeedInput.disabled = unlimited;
                downloadSpeedInput.required = !unlimited;
                uploadSpeedInput.required = !unlimited;

                if (unlimited) {
                    downloadSpeedInput.setAttribute('aria-disabled', 'true');
                    uploadSpeedInput.setAttribute('aria-disabled', 'true');
                } else {
                    downloadSpeedInput.removeAttribute('aria-disabled');
                    uploadSpeedInput.removeAttribute('aria-disabled');
                }
            }

            hoursInput.addEventListener('input', calculateDuration);
            minutesInput.addEventListener('input', calculateDuration);

            presetButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setPreset(
                        safeInteger(button.dataset.duration)
                    );
                });
            });

            speedUnlimitedCheckbox.addEventListener(
                'change',
                updateSpeedState
            );

            form.addEventListener('submit', function (event) {
                calculateDuration();
                updateSpeedState();

                const totalMinutes =
                    safeInteger(totalMinutesInput.value);

                if (totalMinutes < 1) {
                    event.preventDefault();

                    summaryValue.textContent =
                        'Please select a duration of at least 1 minute.';

                    hoursInput.focus();
                }
            });

            calculateDuration();
            updateSpeedState();
        });
    </script>
</x-app-layout>