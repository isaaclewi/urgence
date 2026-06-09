<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CongoAssist — Espace Équipe')</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    {{-- Leaflet CSS pour la cartographie --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Palette identité équipe — orange terrain, sombre professionnel */
            --brand:        #0B1E3D;
            --accent:       #F97316;
            --accent-dark:  #EA6A00;
            --accent-light: #FFF7ED;
            --success:      #10B981;
            --warning:      #F59E0B;
            --danger:       #EF4444;
            --info:         #3B82F6;

            --bg:           #F1F5F9;
            --surface:      #FFFFFF;
            --surface2:     #F8FAFC;
            --border:       #E2E8F0;
            --text:         #0F172A;
            --text-sec:     #475569;
            --text-muted:   #94A3B8;

            --sidebar-w:    240px;
            --header-h:     60px;
            --radius:       12px;
            --shadow:       0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md:    0 4px 6px -1px rgba(0,0,0,.08), 0 2px 4px -1px rgba(0,0,0,.04);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: grid;
            grid-template-columns: var(--sidebar-w) 1fr;
            grid-template-rows: var(--header-h) 1fr;
            min-height: 100vh;
        }

        /* ── HEADER ── */
        .app-header {
            grid-column: 1 / -1;
            background: var(--brand);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px 0 0;
            z-index: 40;
            box-shadow: 0 1px 0 rgba(255,255,255,.05);
        }
        .header-brand {
            width: var(--sidebar-w);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            text-decoration: none;
        }
        .brand-mark {
            width: 34px; height: 34px;
            background: var(--accent);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-mark span {
            font-family: 'Sora', sans-serif;
            font-size: 16px; font-weight: 700;
            color: #fff;
        }
        .brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 1rem; font-weight: 700;
            color: #fff;
        }
        .brand-name em { font-style: normal; color: var(--accent); }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-badge {
            background: rgba(249,115,22,.15);
            border: 1px solid rgba(249,115,22,.3);
            color: var(--accent);
            font-size: 11px; font-weight: 700;
            padding: 3px 10px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .header-user {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px; font-weight: 600;
            color: rgba(255,255,255,.8);
        }
        .header-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            overflow: hidden;
        }
        .header-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* ── SIDEBAR ── */
        .app-sidebar {
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 20px 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            overflow-y: auto;
        }
        .sb-section-label {
            font-size: 10px; font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 8px 10px 4px;
            margin-top: 4px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px; font-weight: 500;
            color: var(--text-sec);
            transition: background .15s, color .15s;
            position: relative;
        }
        .sidebar-link:hover { background: var(--surface2); color: var(--text); }
        .sidebar-link.active {
            background: var(--accent-light);
            color: var(--accent);
            font-weight: 600;
        }
        .sidebar-link.active svg { stroke: var(--accent); }
        .sidebar-link.danger:hover { background: #FEE2E2; color: var(--danger); }
        .sidebar-link.danger:hover svg { stroke: var(--danger); }
        .sb-badge {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: 10px; font-weight: 700;
            padding: 1px 6px;
            border-radius: 100px;
        }
        .sb-divider {
            height: 1px;
            background: var(--border);
            margin: 8px 0;
        }

        /* ── MAIN ── */
        .app-main {
            overflow-y: auto;
            padding: 24px;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            margin-bottom: 20px;
        }
        .page-title {
            font-family: 'Sora', sans-serif;
            font-size: 20px; font-weight: 700;
            color: var(--text);
            display: flex; align-items: center; gap: 8px;
        }
        .page-title svg { stroke: var(--accent); }
        .page-subtitle {
            font-size: 13px; color: var(--text-muted);
            margin-top: 2px;
        }

        /* ── CARDS ── */
        .content-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }
        .cc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }
        .cc-title {
            font-family: 'Sora', sans-serif;
            font-size: 15px; font-weight: 700;
            color: var(--text);
            display: flex; align-items: center; gap: 8px;
        }
        .cc-subtitle {
            font-size: 12px; color: var(--text-muted);
            margin-top: 2px;
        }
        .cc-body { padding: 20px; }

        /* ── STAT CARDS ── */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: var(--shadow);
        }
        .sc-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .sc-value {
            font-family: 'Sora', sans-serif;
            font-size: 26px; font-weight: 700;
            color: var(--text);
        }
        .sc-label {
            font-size: 12px; color: var(--text-muted);
            font-weight: 500;
        }

        /* ── TABLE ── */
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .data-table th {
            background: var(--surface2);
            padding: 10px 14px;
            text-align: left;
            font-size: 11px; font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .data-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover td { background: #FAFBFC; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px; font-weight: 600;
            font-family: inherit;
            border: none; cursor: pointer;
            text-decoration: none;
            transition: all .15s;
        }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-accent { background: var(--accent); color: #fff; }
        .btn-accent:hover { background: var(--accent-dark); }
        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--border);
            color: var(--text-sec);
        }
        .btn-outline:hover { border-color: var(--text-sec); color: var(--text); }
        .btn-danger { background: #FEE2E2; color: var(--danger); }
        .btn-success { background: #D1FAE5; color: #065F46; }

        /* ── PILLS / BADGES ── */
        .pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px;
            border-radius: 100px;
            font-size: 11px; font-weight: 700;
            white-space: nowrap;
        }
        .pill svg { width: 11px; height: 11px; }
        .pill-orange { background: #FFF7ED; color: #C2410C; }
        .pill-yellow { background: #FEF3C7; color: #92400E; }
        .pill-green  { background: #D1FAE5; color: #065F46; }
        .pill-blue   { background: #DBEAFE; color: #1E40AF; }
        .pill-red    { background: #FEE2E2; color: #991B1B; }

        /* ── MAP ── */
        .map-container {
            width: 100%;
            height: 400px;
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        /* ── ALERTS ── */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            border-radius: 9px;
            font-size: 13px; font-weight: 500;
            margin-bottom: 16px;
        }
        .alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .alert-error   { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
        }
        .empty-icon {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: var(--surface2);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .empty-icon svg { stroke: var(--text-muted); }
        .empty-state p { font-size: 14px; font-weight: 600; color: var(--text-sec); margin-bottom: 4px; }
        .empty-state span { font-size: 12px; color: var(--text-muted); }

        /* ── MODAL ── */
        .modal-backdrop {
            position: fixed; inset: 0; z-index: 80;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center; justify-content: center;
            padding: 16px;
        }
        .modal-backdrop.open { display: flex; }
        .modal-box {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
            width: 100%; max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }
        .modal-title {
            font-family: 'Sora', sans-serif;
            font-size: 15px; font-weight: 700;
            color: var(--text);
        }
        .modal-body { padding: 20px; }
        .modal-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex; justify-content: flex-end; gap: 8px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .app-sidebar { display: none; }
            .app-main { padding: 16px; }
        }

        /* ── ANIMATIONS ── */
        .anim-fade { animation: fadeIn .3s ease; }
        .anim-slide { animation: slideUp .3s ease; }
        @keyframes fadeIn  { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── HEADER ── --}}
<header class="app-header">
    <a href="{{ route('equipe.dashboard') }}" class="header-brand">
        <div class="brand-mark"><span>C</span></div>
        <span class="brand-name">Congo<em>Assist</em></span>
    </a>
    <div class="header-right">
        <span class="header-badge">Équipe terrain</span>
        <div class="header-user">
            <div class="header-avatar">
                @if(session('equipe_photo'))
                    <img src="{{ asset(session('equipe_photo')) }}" alt="">
                @else
                    {{ strtoupper(substr(session('equipe_nom', 'E'), 0, 1)) }}
                @endif
            </div>
            <span>{{ session('equipe_nom', 'Équipe') }}</span>
        </div>
    </div>
</header>

{{-- ── SIDEBAR ── --}}
<aside class="app-sidebar">
    <div class="sb-section-label">Navigation</div>

    <a href="{{ route('equipe.dashboard') }}" class="sidebar-link {{ request()->routeIs('equipe.dashboard') ? 'active' : '' }}">
        <i data-feather="home"></i>
        Tableau de bord
    </a>

    <a href="{{ route('equipe.alertes') }}" class="sidebar-link {{ request()->routeIs('equipe.alertes*') ? 'active' : '' }}">
        <i data-feather="bell"></i>
        Mes alertes
        {{-- Badge alertes transmises non traitées --}}
    </a>

    <a href="{{ route('equipe.profil') }}" class="sidebar-link {{ request()->routeIs('equipe.profil') ? 'active' : '' }}">
        <i data-feather="settings"></i>
        Profil équipe
    </a>

    <div class="sb-divider"></div>

    <form method="POST" action="{{ route('equipe.logout') }}">
        @csrf
        <button type="submit" class="sidebar-link danger" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
            <i data-feather="log-out"></i>
            Déconnexion
        </button>
    </form>
</aside>

{{-- ── MAIN ── --}}
<main class="app-main">

    @if(session('success'))
    <div class="alert alert-success anim-fade">
        <i data-feather="check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error anim-fade">
        <i data-feather="alert-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="page-header">
        <h1 class="page-title">
            <i data-feather="@yield('page-icon', 'home')"></i>
            @yield('page-title', 'Dashboard')
        </h1>
        <p class="page-subtitle">@yield('page-subtitle', '')</p>
    </div>

    @yield('content')

</main>

<script>
    feather.replace({ width: 16, height: 16 });
</script>
@stack('scripts')
</body>
</html>
