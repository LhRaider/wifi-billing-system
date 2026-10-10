<x-app-layout>
    <x-slot name="header">
        <div class="admin-hero">
            <div class="hero-rain" aria-hidden="true">
                <span style="--x:4%;--delay:-1.4s;--duration:4.8s;--size:3px;"></span>
                <span style="--x:11%;--delay:-3.1s;--duration:5.6s;--size:2px;"></span>
                <span style="--x:18%;--delay:-.8s;--duration:4.4s;--size:3px;"></span>
                <span style="--x:26%;--delay:-4.2s;--duration:6.1s;--size:2px;"></span>
                <span style="--x:34%;--delay:-2.2s;--duration:5.1s;--size:3px;"></span>
                <span style="--x:42%;--delay:-5.1s;--duration:6.4s;--size:2px;"></span>
                <span style="--x:50%;--delay:-1.8s;--duration:4.9s;--size:3px;"></span>
                <span style="--x:58%;--delay:-3.7s;--duration:5.8s;--size:2px;"></span>
                <span style="--x:66%;--delay:-.4s;--duration:4.6s;--size:3px;"></span>
                <span style="--x:74%;--delay:-4.8s;--duration:6.2s;--size:2px;"></span>
                <span style="--x:82%;--delay:-2.7s;--duration:5.3s;--size:3px;"></span>
                <span style="--x:90%;--delay:-5.6s;--duration:6.6s;--size:2px;"></span>
                <span style="--x:96%;--delay:-1.1s;--duration:4.7s;--size:3px;"></span>
            </div>

            <div class="admin-hero__identity">
                <div class="admin-hero__icon" id="dashboardGreetingIconWrap">
                    <i id="dashboardGreetingIcon" class="fas fa-moon" aria-hidden="true"></i>
                </div>
                <div>
                    <div id="dashboardGreeting"
                         data-user-name="{{ auth()->user()->name }}"
                         class="admin-hero__title">
                        <span id="dashboardGreetingText">Welcome, {{ auth()->user()->name }}</span>
                    </div>
                    <div class="admin-hero__subtitle">
                        {{ $isSuperAdmin ? 'Platform overview and administration' : 'Your ISP performance and network overview' }}
                    </div>
                </div>
            </div>

            <div class="hero-clock" aria-label="Current date and time">
                <span class="hero-clock__icon"><i class="fas fa-calendar-days"></i></span>
                <span>
                    <strong id="dashboardCurrentDay">Today</strong>
                    <small id="dashboardCurrentDateTime">--</small>
                </span>
            </div>

            @if(!$isSuperAdmin)
                @if(
                    auth()->user()->service_status === 'trial'
                    && auth()->user()->trial_ends_at
                    && auth()->user()->trial_ends_at->isFuture()
                )
                    <div class="license-pill license-pill--trial">
                        <span class="license-pill__icon"><i class="fas fa-hourglass-half"></i></span>
                        <span>
                            <small>Trial period</small>
                            <strong>Ends {{ auth()->user()->trial_ends_at->format('d M Y') }}</strong>
                        </span>
                    </div>
                @elseif(
                    auth()->user()->service_status === 'active'
                    && auth()->user()->service_expires_at
                    && auth()->user()->service_expires_at->isFuture()
                )
                    <div class="license-pill license-pill--active">
                        <span class="license-pill__icon"><i class="fas fa-shield-halved"></i></span>
                        <span>
                            <small>Platform license</small>
                            <strong>Active until {{ auth()->user()->service_expires_at->format('d M Y') }}</strong>
                        </span>
                    </div>
                @else
                    <a href="{{ route('admin.license-payments.index') }}" class="license-pill license-pill--danger">
                        <span class="license-pill__icon"><i class="fas fa-triangle-exclamation"></i></span>
                        <span>
                            <small>Platform license</small>
                            <strong>Renew license</strong>
                        </span>
                    </a>
                @endif
            @endif
        </div>
    </x-slot>

    <style>
        .admin-dashboard {
            --ink:#10204b;
            --muted:#65749a;
            --line:#dfe7f6;
            --blue:#1769ff;
            --green:#0aa64f;
            --purple:#7a2df4;
            --orange:#ff7a00;
            --red:#ef3340;
            --cyan:#00a9c7;
            min-height:100vh;
            padding:18px 0 48px;
            color:var(--ink);
            background:
                radial-gradient(circle at 82% 8%,rgba(81,177,255,.20),transparent 27%),
                radial-gradient(circle at 48% 58%,rgba(117,72,255,.11),transparent 32%),
                linear-gradient(135deg,#f7f9ff 0%,#edf5ff 52%,#f7f3ff 100%);
            background-size:160% 160%;
            animation:dashboardBackground 24s ease-in-out infinite;
        }
        @keyframes dashboardBackground {
            0%,100% { background-position:0% 0%; }
            50% { background-position:100% 100%; }
        }

        .admin-hero {
            width:100%;
            min-height:104px;
            display:flex;
            align-items:center;
            gap:18px;
            position:relative;
            overflow:hidden;
            padding:20px 24px;
            border-radius:22px;
            color:#fff;
            background:linear-gradient(115deg,#3158f5 0%,#4837e9 38%,#1566f6 100%);
            background-size:220% 220%;
            animation:heroShift 16s ease infinite;
            box-shadow:0 18px 40px rgba(37,80,220,.24);
        }
        @keyframes heroShift {
            0%,100% { background-position:0% 50%; }
            50% { background-position:100% 50%; }
        }

        .hero-rain {
            position:absolute;
            inset:0;
            z-index:1;
            overflow:hidden;
            pointer-events:none;
            opacity:.72;
            mask-image:linear-gradient(to bottom,rgba(0,0,0,.95),rgba(0,0,0,.18));
        }

        .hero-rain::before {
            content:"";
            position:absolute;
            inset:0;
            background:
                radial-gradient(circle at 16% 28%,rgba(255,255,255,.18) 0 1px,transparent 2px),
                radial-gradient(circle at 67% 18%,rgba(255,255,255,.14) 0 1px,transparent 2px),
                radial-gradient(circle at 83% 67%,rgba(255,255,255,.12) 0 1px,transparent 2px);
            background-size:95px 72px,130px 88px,160px 105px;
            animation:heroSparkleDrift 18s linear infinite;
        }

        .hero-rain span {
            position:absolute;
            left:var(--x);
            top:-28px;
            width:var(--size);
            height:24px;
            border-radius:999px;
            background:linear-gradient(to bottom,rgba(255,255,255,0),rgba(255,255,255,.82));
            box-shadow:0 0 8px rgba(255,255,255,.22);
            transform:rotate(12deg);
            animation:heroRainDrop var(--duration) linear infinite;
            animation-delay:var(--delay);
        }

        @keyframes heroRainDrop {
            0% {
                transform:translate3d(0,-35px,0) rotate(12deg);
                opacity:0;
            }
            12% { opacity:.9; }
            88% { opacity:.55; }
            100% {
                transform:translate3d(34px,165px,0) rotate(12deg);
                opacity:0;
            }
        }

        @keyframes heroSparkleDrift {
            from { transform:translate3d(0,0,0); }
            to { transform:translate3d(38px,18px,0); }
        }
        .admin-hero::before {
            content:"";
            position:absolute;
            width:520px;height:160px;
            right:21%;bottom:-105px;
            border-radius:50%;
            background:rgba(255,255,255,.08);
            transform:rotate(-8deg);
        }
        .admin-hero::after {
            content:"";
            position:absolute;
            width:390px;height:150px;
            right:7%;top:-112px;
            border-radius:50%;
            background:rgba(255,255,255,.08);
            transform:rotate(12deg);
        }
        .admin-hero__identity {
            display:flex;align-items:center;gap:14px;min-width:0;
            position:relative;z-index:2;margin-right:auto;
        }
        .admin-hero__icon {
            width:54px;height:54px;border-radius:17px;
            display:grid;place-items:center;flex:0 0 54px;
            position:relative;overflow:hidden;
            background:linear-gradient(145deg,rgba(255,255,255,.24),rgba(255,255,255,.10));
            color:#fff;font-size:23px;
            border:1px solid rgba(255,255,255,.18);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.20),
                0 10px 24px rgba(9,30,100,.18);
            backdrop-filter:blur(8px);
        }

        .admin-hero__icon::after {
            content:"";
            position:absolute;
            width:28px;height:70px;
            left:-38px;top:-8px;
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.30),transparent);
            transform:rotate(18deg);
            animation:heroIconShine 7s ease-in-out infinite;
        }

        @keyframes heroIconShine {
            0%,68%,100% { left:-38px;opacity:0; }
            76% { opacity:1; }
            88% { left:72px;opacity:0; }
        }
        .admin-hero__title {
            font-size:27px;font-weight:900;line-height:1.15;letter-spacing:-.03em;color:#fff;
        }
        .admin-hero__subtitle {
            margin-top:5px;color:rgba(255,255,255,.86);font-size:13px;font-weight:600;
        }
        .hero-clock {
            display:flex;align-items:center;gap:10px;
            position:relative;z-index:2;
            padding:9px 13px;border-radius:14px;
            color:#fff;background:rgba(255,255,255,.09);
            border:1px solid rgba(255,255,255,.14);
        }
        .hero-clock__icon {
            width:35px;height:35px;border-radius:10px;display:grid;place-items:center;
            color:#fff;background:rgba(255,255,255,.12);font-size:15px;
        }
        .hero-clock strong { display:block;font-size:11px;font-weight:900; }
        .hero-clock small { display:block;margin-top:2px;color:rgba(255,255,255,.82);font-size:9px;font-weight:700; }

        .license-pill {
            display:flex;align-items:center;gap:10px;
            position:relative;z-index:2;
            padding:10px 13px;border-radius:15px;
            color:#fff;text-decoration:none;
            background:rgba(8,36,116,.34);
            border:1px solid rgba(255,255,255,.28);
            box-shadow:inset 0 0 0 1px rgba(255,255,255,.05);
            backdrop-filter:blur(8px);
        }
        .license-pill__icon {
            width:36px;height:36px;border-radius:11px;display:grid;place-items:center;
            background:#dcfce7;color:#0a9f4b;
        }
        .license-pill small {
            display:block;color:#c8d8ff;font-size:9px;text-transform:uppercase;
            letter-spacing:.055em;font-weight:900;
        }
        .license-pill strong { display:block;margin-top:2px;font-size:12px;color:#fff; }
        .license-pill--trial .license-pill__icon { background:#fff7d6;color:#d97706; }
        .license-pill--danger .license-pill__icon { background:#fee2e2;color:#dc2626; }

        .dashboard-shell { max-width:86rem;margin:0 auto;padding:0 22px; }
        .dashboard-section { margin-top:16px; }
        .dashboard-section:first-child { margin-top:0; }

        .surface-card {
            background:rgba(255,255,255,.95);
            border:1px solid rgba(199,211,234,.68);
            border-radius:20px;
            box-shadow:0 12px 32px rgba(43,73,145,.075);
            backdrop-filter:blur(10px);
        }

        /* Revenue summary — three compact visual cards */
        .revenue-card {
            padding:0;background:transparent;border:0;box-shadow:none;backdrop-filter:none;
        }
        .revenue-card > .section-heading {
            margin:0 0 8px 2px;justify-content:flex-end;min-height:36px;
        }
        .revenue-card > .section-heading .section-heading__left { display:none; }
        .section-heading {
            display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:17px;
        }
        .section-heading__left { display:flex;align-items:center;gap:11px;min-width:0; }
        .section-heading__icon {
            width:39px;height:39px;border-radius:12px;display:grid;place-items:center;
            background:#edf4ff;color:#2563eb;font-size:16px;
        }
        .section-heading h2,.section-heading h3 {
            margin:0;color:#10204b;font-weight:900;letter-spacing:-.018em;
        }
        .section-heading h2 { font-size:18px; }
        .section-heading h3 { font-size:17px; }
        .section-heading p { margin:3px 0 0;color:#7280a2;font-size:11px;font-weight:500; }
        .icon-button {
            width:38px;height:38px;border-radius:12px;border:1px solid #dbe4f4;
            background:#fff;color:#33466f;display:grid;place-items:center;cursor:pointer;
            box-shadow:0 5px 14px rgba(35,63,130,.05);transition:.2s ease;
        }
        .icon-button:hover { transform:translateY(-2px);box-shadow:0 10px 22px rgba(35,63,130,.10); }

        .revenue-grid { display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px; }
        .revenue-tile {
            min-height:126px;position:relative;overflow:hidden;
            display:grid;grid-template-columns:58px minmax(0,1fr) 118px;
            align-items:center;gap:13px;padding:17px 18px;
            border-radius:18px;border:1px solid;
            box-shadow:0 10px 25px rgba(30,64,175,.07);
            transition:transform .2s ease,box-shadow .2s ease;
        }
        .revenue-tile:hover { transform:translateY(-3px);box-shadow:0 16px 32px rgba(30,64,175,.12); }
        .revenue-tile::before,.revenue-tile::after { display:none; }
        .revenue-tile__icon {
            width:58px;height:58px;border-radius:17px;display:grid;place-items:center;
            font-size:25px;background:rgba(255,255,255,.62);
            box-shadow:inset 0 0 0 1px rgba(255,255,255,.7),0 8px 18px rgba(30,64,175,.06);
        }
        .revenue-tile__content { min-width:0;position:relative;z-index:2; }
        .revenue-tile__label {
            margin:0;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.035em;
        }
        .revenue-tile__amount {
            margin:6px 0 0;font-size:24px;font-weight:900;line-height:1;letter-spacing:-.035em;white-space:nowrap;
        }
        .revenue-tile__meta { display:flex;align-items:center;gap:7px;margin-top:10px;white-space:nowrap; }
        .revenue-tile__meta span {
            display:inline-flex;align-items:center;gap:4px;padding:4px 7px;border-radius:999px;
            background:#dff8e9;color:#078a40;font-size:9px;font-weight:900;
        }
        .revenue-tile__meta small { color:#66769b;font-size:9px;font-weight:700; }
        .revenue-sparkline { width:118px;height:54px;overflow:visible; }
        .sparkline-line { fill:none;stroke:currentColor;stroke-width:2.4;stroke-linecap:round; }
        .sparkline-fill { fill:currentColor;opacity:.09;stroke:none; }
        .revenue-tile--today { color:#079447;background:linear-gradient(135deg,#fbfffd,#e9fff2);border-color:#c7f0d8; }
        .revenue-tile--month { color:#1769df;background:linear-gradient(135deg,#fcfdff,#eaf4ff);border-color:#d0e5ff; }
        .revenue-tile--total { color:#7625e8;background:linear-gradient(135deg,#fff,#f6edff);border-color:#e5d3ff; }

        /* Compact KPI cards with subtitle and trend badge */
        .stats-grid { display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px; }
        .metric-card {
            --metric:#1769ff;--metric-soft:#eaf3ff;
            min-height:112px;position:relative;overflow:hidden;
            display:grid;
            grid-template-columns:58px minmax(0,1fr) auto;
            grid-template-rows:auto auto auto;
            column-gap:13px;row-gap:2px;align-items:center;
            padding:15px 14px;
            border:1px solid rgba(200,211,232,.72);border-radius:17px;
            background:linear-gradient(135deg,#fff 58%,var(--metric-soft) 165%);
            box-shadow:0 9px 22px rgba(42,68,128,.06);
            transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
        }
        .metric-card:hover {
            transform:translateY(-3px);box-shadow:0 15px 30px rgba(42,68,128,.11);
            border-color:var(--metric);
        }
        .metric-card::after {
            content:"";position:absolute;width:85px;height:85px;right:-45px;top:-45px;
            border-radius:50%;background:var(--metric);opacity:.045;
        }
        .metric-card__top { display:contents; }
        .metric-card__icon {
            grid-column:1;grid-row:1 / span 3;
            width:56px;height:56px;border-radius:17px;display:grid;place-items:center;
            background:var(--metric-soft);color:var(--metric);font-size:23px;
            box-shadow:0 7px 17px rgba(37,99,235,.065);
        }
        .metric-card__arrow {
            position:absolute;right:12px;top:12px;
            width:26px;height:26px;border-radius:50%;display:grid;place-items:center;
            color:var(--metric);background:var(--metric-soft);font-size:9px;
        }
        .metric-card__label {
            grid-column:2;grid-row:1;margin:0;align-self:end;
            color:#172951;font-size:11px;font-weight:900;line-height:1.2;padding-right:18px;
        }
        .metric-card__value {
            grid-column:2;grid-row:2;margin:1px 0 0;
            font-size:24px;line-height:1;font-weight:900;color:#10204b;letter-spacing:-.035em;
        }
        .metric-card__sub {
            grid-column:2;grid-row:3;color:#6d7c9e;font-size:9px;font-weight:700;white-space:nowrap;
        }
        .metric-card__trend {
            grid-column:3;grid-row:2 / span 2;align-self:center;justify-self:end;
            display:inline-flex;align-items:center;gap:3px;padding:5px 7px;border-radius:999px;
            background:#ddf8e7;color:#078b40;font-size:9px;font-weight:900;white-space:nowrap;
        }
        .metric-blue { --metric:#1769ff;--metric-soft:#eaf3ff; }
        .metric-purple { --metric:#12a64e;--metric-soft:#e7faef; }
        .metric-orange { --metric:#ff7a00;--metric-soft:#fff0df; }
        .metric-red { --metric:#e92e3d;--metric-soft:#ffe9ec; }
        .metric-cyan { --metric:#7428ed;--metric-soft:#f1e8ff; }
        .metric-teal { --metric:#00a8b8;--metric-soft:#e2fbfd; }
        .metric-sky { --metric:#ed2461;--metric-soft:#ffe7ef; }
        .metric-slate { --metric:#40577c;--metric-soft:#eaf0f7; }

        /* Quick Actions — tighter, colorful, six-across on wide screens */
        .quick-card { padding:15px 16px; }
        .quick-card .section-heading { margin-bottom:12px; }
        .quick-grid { display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:9px; }
        .quick-action {
            --quick:#1769ff;--quick-soft:#edf5ff;
            min-height:68px;display:flex;align-items:center;gap:9px;
            padding:9px 10px;border:1px solid color-mix(in srgb,var(--quick) 20%,#e2e8f0);
            border-radius:14px;background:linear-gradient(135deg,#fff,var(--quick-soft));
            color:#1f315a;text-decoration:none;
            box-shadow:0 6px 15px rgba(30,64,175,.04);
            transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
        }
        .quick-action:hover {
            transform:translateY(-2px);box-shadow:0 11px 22px rgba(30,64,175,.09);
            border-color:var(--quick);
        }
        .quick-action__icon {
            width:43px;height:43px;flex:0 0 43px;border-radius:13px;
            display:grid;place-items:center;background:color-mix(in srgb,var(--quick) 11%,#fff);
            color:var(--quick);font-size:18px;
        }
        .quick-action__text strong { display:block;color:var(--quick);font-size:11px;font-weight:900; }
        .quick-action__text span { display:block;margin-top:2px;color:#7583a2;font-size:9px;font-weight:600;line-height:1.2; }
        .quick-action:nth-child(1) { --quick:#1769ff;--quick-soft:#edf5ff; }
        .quick-action:nth-child(2) { --quick:#0a9f4b;--quick-soft:#ecfbf2; }
        .quick-action:nth-child(3) { --quick:#7428ed;--quick-soft:#f4edff; }
        .quick-action:nth-child(4) { --quick:#f16b0b;--quick-soft:#fff2e7; }
        .quick-action:nth-child(5) { --quick:#df1f69;--quick-soft:#fff0f6; }
        .quick-action:nth-child(6) { --quick:#0785a8;--quick-soft:#eafaff; }
        .quick-action:nth-child(7) { --quick:#c78100;--quick-soft:#fff8e7; }
        .quick-action:nth-child(8) { --quick:#7c2be8;--quick-soft:#f5efff; }

        .two-column { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;align-items:start; }
        .content-card { padding:20px;min-width:0; }
        .chart-box { height:245px;position:relative; }
        .chart-box--doughnut { height:245px;max-width:520px;margin:0 auto; }
        .chart-box--trend { height:220px; }
        .chart-empty-note {
            position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);
            display:none;align-items:center;gap:7px;padding:8px 11px;border-radius:10px;
            background:#f7f9fd;border:1px solid #e7edf7;color:#7b89a8;
            font-size:10px;font-weight:700;pointer-events:none;
        }
        .activity-list { margin-top:2px; }
        .activity-row {
            display:flex;justify-content:space-between;align-items:flex-start;gap:14px;
            padding:12px 0;border-bottom:1px solid #edf1f8;
        }
        .activity-row:last-child { border-bottom:0;padding-bottom:0; }
        .activity-row__message { display:flex;gap:10px;color:#37466a;font-size:12px;line-height:1.45; }
        .activity-row__icon {
            width:31px;height:31px;border-radius:10px;background:#f4f7fc;
            display:grid;place-items:center;flex:0 0 31px;
        }
        .activity-row time { color:#96a1bb;font-size:9px;font-weight:700;white-space:nowrap;padding-top:7px; }
        .view-link { color:#1769ff;font-size:11px;font-weight:900;text-decoration:none;white-space:nowrap; }
        .view-link:hover { text-decoration:underline; }

        .admin-dashboard .dashboard-section > .surface-card.content-card {
            width:100%;
        }
        .responsive-table { width:100%;overflow-x:auto; }
        .dashboard-table { width:100%;border-collapse:separate;border-spacing:0;min-width:560px; }
        .dashboard-table th {
            padding:10px 11px;background:#f5f8fd;color:#7180a1;font-size:9px;
            text-transform:uppercase;letter-spacing:.05em;text-align:left;font-weight:900;
            border-top:1px solid #e8edf6;border-bottom:1px solid #e8edf6;
        }
        .dashboard-table th:first-child { border-left:1px solid #e8edf6;border-radius:10px 0 0 10px; }
        .dashboard-table th:last-child { border-right:1px solid #e8edf6;border-radius:0 10px 10px 0; }
        .dashboard-table td {
            padding:12px 11px;border-bottom:1px solid #eef2f8;font-size:11px;color:#39486a;vertical-align:middle;
        }
        .dashboard-table tbody tr:hover td { background:#f9fbff; }
        .dashboard-table .money { font-weight:900;color:#13234a; }
        .status-badge {
            display:inline-flex;align-items:center;gap:5px;padding:5px 8px;border-radius:999px;font-size:9px;font-weight:900;
        }
        .status-badge--success { background:#e8faef;color:#098e43; }
        .status-badge--pending { background:#fff7df;color:#ad7200; }
        .status-badge--danger { background:#ffe9ec;color:#c51f31; }
        .count-badge {
            display:inline-grid;min-width:29px;height:27px;padding:0 8px;place-items:center;
            border-radius:9px;background:#eaf3ff;color:#1769df;font-weight:900;
        }
        .empty-state { padding:27px 12px;text-align:center;color:#98a4bd; }
        .empty-state i { display:block;font-size:21px;margin-bottom:8px;color:#c7d0e0; }
        .empty-state span { font-size:11px;font-weight:700; }

        @media (max-width:1180px) {
            .hero-clock { display:none; }
            .stats-grid,.quick-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }
        @media (max-width:900px) {
            .revenue-grid,.two-column { grid-template-columns:1fr; }
            .admin-hero { flex-wrap:wrap; }
            .revenue-tile { grid-template-columns:58px minmax(0,1fr) 105px; }
            .revenue-sparkline { width:105px; }
        }
        @media (max-width:640px) {
            .admin-dashboard { padding-top:12px; }
            .dashboard-shell { padding:0 13px; }
            .admin-hero { padding:17px;border-radius:17px; }
            .admin-hero__title { font-size:20px; }
            .admin-hero__subtitle { display:none; }
            .license-pill { width:100%; }
            .stats-grid,.quick-grid { grid-template-columns:1fr; }
            .metric-card { min-height:108px; }
            .revenue-tile { grid-template-columns:52px minmax(0,1fr); }
            .revenue-tile__icon { width:52px;height:52px; }
            .revenue-sparkline { display:none; }
            .content-card,.quick-card { padding:16px; }
        }

        /* Global light/dark theme support.
           The shared layout will toggle the .dark class on <html>. */
        html.dark .admin-dashboard {
            --ink:#e8eeff;
            --muted:#9aa9c8;
            --line:#263659;
            color:var(--ink);
            background:
                radial-gradient(circle at 82% 8%,rgba(45,107,255,.16),transparent 27%),
                radial-gradient(circle at 48% 58%,rgba(116,72,255,.10),transparent 32%),
                linear-gradient(135deg,#091226 0%,#0d1730 52%,#11142b 100%);
        }

        html.dark .surface-card {
            background:rgba(15,25,49,.94);
            border-color:rgba(69,88,126,.58);
            box-shadow:0 14px 34px rgba(0,0,0,.22);
        }

        html.dark .section-heading h2,
        html.dark .section-heading h3,
        html.dark .metric-card__label,
        html.dark .metric-card__value,
        html.dark .dashboard-table .money {
            color:#eef3ff;
        }

        html.dark .section-heading p,
        html.dark .metric-card__sub,
        html.dark .quick-action__text span,
        html.dark .activity-row__message,
        html.dark .view-link {
            color:#9cacca;
        }

        html.dark .section-heading__icon,
        html.dark .icon-button {
            border-color:#2b3a5d;
            background:#162543;
            color:#8bb4ff;
        }

        html.dark .icon-button:hover {
            background:#1b2e52;
        }

        html.dark .revenue-tile--today {
            background:linear-gradient(135deg,#102b25,#10241f);
            border-color:#1f5b45;
        }

        html.dark .revenue-tile--month {
            background:linear-gradient(135deg,#10233f,#111e36);
            border-color:#294f80;
        }

        html.dark .revenue-tile--total {
            background:linear-gradient(135deg,#24183d,#1d1732);
            border-color:#49306f;
        }

        html.dark .revenue-tile__meta small {
            color:#9dacc7;
        }

        html.dark .metric-card {
            background:linear-gradient(135deg,#101c36 58%,color-mix(in srgb,var(--metric) 12%,#101c36) 165%);
            border-color:#273858;
            box-shadow:0 10px 24px rgba(0,0,0,.16);
        }

        html.dark .metric-card:hover {
            border-color:var(--metric);
            box-shadow:0 16px 30px rgba(0,0,0,.25);
        }

        html.dark .quick-action {
            background:linear-gradient(135deg,#101c36,color-mix(in srgb,var(--quick) 9%,#101c36));
            border-color:#293a5a;
            color:#dfe8fb;
        }

        html.dark .quick-action:hover {
            border-color:var(--quick);
        }

        html.dark .quick-action__icon {
            background:color-mix(in srgb,var(--quick) 15%,#13213d);
        }

        html.dark .dashboard-table th {
            background:#13213d;
            color:#9cacca;
            border-color:#2a3958;
        }

        html.dark .dashboard-table th:first-child,
        html.dark .dashboard-table th:last-child {
            border-color:#2a3958;
        }

        html.dark .dashboard-table td {
            color:#cbd6ed;
            border-bottom-color:#243351;
        }

        html.dark .dashboard-table tbody tr:hover td {
            background:#14223e;
        }

        html.dark .activity-row {
            border-bottom-color:#243351;
        }

        html.dark .activity-row__icon {
            background:#172642;
        }

        html.dark .activity-row time,
        html.dark .empty-state,
        html.dark .empty-state span {
            color:#8797b6;
        }

        html.dark .empty-state i {
            color:#526382;
        }

        html.dark .count-badge {
            background:#172c50;
            color:#8bb9ff;
        }

        html.dark .chart-empty-note {
            background:#13213d;
            border-color:#2a3958;
            color:#9aa9c8;
        }

        html.dark .status-badge--success {
            background:#123c2a;
            color:#72e5a4;
        }

        html.dark .status-badge--pending {
            background:#403414;
            color:#f4cf66;
        }

        html.dark .status-badge--danger {
            background:#451d29;
            color:#ff94a4;
        }

        @media (prefers-reduced-motion:reduce) {
            .admin-dashboard,.admin-hero,
            .hero-rain::before,.hero-rain span,.admin-hero__icon::after {
                animation:none!important;
            }
            .hero-rain { display:none; }
            .metric-card,.quick-action,.revenue-tile { transition:none!important; }
        }
    </style>

    <div class="admin-dashboard" id="adminDashboard">
        <div class="dashboard-shell">

            <section class="dashboard-section">
                <div class="surface-card revenue-card">
                    <div class="section-heading">
                        <div class="section-heading__left">
                            <span class="section-heading__icon"><i class="fas fa-arrow-trend-up"></i></span>
                            <div>
                                <h2>{{ $isSuperAdmin ? 'Platform License Revenue' : 'Customer Revenue' }}</h2>
                                <p>{{ $isSuperAdmin ? 'Approved platform license collections' : 'Completed customer payment overview' }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="toggleRevenueSummary(this)" class="icon-button" title="Show or hide revenue">
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>

                    <div class="revenue-grid">
                        <div class="revenue-tile revenue-tile--today">
                            <span class="revenue-tile__icon"><i class="fas fa-coins"></i></span>
                            <div class="revenue-tile__content">
                                <div class="revenue-tile__label">Today's Revenue</div>
                                <div class="revenue-tile__amount revenue-item" data-amount="KES {{ number_format($todayRevenue, 2) }}">
                                    KES {{ number_format($todayRevenue, 2) }}
                                </div>
                                <div class="revenue-tile__meta"><span><i class="fas fa-arrow-trend-up"></i> 0%</span><small>today</small></div>
                            </div>
                            <svg class="revenue-sparkline" viewBox="0 0 150 54" aria-hidden="true">
                                <path class="sparkline-fill" d="M2 45 C20 49,28 17,45 25 S68 47,84 31 S105 8,120 22 S138 36,148 13 L148 54 L2 54 Z"></path>
                                <path class="sparkline-line" d="M2 45 C20 49,28 17,45 25 S68 47,84 31 S105 8,120 22 S138 36,148 13"></path>
                            </svg>
                        </div>
                        <div class="revenue-tile revenue-tile--month">
                            <span class="revenue-tile__icon"><i class="fas fa-calendar-days"></i></span>
                            <div class="revenue-tile__content">
                                <div class="revenue-tile__label">This Month</div>
                                <div class="revenue-tile__amount revenue-item" data-amount="KES {{ number_format($monthRevenue, 2) }}">
                                    KES {{ number_format($monthRevenue, 2) }}
                                </div>
                                <div class="revenue-tile__meta"><span><i class="fas fa-arrow-trend-up"></i> 0%</span><small>this month</small></div>
                            </div>
                            <svg class="revenue-sparkline" viewBox="0 0 150 54" aria-hidden="true">
                                <path class="sparkline-fill" d="M2 41 C18 47,28 37,39 23 S59 15,71 32 S91 46,105 26 S128 11,148 35 L148 54 L2 54 Z"></path>
                                <path class="sparkline-line" d="M2 41 C18 47,28 37,39 23 S59 15,71 32 S91 46,105 26 S128 11,148 35"></path>
                            </svg>
                        </div>
                        <div class="revenue-tile revenue-tile--total">
                            <span class="revenue-tile__icon"><i class="fas fa-chart-column"></i></span>
                            <div class="revenue-tile__content">
                                <div class="revenue-tile__label">All Time Revenue</div>
                                <div class="revenue-tile__amount revenue-item" data-amount="KES {{ number_format($totalRevenue, 2) }}">
                                    KES {{ number_format($totalRevenue, 2) }}
                                </div>
                                <div class="revenue-tile__meta"><span><i class="fas fa-circle-check"></i> Total</span><small>collected</small></div>
                            </div>
                            <svg class="revenue-sparkline" viewBox="0 0 150 54" aria-hidden="true">
                                <path class="sparkline-fill" d="M2 42 C16 21,27 16,39 37 S62 49,76 23 S99 8,111 31 S134 45,148 18 L148 54 L2 54 Z"></path>
                                <path class="sparkline-line" d="M2 42 C16 21,27 16,39 37 S62 49,76 23 S99 8,111 31 S134 45,148 18"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="stats-grid">
                    <div class="metric-card metric-blue">
                        <div class="metric-card__top">
                            <span class="metric-card__icon"><i class="fas {{ $isSuperAdmin ? 'fa-user-tie' : 'fa-users' }}"></i></span>
                            <i class="fas fa-arrow-up-right-dots metric-card__arrow"></i>
                        </div>
                        <div class="metric-card__value">{{ $totalCustomers }}</div>
                        <div class="metric-card__label">{{ $isSuperAdmin ? 'ISP Admins' : 'Customers' }}</div>
                        <div class="metric-card__sub">{{ $isSuperAdmin ? 'Total Registered' : 'Total Registered' }}</div>
                        <span class="metric-card__trend"><i class="fas fa-arrow-up"></i> 0%</span>
                    </div>

                    <div class="metric-card metric-purple">
                        <div class="metric-card__top">
                            <span class="metric-card__icon"><i class="fas fa-credit-card"></i></span>
                            <i class="fas fa-receipt metric-card__arrow"></i>
                        </div>
                        <div class="metric-card__value">{{ $totalPayments }}</div>
                        <div class="metric-card__label">{{ $isSuperAdmin ? 'Approved License Payments' : 'Completed Payments' }}</div>
                        <div class="metric-card__sub">{{ $isSuperAdmin ? 'Payments Received' : 'Payments Received' }}</div>
                        <span class="metric-card__trend"><i class="fas fa-arrow-up"></i> 0%</span>
                    </div>

                    <div class="metric-card metric-orange">
                        <div class="metric-card__top">
                            <span class="metric-card__icon"><i class="fas fa-bolt"></i></span>
                            <i class="fas fa-signal metric-card__arrow"></i>
                        </div>
                        <div class="metric-card__value">{{ $activeSubscriptions }}</div>
                        <div class="metric-card__label">{{ $isSuperAdmin ? 'Active ISP Admins' : 'Active Subscriptions' }}</div>
                        <div class="metric-card__sub">{{ $isSuperAdmin ? 'Currently Active' : 'Currently Active' }}</div>
                        <span class="metric-card__trend"><i class="fas fa-arrow-up"></i> 0%</span>
                    </div>

                    <div class="metric-card metric-red">
                        <div class="metric-card__top">
                            <span class="metric-card__icon"><i class="fas fa-clock-rotate-left"></i></span>
                            <i class="fas fa-circle-exclamation metric-card__arrow"></i>
                        </div>
                        <div class="metric-card__value">{{ $expiredSubscriptions }}</div>
                        <div class="metric-card__label">{{ $isSuperAdmin ? 'Expired ISP Admins' : 'Expired Subscriptions' }}</div>
                        <div class="metric-card__sub">{{ $isSuperAdmin ? 'Licenses Expired' : 'Subscriptions Expired' }}</div>
                        <span class="metric-card__trend"><i class="fas fa-arrow-up"></i> 0%</span>
                    </div>

                    @if(!$isSuperAdmin)
                        <div class="metric-card metric-cyan">
                            <div class="metric-card__top">
                                <span class="metric-card__icon"><i class="fas fa-cube"></i></span>
                                <i class="fas fa-boxes-stacked metric-card__arrow"></i>
                            </div>
                            <div class="metric-card__value">{{ $totalPackages }}</div>
                            <div class="metric-card__label">Packages</div>
                            <div class="metric-card__sub">Available Packages</div>
                            <span class="metric-card__trend"><i class="fas fa-arrow-up"></i> 0%</span>
                        </div>

                        <div class="metric-card metric-teal">
                            <div class="metric-card__top">
                                <span class="metric-card__icon"><i class="fas fa-wifi"></i></span>
                                <i class="fas fa-wifi metric-card__arrow"></i>
                            </div>
                            <div class="metric-card__value">{{ $totalHotspotUsers }}</div>
                            <div class="metric-card__label">Hotspot Users</div>
                            <div class="metric-card__sub">Registered Users</div>
                            <span class="metric-card__trend"><i class="fas fa-arrow-up"></i> 0%</span>
                        </div>

                        <div class="metric-card metric-sky">
                            <div class="metric-card__top">
                                <span class="metric-card__icon"><i class="fas fa-tower-broadcast"></i></span>
                                <i class="fas fa-circle metric-card__arrow"></i>
                            </div>
                            <div class="metric-card__value">{{ $activeHotspotUsers }}</div>
                            <div class="metric-card__label">Active Hotspots</div>
                            <div class="metric-card__sub">Hotspots Running</div>
                            <span class="metric-card__trend"><i class="fas fa-arrow-up"></i> 0%</span>
                        </div>

                        <div class="metric-card metric-slate">
                            <div class="metric-card__top">
                                <span class="metric-card__icon"><i class="fas fa-network-wired"></i></span>
                                <i class="fas fa-network-wired metric-card__arrow"></i>
                            </div>
                            <div class="metric-card__value">{{ $totalRouters }}</div>
                            <div class="metric-card__label">Routers</div>
                            <div class="metric-card__sub">MikroTik Routers</div>
                            <span class="metric-card__trend"><i class="fas fa-arrow-up"></i> 0%</span>
                        </div>
                    @endif
                </div>
            </section>

            <section class="dashboard-section">
                <div class="surface-card quick-card">
                    <div class="section-heading">
                        <div class="section-heading__left">
                            <span class="section-heading__icon" style="background:#fff7ed;color:#ea580c;"><i class="fas fa-wand-magic-sparkles"></i></span>
                            <div>
                                <h2>Quick Actions</h2>
                                <p>Jump directly to common administration tasks</p>
                            </div>
                        </div>
                    </div>

                    <div class="quick-grid">
                        @if($isSuperAdmin)
                            <a href="{{ route('admin.admin-users.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-user-tie"></i></span>
                                <span class="quick-action__text"><strong>ISP Admins</strong><span>Manage ISP accounts</span></span>
                            </a>
                            <a href="{{ route('admin.license-payments.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-money-check-dollar"></i></span>
                                <span class="quick-action__text"><strong>License Payments</strong><span>Review platform payments</span></span>
                            </a>
                            <a href="{{ route('admin.settings.edit') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-sliders"></i></span>
                                <span class="quick-action__text"><strong>Platform Settings</strong><span>Configure the platform</span></span>
                            </a>
                            <a href="{{ route('admin.audit-logs.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-shield-halved"></i></span>
                                <span class="quick-action__text"><strong>Platform Logs</strong><span>Review system activity</span></span>
                            </a>
                            <a href="{{ route('admin.notifications.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-bell"></i></span>
                                <span class="quick-action__text"><strong>Notifications</strong><span>View platform alerts</span></span>
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-chart-pie"></i></span>
                                <span class="quick-action__text"><strong>Reports</strong><span>Open analytics reports</span></span>
                            </a>
                        @else
                            <a href="{{ route('admin.packages.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-cube"></i></span>
                                <span class="quick-action__text"><strong>Manage Packages</strong><span>Create and edit packages</span></span>
                            </a>
                            <a href="{{ route('admin.customers.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-users"></i></span>
                                <span class="quick-action__text"><strong>Add Customer</strong><span>Manage customer accounts</span></span>
                            </a>
                            <a href="{{ route('admin.routers.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-network-wired"></i></span>
                                <span class="quick-action__text"><strong>Manage Routers</strong><span>Configure MikroTik routers</span></span>
                            </a>
                            <a href="{{ route('admin.hotspot-users.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-tower-broadcast"></i></span>
                                <span class="quick-action__text"><strong>Hotspot Users</strong><span>Manage network access</span></span>
                            </a>
                            <a href="{{ route('admin.business-profile.edit') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-building"></i></span>
                                <span class="quick-action__text"><strong>Business Profile</strong><span>ISP identity and details</span></span>
                            </a>
                            <a href="{{ route('admin.license-payments.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-shield-halved"></i></span>
                                <span class="quick-action__text"><strong>Platform License</strong><span>License status and payment</span></span>
                            </a>
                            <a href="{{ route('admin.notifications.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-bell"></i></span>
                                <span class="quick-action__text"><strong>Notifications</strong><span>View important alerts</span></span>
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="quick-action">
                                <span class="quick-action__icon"><i class="fas fa-chart-column"></i></span>
                                <span class="quick-action__text"><strong>Reports</strong><span>Revenue and usage reports</span></span>
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            <section class="dashboard-section">
                <div class="two-column">
                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-chart-column"></i></span>
                                <div>
                                    <h3>{{ $isSuperAdmin ? 'Monthly License Revenue' : 'Monthly Customer Revenue' }}</h3>
                                    <p>Revenue movement across recent months</p>
                                </div>
                            </div>
                        </div>
                        <div class="chart-box"><canvas id="revenueChart"></canvas></div>
                    </div>

                    @if(!$isSuperAdmin)
                        <div class="surface-card content-card">
                            <div class="section-heading">
                                <div class="section-heading__left">
                                    <span class="section-heading__icon" style="background:#faf5ff;color:#9333ea;"><i class="fas fa-chart-pie"></i></span>
                                    <div>
                                        <h3>Package Distribution</h3>
                                        <p>Customer activity across your packages</p>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-box chart-box--doughnut"><canvas id="packageDistributionChart"></canvas></div>
                        </div>
                    @endif

                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon" style="background:#f0fdf4;color:#16a34a;"><i class="fas fa-user-plus"></i></span>
                                <div>
                                    <h3>{{ $isSuperAdmin ? 'ISP Admin Registration Trend' : 'Customer Registration Trend' }}</h3>
                                    <p>New registrations over time</p>
                                </div>
                            </div>
                        </div>
                        <div class="chart-box"><canvas id="customerTrendChart"></canvas></div>
                    </div>

                    @if(!$isSuperAdmin)
                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-clock-rotate-left"></i></span>
                                <div>
                                    <h3>Recent Activity</h3>
                                    <p>Latest actions across your account</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.recent-activity') }}" class="view-link">View all <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <div class="activity-list">
                            @forelse($recentActivities as $activity)
                                <div class="activity-row">
                                    <div class="activity-row__message">
                                        <span class="activity-row__icon">
                                            <i class="{{ $activity['icon'] }}" style="color:{{ $activity['color'] }};"></i>
                                        </span>
                                        <span>{{ $activity['message'] }}</span>
                                    </div>
                                    <time>{{ $activity['time']->diffForHumans() }}</time>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-clock-rotate-left"></i>
                                    <span>No recent activity</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>
            </section>

            @if($isSuperAdmin)
                <section class="dashboard-section">
                    <div class="two-column">
                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-clock-rotate-left"></i></span>
                                <div>
                                    <h3>Recent Activity</h3>
                                    <p>Latest actions across your account</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.recent-activity') }}" class="view-link">View all <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <div class="activity-list">
                            @forelse($recentActivities as $activity)
                                <div class="activity-row">
                                    <div class="activity-row__message">
                                        <span class="activity-row__icon">
                                            <i class="{{ $activity['icon'] }}" style="color:{{ $activity['color'] }};"></i>
                                        </span>
                                        <span>{{ $activity['message'] }}</span>
                                    </div>
                                    <time>{{ $activity['time']->diffForHumans() }}</time>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-clock-rotate-left"></i>
                                    <span>No recent activity</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon"><i class="fas fa-receipt"></i></span>
                                <div>
                                    <h3>{{ $isSuperAdmin ? 'Recent License Payments' : 'Recent Customer Payments' }}</h3>
                                    <p>Latest recorded payment activity</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.payments') }}" class="view-link">View all <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <div class="responsive-table">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>{{ $isSuperAdmin ? 'ISP Admin' : 'Customer' }}</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPayments as $payment)
                                        <tr>
                                            <td>{{ $payment->user->name ?? $payment->phone_number ?? 'Unknown' }}</td>
                                            <td class="money">KES {{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                @if(in_array($payment->status, ['completed', 'approved']))
                                                    <span class="status-badge status-badge--success"><i class="fas fa-circle-check"></i>{{ ucfirst($payment->status) }}</span>
                                                @elseif($payment->status === 'pending')
                                                    <span class="status-badge status-badge--pending"><i class="fas fa-clock"></i>Pending</span>
                                                @else
                                                    <span class="status-badge status-badge--danger"><i class="fas fa-circle-xmark"></i>{{ ucfirst($payment->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4"><div class="empty-state"><i class="fas fa-receipt"></i><span>No recent payments</span></div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </section>

                <section class="dashboard-section">
                    <div class="surface-card content-card">
                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-trophy"></i></span>
                                <div>
                                    <h3>{{ $isSuperAdmin ? 'Top Paying ISP Admins' : 'Top Customers' }}</h3>
                                    <p>Highest payment activity</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.top-customers') }}" class="view-link">View all <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <div class="responsive-table">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>{{ $isSuperAdmin ? 'ISP Admin' : 'Customer' }}</th>
                                        <th>Payments</th>
                                        <th>Total Spent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCustomers as $customer)
                                        <tr>
                                            <td><strong>{{ $customer['name'] }}</strong></td>
                                            <td><span class="count-badge">{{ $customer['payments'] }}</span></td>
                                            <td class="money">KES {{ number_format($customer['spent'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3"><div class="empty-state"><i class="fas fa-trophy"></i><span>No records found</span></div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </section>
            @else
                <section class="dashboard-section">
                    <div class="two-column">
                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon"><i class="fas fa-receipt"></i></span>
                                <div>
                                    <h3>{{ $isSuperAdmin ? 'Recent License Payments' : 'Recent Customer Payments' }}</h3>
                                    <p>Latest recorded payment activity</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.payments') }}" class="view-link">View all <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <div class="responsive-table">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>{{ $isSuperAdmin ? 'ISP Admin' : 'Customer' }}</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentPayments as $payment)
                                        <tr>
                                            <td>{{ $payment->user->name ?? $payment->phone_number ?? 'Unknown' }}</td>
                                            <td class="money">KES {{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                @if(in_array($payment->status, ['completed', 'approved']))
                                                    <span class="status-badge status-badge--success"><i class="fas fa-circle-check"></i>{{ ucfirst($payment->status) }}</span>
                                                @elseif($payment->status === 'pending')
                                                    <span class="status-badge status-badge--pending"><i class="fas fa-clock"></i>Pending</span>
                                                @else
                                                    <span class="status-badge status-badge--danger"><i class="fas fa-circle-xmark"></i>{{ ucfirst($payment->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4"><div class="empty-state"><i class="fas fa-receipt"></i><span>No recent payments</span></div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-trophy"></i></span>
                                <div>
                                    <h3>{{ $isSuperAdmin ? 'Top Paying ISP Admins' : 'Top Customers' }}</h3>
                                    <p>Highest payment activity</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.top-customers') }}" class="view-link">View all <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <div class="responsive-table">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>{{ $isSuperAdmin ? 'ISP Admin' : 'Customer' }}</th>
                                        <th>Payments</th>
                                        <th>Total Spent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCustomers as $customer)
                                        <tr>
                                            <td><strong>{{ $customer['name'] }}</strong></td>
                                            <td><span class="count-badge">{{ $customer['payments'] }}</span></td>
                                            <td class="money">KES {{ number_format($customer['spent'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3"><div class="empty-state"><i class="fas fa-trophy"></i><span>No records found</span></div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </section>
            @endif

            @if(!$isSuperAdmin)
                <section class="dashboard-section">
                    <div class="surface-card content-card">
                        <div class="section-heading">
                            <div class="section-heading__left">
                                <span class="section-heading__icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-chart-simple"></i></span>
                                <div>
                                    <h3>Package Performance</h3>
                                    <p>Current month revenue and active subscriptions by package</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.packages.index') }}" class="view-link">Manage packages <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <div class="responsive-table">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Package</th>
                                        <th>Price</th>
                                        <th>Active Users</th>
                                        <th>This Month Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($packagePerformance as $package)
                                        <tr>
                                            <td><strong>{{ $package['name'] }}</strong></td>
                                            <td class="money">KES {{ number_format($package['price'], 2) }}</td>
                                            <td><span class="count-badge">{{ $package['active_users'] }}</span></td>
                                            <td class="money">KES {{ number_format($package['monthly_revenue'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4"><div class="empty-state"><i class="fas fa-layer-group"></i><span>No package performance data</span></div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>

    <script>
        function updateDashboardClock() {
            const day = document.getElementById('dashboardCurrentDay');
            const value = document.getElementById('dashboardCurrentDateTime');
            if (!day || !value) return;

            const now = new Date();
            day.textContent = new Intl.DateTimeFormat(undefined, { weekday: 'long' }).format(now);
            value.textContent = new Intl.DateTimeFormat(undefined, {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            }).format(now);
        }

        updateDashboardClock();
        window.setInterval(updateDashboardClock, 60000);

        function initializeDashboardGreeting() {
            const greeting = document.getElementById('dashboardGreeting');
            const icon = document.getElementById('dashboardGreetingIcon');
            const iconWrap = document.getElementById('dashboardGreetingIconWrap');
            const text = document.getElementById('dashboardGreetingText');

            if (!greeting || !icon || !text) return;

            const userName = greeting.dataset.userName || '';
            const hour = new Date().getHours();
            let greetingText = 'Good Night';
            let iconClass = 'fas fa-moon';
            let iconColor = '#4f46e5';
            let iconBg = '#eef2ff';

            if (hour >= 5 && hour < 12) {
                greetingText = 'Good Morning'; iconClass = 'fas fa-sun'; iconColor = '#d97706'; iconBg = '#fffbeb';
            } else if (hour >= 12 && hour < 18) {
                greetingText = 'Good Afternoon'; iconClass = 'fas fa-cloud-sun'; iconColor = '#0284c7'; iconBg = '#f0f9ff';
            } else if (hour >= 18 && hour < 21) {
                greetingText = 'Good Evening'; iconClass = 'fas fa-cloud-sun'; iconColor = '#ea580c'; iconBg = '#fff7ed';
            }

            icon.className = iconClass;
            text.textContent = `${greetingText}, ${userName}`;

            const dashboard = document.getElementById('adminDashboard');
            if (dashboard) {
                dashboard.dataset.daypart =
                    hour >= 5 && hour < 12 ? 'morning' :
                    hour >= 12 && hour < 18 ? 'afternoon' :
                    hour >= 18 && hour < 21 ? 'evening' : 'night';
            }
        }

        initializeDashboardGreeting();

        function initializeDashboardCharts() {
            if (typeof window.Chart === 'undefined') {
                console.warn('Chart.js is not available.');
                return;
            }

            const fontFamily = getComputedStyle(document.body).fontFamily || 'Arial, sans-serif';
            const isDarkMode = document.documentElement.classList.contains('dark');

            Chart.defaults.font.family = fontFamily;
            Chart.defaults.color = isDarkMode ? '#9cacca' : '#7180a1';
            Chart.defaults.animation.duration = 650;

            const gridColor = isDarkMode
                ? 'rgba(148, 163, 184, 0.13)'
                : 'rgba(148, 163, 184, 0.16)';

            const tickColor = isDarkMode ? '#9cacca' : '#7180a1';

            const money = (value) => 'KES ' + Number(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const revenueLabels = @json($chartLabels ?? []);
            const revenueData = @json($chartData ?? []);
            const revenueCanvas = document.getElementById('revenueChart');

            if (revenueCanvas) {
                const ctx = revenueCanvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 245);
                gradient.addColorStop(0, 'rgba(91, 69, 239, 0.30)');
                gradient.addColorStop(0.55, 'rgba(49, 88, 245, 0.12)');
                gradient.addColorStop(1, 'rgba(49, 88, 245, 0.01)');

                new Chart(revenueCanvas, {
                    type: 'line',
                    data: {
                        labels: revenueLabels,
                        datasets: [{
                            label: 'Revenue',
                            data: revenueData,
                            borderColor: '#4f46e5',
                            backgroundColor: gradient,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.38,
                            pointRadius: revenueData.length <= 3 ? 5 : 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                displayColors: false,
                                backgroundColor: '#10204b',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                padding: 11,
                                cornerRadius: 10,
                                callbacks: {
                                    label: (context) => money(context.parsed.y)
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { color: tickColor, font: { size: 10, weight: '600' } }
                            },
                            y: {
                                beginAtZero: true,
                                grace: '18%',
                                border: { display: false },
                                grid: { color: gridColor, drawTicks: false },
                                ticks: {
                                    color: tickColor,
                                    padding: 8,
                                    font: { size: 10, weight: '600' },
                                    callback: (value) => Number(value).toLocaleString()
                                }
                            }
                        }
                    }
                });
            }

            const packageLabels = @json($packageDistributionLabels ?? []);
            const packageData = @json($packageDistributionData ?? []);
            const packageCanvas = document.getElementById('packageDistributionChart');

            if (packageCanvas) {
                const total = packageData.reduce((sum, value) => sum + Number(value || 0), 0);

                const centerTextPlugin = {
                    id: 'dashboardDoughnutCenter',
                    afterDraw(chart) {
                        const meta = chart.getDatasetMeta(0);
                        if (!meta || !meta.data || !meta.data.length) return;

                        const { ctx } = chart;
                        const point = meta.data[0];
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        ctx.fillStyle = isDarkMode ? '#f8fbff' : '#10204b';
                        ctx.font = '900 24px ' + fontFamily;
                        ctx.fillText(String(total), point.x, point.y - 6);

                        ctx.fillStyle = isDarkMode ? '#b8c5df' : '#7b89a8';
                        ctx.font = '800 9px ' + fontFamily;
                        ctx.fillText('TOTAL USERS', point.x, point.y + 14);
                        ctx.restore();
                    }
                };

                new Chart(packageCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: packageLabels,
                        datasets: [{
                            data: packageData,
                            backgroundColor: ['#2563eb', '#16a34a', '#7c3aed', '#f97316', '#ec4899', '#06b6d4'],
                            borderColor: isDarkMode ? '#0f1931' : '#ffffff',
                            borderWidth: 3,
                            hoverOffset: 7
                        }]
                    },
                    plugins: [centerTextPlugin],
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        radius: '78%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    boxWidth: 7,
                                    boxHeight: 7,
                                    padding: 13,
                                    color: isDarkMode ? '#c2cee5' : '#5f6f91',
                                    font: { size: 10, weight: '700' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#10204b',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                padding: 10,
                                cornerRadius: 10,
                                callbacks: {
                                    label: (context) => {
                                        const value = Number(context.raw || 0);
                                        const percent = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
                                        return ` ${context.label}: ${value} (${percent}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            const customerLabels = @json($customerChartLabels ?? []);
            const customerData = @json($customerChartData ?? []);
            const customerCanvas = document.getElementById('customerTrendChart');

            if (customerCanvas) {
                new Chart(customerCanvas, {
                    type: 'bar',
                    data: {
                        labels: customerLabels,
                        datasets: [{
                            label: 'New Customers',
                            data: customerData,
                            backgroundColor: '#22c55e',
                            hoverBackgroundColor: '#16a34a',
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 42,
                            categoryPercentage: 0.55,
                            barPercentage: 0.72
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                displayColors: false,
                                backgroundColor: '#10204b',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                padding: 10,
                                cornerRadius: 10,
                                callbacks: {
                                    label: (context) => ` ${context.parsed.y} new customer${context.parsed.y === 1 ? '' : 's'}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { color: tickColor, font: { size: 10, weight: '600' } }
                            },
                            y: {
                                beginAtZero: true,
                                suggestedMax: Math.max(2, ...customerData.map(Number)) + 1,
                                border: { display: false },
                                grid: { color: gridColor, drawTicks: false },
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    color: tickColor,
                                    padding: 8,
                                    font: { size: 10, weight: '600' }
                                }
                            }
                        }
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.revenue-item').forEach(function (item) {
                item.dataset.amount = item.innerText.trim();
                item.innerText = '••••••••';
            });
        });

        function toggleRevenueSummary(button) {
            const items = document.querySelectorAll('.revenue-item');
            if (!items.length) return;

            const hidden = items[0].innerText.trim() === '••••••••';
            items.forEach(function (item) {
                item.innerText = hidden ? item.dataset.amount : '••••••••';
            });

            button.innerHTML = hidden
                ? '<i class="fas fa-eye"></i>'
                : '<i class="fas fa-eye-slash"></i>';
        }
        document.addEventListener('DOMContentLoaded', function () {
            initializeDashboardCharts();
        });
</script>
</x-app-layout>
