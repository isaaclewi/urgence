<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CongoAssist')</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a56db">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="CongoAssist">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Icons --}}
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    {{-- Leaflet (chargé uniquement si la page en a besoin) --}}
    @stack('head-styles')

    <style>
        /* ─── Reset & Base ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; font-size: 15px; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f5f6fa;
            color: #111827;
            min-height: 100vh;
            display: flex;
        }

        /* ─── CSS Variables ─── */
        :root {
            --sidebar-w: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-border: rgba(255,255,255,0.06);
            --accent: #2563eb;
            --accent-light: #eff6ff;
            --accent-hover: #1d4ed8;
            --green: #059669;
            --green-light: #ecfdf5;
            --red: #dc2626;
            --text: #111827;
            --text-sec: #6b7280;
            --text-muted: #9ca3af;
            --border: #e5e7eb;
            --surface: #ffffff;
            --surface2: #f9fafb;
            --radius: 10px;
            --radius-lg: 14px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
            --shadow: 0 4px 16px rgba(0,0,0,.08);
            --shadow-lg: 0 12px 40px rgba(0,0,0,.12);
            --nav-h: 60px;
        }

        /* ─── Sidebar ─── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            flex-shrink: 0;
            border-right: 1px solid var(--sidebar-border);
            transition: width .25s ease;
            overflow: hidden;
        }

        .sidebar-inner {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 20px 0;
        }

        /* Logo */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--sidebar-border);
            margin-bottom: 16px;
            text-decoration: none;
        }
        .sidebar-logo-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-logo-icon span {
            font-weight: 700; font-size: 16px; color: #fff;
        }
        .sidebar-logo-text {
            font-size: 15px; font-weight: 700; color: #f9fafb;
            letter-spacing: -.02em;
        }
        .sidebar-logo-text em { color: #34d399; font-style: normal; }

        /* Section label */
        .sb-label {
            font-size: 10px; font-weight: 600; letter-spacing: .1em;
            text-transform: uppercase; color: rgba(255,255,255,.3);
            padding: 0 20px 8px;
            margin-top: 8px;
        }

        /* Nav link */
        .sb-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 20px;
            margin: 1px 10px;
            border-radius: var(--radius);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            color: rgba(255,255,255,.55);
            transition: background .15s, color .15s;
            white-space: nowrap;
        }
        .sb-link i { flex-shrink: 0; opacity: .8; }
        .sb-link:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }
        .sb-link.active {
            background: rgba(37,99,235,.3);
            color: #fff;
            font-weight: 600;
        }
        .sb-link.active i { opacity: 1; }

        /* Badge */
        .sb-badge {
            margin-left: auto;
            background: var(--red);
            color: #fff;
            font-size: 10px; font-weight: 700;
            padding: 2px 6px; border-radius: 99px;
            min-width: 18px; text-align: center;
        }

        /* Divider */
        .sb-divider {
            height: 1px;
            background: var(--sidebar-border);
            margin: 10px 20px;
        }

        /* Logout */
        .sb-link.danger { color: rgba(252,165,165,.7); }
        .sb-link.danger:hover { background: rgba(220,38,38,.15); color: #fca5a5; }

        /* Sidebar spacer */
        .sb-spacer { flex: 1; }

        /* User chip */
        .sb-user {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 20px;
            border-top: 1px solid var(--sidebar-border);
            margin-top: 8px;
        }
        .sb-user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            object-fit: cover; flex-shrink: 0;
            border: 2px solid rgba(255,255,255,.15);
        }
        .sb-user-name {
            font-size: 12.5px; font-weight: 600; color: #e5e7eb;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sb-user-role {
            font-size: 11px; color: rgba(255,255,255,.35);
        }

        /* ─── Main area ─── */
        .main-wrapper {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* Top bar (mobile) */
        .topbar {
            display: none;
            align-items: center;
            justify-content: space-between;
            height: var(--nav-h);
            padding: 0 16px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0; z-index: 40;
        }
        .topbar-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .topbar-logo-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
        }
        .topbar-logo-icon span { font-weight: 700; font-size: 14px; color: #fff; }
        .topbar-logo-text { font-size: 14px; font-weight: 700; color: var(--text); }
        .topbar-logo-text em { color: var(--green); font-style: normal; }
        .topbar-btn {
            width: 36px; height: 36px; border: none; background: none;
            cursor: pointer; border-radius: 8px; display: flex;
            align-items: center; justify-content: center; color: var(--text);
            transition: background .15s;
        }
        .topbar-btn:hover { background: var(--surface2); }

        /* Page content */
        .page-content {
            flex: 1;
            padding: 28px 28px 48px;
        }

        /* ─── Page header ─── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .page-header-left h1 {
            font-size: 22px; font-weight: 700;
            color: var(--text); letter-spacing: -.02em;
            line-height: 1.2;
        }
        .page-header-left p {
            font-size: 13px; color: var(--text-sec);
            margin-top: 4px; font-weight: 400;
        }

        /* ─── Cards ─── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }
        .card-header {
            padding: 18px 22px 0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title {
            font-size: 14px; font-weight: 600; color: var(--text);
        }
        .card-body { padding: 18px 22px 22px; }

        /* Stat card */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            display: flex; align-items: center; gap: 16px;
        }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--text); line-height: 1; }
        .stat-label { font-size: 12px; color: var(--text-sec); margin-top: 3px; font-weight: 500; }

        /* Color helpers */
        .bg-blue-soft { background: #dbeafe; color: #1d4ed8; }
        .bg-green-soft { background: #d1fae5; color: #047857; }
        .bg-red-soft { background: #fee2e2; color: #b91c1c; }
        .bg-amber-soft { background: #fef3c7; color: #92400e; }
        .bg-purple-soft { background: #ede9fe; color: #5b21b6; }
        .bg-gray-soft { background: #f3f4f6; color: #374151; }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 16px; border-radius: var(--radius);
            font-size: 13px; font-weight: 600;
            cursor: pointer; border: 1px solid transparent;
            transition: all .15s; text-decoration: none;
            font-family: inherit; white-space: nowrap;
        }
        .btn svg, .btn i { flex-shrink: 0; }
        .btn-primary { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-secondary { background: var(--surface2); color: var(--text); border-color: var(--border); }
        .btn-secondary:hover { background: var(--border); }
        .btn-danger { background: #fef2f2; color: var(--red); border-color: #fecaca; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-success { background: var(--green-light); color: var(--green); border-color: #a7f3d0; }
        .btn-success:hover { background: #d1fae5; }
        .btn-sm { padding: 6px 11px; font-size: 12px; }
        .btn-lg { padding: 12px 22px; font-size: 14px; }
        .btn-full { width: 100%; justify-content: center; }

        /* ─── Badges / Pills ─── */
        .pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 99px;
            font-size: 11.5px; font-weight: 600;
        }
        .pill svg, .pill i { width: 11px; height: 11px; }
        .pill-green { background: #d1fae5; color: #065f46; }
        .pill-yellow { background: #fef3c7; color: #78350f; }
        .pill-red { background: #fee2e2; color: #991b1b; }
        .pill-blue { background: #dbeafe; color: #1e40af; }
        .pill-gray { background: #f3f4f6; color: #374151; }

        /* ─── Alert / Flash ─── */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border-radius: var(--radius);
            font-size: 13.5px; font-weight: 500;
            margin-bottom: 20px;
        }
        .alert svg { flex-shrink: 0; }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info    { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .alert-warning { background: #fffbeb; color: #78350f; border: 1px solid #fde68a; }

        /* ─── Forms ─── */
        .form-label {
            display: block; font-size: 12.5px; font-weight: 600;
            color: var(--text); margin-bottom: 6px;
        }
        .form-control {
            width: 100%; padding: 9px 13px;
            border: 1.5px solid var(--border); border-radius: var(--radius);
            font-size: 13.5px; color: var(--text); background: var(--surface);
            outline: none; transition: border-color .15s, box-shadow .15s;
            font-family: inherit;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .form-control[readonly] { background: var(--surface2); color: var(--text-sec); }
        .form-group { margin-bottom: 18px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }

        /* ─── Table ─── */
        .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            padding: 11px 16px; text-align: left;
            font-size: 11px; font-weight: 700; letter-spacing: .06em;
            text-transform: uppercase; color: var(--text-sec);
            background: var(--surface2); border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .data-table td {
            padding: 13px 16px; font-size: 13.5px;
            border-bottom: 1px solid var(--border); color: var(--text);
            vertical-align: middle;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background: var(--surface2); }

        /* ─── Mobile drawer ─── */
        .mobile-overlay {
            display: none; position: fixed; inset: 0; z-index: 60;
            background: rgba(0,0,0,.5); backdrop-filter: blur(2px);
        }
        .mobile-overlay.open { display: block; }
        .mobile-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 70;
            width: var(--sidebar-w); transform: translateX(-100%);
            transition: transform .25s ease;
            background: var(--sidebar-bg);
            overflow-y: auto;
        }
        .mobile-sidebar.open { transform: translateX(0); }

        /* ─── Animations ─── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .anim-fade { animation: fadeUp .4s ease both; }

        /* ─── Responsive ─── */
        @media (max-width: 1023px) {
            .sidebar { display: none; }
            .topbar { display: flex; }
            .page-content { padding: 20px 16px 48px; }
            .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
        }
        @media (max-width: 639px) {
            .page-header { flex-direction: column; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ═══ SIDEBAR DESKTOP ═══ --}}
    <aside class="sidebar" id="desktopSidebar">
        <div class="sidebar-inner">

            {{-- Logo --}}
            <a href="{{ route('compteController') }}" class="sidebar-logo">
                <div class="sidebar-logo-icon"><span>C</span></div>
                <span class="sidebar-logo-text">Congo<em>Assist</em></span>
            </a>

            {{-- Navigation principale --}}
            <div class="sb-label">Navigation</div>

            <a href="{{ route('compteController') }}"
               class="sb-link {{ request()->routeIs('compteController') ? 'active' : '' }}">
                <i data-feather="layout" style="width:16px;height:16px;"></i>
                Tableau de bord
            </a>
            <a href="{{ route('bilanController') }}"
               class="sb-link {{ request()->routeIs('bilanController') ? 'active' : '' }}">
                <i data-feather="activity" style="width:16px;height:16px;"></i>
                Mon bilan de santé
            </a>
            <a href="{{ route('vaccinationMenuController') }}"
               class="sb-link {{ request()->routeIs('vaccinationMenuController') ? 'active' : '' }}">
                <i data-feather="shield" style="width:16px;height:16px;"></i>
                Vaccinations
            </a>
            <a href="{{ route('actualitesController') }}"
               class="sb-link {{ request()->routeIs('actualitesController') ? 'active' : '' }}">
                <i data-feather="rss" style="width:16px;height:16px;"></i>
                Actualités
            </a>
            <a href="{{ route('MesAlertesController') }}"
               class="sb-link {{ request()->routeIs('MesAlertesController') ? 'active' : '' }}">
                <i data-feather="bell" style="width:16px;height:16px;"></i>
                Urgences
            </a>
            <a href="{{ route('SuiviAlertesController') }}"
               class="sb-link {{ request()->routeIs('SuiviAlertesController') ? 'active' : '' }}">
                <i data-feather="list" style="width:16px;height:16px;"></i>
                Suivi alertes
            </a>
            <a href="{{ route('forumCitoyen') }}"
               class="sb-link {{ request()->routeIs('forumCitoyen') ? 'active' : '' }}">
                <i data-feather="message-square" style="width:16px;height:16px;"></i>
                Forum
            </a>

            <div class="sb-divider"></div>

            <div class="sb-label">Mon compte</div>

            <a href="{{ route('profilController') }}"
               class="sb-link {{ request()->routeIs('profilController') ? 'active' : '' }}">
                <i data-feather="user" style="width:16px;height:16px;"></i>
                Mon profil
            </a>
            <a href="{{ route('codeQR') }}"
               class="sb-link {{ request()->routeIs('codeQR') ? 'active' : '' }}">
                <i data-feather="grid" style="width:16px;height:16px;"></i>
                Code QR santé
            </a>

            <div class="sb-spacer"></div>

            @if(isset($citoyen))
            <div class="sb-user">
                <img src="{{ $citoyen->photo_profil ?: asset('medias/default.png') }}"
                     alt="Profil" class="sb-user-avatar">
                <div style="min-width:0">
                    <div class="sb-user-name">{{ $citoyen->nom ?? '' }} {{ $citoyen->prenom ?? '' }}</div>
                    <div class="sb-user-role">Citoyen actif</div>
                </div>
            </div>
            @endif

            <a href="{{ route('accueil') }}" class="sb-link danger" style="margin-bottom:8px;">
                <i data-feather="log-out" style="width:16px;height:16px;"></i>
                Déconnexion
            </a>

        </div>
    </aside>

    {{-- ═══ MAIN WRAPPER ═══ --}}
    <div class="main-wrapper">

        {{-- Top bar mobile --}}
        <header class="topbar">
            <a href="{{ route('compteController') }}" class="topbar-logo">
                <div class="topbar-logo-icon"><span>C</span></div>
                <span class="topbar-logo-text">Congo<em>Assist</em></span>
            </a>
            <button class="topbar-btn" id="menuToggle" aria-label="Menu">
                <i data-feather="menu" style="width:20px;height:20px;"></i>
            </button>
        </header>

        {{-- Page content --}}
        <main class="page-content">

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="alert alert-success anim-fade">
                <i data-feather="check-circle" style="width:16px;height:16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-error anim-fade">
                <i data-feather="x-circle" style="width:16px;height:16px;"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @yield('content')

        </main>
    </div>

    {{-- ═══ MOBILE OVERLAY ═══ --}}
    <div class="mobile-overlay" id="mobileOverlay"></div>

    {{-- ═══ MOBILE SIDEBAR ═══ --}}
    <aside class="mobile-sidebar" id="mobileSidebar">
        <div class="sidebar-inner">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0 20px 20px;border-bottom:1px solid rgba(255,255,255,.06);margin-bottom:16px;">
                <a href="{{ route('compteController') }}" class="sidebar-logo" style="padding:0;border:none;margin:0;">
                    <div class="sidebar-logo-icon"><span>C</span></div>
                    <span class="sidebar-logo-text">Congo<em>Assist</em></span>
                </a>
                <button id="menuClose" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.5);padding:4px;">
                    <i data-feather="x" style="width:18px;height:18px;"></i>
                </button>
            </div>

            <div class="sb-label">Navigation</div>
            <a href="{{ route('compteController') }}" class="sb-link {{ request()->routeIs('compteController') ? 'active' : '' }}">
                <i data-feather="layout" style="width:16px;height:16px;"></i> Tableau de bord
            </a>
            <a href="{{ route('bilanController') }}" class="sb-link {{ request()->routeIs('bilanController') ? 'active' : '' }}">
                <i data-feather="activity" style="width:16px;height:16px;"></i> Mon bilan de santé
            </a>
            <a href="{{ route('vaccinationMenuController') }}" class="sb-link {{ request()->routeIs('vaccinationMenuController') ? 'active' : '' }}">
                <i data-feather="shield" style="width:16px;height:16px;"></i> Vaccinations
            </a>
            <a href="{{ route('actualitesController') }}" class="sb-link {{ request()->routeIs('actualitesController') ? 'active' : '' }}">
                <i data-feather="rss" style="width:16px;height:16px;"></i> Actualités
            </a>
            <a href="{{ route('MesAlertesController') }}" class="sb-link {{ request()->routeIs('MesAlertesController') ? 'active' : '' }}">
                <i data-feather="bell" style="width:16px;height:16px;"></i> Urgences
            </a>
            <a href="{{ route('SuiviAlertesController') }}" class="sb-link {{ request()->routeIs('SuiviAlertesController') ? 'active' : '' }}">
                <i data-feather="list" style="width:16px;height:16px;"></i> Suivi alertes
            </a>
            <a href="{{ route('forumCitoyen') }}" class="sb-link {{ request()->routeIs('forumCitoyen') ? 'active' : '' }}">
                <i data-feather="message-square" style="width:16px;height:16px;"></i> Forum
            </a>
            <div class="sb-divider"></div>
            <div class="sb-label">Mon compte</div>
            <a href="{{ route('profilController') }}" class="sb-link {{ request()->routeIs('profilController') ? 'active' : '' }}">
                <i data-feather="user" style="width:16px;height:16px;"></i> Mon profil
            </a>
            <a href="{{ route('codeQR') }}" class="sb-link {{ request()->routeIs('codeQR') ? 'active' : '' }}">
                <i data-feather="grid" style="width:16px;height:16px;"></i> Code QR santé
            </a>
            <div class="sb-spacer"></div>
            <a href="{{ route('accueil') }}" class="sb-link danger" style="margin-bottom:12px;">
                <i data-feather="log-out" style="width:16px;height:16px;"></i> Déconnexion
            </a>
        </div>
    </aside>

    <script>
        feather.replace({ 'stroke-width': 1.75 });

        const menuToggle  = document.getElementById('menuToggle');
        const menuClose   = document.getElementById('menuClose');
        const overlay     = document.getElementById('mobileOverlay');
        const mobileSb    = document.getElementById('mobileSidebar');

        function openMenu()  { mobileSb.classList.add('open'); overlay.classList.add('open'); document.body.style.overflow='hidden'; }
        function closeMenu() { mobileSb.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow=''; }

        if (menuToggle) menuToggle.addEventListener('click', openMenu);
        if (menuClose)  menuClose.addEventListener('click', closeMenu);
        if (overlay)    overlay.addEventListener('click', closeMenu);

        document.addEventListener('keydown', e => { if(e.key==='Escape') closeMenu(); });
    </script>

    @stack('scripts')

</body>
</html>
