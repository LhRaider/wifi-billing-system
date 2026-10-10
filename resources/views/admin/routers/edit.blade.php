<x-app-layout>
    <x-slot name="header">
        <div class="router-edit-header">
            <div class="router-edit-header__icon">
                <i class="fas fa-pen-to-square" aria-hidden="true"></i>
            </div>

            <div>
                <h2>Edit MikroTik Router</h2>
                <p>Update this router's connection and configuration details.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .router-edit-page {
            --re-bg: #f3f6fa;
            --re-card: #ffffff;
            --re-soft: #f8fafc;
            --re-input: #ffffff;
            --re-text: #0f172a;
            --re-body: #334155;
            --re-muted: #64748b;
            --re-border: #e2e8f0;

            min-height: 100vh;
            padding: 42px 24px 60px;
            color: var(--re-text);
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, .58),
                    transparent 31%
                ),
                var(--re-bg);
        }

        html.dark .router-edit-page {
            --re-bg: #081225;
            --re-card: #0f1b31;
            --re-soft: #13213a;
            --re-input: #0b172b;
            --re-text: #e8efff;
            --re-body: #d7e1ef;
            --re-muted: #9baaca;
            --re-border: #293a56;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, .12),
                    transparent 32%
                ),
                var(--re-bg);
        }

        .router-edit-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .router-edit-header__icon {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(37, 99, 235, .22);
        }

        .router-edit-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
        }

        .router-edit-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        html.dark .router-edit-header h2 {
            color: #f8fafc !important;
        }

        html.dark .router-edit-header p {
            color: #9baaca !important;
        }

        .router-edit-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .router-edit-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .router-back-button {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 15px;
            border: 1px solid var(--re-border);
            border-radius: 11px;
            background: var(--re-card);
            color: var(--re-body) !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            transition:
                transform .18s ease,
                background .18s ease;
        }

        .router-back-button:hover {
            transform: translateY(-1px);
            background: var(--re-soft);
        }

        .router-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }

        .router-status-pill.active {
            border: 1px solid #bbf7d0;
            background: #dcfce7;
            color: #166534;
        }

        .router-status-pill.inactive {
            border: 1px solid #fecaca;
            background: #fee2e2;
            color: #991b1b;
        }

        html.dark .router-status-pill.active {
            border-color: #245f42;
            background: #153b2a;
            color: #8ce8b3;
        }

        html.dark .router-status-pill.inactive {
            border-color: #71313c;
            background: #421f28;
            color: #ffadb9;
        }

        .router-security-guide {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            margin-bottom: 20px;
            padding: 17px 18px;
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            background: #eff6ff;
        }

        .router-security-guide__icon {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            background: #dbeafe;
            color: #2563eb;
        }

        .router-security-guide strong {
            display: block;
            margin-bottom: 4px;
            color: #1e3a8a;
            font-size: 13px;
            font-weight: 800;
        }

        .router-security-guide p {
            margin: 0;
            color: #475569;
            font-size: 12px;
            line-height: 1.65;
        }

        html.dark .router-security-guide {
            border-color: #29466f;
            background: #10213c;
        }

        html.dark .router-security-guide__icon {
            background: #16294b;
            color: #93c5fd;
        }

        html.dark .router-security-guide strong {
            color: #bfdbfe !important;
        }

        html.dark .router-security-guide p {
            color: #aebed3 !important;
        }

        .router-form-card {
            overflow: hidden;
            border: 1px solid var(--re-border);
            border-radius: 20px;
            background: var(--re-card);
            box-shadow: 0 10px 30px rgba(15, 23, 42, .07);
        }

        html.dark .router-form-card {
            box-shadow: 0 12px 34px rgba(0, 0, 0, .22);
        }

        .router-form-heading {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 22px 24px;
            border-bottom: 1px solid var(--re-border);
        }

        .router-form-heading__icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #dbeafe;
            color: #2563eb;
        }

        html.dark .router-form-heading__icon {
            background: #16294b;
            color: #93c5fd;
        }

        .router-form-heading h3 {
            margin: 0;
            color: var(--re-text);
            font-size: 19px;
            font-weight: 800;
        }

        .router-form-heading p {
            margin: 4px 0 0;
            color: var(--re-muted);
            font-size: 12px;
        }

        .router-validation {
            margin: 20px 24px 0;
            padding: 15px 17px;
            border: 1px solid #fecaca;
            border-radius: 12px;
            background: #fef2f2;
            color: #991b1b;
            font-size: 12px;
            line-height: 1.6;
        }

        .router-validation strong {
            display: block;
            margin-bottom: 6px;
        }

        .router-validation ul {
            margin: 0;
            padding-left: 19px;
            list-style: disc;
        }

        html.dark .router-validation {
            border-color: #71313c;
            background: #351a24;
            color: #fecaca;
        }

        .router-form-body {
            padding: 26px 24px;
        }

        .router-form-section + .router-form-section {
            margin-top: 30px;
            padding-top: 27px;
            border-top: 1px solid var(--re-border);
        }

        .router-section-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 5px;
            color: var(--re-text);
            font-size: 15px;
            font-weight: 800;
        }

        .router-section-title i {
            color: #2563eb;
        }

        html.dark .router-section-title i {
            color: #7da7ff;
        }

        .router-section-description {
            margin: 0 0 19px;
            color: var(--re-muted);
            font-size: 11px;
            line-height: 1.6;
        }

        .router-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .router-field {
            min-width: 0;
        }

        .router-field.full {
            grid-column: 1 / -1;
        }

        .router-label {
            display: block;
            margin-bottom: 7px;
            color: var(--re-text);
            font-size: 12px;
            font-weight: 800;
        }

        .router-required {
            color: #dc2626;
        }

        html.dark .router-required {
            color: #fca5a5;
        }

        .router-control {
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px;
            outline: none;
            background: var(--re-input) !important;
            color: var(--re-text) !important;
            font-size: 13px;
            transition:
                border-color .18s ease,
                box-shadow .18s ease;
        }

        textarea.router-control {
            min-height: 105px;
            resize: vertical;
        }

        .router-control::placeholder {
            color: #94a3b8;
        }

        .router-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .13) !important;
        }

        html.dark .router-control {
            border-color: #344762 !important;
        }

        html.dark .router-control::placeholder {
            color: #71839e;
        }

        .router-help {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            margin-top: 7px;
            color: var(--re-muted);
            font-size: 10px;
            line-height: 1.55;
        }

        .router-help i {
            margin-top: 2px;
            color: #64748b;
        }

        html.dark .router-help i {
            color: #8fa1bb;
        }

        .router-password-wrap {
            position: relative;
        }

        .router-password-wrap .router-control {
            padding-right: 47px;
        }

        .router-password-toggle {
            position: absolute;
            top: 50%;
            right: 7px;
            width: 34px;
            height: 34px;
            padding: 0;
            border: 0;
            border-radius: 8px;
            transform: translateY(-50%);
            display: grid;
            place-items: center;
            background: transparent;
            color: #64748b;
            cursor: pointer;
        }

        .router-password-toggle:hover {
            background: #f1f5f9;
            color: #2563eb;
        }

        html.dark .router-password-toggle {
            color: #9baaca;
        }

        html.dark .router-password-toggle:hover {
            background: #1a2942;
            color: #93c5fd;
        }

        .router-password-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 12px;
            padding: 12px 14px;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 10px;
            line-height: 1.6;
        }

        html.dark .router-password-notice {
            border-color: #29466f;
            background: #12213a;
            color: #bfdbfe;
        }

        .router-status-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 17px 18px;
            border: 1px solid var(--re-border);
            border-radius: 13px;
            background: var(--re-soft);
        }

        .router-status-panel__text strong {
            display: block;
            margin-bottom: 4px;
            color: var(--re-text);
            font-size: 13px;
        }

        .router-status-panel__text span {
            display: block;
            max-width: 570px;
            color: var(--re-muted);
            font-size: 10px;
            line-height: 1.6;
        }

        .router-switch {
            position: relative;
            width: 48px;
            height: 27px;
            flex: 0 0 48px;
            display: inline-block;
        }

        .router-switch input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
        }

        .router-switch-slider {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: #cbd5e1;
            cursor: pointer;
            transition: background .2s ease;
        }

        .router-switch-slider::before {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            width: 19px;
            height: 19px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(15, 23, 42, .2);
            transition: transform .2s ease;
        }

        .router-switch input:checked + .router-switch-slider {
            background: #2563eb;
        }

        .router-switch input:checked + .router-switch-slider::before {
            transform: translateX(21px);
        }

        .router-switch input:focus-visible + .router-switch-slider {
            outline: 3px solid rgba(59, 130, 246, .25);
            outline-offset: 2px;
        }

        html.dark .router-switch-slider {
            background: #475569;
        }

        .router-warning {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 12px;
            padding: 12px 14px;
            border: 1px solid #fde68a;
            border-radius: 10px;
            background: #fffbeb;
            color: #92400e;
            font-size: 10px;
            line-height: 1.6;
        }

        html.dark .router-warning {
            border-color: #6b5518;
            background: #30270f;
            color: #f9dc82;
        }

        .router-form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 20px 24px;
            border-top: 1px solid var(--re-border);
            background: var(--re-soft);
        }

        .router-footer-note {
            display: flex;
            align-items: flex-start;
            gap: 7px;
            max-width: 430px;
            color: var(--re-muted);
            font-size: 10px;
            line-height: 1.55;
        }

        .router-footer-note i {
            margin-top: 2px;
            color: #64748b;
        }

        .router-form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 9px;
            flex-wrap: wrap;
        }

        .router-button {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 17px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            transition:
                transform .18s ease,
                background .18s ease;
        }

        .router-button:hover {
            transform: translateY(-1px);
        }

        .router-button.cancel {
            border: 1px solid var(--re-border);
            background: var(--re-card);
            color: var(--re-body) !important;
        }

        .router-button.cancel:hover {
            background: var(--re-soft);
        }

        .router-button.save {
            border: 0;
            background: #2563eb;
            color: #ffffff !important;
            box-shadow: 0 7px 18px rgba(37, 99, 235, .18);
        }

        .router-button.save:hover {
            background: #1d4ed8;
        }

        @media (max-width: 767px) {
            .router-edit-page {
                padding: 28px 14px 45px;
            }

            .router-edit-header h2 {
                font-size: 20px;
            }

            .router-edit-header p {
                display: none;
            }

            .router-form-grid {
                grid-template-columns: 1fr;
            }

            .router-field.full {
                grid-column: auto;
            }

            .router-form-heading,
            .router-form-body,
            .router-form-footer {
                padding-left: 18px;
                padding-right: 18px;
            }

            .router-form-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .router-footer-note {
                max-width: none;
            }

            .router-form-actions {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .router-edit-page {
                padding-left: 10px;
                padding-right: 10px;
            }

            .router-edit-header__icon {
                width: 42px;
                height: 42px;
                flex-basis: 42px;
                border-radius: 12px;
            }

            .router-edit-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .router-back-button {
                width: 100%;
            }

            .router-status-pill {
                align-self: flex-start;
            }

            .router-form-card {
                border-radius: 16px;
            }

            .router-status-panel {
                align-items: flex-start;
            }

            .router-form-actions {
                flex-direction: column-reverse;
            }

            .router-form-actions .router-button {
                width: 100%;
            }
        }
    </style>

    <div class="router-edit-page">
        <div class="router-edit-container">

            <div class="router-edit-toolbar">
                <a
                    href="{{ route('admin.routers.index') }}"
                    class="router-back-button"
                >
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Back to MikroTik Routers
                </a>

                @if($router->is_active)
                    <span class="router-status-pill active">
                        <i class="fas fa-circle-check" aria-hidden="true"></i>
                        Currently Active
                    </span>
                @else
                    <span class="router-status-pill inactive">
                        <i class="fas fa-circle-xmark" aria-hidden="true"></i>
                        Currently Inactive
                    </span>
                @endif
            </div>

            <div class="router-security-guide">
                <div class="router-security-guide__icon">
                    <i class="fas fa-shield-halved" aria-hidden="true"></i>
                </div>

                <div>
                    <strong>Router credentials remain protected</strong>

                    <p>
                        The saved MikroTik password is never displayed on this page.
                        Leave the password field blank unless you need to replace the
                        router's existing API password.
                    </p>
                </div>
            </div>

            <div class="router-form-card">

                <div class="router-form-heading">
                    <div class="router-form-heading__icon">
                        <i class="fas fa-network-wired" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h3>{{ $router->name }}</h3>
                        <p>
                            Update this router's connection, credentials and availability.
                        </p>
                    </div>
                </div>

                @if($errors->any())
                    <div
                        class="router-validation"
                        role="alert"
                    >
                        <strong>Please correct the following:</strong>

                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('admin.routers.update', $router) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="router-form-body">

                        <section class="router-form-section">
                            <h4 class="router-section-title">
                                <i class="fas fa-circle-info" aria-hidden="true"></i>
                                Router Information
                            </h4>

                            <p class="router-section-description">
                                Update the router name and the network address used by
                                the billing server.
                            </p>

                            <div class="router-form-grid">
                                <div class="router-field">
                                    <label
                                        for="name"
                                        class="router-label"
                                    >
                                        Router Name
                                        <span class="router-required">*</span>
                                    </label>

                                    <input
                                        id="name"
                                        type="text"
                                        name="name"
                                        value="{{ old('name', $router->name) }}"
                                        maxlength="100"
                                        required
                                        autocomplete="off"
                                        placeholder="Main Office Router"
                                        class="router-control"
                                    >

                                    <div class="router-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>

                                        <span>
                                            Letters, numbers, spaces, dots, underscores
                                            and hyphens are allowed.
                                        </span>
                                    </div>
                                </div>

                                <div class="router-field">
                                    <label
                                        for="ip_address"
                                        class="router-label"
                                    >
                                        IP Address
                                        <span class="router-required">*</span>
                                    </label>

                                    <input
                                        id="ip_address"
                                        type="text"
                                        name="ip_address"
                                        value="{{ old('ip_address', $router->ip_address) }}"
                                        required
                                        autocomplete="off"
                                        spellcheck="false"
                                        placeholder="192.168.88.1"
                                        class="router-control"
                                    >

                                    <div class="router-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>

                                        <span>
                                            This address must be reachable from the
                                            billing server.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="router-form-section">
                            <h4 class="router-section-title">
                                <i class="fas fa-key" aria-hidden="true"></i>
                                API Credentials
                            </h4>

                            <p class="router-section-description">
                                Update the RouterOS account used for billing-system
                                communication.
                            </p>

                            <div class="router-form-grid">
                                <div class="router-field">
                                    <label
                                        for="username"
                                        class="router-label"
                                    >
                                        MikroTik Username
                                        <span class="router-required">*</span>
                                    </label>

                                    <input
                                        id="username"
                                        type="text"
                                        name="username"
                                        value="{{ old('username', $router->username) }}"
                                        maxlength="255"
                                        required
                                        autocomplete="off"
                                        spellcheck="false"
                                        placeholder="billing-api"
                                        class="router-control"
                                    >

                                    <div class="router-help">
                                        <i class="fas fa-user-shield" aria-hidden="true"></i>

                                        <span>
                                            A dedicated RouterOS API account is recommended.
                                        </span>
                                    </div>
                                </div>

                                <div class="router-field">
                                    <label
                                        for="password"
                                        class="router-label"
                                    >
                                        New MikroTik Password
                                    </label>

                                    <div class="router-password-wrap">
                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            maxlength="255"
                                            autocomplete="new-password"
                                            spellcheck="false"
                                            placeholder="Leave blank to keep current password"
                                            class="router-control"
                                        >

                                        <button
                                            type="button"
                                            class="router-password-toggle"
                                            id="passwordToggle"
                                            aria-label="Show new MikroTik password"
                                            aria-pressed="false"
                                        >
                                            <i
                                                class="fas fa-eye"
                                                id="passwordToggleIcon"
                                                aria-hidden="true"
                                            ></i>
                                        </button>
                                    </div>

                                    <div class="router-help">
                                        <i class="fas fa-lock" aria-hidden="true"></i>

                                        <span>
                                            Leave blank to keep the existing encrypted
                                            password.
                                        </span>
                                    </div>

                                    <div class="router-password-notice">
                                        <i class="fas fa-shield-halved" aria-hidden="true"></i>

                                        <span>
                                            The current password is intentionally never
                                            sent to or rendered in this form.
                                        </span>
                                    </div>
                                </div>

                                <div class="router-field">
                                    <label
                                        for="api_port"
                                        class="router-label"
                                    >
                                        API Port
                                        <span class="router-required">*</span>
                                    </label>

                                    <input
                                        id="api_port"
                                        type="number"
                                        name="api_port"
                                        value="{{ old('api_port', $router->api_port) }}"
                                        min="1"
                                        max="65535"
                                        required
                                        inputmode="numeric"
                                        class="router-control"
                                    >

                                    <div class="router-help">
                                        <i class="fas fa-plug" aria-hidden="true"></i>

                                        <span>
                                            Standard RouterOS API uses port 8728 unless
                                            you configured a different API port.
                                        </span>
                                    </div>
                                </div>

                                <div class="router-field">
                                    <label
                                        for="router_type"
                                        class="router-label"
                                    >
                                        Router Type
                                        <span class="router-required">*</span>
                                    </label>

                                    <select
                                        id="router_type"
                                        name="router_type"
                                        required
                                        class="router-control"
                                    >
                                        <option
                                            value="MikroTik"
                                            {{ old('router_type', $router->router_type) === 'MikroTik' ? 'selected' : '' }}
                                        >
                                            MikroTik
                                        </option>
                                    </select>

                                    <div class="router-help">
                                        <i class="fas fa-network-wired" aria-hidden="true"></i>

                                        <span>
                                            MikroTik RouterOS is currently the supported
                                            network platform.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="router-form-section">
                            <h4 class="router-section-title">
                                <i class="fas fa-note-sticky" aria-hidden="true"></i>
                                Internal Notes
                            </h4>

                            <p class="router-section-description">
                                Optional information to help identify or manage this router.
                            </p>

                            <div class="router-field full">
                                <label
                                    for="notes"
                                    class="router-label"
                                >
                                    Notes
                                </label>

                                <textarea
                                    id="notes"
                                    name="notes"
                                    maxlength="2000"
                                    rows="4"
                                    placeholder="Optional router location or internal notes"
                                    class="router-control"
                                >{{ old('notes', $router->notes) }}</textarea>

                                <div class="router-help">
                                    <i class="fas fa-circle-info" aria-hidden="true"></i>

                                    <span>
                                        Do not place passwords, API keys, or other secrets
                                        in the notes field.
                                    </span>
                                </div>
                            </div>
                        </section>

                        <section class="router-form-section">
                            <h4 class="router-section-title">
                                <i class="fas fa-power-off" aria-hidden="true"></i>
                                Router Availability
                            </h4>

                            <p class="router-section-description">
                                Control whether this router can be used for customer
                                provisioning and synchronization.
                            </p>

                            <div class="router-status-panel">
                                <div class="router-status-panel__text">
                                    <strong>Active Router</strong>

                                    <span>
                                        When enabled, this router may be used for eligible
                                        customer provisioning and synchronization.
                                    </span>
                                </div>

                                <label
                                    class="router-switch"
                                    for="is_active"
                                    aria-label="Set router active status"
                                >
                                    <input
                                        id="is_active"
                                        type="checkbox"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', $router->is_active) ? 'checked' : '' }}
                                    >

                                    <span class="router-switch-slider"></span>
                                </label>
                            </div>

                            <div class="router-warning">
                                <i
                                    class="fas fa-triangle-exclamation"
                                    aria-hidden="true"
                                ></i>

                                <span>
                                    Deactivating this router prevents it from being used
                                    to grant or synchronize new customer access. It does
                                    not automatically disconnect every customer who may
                                    already have an active session on the physical router.
                                </span>
                            </div>
                        </section>

                    </div>

                    <div class="router-form-footer">
                        <div class="router-footer-note">
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>

                            <span>
                                Saving changes does not reveal the existing router password.
                            </span>
                        </div>

                        <div class="router-form-actions">
                            <a
                                href="{{ route('admin.routers.index') }}"
                                class="router-button cancel"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="router-button save"
                            >
                                <i class="fas fa-floppy-disk" aria-hidden="true"></i>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordToggleIcon = document.getElementById('passwordToggleIcon');

            if (!passwordInput || !passwordToggle || !passwordToggleIcon) {
                return;
            }

            passwordToggle.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';

                passwordInput.type = isHidden ? 'text' : 'password';

                passwordToggle.setAttribute(
                    'aria-pressed',
                    isHidden ? 'true' : 'false'
                );

                passwordToggle.setAttribute(
                    'aria-label',
                    isHidden
                        ? 'Hide new MikroTik password'
                        : 'Show new MikroTik password'
                );

                passwordToggleIcon.classList.toggle('fa-eye', !isHidden);
                passwordToggleIcon.classList.toggle('fa-eye-slash', isHidden);

                passwordInput.focus();
            });
        });
    </script>
</x-app-layout>