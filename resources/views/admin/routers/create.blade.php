<x-app-layout>
    <x-slot name="header">
        <div class="router-create-header">
            <div class="router-create-header__icon">
                <i class="fas fa-router" aria-hidden="true"></i>
            </div>

            <div>
                <h2>Add MikroTik Router</h2>
                <p>Connect a MikroTik router to your ISP billing account.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .router-create-page {
            --rc-bg: #f3f6fa;
            --rc-card: #ffffff;
            --rc-soft: #f8fafc;
            --rc-input: #ffffff;
            --rc-text: #0f172a;
            --rc-body: #334155;
            --rc-muted: #64748b;
            --rc-border: #e2e8f0;
            --rc-blue: #2563eb;

            min-height: 100vh;
            padding: 42px 24px 60px;
            color: var(--rc-text);
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, .58),
                    transparent 31%
                ),
                var(--rc-bg);
        }

        html.dark .router-create-page {
            --rc-bg: #081225;
            --rc-card: #0f1b31;
            --rc-soft: #13213a;
            --rc-input: #0b172b;
            --rc-text: #e8efff;
            --rc-body: #d7e1ef;
            --rc-muted: #9baaca;
            --rc-border: #293a56;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, .12),
                    transparent 32%
                ),
                var(--rc-bg);
        }

        .router-create-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .router-create-header__icon {
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

        .router-create-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
        }

        .router-create-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        html.dark .router-create-header h2 {
            color: #f8fafc !important;
        }

        html.dark .router-create-header p {
            color: #9baaca !important;
        }

        .router-create-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .router-create-toolbar {
            margin-bottom: 18px;
        }

        .router-back-button {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 15px;
            border: 1px solid var(--rc-border);
            border-radius: 11px;
            background: var(--rc-card);
            color: var(--rc-body) !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            transition:
                transform .18s ease,
                background .18s ease;
        }

        .router-back-button:hover {
            transform: translateY(-1px);
            background: var(--rc-soft);
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
            color: #334155;
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
            border: 1px solid var(--rc-border);
            border-radius: 20px;
            background: var(--rc-card);
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
            border-bottom: 1px solid var(--rc-border);
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
            color: var(--rc-text);
            font-size: 19px;
            font-weight: 800;
        }

        .router-form-heading p {
            margin: 4px 0 0;
            color: var(--rc-muted);
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
            border-top: 1px solid var(--rc-border);
        }

        .router-section-title {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 5px;
            color: var(--rc-text);
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
            color: var(--rc-muted);
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
            color: var(--rc-text);
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
            background: var(--rc-input) !important;
            color: var(--rc-text) !important;
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
            color: var(--rc-muted);
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

        .router-active-notice {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            margin-top: 25px;
            padding: 14px 16px;
            border: 1px solid #bfdbfe;
            border-radius: 11px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 11px;
            line-height: 1.6;
        }

        .router-active-notice i {
            margin-top: 2px;
            color: #2563eb;
        }

        html.dark .router-active-notice {
            border-color: #29466f;
            background: #12213a;
            color: #bfdbfe;
        }

        html.dark .router-active-notice i {
            color: #7da7ff;
        }

        .router-form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 20px 24px;
            border-top: 1px solid var(--rc-border);
            background: var(--rc-soft);
        }

        .router-footer-note {
            display: flex;
            align-items: flex-start;
            gap: 7px;
            max-width: 430px;
            color: var(--rc-muted);
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
            border: 1px solid var(--rc-border);
            background: var(--rc-card);
            color: var(--rc-body) !important;
        }

        .router-button.cancel:hover {
            background: var(--rc-soft);
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
            .router-create-page {
                padding: 28px 14px 45px;
            }

            .router-create-header h2 {
                font-size: 20px;
            }

            .router-create-header p {
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
            .router-create-page {
                padding-left: 10px;
                padding-right: 10px;
            }

            .router-create-header__icon {
                width: 42px;
                height: 42px;
                flex-basis: 42px;
                border-radius: 12px;
            }

            .router-back-button {
                width: 100%;
            }

            .router-security-guide {
                padding: 15px;
            }

            .router-form-card {
                border-radius: 16px;
            }

            .router-form-actions {
                flex-direction: column-reverse;
            }

            .router-form-actions .router-button {
                width: 100%;
            }
        }
    </style>

    <div class="router-create-page">
        <div class="router-create-container">

            <div class="router-create-toolbar">
                <a
                    href="{{ route('admin.routers.index') }}"
                    class="router-back-button"
                >
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Back to MikroTik Routers
                </a>
            </div>

            <div class="router-security-guide">
                <div class="router-security-guide__icon">
                    <i class="fas fa-shield-halved" aria-hidden="true"></i>
                </div>

                <div>
                    <strong>Secure router connection</strong>

                    <p>
                        Use a dedicated RouterOS account for this billing system.
                        Do not reuse your normal MikroTik administrator password,
                        and only use an API address that this billing server can securely reach.
                    </p>
                </div>
            </div>

            <div class="router-form-card">

                <div class="router-form-heading">
                    <div class="router-form-heading__icon">
                        <i class="fas fa-network-wired" aria-hidden="true"></i>
                    </div>

                    <div>
                        <h3>Router Connection</h3>
                        <p>
                            Enter the MikroTik API connection details for this ISP account.
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
                    action="{{ route('admin.routers.store') }}"
                >
                    @csrf

                    <div class="router-form-body">

                        <section class="router-form-section">
                            <h4 class="router-section-title">
                                <i class="fas fa-circle-info" aria-hidden="true"></i>
                                Router Information
                            </h4>

                            <p class="router-section-description">
                                Give the router a recognizable name and enter the address
                                the billing server will use to reach it.
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
                                        value="{{ old('name') }}"
                                        maxlength="100"
                                        required
                                        autocomplete="off"
                                        placeholder="Main Office Router"
                                        class="router-control"
                                    >

                                    <div class="router-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>

                                        <span>
                                            Letters, numbers, spaces, dots, underscores and
                                            hyphens are allowed.
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
                                        value="{{ old('ip_address') }}"
                                        required
                                        autocomplete="off"
                                        spellcheck="false"
                                        placeholder="192.168.88.1"
                                        class="router-control"
                                    >

                                    <div class="router-help">
                                        <i class="fas fa-circle-info" aria-hidden="true"></i>

                                        <span>
                                            Enter an address that the Laravel server can
                                            actually reach.
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
                                These credentials are used only when the system communicates
                                with this MikroTik router.
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
                                        value="{{ old('username') }}"
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
                                        MikroTik Password
                                        <span class="router-required">*</span>
                                    </label>

                                    <div class="router-password-wrap">
                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            maxlength="255"
                                            required
                                            autocomplete="new-password"
                                            spellcheck="false"
                                            class="router-control"
                                        >

                                        <button
                                            type="button"
                                            class="router-password-toggle"
                                            id="passwordToggle"
                                            aria-label="Show MikroTik password"
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
                                            The password is stored encrypted and is never
                                            displayed after saving.
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
                                        value="{{ old('api_port', 8728) }}"
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
                                            {{ old('router_type', 'MikroTik') === 'MikroTik' ? 'selected' : '' }}
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
                                Optional information to help identify the router later.
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
                                >{{ old('notes') }}</textarea>

                                <div class="router-help">
                                    <i class="fas fa-circle-info" aria-hidden="true"></i>

                                    <span>
                                        Do not place passwords, API keys, or other secrets
                                        in the notes field.
                                    </span>
                                </div>
                            </div>
                        </section>

                        <div class="router-active-notice">
                            <i class="fas fa-circle-check" aria-hidden="true"></i>

                            <div>
                                <strong>Initial status:</strong>
                                this router will be created as active. You can deactivate
                                it later from the Edit Router page. Deactivating a router
                                prevents it from being used to grant or synchronize new
                                customer access.
                            </div>
                        </div>

                    </div>

                    <div class="router-form-footer">
                        <div class="router-footer-note">
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>

                            <span>
                                Router credentials belong only to your ISP account and
                                are not displayed to customers.
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
                                Save Router
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
                        ? 'Hide MikroTik password'
                        : 'Show MikroTik password'
                );

                passwordToggleIcon.classList.toggle('fa-eye', !isHidden);
                passwordToggleIcon.classList.toggle('fa-eye-slash', isHidden);

                passwordInput.focus();
            });
        });
    </script>
</x-app-layout>