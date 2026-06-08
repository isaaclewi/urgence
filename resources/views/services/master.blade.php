<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', $service->nom ?? 'CongoAssist') — Dashboard</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        /* ─── Reset & base ─── */
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --accent:       16, 185, 129;
            --accent-light: 209, 250, 229;
            --sidebar-w:    272px;
            --topbar-h:     60px;
        }

        html { -webkit-text-size-adjust: 100%; }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
            min-height: 100vh;
            overflow-x: hidden;   /* no horizontal scroll on any screen */
        }

        /* ─── Role accent colours ─── */
        body[data-role="pompier"]    { --accent: 239,68,68;   --accent-light: 254,226,226; }
        body[data-role="police"]     { --accent: 59,130,246;  --accent-light: 219,234,254; }
        body[data-role="hopital"]    { --accent: 16,185,129;  --accent-light: 209,250,229; }
        body[data-role="electricite"]{ --accent: 245,158,11;  --accent-light: 254,243,199; }

        .accent-bg        { background: rgb(var(--accent)); }
        .accent-text      { color:      rgb(var(--accent)); }
        .accent-border    { border-color: rgb(var(--accent)); }
        .accent-light-bg  { background: rgb(var(--accent-light)); }

        /* ─── Animations ─── */
        @keyframes fadeIn  { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        @keyframes slideIn { from{opacity:0;transform:translateX(-20px)} to{opacity:1;transform:translateX(0)} }
        @keyframes pulse   { 0%,100%{opacity:1} 50%{opacity:.5} }

        .animate-fade-in     { animation: fadeIn  .5s ease-out; }
        .animate-slide-in    { animation: slideIn .4s ease-out; }
        .animate-pulse-slow  { animation: pulse  2s ease-in-out infinite; }

        /* ─── Scrollbar ─── */
        .custom-scrollbar::-webkit-scrollbar       { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background:#f1f5f9; border-radius:10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background:#94a3b8; }

        /* ══════════════════════════════════════
           LAYOUT SHELL
        ══════════════════════════════════════ */
        .shell {
            display: flex;
            min-height: 100vh;
        }

        /* ─── Sidebar ─── */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            background: #fff;
            box-shadow: 2px 0 16px rgba(0,0,0,.06);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            transition: transform .3s ease;
            z-index: 50;
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
        }
        .sidebar-brand-inner {
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-logo-wrap { position: relative; flex-shrink: 0; }
        .sidebar-logo {
            width: 48px; height: 48px;
            border-radius: 12px; object-fit: cover;
            border: 2px solid #f1f5f9;
        }
        .sidebar-badge {
            position: absolute; top: -4px; right: -4px;
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .sidebar-badge svg { width: 10px; height: 10px; color: #fff; }
        .sidebar-name  { font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1.2; }
        .sidebar-role  {
            display: inline-block; margin-top: 4px;
            padding: 2px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: .04em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px 10px;
            overflow-y: auto;
        }

        /* sidebar links — yielded by child views */
        .sidebar-link {
            position: relative;
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px; font-weight: 500;
            color: #475569;
            transition: background .2s, color .2s, padding-left .2s;
            margin-bottom: 2px;
        }
        .sidebar-link svg { width: 18px; height: 18px; flex-shrink: 0; }
        .sidebar-link::before {
            content: '';
            position: absolute; left: 0; top: 0;
            height: 100%; width: 4px;
            background: rgb(var(--accent));
            border-radius: 0 4px 4px 0;
            transform: scaleY(0);
            transition: transform .25s ease;
        }
        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(var(--accent), .08);
            color: rgb(var(--accent));
            padding-left: 20px;
        }
        .sidebar-link:hover::before,
        .sidebar-link.active::before { transform: scaleY(1); }

        .sidebar-profile {
            flex-shrink: 0;
            padding: 12px 16px;
            border-top: 1px solid #f1f5f9;
        }
        .sidebar-profile-inner {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: #f8fafc;
            cursor: pointer;
            transition: background .2s;
        }
        .sidebar-profile-inner:hover { background: #f1f5f9; }
        .sidebar-avatar-wrap { position: relative; flex-shrink: 0; }
        .sidebar-avatar {
            width: 40px; height: 40px;
            border-radius: 50%; object-fit: cover;
            border: 2px solid #fff; box-shadow: 0 1px 4px rgba(0,0,0,.1);
        }
        .sidebar-online {
            position: absolute; top: 0; right: 0;
            width: 11px; height: 11px;
            background: #10b981; border-radius: 50%;
            border: 2px solid #fff;
        }
        .sidebar-profile-name { font-size: 13px; font-weight: 600; color: #0f172a; }
        .sidebar-profile-status { font-size: 11px; color: #10b981; display: flex; align-items: center; gap: 4px; margin-top: 1px; }

        /* ─── Main column ─── */
        .main-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;          /* prevents flex blowout */
            min-height: 100vh;
        }

        /* ─── Top bar (mobile) ─── */
        .topbar {
            display: none;
            position: sticky; top: 0; z-index: 40;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 6px rgba(0,0,0,.06);
            align-items: center;
            padding: 0 16px;
            gap: 12px;
        }
        .topbar-hamburger {
            background: none; border: none; cursor: pointer;
            color: #475569; padding: 6px; border-radius: 8px;
            transition: background .18s;
        }
        .topbar-hamburger:hover { background: #f1f5f9; }
        .topbar-hamburger svg { width: 22px; height: 22px; display: block; }
        .topbar-logo {
            display: flex; align-items: center; gap: 8px;
            margin: 0 auto;
        }
        .topbar-logo img { width: 32px; height: 32px; border-radius: 8px; object-fit: cover; }
        .topbar-logo span { font-size: 15px; font-weight: 700; color: #0f172a; }
        .topbar-spacer { width: 34px; }

        /* ─── Page header ─── */
        .page-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 24px 32px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            animation: fadeIn .5s ease-out;
        }
        .page-header-title { font-size: clamp(20px,4vw,28px); font-weight: 700; color: #0f172a; margin: 0; }
        .page-header-sub   { font-size: 14px; color: #64748b; margin: 4px 0 0; }
        .page-header-right { display: flex; align-items: center; gap: 12px; }
        .service-badge {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 14px; border-radius: 10px;
            font-size: 13px; font-weight: 600;
        }
        .service-badge-dot { width: 10px; height: 10px; border-radius: 50%; }
        .page-header-avatar {
            width: 44px; height: 44px;
            border-radius: 50%; object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        /* ─── Content area ─── */
        .page-content {
            flex: 1;
            padding: 28px 32px;
            overflow-y: auto;
        }

        /* ─── Footer ─── */
        .page-footer {
            background: #fff;
            border-top: 1px solid #f1f5f9;
            padding: 16px 32px;
        }
        .page-footer-inner {
            display: flex; flex-wrap: wrap;
            align-items: center; justify-content: space-between;
            gap: 12px;
            font-size: 13px; color: #94a3b8;
        }
        .page-footer-links { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .page-footer-links a { color: #94a3b8; text-decoration: none; transition: color .18s; }
        .page-footer-links a:hover { color: #0f172a; }

        /* ─── Mobile overlay drawer ─── */
        .mobile-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 60;
            background: rgba(15,23,42,.45);
            backdrop-filter: blur(2px);
        }
        .mobile-overlay.open { display: block; }

        .mobile-drawer {
            position: absolute; top: 0; left: 0;
            width: min(var(--sidebar-w), 85vw);
            height: 100%;
            background: #fff;
            display: flex; flex-direction: column;
            transform: translateX(-100%);
            transition: transform .3s ease;
            overflow: hidden;
        }
        .mobile-overlay.open .mobile-drawer { transform: translateX(0); }

        .drawer-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
        }
        .drawer-brand { display: flex; align-items: center; gap: 10px; }
        .drawer-brand img { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; }
        .drawer-brand-name { font-size: 15px; font-weight: 700; color: #0f172a; }
        .drawer-brand-role { font-size: 11px; font-weight: 600; }

        .drawer-close {
            background: none; border: none; cursor: pointer;
            color: #64748b; padding: 6px; border-radius: 8px;
            transition: background .18s;
        }
        .drawer-close:hover { background: #f1f5f9; }
        .drawer-close svg { width: 20px; height: 20px; display: block; }

        .drawer-nav {
            flex: 1; padding: 12px 10px;
            overflow-y: auto;
        }

        .drawer-profile {
            flex-shrink: 0;
            padding: 12px 16px;
            border-top: 1px solid #f1f5f9;
            display: flex; align-items: center; gap: 10px;
        }
        .drawer-profile img {
            width: 38px; height: 38px;
            border-radius: 50%; object-fit: cover;
        }
        .drawer-profile-name   { font-size: 13px; font-weight: 600; color: #0f172a; }
        .drawer-profile-status { font-size: 11px; color: #10b981; }

        /* ─── Card helpers (child views use these) ─── */
        .card-hover {
            transition: transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,0,0,.12);
        }

        /* ══════════════════════════════════════
           RESPONSIVE BREAKPOINTS
        ══════════════════════════════════════ */

        /* Tablet portrait & smaller — hide desktop sidebar, show topbar */
        @media (max-width: 1023px) {
            .sidebar  { display: none; }
            .topbar   { display: flex; }
            .page-header  { padding: 18px 20px; }
            .page-content { padding: 20px 16px; }
            .page-footer  { padding: 14px 20px; }
        }

        /* Phone — tighter spacing, full-width content */
        @media (max-width: 639px) {
            .page-header       { padding: 14px 16px; }
            .page-header-right { display: none; }   /* hide avatar / badge on small phones */
            .page-content      { padding: 16px 12px; }
            .page-footer       { padding: 12px 16px; }
            .page-footer-links { gap: 10px; }
        }

        /* Very small phones (SE, Galaxy A) */
        @media (max-width: 374px) {
            :root { --topbar-h: 54px; }
            .page-header-title { font-size: 18px; }
            .page-content { padding: 12px 10px; }
        }

        /* Large desktop — wider sidebar comfortable */
        @media (min-width: 1280px) {
            :root { --sidebar-w: 288px; }
        }

        /* Force mobile-overlay to never show on desktop */
        @media (min-width: 1024px) {
            .mobile-overlay { display: none !important; }
        }

        /* ── Utility: prevent text overflow in cards yielded by children ── */
        .truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ── Images never overflow their containers ── */
        img { max-width: 100%; height: auto; }

        /* ── Tables responsive wrapper (child views benefit) ── */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
    @stack('styles')
</head>

<body data-role="{{ $service->role ?? 'hopital' }}">
<div class="shell">

    <!-- ══════════════════════════════
         DESKTOP SIDEBAR
    ══════════════════════════════ -->
    <aside class="sidebar custom-scrollbar">

        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-inner">
                <div class="sidebar-logo-wrap">
                    <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist" class="sidebar-logo">
                    <div class="sidebar-badge accent-bg">
                        <i data-feather="shield"></i>
                    </div>
                </div>
                <div>
                    <div class="sidebar-name">{{ $service->nom ?? 'CongoAssist' }}</div>
                    <span class="sidebar-role accent-text accent-light-bg">
                        {{ ucfirst($service->role ?? 'Service') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav custom-scrollbar">
            @yield('sidebar')
        </nav>

        <!-- Profile -->
        <div class="sidebar-profile">
            <div class="sidebar-profile-inner">
                <div class="sidebar-avatar-wrap">
                    <img src="{{ asset($service->photo_profil ?? 'medias/default.jpg') }}"
                         alt="Profil" class="sidebar-avatar">
                    <span class="sidebar-online"></span>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="sidebar-profile-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $service->nom ?? 'Service' }}
                    </div>
                    <div class="sidebar-profile-status">
                        <span style="width:7px;height:7px;background:#10b981;border-radius:50%;flex-shrink:0;" class="animate-pulse-slow"></span>
                        En ligne
                    </div>
                </div>
                <i data-feather="settings" style="width:16px;height:16px;color:#94a3b8;flex-shrink:0;"></i>
            </div>
        </div>
    </aside>

    <!-- ══════════════════════════════
         MAIN COLUMN
    ══════════════════════════════ -->
    <div class="main-col">

        <!-- Mobile top bar -->
        <div class="topbar">
            <button class="topbar-hamburger" id="hamburger" aria-label="Ouvrir le menu">
                <i data-feather="menu"></i>
            </button>
            <div class="topbar-logo">
                <img src="{{ asset('medias/Clogo.jpg') }}" alt="Logo">
                <span>{{ $service->nom ?? 'CongoAssist' }}</span>
            </div>
            <div class="topbar-spacer"></div>
        </div>

        <!-- Page header -->
        <header class="page-header">
            <div>
                <h1 class="page-header-title">@yield('page-title', 'Dashboard')</h1>
                <p class="page-header-sub">@yield('page-subtitle', 'Bienvenue sur votre tableau de bord')</p>
            </div>
            <div class="page-header-right">
                <div class="service-badge accent-light-bg">
                    <span class="service-badge-dot accent-bg animate-pulse-slow"></span>
                    <span class="accent-text" style="font-size:13px;font-weight:600;">Service actif</span>
                </div>
                <img src="{{ asset($service->photo_profil ?? 'medias/default.jpg') }}"
                     alt="Profil" class="page-header-avatar">
            </div>
        </header>

        <!-- Content -->
        <main class="page-content custom-scrollbar">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="page-footer">
            <div class="page-footer-inner">
                <span>© 2025 CongoAssist — Plateforme de gestion des urgences</span>
                <div class="page-footer-links">
                    <a href="#">Support</a>
                    <a href="#">Documentation</a>
                    <a href="#">Confidentialité</a>
                </div>
            </div>
        </footer>

    </div><!-- /main-col -->
</div><!-- /shell -->

<!-- ══════════════════════════════
     MOBILE DRAWER
══════════════════════════════ -->
<div class="mobile-overlay" id="mobileOverlay">
    <div class="mobile-drawer">

        <!-- Drawer header -->
        <div class="drawer-header">
            <div class="drawer-brand">
                <img src="{{ asset('medias/Clogo.jpg') }}" alt="Logo">
                <div>
                    <div class="drawer-brand-name">{{ $service->nom ?? 'CongoAssist' }}</div>
                    <div class="drawer-brand-role accent-text">{{ ucfirst($service->role ?? 'Service') }}</div>
                </div>
            </div>
            <button class="drawer-close" id="drawerClose" aria-label="Fermer">
                <i data-feather="x"></i>
            </button>
        </div>

        <!-- Drawer nav (same links as sidebar) -->
        <nav class="drawer-nav custom-scrollbar">
            @yield('sidebar')
        </nav>

        <!-- Drawer profile -->
        <div class="drawer-profile">
            <img src="{{ asset($service->photo_profil ?? 'medias/default.jpg') }}" alt="Profil">
            <div>
                <div class="drawer-profile-name">{{ $service->nom ?? 'Service' }}</div>
                <div class="drawer-profile-status">● En ligne</div>
            </div>
        </div>
    </div>
</div>

<script>
feather.replace();

/* ── Mobile drawer ── */
const overlay     = document.getElementById('mobileOverlay');
const hamburger   = document.getElementById('hamburger');
const drawerClose = document.getElementById('drawerClose');

function openDrawer()  { overlay.classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeDrawer() { overlay.classList.remove('open'); document.body.style.overflow = ''; }

hamburger?.addEventListener('click',   openDrawer);
drawerClose?.addEventListener('click', closeDrawer);
overlay?.addEventListener('click', e => { if (e.target === overlay) closeDrawer(); });

/* Close drawer when a link inside is tapped (UX) */
document.querySelectorAll('.drawer-nav .sidebar-link').forEach(a => {
    a.addEventListener('click', closeDrawer);
});
</script>

@stack('scripts')
</body>
</html>