<x-app-layout>
    <x-slot name="header">
        <div class="setup-page-header">
            <div class="setup-page-header__icon">
                <i class="fas fa-wand-magic-sparkles" aria-hidden="true"></i>
            </div>

            <div>
                <h2>Connect Your MikroTik</h2>
                <p>Follow these steps to connect your router to the billing system.</p>
            </div>
        </div>
    </x-slot>

    <style>
        .setup-page {
            --setup-bg: #f3f6fa;
            --setup-card: #ffffff;
            --setup-soft: #f8fafc;
            --setup-text: #0f172a;
            --setup-body: #334155;
            --setup-muted: #64748b;
            --setup-border: #e2e8f0;

            min-height: 100vh;
            padding: 42px 24px 60px;
            color: var(--setup-text);
            background:
                radial-gradient(
                    circle at top right,
                    rgba(219, 234, 254, .58),
                    transparent 31%
                ),
                var(--setup-bg);
        }

        html.dark .setup-page {
            --setup-bg: #081225;
            --setup-card: #0f1b31;
            --setup-soft: #13213a;
            --setup-text: #e8efff;
            --setup-body: #d7e1ef;
            --setup-muted: #9baaca;
            --setup-border: #293a56;

            background:
                radial-gradient(
                    circle at top right,
                    rgba(37, 99, 235, .12),
                    transparent 32%
                ),
                var(--setup-bg);
        }

        .setup-page-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .setup-page-header__icon {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            box-shadow: 0 8px 20px rgba(37, 99, 235, .22);
        }

        .setup-page-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
        }

        .setup-page-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        html.dark .setup-page-header h2 {
            color: #f8fafc !important;
        }

        html.dark .setup-page-header p {
            color: #9baaca !important;
        }

        .setup-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .setup-toolbar {
            margin-bottom: 18px;
        }

        .setup-back {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 15px;
            border: 1px solid var(--setup-border);
            border-radius: 11px;
            background: var(--setup-card);
            color: var(--setup-body) !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
        }

        .setup-hero {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            padding: 22px;
            border: 1px solid var(--setup-border);
            border-radius: 18px;
            background: var(--setup-card);
            box-shadow: 0 8px 25px rgba(15, 23, 42, .06);
        }

        .setup-hero__icon {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            background: #dbeafe;
            color: #2563eb;
            font-size: 20px;
        }

        html.dark .setup-hero__icon {
            background: #16294b;
            color: #93c5fd;
        }

        .setup-hero h3 {
            margin: 0 0 4px;
            color: var(--setup-text);
            font-size: 20px;
            font-weight: 900;
        }

        .setup-hero p {
            margin: 0;
            color: var(--setup-muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .setup-steps {
            display: grid;
            gap: 16px;
        }

        .setup-step {
            display: grid;
            grid-template-columns: 50px minmax(0, 1fr);
            overflow: hidden;
            border: 1px solid var(--setup-border);
            border-radius: 17px;
            background: var(--setup-card);
            box-shadow: 0 6px 20px rgba(15, 23, 42, .04);
        }

        .setup-step-number {
            width: 36px;
            height: 36px;
            margin: 20px 0 0 15px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #2563eb;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            box-shadow: 0 5px 13px rgba(37, 99, 235, .22);
        }

        .setup-step-content {
            padding: 20px 22px 22px;
        }

        .setup-step h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 7px;
            color: var(--setup-text);
            font-size: 16px;
            font-weight: 900;
        }

        .setup-step h4 i {
            color: #2563eb;
        }

        html.dark .setup-step h4 i {
            color: #7da7ff;
        }

        .setup-step p {
            margin: 0;
            color: var(--setup-muted);
            font-size: 12px;
            line-height: 1.75;
        }

        .setup-step strong {
            color: var(--setup-text);
        }

        .setup-mini-guide {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .setup-mini-guide div {
            padding: 13px;
            border: 1px solid var(--setup-border);
            border-radius: 11px;
            background: var(--setup-soft);
        }

        .setup-mini-guide strong {
            display: block;
            margin-bottom: 4px;
            color: var(--setup-text);
            font-size: 11px;
        }

        .setup-mini-guide span {
            display: block;
            color: var(--setup-muted);
            font-size: 10px;
            line-height: 1.55;
        }

        .setup-command {
            margin-top: 15px;
            padding: 13px 15px;
            overflow-x: auto;
            border: 1px solid #26364d;
            border-radius: 10px;
            background: #111827;
            color: #a7f3d0;
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 12px;
            line-height: 1.65;
            white-space: nowrap;
        }

        html.dark .setup-command {
            border-color: #334155;
            background: #050d1b;
            color: #86efac;
        }

        .setup-note {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            margin-top: 14px;
            padding: 12px 14px;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 11px;
            line-height: 1.6;
        }

        html.dark .setup-note {
            border-color: #29466f;
            background: #10213c;
            color: #bfdbfe;
        }

        .setup-warning {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            margin-top: 14px;
            padding: 12px 14px;
            border: 1px solid #fde68a;
            border-radius: 10px;
            background: #fffbeb;
            color: #92400e;
            font-size: 11px;
            line-height: 1.6;
        }

        html.dark .setup-warning {
            border-color: #6b5518;
            background: #30270f;
            color: #f9dc82;
        }

        .setup-script-area {
            margin-top: 15px;
        }

        .setup-script-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 9px;
        }

        .setup-script-label strong {
            color: var(--setup-text);
            font-size: 12px;
        }

        .setup-readonly {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 999px;
            background: var(--setup-soft);
            color: var(--setup-muted);
            font-size: 9px;
            font-weight: 800;
        }

        #scriptBox {
            width: 100%;
            min-height: 330px;
            padding: 16px;
            border: 1px solid #334155;
            border-radius: 12px;
            outline: none;
            resize: vertical;
            background: #0b1220;
            color: #d1fae5;
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 12px;
            line-height: 1.65;
        }

        #scriptBox:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        .setup-script-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .setup-button {
            min-height: 41px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 15px;
            border-radius: 10px;
            border: 0;
            text-decoration: none;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            transition:
                transform .18s ease,
                background .18s ease;
        }

        .setup-button:hover {
            transform: translateY(-1px);
        }

        .setup-button.copy {
            background: #2563eb;
            color: #ffffff !important;
        }

        .setup-button.copy:hover {
            background: #1d4ed8;
        }

        .setup-button.select {
            border: 1px solid var(--setup-border);
            background: var(--setup-soft);
            color: var(--setup-body) !important;
        }

        .setup-copy-message {
            display: none;
            margin-top: 11px;
            padding: 10px 12px;
            border-radius: 9px;
            font-size: 11px;
            font-weight: 700;
        }

        .setup-copy-message.success {
            display: block;
            border: 1px solid #bbf7d0;
            background: #dcfce7;
            color: #166534;
        }

        .setup-copy-message.error {
            display: block;
            border: 1px solid #fecaca;
            background: #fee2e2;
            color: #991b1b;
        }

        html.dark .setup-copy-message.success {
            border-color: #245f42;
            background: #153b2a;
            color: #8ce8b3;
        }

        html.dark .setup-copy-message.error {
            border-color: #71313c;
            background: #421f28;
            color: #ffadb9;
        }

        .setup-terminal-steps {
            margin: 15px 0 0;
            padding: 0;
            list-style: none;
            counter-reset: terminal-step;
        }

        .setup-terminal-steps li {
            position: relative;
            margin-bottom: 9px;
            padding-left: 30px;
            color: var(--setup-muted);
            font-size: 11px;
            line-height: 1.65;
            counter-increment: terminal-step;
        }

        .setup-terminal-steps li::before {
            content: counter(terminal-step);
            position: absolute;
            top: 0;
            left: 0;
            width: 21px;
            height: 21px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 9px;
            font-weight: 900;
        }

        html.dark .setup-terminal-steps li::before {
            background: #16294b;
            color: #93c5fd;
        }

        .setup-checklist {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
            margin-top: 15px;
        }

        .setup-check {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 11px 12px;
            border: 1px solid var(--setup-border);
            border-radius: 10px;
            background: var(--setup-soft);
            color: var(--setup-muted);
            font-size: 10px;
            line-height: 1.55;
        }

        .setup-check i {
            margin-top: 2px;
            color: #16a34a;
        }

        .setup-finish {
            margin-top: 16px;
            padding: 22px;
            border: 1px solid var(--setup-border);
            border-radius: 17px;
            background: var(--setup-card);
        }

        .setup-finish h4 {
            margin: 0 0 7px;
            color: var(--setup-text);
            font-size: 16px;
            font-weight: 900;
        }

        .setup-finish p {
            margin: 0;
            color: var(--setup-muted);
            font-size: 11px;
            line-height: 1.7;
        }

        .setup-finish-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .setup-button.back {
            border: 1px solid var(--setup-border);
            background: var(--setup-soft);
            color: var(--setup-body) !important;
        }

        @media (max-width: 767px) {
            .setup-page {
                padding: 28px 14px 45px;
            }

            .setup-page-header h2 {
                font-size: 20px;
            }

            .setup-page-header p {
                display: none;
            }

            .setup-mini-guide {
                grid-template-columns: 1fr;
            }

            .setup-checklist {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .setup-page {
                padding-left: 10px;
                padding-right: 10px;
            }

            .setup-back {
                width: 100%;
            }

            .setup-hero {
                align-items: flex-start;
                padding: 17px;
            }

            .setup-hero__icon {
                width: 44px;
                height: 44px;
                flex-basis: 44px;
            }

            .setup-step {
                grid-template-columns: 1fr;
            }

            .setup-step-number {
                margin: 16px 0 0 18px;
            }

            .setup-step-content {
                padding-top: 12px;
            }

            .setup-script-actions,
            .setup-finish-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .setup-button {
                width: 100%;
            }
        }
    </style>

    <div class="setup-page">
        <div class="setup-container">

            <div class="setup-toolbar">
                <a
                    href="{{ route('admin.routers.index') }}"
                    class="setup-back"
                >
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Back to MikroTik Routers
                </a>
            </div>

            <div class="setup-hero">
                <div class="setup-hero__icon">
                    <i class="fas fa-router" aria-hidden="true"></i>
                </div>

                <div>
                    <h3>{{ $router->name }}</h3>

                    <p>
                        You do not need to manually type the generated commands below.
                        Follow the steps in order, copy the script, paste it into the
                        MikroTik Terminal, and then test the connection.
                    </p>
                </div>
            </div>

            <div class="setup-steps">

                {{-- STEP 1 --}}
                <section class="setup-step">
                    <div class="setup-step-number">1</div>

                    <div class="setup-step-content">
                        <h4>
                            <i class="fas fa-list-check" aria-hidden="true"></i>
                            Before You Start
                        </h4>

                        <p>
                            Make sure you can log in to the MikroTik router before
                            changing anything. Keep Winbox or WebFig open while completing
                            this setup.
                        </p>

                        <div class="setup-mini-guide">
                            <div>
                                <strong>MikroTik Access</strong>
                                <span>
                                    You should be able to log in using Winbox,
                                    WebFig, or the RouterOS terminal.
                                </span>
                            </div>

                            <div>
                                <strong>Router Address</strong>
                                <span>
                                    The IP address saved in this billing system must
                                    be reachable from the server.
                                </span>
                            </div>

                            <div>
                                <strong>Backup Recommended</strong>
                                <span>
                                    Keep a current RouterOS backup before applying
                                    configuration changes.
                                </span>
                            </div>
                        </div>

                        <div class="setup-warning">
                            <i
                                class="fas fa-triangle-exclamation"
                                aria-hidden="true"
                            ></i>

                            <span>
                                Do not continue if you cannot access the router or do not
                                have permission to change its configuration.
                            </span>
                        </div>
                    </div>
                </section>

                {{-- STEP 2 --}}
                <section class="setup-step">
                    <div class="setup-step-number">2</div>

                    <div class="setup-step-content">
                        <h4>
                            <i class="fas fa-desktop" aria-hidden="true"></i>
                            Open the MikroTik Terminal
                        </h4>

                        <p>
                            Log in to your MikroTik using <strong>Winbox</strong> or
                            <strong>WebFig</strong>. After logging in, open
                            <strong>New Terminal</strong>. This is where you will paste
                            the setup script.
                        </p>

                        <ol class="setup-terminal-steps">
                            <li>Open Winbox and connect to your MikroTik router.</li>
                            <li>Log in with an account allowed to change router settings.</li>
                            <li>Click <strong>New Terminal</strong> in Winbox.</li>
                            <li>Leave the terminal open and continue to Step 3 below.</li>
                        </ol>
                    </div>
                </section>

                {{-- STEP 3 --}}
                <section class="setup-step">
                    <div class="setup-step-number">3</div>

                    <div class="setup-step-content">
                        <h4>
                            <i class="fas fa-plug" aria-hidden="true"></i>
                            Confirm the MikroTik API
                        </h4>

                        <p>
                            The billing system communicates with RouterOS through its API.
                            Your generated setup script may configure the API as required.
                            The standard RouterOS API port is <strong>8728</strong>.
                        </p>

                        <div class="setup-command">
                            /ip service print
                        </div>

                        <div class="setup-note">
                            <i class="fas fa-circle-info" aria-hidden="true"></i>

                            <span>
                                The API port configured on the MikroTik must match the
                                API port saved for this router in the billing system.
                            </span>
                        </div>
                    </div>
                </section>

                {{-- STEP 4 --}}
                <section class="setup-step">
                    <div class="setup-step-number">4</div>

                    <div class="setup-step-content">
                        <h4>
                            <i class="fas fa-copy" aria-hidden="true"></i>
                            Copy the Generated Setup Script
                        </h4>

                        <p>
                            The commands below were generated by the billing system for
                            this router. Review them before applying them. The router
                            password stored in this application is not shown in this box.
                        </p>

                        <div class="setup-script-area">
                            <div class="setup-script-label">
                                <strong>Generated RouterOS Commands</strong>

                                <span class="setup-readonly">
                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                    Read Only
                                </span>
                            </div>

                            <textarea
                                id="scriptBox"
                                readonly
                                spellcheck="false"
                                autocomplete="off"
                            >{{ $script }}</textarea>

                            <div class="setup-script-actions">
                                <button
                                    type="button"
                                    class="setup-button copy"
                                    id="copyScriptButton"
                                    onclick="copyScript()"
                                >
                                    <i class="fas fa-copy" aria-hidden="true"></i>
                                    Copy Setup Script
                                </button>

                                <button
                                    type="button"
                                    class="setup-button select"
                                    onclick="selectScript()"
                                >
                                    <i
                                        class="fas fa-i-cursor"
                                        aria-hidden="true"
                                    ></i>
                                    Select Script Manually
                                </button>
                            </div>

                            <div
                                id="copyMessage"
                                class="setup-copy-message"
                                role="status"
                                aria-live="polite"
                            ></div>
                        </div>
                    </div>
                </section>

                {{-- STEP 5 --}}
                <section class="setup-step">
                    <div class="setup-step-number">5</div>

                    <div class="setup-step-content">
                        <h4>
                            <i class="fas fa-terminal" aria-hidden="true"></i>
                            Paste the Script into MikroTik
                        </h4>

                        <p>
                            Return to the <strong>New Terminal</strong> window you opened
                            in Winbox. Paste the copied commands into the terminal and run
                            them.
                        </p>

                        <ol class="setup-terminal-steps">
                            <li>Return to the MikroTik New Terminal window.</li>
                            <li>Paste the script you copied from Step 4.</li>
                            <li>Run the commands and allow RouterOS to process them.</li>
                            <li>Watch the terminal for any RouterOS error messages.</li>
                        </ol>

                        <div class="setup-warning">
                            <i
                                class="fas fa-triangle-exclamation"
                                aria-hidden="true"
                            ></i>

                            <span>
                                Do not repeatedly paste the script if RouterOS reports
                                an error. Check the error first before trying again.
                            </span>
                        </div>
                    </div>
                </section>

                {{-- STEP 6 --}}
                <section class="setup-step">
                    <div class="setup-step-number">6</div>

                    <div class="setup-step-content">
                        <h4>
                            <i
                                class="fas fa-plug-circle-check"
                                aria-hidden="true"
                            ></i>
                            Test the Connection
                        </h4>

                        <p>
                            After the script finishes, return to the
                            <strong>MikroTik Routers</strong> page in this billing system.
                            Find this router and click <strong>Test Connection</strong>.
                        </p>

                        <div class="setup-checklist">
                            <div class="setup-check">
                                <i class="fas fa-circle-check" aria-hidden="true"></i>
                                <span>
                                    Router is powered on and reachable.
                                </span>
                            </div>

                            <div class="setup-check">
                                <i class="fas fa-circle-check" aria-hidden="true"></i>
                                <span>
                                    Router IP address is correct.
                                </span>
                            </div>

                            <div class="setup-check">
                                <i class="fas fa-circle-check" aria-hidden="true"></i>
                                <span>
                                    API port matches the saved router settings.
                                </span>
                            </div>

                            <div class="setup-check">
                                <i class="fas fa-circle-check" aria-hidden="true"></i>
                                <span>
                                    MikroTik username and password are correct.
                                </span>
                            </div>

                            <div class="setup-check">
                                <i class="fas fa-circle-check" aria-hidden="true"></i>
                                <span>
                                    API access is allowed through the router firewall.
                                </span>
                            </div>

                            <div class="setup-check">
                                <i class="fas fa-circle-check" aria-hidden="true"></i>
                                <span>
                                    Router is marked Active in the billing system.
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- STEP 7 --}}
                <section class="setup-step">
                    <div class="setup-step-number">7</div>

                    <div class="setup-step-content">
                        <h4>
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>
                            Keep the Connection Secure
                        </h4>

                        <p>
                            After the router is connected, keep RouterOS and the billing
                            connection protected. The API should only be reachable by
                            systems that actually need access.
                        </p>

                        <div class="setup-warning">
                            <i class="fas fa-lock" aria-hidden="true"></i>

                            <span>
                                Do not expose the MikroTik API to the entire public
                                internet. Restrict access with RouterOS firewall rules,
                                trusted management addresses, private networking, or a VPN.
                            </span>
                        </div>

                        <div class="setup-note">
                            <i class="fas fa-user-shield" aria-hidden="true"></i>

                            <span>
                                Use a dedicated RouterOS account for the billing system.
                                Do not reuse your normal router administrator password.
                            </span>
                        </div>
                    </div>
                </section>

            </div>

            <div class="setup-finish">
                <h4>
                    <i
                        class="fas fa-circle-check"
                        style="color:#16a34a;margin-right:6px;"
                        aria-hidden="true"
                    ></i>
                    Ready to Test
                </h4>

                <p>
                    Once the script has been applied successfully, return to your router
                    list and use <strong>Test Connection</strong>. A successful connection
                    confirms that the billing system can communicate with this MikroTik.
                </p>

                <div class="setup-finish-actions">
                    <a
                        href="{{ route('admin.routers.index') }}"
                        class="setup-button copy"
                    >
                        <i
                            class="fas fa-plug-circle-check"
                            aria-hidden="true"
                        ></i>
                        Go to Routers &amp; Test Connection
                    </a>

                    <button
                        type="button"
                        class="setup-button back"
                        onclick="copyScript()"
                    >
                        <i class="fas fa-copy" aria-hidden="true"></i>
                        Copy Script Again
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        async function copyScript() {
            const scriptBox = document.getElementById('scriptBox');
            const copyMessage = document.getElementById('copyMessage');

            if (!scriptBox || !copyMessage) {
                return;
            }

            try {
                await navigator.clipboard.writeText(scriptBox.value);

                copyMessage.textContent =
                    'Setup script copied. Return to the MikroTik terminal and paste it there.';

                copyMessage.className =
                    'setup-copy-message success';
            } catch (error) {
                scriptBox.focus();
                scriptBox.select();

                copyMessage.textContent =
                    'Automatic copying was blocked by your browser. The script has been selected. Copy it manually using Ctrl+C.';

                copyMessage.className =
                    'setup-copy-message error';
            }
        }

        function selectScript() {
            const scriptBox = document.getElementById('scriptBox');
            const copyMessage = document.getElementById('copyMessage');

            if (!scriptBox || !copyMessage) {
                return;
            }

            scriptBox.focus();
            scriptBox.select();

            copyMessage.textContent =
                'The full script is selected. Press Ctrl+C to copy it.';

            copyMessage.className =
                'setup-copy-message success';
        }
    </script>
</x-app-layout>