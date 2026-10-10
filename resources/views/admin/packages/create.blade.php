<x-app-layout>
    <x-slot name="header">
        <div class="package-page-header">
            <div class="package-page-heading">
                <span class="package-page-heading-icon">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                </span>

                <div>
                    <h2>Add Internet Package</h2>
                    <p>Create a new internet package for your customers and MikroTik router.</p>
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
        $oldDurationMinutes = (int) old('duration_minutes', 0);
        $oldDurationHours = intdiv($oldDurationMinutes, 60);
        $oldRemainingMinutes = $oldDurationMinutes % 60;
        $oldSpeedUnlimited = old('is_speed_unlimited', '0') == '1';
        $oldActive = old('is_active', '1') == '1';
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
            min-height: 44px;
            padding: 10px 16px;
            border: 1px solid #dbe3ed;
            border-radius: 13px;
            background: #ffffff;
            color: #334155;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            transition: 0.2s ease;
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
                    rgba(219, 234, 254, 0.68),
                    transparent 30%
                ),
                #f4f7fb;
        }

        .package-edit-container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .admin-guide {
            margin-bottom: 20px;
            padding: 18px 20px;
            border: 1px solid #bfdbfe;
            border-radius: 18px;
            background: linear-gradient(135deg, #eff6ff, #f8fbff);
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .admin-guide-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: #2563eb;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .admin-guide strong {
            display: block;
            margin-bottom: 4px;
            color: #1e3a8a;
            font-size: 13px;
        }

        .admin-guide p {
            margin: 0;
            color: #475569;
            font-size: 12px;
            line-height: 1.65;
        }

        .validation-alert {
            margin-bottom: 20px;
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
            margin-bottom: 34px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .form-section-heading {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 5px;
            color: #0f172a;
            font-size: 15px;
            font-weight: 900;
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

        .section-description {
            margin: 0 0 18px 43px;
            color: #64748b;
            font-size: 11px;
            line-height: 1.55;
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
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 8px;
            color: #334155;
            font-size: 13px;
            font-weight: 800;
        }

        .form-label i {
            width: 16px;
            color: #64748b;
            text-align: center;
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
            min-height: 105px;
            resize: vertical;
        }

        .form-control::placeholder {
            color: #94a3b8;
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
            display: flex;
            align-items: flex-start;
            gap: 6px;
            margin-top: 7px;
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }

        .form-help i {
            margin-top: 2px;
            color: #94a3b8;
        }

        .example-box {
            grid-column: 1 / -1;
            padding: 13px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            background: #f8fafc;
            color: #475569;
            font-size: 11px;
            line-height: 1.6;
        }

        .example-box i {
            margin-right: 6px;
            color: #2563eb;
        }

        .example-box strong {
            color: #334155;
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
            line-height: 1.55;
        }

        .duration-main-icon {
            width: 39px;
            height: 39px;
            border-radius: 11px;
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
            padding-right: 75px;
        }

        .duration-unit {
            position: absolute;
            top: 50%;
            right: 13px;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 10px;
            font-weight: 900;
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
            font-weight: 800;
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
            transition: 0.2s ease;
        }

        .duration-preset:hover,
        .duration-preset.selected {
            border-color: #93c5fd;
            background: #dbeafe;
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

        .duration-summary i {
            margin-right: 6px;
            color: #2563eb;
        }

        .duration-summary strong {
            color: #2563eb;
        }

        .speed-guide {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .speed-guide-card {
            padding: 13px;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            background: #f8fafc;
        }

        .speed-guide-card strong {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 4px;
            color: #334155;
            font-size: 11px;
        }

        .speed-guide-card strong i {
            color: #2563eb;
        }

        .speed-guide-card span {
            color: #64748b;
            font-size: 10px;
            line-height: 1.5;
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

        .toggle-panel + .toggle-panel {
            margin-top: 13px;
        }

        .toggle-copy {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 0;
        }

        .toggle-icon {
            width: 22px;
            height: 22px;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 15px;
            box-shadow: none;
        }

        .toggle-icon i {
            display: block;
            margin: 0;
            line-height: 1;
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
            line-height: 1.5;
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
            justify-content: space-between;
            gap: 15px;
        }

        .footer-note {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #64748b;
            font-size: 10px;
            line-height: 1.5;
        }

        .footer-note i {
            color: #2563eb;
        }

        .footer-actions {
            display: flex;
            gap: 10px;
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


        /* Final ISP Admin dark-mode treatment for Package Create. */
        html.dark .package-page-heading h2 {
            color: #f8fafc !important;
        }

        html.dark .package-page-heading p {
            color: #94a3b8 !important;
        }

        html.dark .package-back-button {
            border-color: #33445f;
            background: #16233a;
            color: #e2e8f0 !important;
        }

        html.dark .package-back-button:hover {
            border-color: #475b78;
            background: #1b2b45;
        }

        html.dark .package-edit-page {
            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, 0.12),
                    transparent 31%
                ),
                #091226;
        }

        html.dark .admin-guide {
            border-color: #29466f;
            background: linear-gradient(135deg, #10213c, #101b31);
        }

        html.dark .admin-guide strong {
            color: #bfdbfe !important;
        }

        html.dark .admin-guide p {
            color: #aebed3 !important;
        }

        html.dark .validation-alert {
            border-color: #71313b;
            background: #351a24;
            color: #fecaca;
        }

        html.dark .validation-alert-icon {
            background: #4a2029;
            color: #fca5a5;
        }

        html.dark .validation-alert strong,
        html.dark .validation-alert li {
            color: #fecaca !important;
        }

        html.dark .package-form-card {
            border-color: #293a56;
            background: #101b31;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.22);
        }

        html.dark .package-form-intro {
            border-color: #293a56;
            background: #101b31;
        }

        html.dark .package-form-intro-icon {
            background: #16294b;
            color: #93c5fd;
        }

        html.dark .package-form-intro h3,
        html.dark .form-section-heading,
        html.dark .duration-header h4,
        html.dark .toggle-copy strong {
            color: #f1f5f9 !important;
        }

        html.dark .package-form-intro p,
        html.dark .section-description,
        html.dark .duration-header p,
        html.dark .toggle-copy span {
            color: #94a3b8 !important;
        }

        html.dark .form-section-heading i {
            background: #16294b;
            color: #93c5fd;
        }

        html.dark .form-label {
            color: #dce6f7 !important;
        }

        html.dark .form-label i {
            color: #9fb0c8;
        }

        html.dark .form-required {
            color: #fca5a5 !important;
        }

        html.dark .form-control {
            border-color: #344762 !important;
            background: #0b1629 !important;
            color: #edf3ff !important;
        }

        html.dark .form-control::placeholder {
            color: #71839e !important;
        }

        html.dark .form-control:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15) !important;
        }

        html.dark .form-control:disabled {
            border-color: #2b3b55 !important;
            background: #152138 !important;
            color: #899ab4 !important;
        }

        html.dark .form-help {
            color: #9fb0c8 !important;
        }

        html.dark .form-help i {
            color: #71839e !important;
        }

        html.dark .example-box,
        html.dark .speed-guide-card,
        html.dark .toggle-panel {
            border-color: #293a56;
            background: #14213a;
            color: #cbd5e1;
        }

        html.dark .example-box {
            color: #aebed3;
        }

        html.dark .example-box strong,
        html.dark .speed-guide-card strong {
            color: #dce6f5 !important;
        }

        html.dark .speed-guide-card span {
            color: #9fb0c8 !important;
        }

        html.dark .duration-card {
            border-color: #29466f;
            background: #12213a;
        }

        html.dark .duration-main-icon {
            background: #16294b;
            color: #93c5fd;
        }

        html.dark .duration-unit,
        html.dark .duration-presets-label {
            color: #9fb0c8 !important;
        }

        html.dark .duration-preset {
            border-color: #344762;
            background: #16233a;
            color: #cbd5e1;
        }

        html.dark .duration-preset:hover,
        html.dark .duration-preset.selected {
            border-color: #3b82f6;
            background: #173466;
            color: #bfdbfe;
        }

        html.dark .duration-summary {
            border-color: #344762;
            background: #0f1b30;
            color: #aebed3;
        }

        html.dark .duration-summary strong {
            color: #93c5fd !important;
        }

        html.dark .toggle-icon {
            border: 0;
            background: transparent;
            color: #60a5fa;
            box-shadow: none;
        }

        html.dark .switch-slider {
            background: #475569;
        }

        html.dark .switch input:checked + .switch-slider {
            background: #2563eb;
        }

        html.dark .package-form-footer {
            border-color: #293a56;
            background: #0e192d;
        }

        html.dark .footer-note {
            color: #94a3b8 !important;
        }

        html.dark .form-button.secondary {
            border-color: #344762;
            background: #16233a;
            color: #dce6f5 !important;
        }

        html.dark .form-button.secondary:hover {
            background: #1b2b45;
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
            .duration-input-grid,
            .speed-guide {
                grid-template-columns: 1fr;
            }

            .duration-card,
            .example-box,
            .speed-guide {
                grid-column: auto;
            }

            .section-description {
                margin-left: 0;
            }

            .package-form-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .footer-actions {
                justify-content: flex-end;
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
                flex-direction: column;
            }

            .footer-actions {
                width: 100%;
                flex-direction: column-reverse;
            }

            .form-button {
                width: 100%;
            }

            .duration-header {
                flex-direction: column;
            }

            .toggle-panel {
                align-items: flex-start;
            }
        }
    </style>

    <div class="package-edit-page">
        <div class="package-edit-container">

            <div class="admin-guide">
                <div class="admin-guide-icon">
                    <i class="fas fa-lightbulb" aria-hidden="true"></i>
                </div>

                <div>
                    <strong>Package creation guide</strong>
                    <p>
                        Choose the package name, customer price, access duration and speed
                        settings before saving. Router Profile is the unique profile
                        name used when synchronizing this package with your router.
                    </p>
                </div>
            </div>

            @if($errors->any())
                <div class="validation-alert" role="alert">
                    <div class="validation-alert-icon">
                        <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
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
                action="{{ route('admin.packages.store') }}"
                id="package-form"
            >
                @csrf
                <input
                    type="hidden"
                    name="duration_minutes"
                    id="duration_minutes"
                    value="{{ old('duration_minutes', '') }}"
                >

                <div class="package-form-card">
                    <div class="package-form-intro">
                        <div class="package-form-intro-icon">
                            <i class="fas fa-plus" aria-hidden="true"></i>
                        </div>

                        <div>
                            <h3>Create a New Package</h3>
                            <p>
                                Configure the package carefully. Required fields are marked with an asterisk.
                            </p>
                        </div>
                    </div>

                    <div class="package-form-body">

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i class="fas fa-tag" aria-hidden="true"></i>
                                Package Details
                            </div>

                            <p class="section-description">
                                These details identify the package to customers
                                and determine how it is represented on the router.
                            </p>

                            <div class="form-grid">
                                <div class="form-field">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-signature" aria-hidden="true"></i>
                                        Package Name
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        required
                                        maxlength="100"
                                        autocomplete="off"
                                        placeholder="Example: 2 Hours"
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                                        Use a simple name customers will immediately understand.
                                    </span>
                                </div>

                                <div class="form-field">
                                    <label for="price" class="form-label">
                                        <i class="fas fa-money-bill-wave" aria-hidden="true"></i>
                                        Customer Price (KES)
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="price"
                                        type="number"
                                        name="price"
                                        value="{{ old('price') }}"
                                        required
                                        min="0"
                                        max="99999999.99"
                                        step="0.01"
                                        inputmode="decimal"
                                        placeholder="Example: 20"
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                                        Enter the full amount the customer pays for one activation.
                                    </span>
                                </div>

                                <div class="form-field">
                                    <label for="package_type" class="form-label">
                                        <i class="fas fa-plug" aria-hidden="true"></i>
                                        Connection Type
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
                                            {{ old('package_type', 'hotspot') === 'hotspot' ? 'selected' : '' }}
                                        >
                                            Hotspot
                                        </option>

                                        <option
                                            value="pppoe"
                                            {{ old('package_type', 'hotspot') === 'pppoe' ? 'selected' : '' }}
                                        >
                                            PPPoE
                                        </option>
                                    </select>

                                    <span class="form-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                                        Hotspot is normally used for WiFi login access.
                                        PPPoE is used for PPPoE username/password connections.
                                    </span>
                                </div>

                                <div class="form-field">
                                    <label for="router_profile" class="form-label">
                                        <i class="fas fa-router" aria-hidden="true"></i>
                                        Router Profile Name
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="router_profile"
                                        type="text"
                                        name="router_profile"
                                        value="{{ old('router_profile') }}"
                                        required
                                        maxlength="100"
                                        autocomplete="off"
                                        placeholder="Example: 2hours-5mbps"
                                        pattern="[A-Za-z0-9._-]+"
                                        title="Use only letters, numbers, dots, underscores and hyphens."
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                                        Use a unique technical name without spaces.
                                        Allowed: letters, numbers, dots, underscores and hyphens.
                                    </span>
                                </div>

                                <div class="example-box">
                                    <i class="fas fa-wand-magic-sparkles" aria-hidden="true"></i>
                                    <strong>Example:</strong>
                                    a customer package named <strong>2 Hours</strong>
                                    could cost <strong>KES 20</strong> and use router
                                    profile <strong>2hours-5mbps</strong>.
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i class="fas fa-stopwatch" aria-hidden="true"></i>
                                Access Duration
                            </div>

                            <p class="section-description">
                                Set exactly how long the customer's internet access
                                should remain valid after activation.
                            </p>

                            <div class="form-grid">
                                <div class="duration-card">
                                    <div class="duration-header">
                                        <div>
                                            <h4>How long should this package last?</h4>

                                            <p>
                                                Enter hours and minutes manually or use
                                                one of the quick options below.
                                            </p>
                                        </div>

                                        <span class="duration-main-icon">
                                            <i class="fas fa-hourglass-half" aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <div class="duration-input-grid">
                                        <div class="form-field">
                                            <label for="duration_hours_ui" class="form-label">
                                                <i class="fas fa-clock" aria-hidden="true"></i>
                                                Hours
                                            </label>

                                            <div class="duration-input-wrap">
                                                <input
                                                    id="duration_hours_ui"
                                                    type="number"
                                                    min="0"
                                                    max="87600"
                                                    step="1"
                                                    value="{{ $oldDurationHours }}"
                                                    inputmode="numeric"
                                                    placeholder="0"
                                                    class="form-control"
                                                >

                                                <span class="duration-unit">
                                                    HOURS
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-field">
                                            <label for="duration_minutes_ui" class="form-label">
                                                <i class="fas fa-stopwatch" aria-hidden="true"></i>
                                                Minutes
                                            </label>

                                            <div class="duration-input-wrap">
                                                <input
                                                    id="duration_minutes_ui"
                                                    type="number"
                                                    min="0"
                                                    max="59"
                                                    step="1"
                                                    value="{{ $oldRemainingMinutes }}"
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
                                            <i class="fas fa-bolt" aria-hidden="true"></i>
                                            Quick duration
                                        </span>

                                        <div class="duration-preset-list">
                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="30"
                                            >
                                                30 Minutes
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="60"
                                            >
                                                1 Hour
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="90"
                                            >
                                                1 Hour 30 Minutes
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="120"
                                            >
                                                2 Hours
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="180"
                                            >
                                                3 Hours
                                            </button>

                                            <button
                                                type="button"
                                                class="duration-preset"
                                                data-duration="1440"
                                            >
                                                1 Day
                                            </button>
                                        </div>
                                    </div>

                                    <div
                                        id="duration_summary"
                                        class="duration-summary"
                                        aria-live="polite"
                                    >
                                        <i class="fas fa-calendar-check" aria-hidden="true"></i>
                                        Selected duration:
                                        <strong id="duration_summary_value">
                                            Not set
                                        </strong>
                                    </div>
                                </div>

                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i class="fas fa-gauge-high" aria-hidden="true"></i>
                                Speed Limits
                            </div>

                            <p class="section-description">
                                Set download and upload limits, or enable Unlimited Speed when no speed cap should apply.
                            </p>

                            <div class="form-grid">
                                <div class="form-field full">
                                    <div class="toggle-panel">
                                        <div class="toggle-copy">
                                            <span class="toggle-icon">
                                                <i class="fas fa-infinity" aria-hidden="true"></i>
                                            </span>

                                            <div>
                                                <strong>Unlimited Speed</strong>
                                                <span>
                                                    Turn this on when the package should have no configured
                                                    download or upload speed cap. Package duration still applies.
                                                </span>
                                            </div>
                                        </div>

                                        <label class="switch" for="is_speed_unlimited">
                                            <input type="hidden" name="is_speed_unlimited" value="0">
                                            <input
                                                id="is_speed_unlimited"
                                                type="checkbox"
                                                name="is_speed_unlimited"
                                                value="1"
                                                {{ $oldSpeedUnlimited ? 'checked' : '' }}
                                            >
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-field">
                                    <label for="download_speed" class="form-label">
                                        <i class="fas fa-download" aria-hidden="true"></i>
                                        Download Speed
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="download_speed"
                                        type="text"
                                        name="download_speed"
                                        value="{{ old('download_speed') }}"
                                        required
                                        maxlength="20"
                                        autocomplete="off"
                                        placeholder="Example: 5M"
                                        pattern="[0-9]+(\.[0-9]+)?[KkMmGg]"
                                        title="Use a value such as 512K, 5M, 10M or 1G."
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                                        Maximum speed for browsing, streaming and downloads.
                                        Example: 5M.
                                    </span>
                                </div>

                                <div class="form-field">
                                    <label for="upload_speed" class="form-label">
                                        <i class="fas fa-upload" aria-hidden="true"></i>
                                        Upload Speed
                                        <span class="form-required">*</span>
                                    </label>

                                    <input
                                        id="upload_speed"
                                        type="text"
                                        name="upload_speed"
                                        value="{{ old('upload_speed') }}"
                                        required
                                        maxlength="20"
                                        autocomplete="off"
                                        placeholder="Example: 5M"
                                        pattern="[0-9]+(\.[0-9]+)?[KkMmGg]"
                                        title="Use a value such as 512K, 5M, 10M or 1G."
                                        class="form-control"
                                    >

                                    <span class="form-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                                        Maximum speed for sending files, calls and uploads.
                                        Example: 5M.
                                    </span>
                                </div>

                                <div class="speed-guide">
                                    <div class="speed-guide-card">
                                        <strong>
                                            <i class="fas fa-equals" aria-hidden="true"></i>
                                            Equal speeds
                                        </strong>

                                        <span>
                                            5M download + 5M upload is a simple
                                            symmetrical package and is perfectly valid.
                                        </span>
                                    </div>

                                    <div class="speed-guide-card">
                                        <strong>
                                            <i class="fas fa-arrow-down-long" aria-hidden="true"></i>
                                            Higher download
                                        </strong>

                                        <span>
                                            10M download + 5M upload is also valid
                                            when customers need more download capacity.
                                        </span>
                                    </div>

                                    <div class="speed-guide-card">
                                        <strong>
                                            <i class="fas fa-ruler" aria-hidden="true"></i>
                                            Format
                                        </strong>

                                        <span>
                                            Use K, M or G, for example 512K, 5M,
                                            20M or 1G. Do not type “Mbps”.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="form-section">
                            <div class="form-section-heading">
                                <i class="fas fa-sliders" aria-hidden="true"></i>
                                Customer Display & Availability
                            </div>

                            <p class="section-description">
                                Add an optional customer-facing description and
                                decide whether the package should be available now.
                            </p>

                            <div class="form-grid">
                                <div class="form-field full">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left" aria-hidden="true"></i>
                                        Customer Description
                                    </label>

                                    <textarea
                                        id="description"
                                        name="description"
                                        maxlength="2000"
                                        placeholder="Example: Affordable 2 hour WiFi access for browsing, social media and streaming."
                                        class="form-control"
                                    >{{ old('description') }}</textarea>

                                    <span class="form-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>
                                        Optional. Keep it short and useful because
                                        customers may see this description when choosing a package.
                                    </span>
                                </div>

                                <div class="form-field full">
                                    <div class="toggle-panel">
                                        <div class="toggle-copy">
                                            <span class="toggle-icon">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </span>

                                            <div>
                                                <strong>Make Package Active</strong>

                                                <span>
                                                    Active packages are offered to
                                                    customers. Turn this off if you
                                                    want to finish configuring the
                                                    package before publishing it.
                                                </span>
                                            </div>
                                        </div>

                                        <label class="switch" for="is_active">
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
                                                {{ $oldActive ? 'checked' : '' }}
                                            >

                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="package-form-footer">
                        <div class="footer-note">
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>
                            Creating this package adds it only to your ISP account and does not expose another ISP Admin's records.
                        </div>

                        <div class="footer-actions">
                            <a
                                href="{{ route('admin.packages.index') }}"
                                class="form-button secondary"
                            >
                                <i class="fas fa-xmark" aria-hidden="true"></i>
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="form-button primary"
                            >
                                <i class="fas fa-floppy-disk" aria-hidden="true"></i>
                                Create Package
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('package-form');
            const speedUnlimitedCheckbox = document.getElementById('is_speed_unlimited');
            const downloadSpeedInput = document.getElementById('download_speed');
            const uploadSpeedInput = document.getElementById('upload_speed');
            const hoursInput = document.getElementById('duration_hours_ui');
            const minutesInput = document.getElementById('duration_minutes_ui');
            const totalMinutesInput = document.getElementById('duration_minutes');
            const summaryValue = document.getElementById('duration_summary_value');
            const presetButtons = document.querySelectorAll('.duration-preset');

            function safeInteger(value) {
                const parsed = Number.parseInt(value, 10);
                return Number.isFinite(parsed) && parsed >= 0 ? parsed : 0;
            }

            function formatDuration(totalMinutes) {
                if (totalMinutes <= 0) {
                    return 'Not set';
                }

                const days = Math.floor(totalMinutes / 1440);
                let remainder = totalMinutes % 1440;
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
                    parts.push(minutes + ' ' + (minutes === 1 ? 'Minute' : 'Minutes'));
                }

                return parts.join(' ');
            }

            function updatePresetState(totalMinutes) {
                presetButtons.forEach(function (button) {
                    const presetMinutes = safeInteger(button.dataset.duration);
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

                totalMinutesInput.value = totalMinutes > 0 ? String(totalMinutes) : '';
                summaryValue.textContent = formatDuration(totalMinutes);
                updatePresetState(totalMinutes);
            }

            function setPreset(totalMinutes) {
                hoursInput.value = String(Math.floor(totalMinutes / 60));
                minutesInput.value = String(totalMinutes % 60);
                calculateDuration();
            }

            function updateSpeedFields() {
                const unlimited = speedUnlimitedCheckbox.checked;

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
            speedUnlimitedCheckbox.addEventListener('change', updateSpeedFields);

            presetButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setPreset(safeInteger(button.dataset.duration));
                });
            });

            form.addEventListener('submit', function (event) {
                calculateDuration();
                updateSpeedFields();

                const totalMinutes = safeInteger(totalMinutesInput.value);

                if (totalMinutes < 1) {
                    event.preventDefault();
                    summaryValue.textContent =
                        'Please select a duration of at least 1 minute.';
                    hoursInput.focus();
                }
            });

            calculateDuration();
            updateSpeedFields();
        });
    </script>
</x-app-layout>