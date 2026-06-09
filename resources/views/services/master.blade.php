<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', $service->nom ?? 'CongoAssist') — Plateforme Nationale</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Design tokens — miroir exact de l'accueil & login ── */
        :root {
            --brand-dark:   #0B1E3D;
            --brand-mid:    #1A3E6E;
            --accent:       #1DB87A;
            --accent-dark:  #15A066;

            --bg:           #F0F4F9;
            --surface:      #FFFFFF;
            --surface2:     #F7F9FC;
            --border:       #E2E8F0;
            --border-mid:   #CBD5E1;

            --text:         #0F172A;
            --text-sec:     #4A5568;
            --text-muted:   #64748B;

            --flag-g:       #009E60;
            --flag-y:       #FBDE4A;
            --flag-r:       #CE1126;

            --sidebar-w:    248px;
            --topbar-h:     60px;
        }

        /* ── Role color overrides ── */
        body[data-role="pompier"]     { --accent: #E83A3A; --accent-dark: #C0392B; }
        body[data-role="police"]      { --accent: #2563EB; --accent-dark: #1D4ED8; }
        body[data-role="hopital"]     { --accent: #1DB87A; --accent-dark: #15A066; }
        body[data-role="electricite"] { --accent: #D97706; --accent-dark: #B45309; }
        body[data-role="vaccination"] { --accent: #7C3AED; --accent-dark: #6D28D9; }

        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        img { max-width: 100%; height: auto; display: block; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-mid); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        /* ════════════════════════════
           LAYOUT SHELL
        ════════════════════════════ */
        .shell { display: flex; min-height: 100vh; }

        /* ════════════════════════════
           SIDEBAR
        ════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--brand-dark);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            z-index: 50;
        }

        /* Bandeau drapeau — identique au login */
        .sidebar-stripe {
            height: 3px;
            background: linear-gradient(90deg,
                var(--flag-g)  0%   33.3%,
                var(--flag-y) 33.3% 66.6%,
                var(--flag-r) 66.6% 100%);
            flex-shrink: 0;
        }

        /* Brand */
        .sb-brand {
            padding: 18px 16px 14px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            flex-shrink: 0;
        }
        .sb-brand-row { display: flex; align-items: center; gap: 11px; }
        .sb-logo-wrap { position: relative; flex-shrink: 0; }
        .sb-logo {
            width: 40px; height: 40px;
            border-radius: 10px;
            object-fit: cover;
            border: 1.5px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.06);
        }
        .sb-logo-badge {
            position: absolute;
            top: -4px; right: -4px;
            width: 16px; height: 16px;
            border-radius: 50%;
            background: var(--accent);
            border: 2px solid var(--brand-dark);
            display: flex; align-items: center; justify-content: center;
        }
        .sb-logo-badge i { font-size: 8px; color: #fff; }

        .sb-name {
            font-family: 'Sora', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            color: #F8FAFC;
            line-height: 1.25;
            letter-spacing: -.01em;
        }
        .sb-role-pill {
            display: inline-block;
            margin-top: 3px;
            padding: 1px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            background: rgba(255,255,255,.06);
            color: var(--accent);
            border: 1px solid rgba(255,255,255,.08);
        }
        .sb-gov-label {
            margin-top: 5px;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.22);
        }

        /* Nav */
        .sb-nav {
            flex: 1;
            padding: 10px 8px;
            overflow-y: auto;
        }
        .sb-nav::-webkit-scrollbar { width: 3px; }
        .sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 3px; }

        .sb-section-label {
            padding: 10px 10px 4px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255,255,255,.2);
        }

        .sidebar-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,.5);
            transition: background .17s, color .17s, padding-left .2s;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar-link i {
            font-size: 15px;
            flex-shrink: 0;
            opacity: .65;
            transition: opacity .17s;
        }
        .sidebar-link .sb-lbl {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%) scaleY(0);
            width: 3px; height: 55%;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
            transition: transform .22s ease;
        }
        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255,255,255,.06);
            color: #F8FAFC;
            padding-left: 14px;
        }
        .sidebar-link:hover i,
        .sidebar-link.active i { opacity: 1; }
        .sidebar-link:hover::before,
        .sidebar-link.active::before { transform: translateY(-50%) scaleY(1); }
        .sidebar-link.active {
            background: rgba(255,255,255,.07);
            color: var(--accent);
            font-weight: 600;
        }
        .sidebar-link.active i { color: var(--accent); opacity: 1; }

        .sidebar-link.danger { color: rgba(232,58,58,.65); }
        .sidebar-link.danger:hover { background: rgba(232,58,58,.08); color: #E83A3A; }

        .sb-badge {
            margin-left: auto;
            min-width: 19px; height: 19px;
            padding: 0 5px;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sb-divider {
            height: 1px;
            background: rgba(255,255,255,.06);
            margin: 6px 8px;
        }

        /* Profile strip */
        .sb-profile {
            flex-shrink: 0;
            padding: 10px 12px;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .sb-profile-inner {
            display: flex; align-items: center; gap: 9px;
            padding: 9px 11px;
            border-radius: 8px;
            background: rgba(255,255,255,.04);
            cursor: pointer;
            transition: background .17s;
        }
        .sb-profile-inner:hover { background: rgba(255,255,255,.08); }

        .sb-avatar-wrap { position: relative; flex-shrink: 0; }
        .sb-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,.12);
            background: var(--brand-mid);
        }
        .sb-online {
            position: absolute; top: 0; right: 0;
            width: 9px; height: 9px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid var(--brand-dark);
        }
        .sb-pname {
            font-size: 12.5px;
            font-weight: 600;
            color: #F8FAFC;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }
        .sb-pstatus {
            font-size: 10px;
            color: #10b981;
            margin-top: 1px;
            display: flex; align-items: center; gap: 4px;
        }
        .sb-pstatus-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot { 0%,100%{opacity:1;} 50%{opacity:.35;} }

        /* ════════════════════════════
           MAIN COLUMN
        ════════════════════════════ */
        .main-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
        }

        /* ── Topbar (desktop) ── */
        .topbar {
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 30;
            flex-shrink: 0;
        }
        .topbar-left { display: flex; align-items: center; gap: 14px; }
        .topbar-page-title {
            font-family: 'Sora', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -.01em;
        }
        .topbar-page-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 1px;
        }
        .topbar-right { display: flex; align-items: center; gap: 8px; }

        .tb-status {
            display: flex; align-items: center; gap: 6px;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 600;
            background: rgba(29,184,122,.08);
            color: #059669;
            border: 1px solid rgba(29,184,122,.18);
        }
        .tb-status-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        .tb-icon-btn {
            width: 34px; height: 34px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: background .17s, border-color .17s;
            position: relative;
        }
        .tb-icon-btn:hover { background: var(--surface2); border-color: var(--border-mid); }
        .tb-icon-btn i { font-size: 16px; color: var(--text-muted); }
        .tb-notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--accent);
            border: 1.5px solid var(--surface);
        }

        .tb-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--border);
            background: var(--brand-mid);
            cursor: pointer;
        }

        /* Mobile topbar */
        .topbar-mobile {
            display: none;
            height: var(--topbar-h);
            background: var(--brand-dark);
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 40;
            flex-shrink: 0;
        }
        /* Bandeau drapeau mobile */
        .topbar-mobile::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg,
                var(--flag-g)  0%   33.3%,
                var(--flag-y) 33.3% 66.6%,
                var(--flag-r) 66.6% 100%);
        }
        .mob-btn {
            background: none; border: none;
            cursor: pointer; padding: 6px; border-radius: 6px;
            color: rgba(255,255,255,.7);
            transition: background .17s; flex-shrink: 0;
        }
        .mob-btn:hover { background: rgba(255,255,255,.1); }
        .mob-btn i { font-size: 20px; display: block; }

        .mob-brand {
            flex: 1;
            display: flex; align-items: center; justify-content: center; gap: 9px;
        }
        .mob-brand img {
            width: 28px; height: 28px;
            border-radius: 7px; object-fit: cover;
        }
        .mob-brand-name {
            font-family: 'Sora', sans-serif;
            font-size: 14px; font-weight: 700; color: #F8FAFC;
        }

        /* ── Breadcrumb ── */
        .breadcrumb {
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            padding: 8px 28px;
            display: flex; align-items: center; gap: 5px;
            font-size: 11.5px;
            color: var(--text-muted);
            flex-shrink: 0;
        }
        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color .17s;
        }
        .breadcrumb a:hover { color: var(--text); }
        .breadcrumb i { font-size: 11px; }

        /* ── Page header ── */
        .page-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 20px 28px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-shrink: 0;
        }
        .ph-left h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(17px, 2.5vw, 22px);
            font-weight: 700;
            color: var(--text);
            letter-spacing: -.015em;
            line-height: 1.2;
        }
        .ph-left p { font-size: 12.5px; color: var(--text-muted); margin-top: 3px; }
        .ph-right { display: flex; align-items: center; gap: 10px; }

        /* ── Content & footer ── */
        .page-content { flex: 1; padding: 28px; }

        .page-footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 13px 28px;
            flex-shrink: 0;
        }
        .pf-inner {
            display: flex; flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 11.5px;
            color: var(--text-muted);
        }
        .pf-gov {
            display: flex; align-items: center; gap: 8px;
            font-weight: 600; color: var(--text);
        }
        .pf-flag {
            width: 22px; height: 13px;
            background: linear-gradient(90deg,
                var(--flag-g)  0%   33.3%,
                var(--flag-y) 33.3% 66.6%,
                var(--flag-r) 66.6% 100%);
            border-radius: 2px;
        }
        .pf-links { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .pf-links a { color: var(--text-muted); text-decoration: none; transition: color .17s; }
        .pf-links a:hover { color: var(--accent); }

        /* ════════════════════════════
           MOBILE DRAWER
        ════════════════════════════ */
        .mob-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 60;
            background: rgba(0,0,0,.5);
            backdrop-filter: blur(4px);
        }
        .mob-overlay.open { display: block; }
        .mob-drawer {
            position: absolute;
            top: 0; left: 0;
            width: min(var(--sidebar-w), 88vw);
            height: 100%;
            background: var(--brand-dark);
            display: flex; flex-direction: column;
            transform: translateX(-100%);
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }
        .mob-drawer::before {
            content: '';
            display: block; height: 3px; flex-shrink: 0;
            background: linear-gradient(90deg,
                var(--flag-g)  0%   33.3%,
                var(--flag-y) 33.3% 66.6%,
                var(--flag-r) 66.6% 100%);
        }
        .mob-overlay.open .mob-drawer { transform: translateX(0); }

        .mob-header {
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            flex-shrink: 0;
        }
        .mob-logo { display: flex; align-items: center; gap: 9px; }
        .mob-logo img { width: 34px; height: 34px; border-radius: 8px; object-fit: cover; }
        .mob-logo-name {
            font-family: 'Sora', sans-serif;
            font-size: 13px; font-weight: 700; color: #F8FAFC;
        }
        .mob-logo-role { font-size: 9px; font-weight: 700; color: var(--accent); letter-spacing: .06em; text-transform: uppercase; margin-top: 1px; }
        .mob-close {
            background: none; border: none; cursor: pointer;
            padding: 6px; border-radius: 6px;
            color: rgba(255,255,255,.5);
            transition: background .17s;
        }
        .mob-close:hover { background: rgba(255,255,255,.1); }
        .mob-close i { font-size: 18px; display: block; }

        .mob-nav { flex: 1; padding: 8px; overflow-y: auto; }
        .mob-nav::-webkit-scrollbar { width: 3px; }
        .mob-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 3px; }

        .mob-foot {
            flex-shrink: 0;
            padding: 10px 12px;
            border-top: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; gap: 9px;
        }
        .mob-foot img { width: 32px; height: 32px; border-radius: 50%; border: 2px solid rgba(255,255,255,.12); object-fit: cover; }
        .mob-foot-name { font-size: 12px; font-weight: 600; color: #F8FAFC; }
        .mob-foot-status { font-size: 10px; color: #10b981; }

        /* ════════════════════════════
           SHARED COMPONENTS
           (pour les child blades)
        ════════════════════════════ */

        /* Stat card */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
            transition: box-shadow .22s, transform .22s;
        }
        .stat-card:hover {
            box-shadow: 0 6px 24px rgba(11,30,61,.07);
            transform: translateY(-2px);
        }
        .stat-card::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
        }
        .stat-card.accent::after  { background: var(--accent); }
        .stat-card.blue::after    { background: #3B82F6; }
        .stat-card.green::after   { background: #10B981; }
        .stat-card.red::after     { background: #EF4444; }
        .stat-card.yellow::after  { background: #F59E0B; }
        .stat-card.purple::after  { background: #8B5CF6; }
        .stat-card.orange::after  { background: #F97316; }

        .sc-icon {
            width: 42px; height: 42px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
        }
        .sc-icon i { font-size: 20px; }
        .sc-value {
            font-family: 'Sora', sans-serif;
            font-size: 26px; font-weight: 700;
            letter-spacing: -.03em; color: var(--text); line-height: 1;
        }
        .sc-label { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-top: 4px; }
        .sc-trend { font-size: 11.5px; font-weight: 600; margin-top: 8px; display: flex; align-items: center; gap: 4px; }
        .sc-trend.up   { color: #059669; }
        .sc-trend.down { color: #DC2626; }
        .sc-trend.neu  { color: var(--text-muted); }

        /* Content card */
        .content-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        .cc-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; flex-wrap: wrap;
        }
        .cc-title {
            font-size: 14px; font-weight: 700; color: var(--text);
            display: flex; align-items: center; gap: 8px;
        }
        .cc-title i { font-size: 16px; color: var(--accent); }
        .cc-subtitle { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
        .cc-body { padding: 18px 20px; }

        /* Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead tr {
            background: var(--surface2);
            border-bottom: 1.5px solid var(--border);
        }
        .data-table thead th {
            padding: 10px 14px;
            text-align: left;
            font-size: 10.5px; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
            color: var(--text-muted); white-space: nowrap;
        }
        .data-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .14s;
        }
        .data-table tbody tr:hover { background: var(--surface2); }
        .data-table tbody td { padding: 12px 14px; font-size: 13px; color: var(--text); }
        .data-table tbody tr:last-child { border-bottom: none; }

        /* Pill / badge */
        .pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 999px;
            font-size: 10.5px; font-weight: 700;
            white-space: nowrap;
        }
        .pill i { font-size: 10px; }
        .pill-green  { background: #D1FAE5; color: #065F46; }
        .pill-red    { background: #FEE2E2; color: #991B1B; }
        .pill-yellow { background: #FEF3C7; color: #92400E; }
        .pill-blue   { background: #DBEAFE; color: #1E40AF; }
        .pill-gray   { background: #F1F5F9; color: #475569; }
        .pill-purple { background: #EDE9FE; color: #5B21B6; }
        .pill-accent { background: rgba(29,184,122,.1); color: #065F46; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            font-family: inherit;
            cursor: pointer; border: none;
            transition: all .17s;
            text-decoration: none; white-space: nowrap;
        }
        .btn i { font-size: 15px; }
        .btn-accent {
            background: var(--accent);
            color: #fff;
        }
        .btn-accent:hover {
            background: var(--accent-dark);
            box-shadow: 0 4px 14px rgba(29,184,122,.3);
            transform: translateY(-1px);
        }
        .btn-outline {
            background: transparent;
            color: var(--text);
            border: 1.5px solid var(--border);
        }
        .btn-outline:hover { background: var(--surface2); border-color: var(--border-mid); }
        .btn-danger { background: #EF4444; color: #fff; }
        .btn-danger:hover { background: #DC2626; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 7px; }
        .btn-icon { padding: 7px; border-radius: 7px; }
        .btn:active { transform: scale(0.98) !important; }

        /* Avatars / initiales */
        .avatar {
            width: 36px; height: 36px;
            border-radius: 50%; object-fit: cover;
            border: 1.5px solid var(--border);
            flex-shrink: 0;
        }
        .avatar-initials {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(29,184,122,.1);
            color: var(--accent);
            font-size: 12.5px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* Form */
        .form-label {
            display: block;
            font-size: 12px; font-weight: 600;
            color: var(--text); margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 13.5px; font-family: inherit;
            color: var(--text); background: var(--surface);
            outline: none;
            transition: border-color .17s, box-shadow .17s;
        }
        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(29,184,122,.1);
        }
        .form-input.error { border-color: #EF4444; }
        .form-input.error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.1); }

        /* Alerts */
        .alert {
            padding: 11px 14px; border-radius: 9px;
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 13px; border-left: 3px solid transparent;
        }
        .alert i { font-size: 15px; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: #F0FDF4; border-color: #10B981; color: #065F46; }
        .alert-error   { background: #FEF2F2; border-color: #EF4444; color: #991B1B; }
        .alert-info    { background: #EFF6FF; border-color: #3B82F6; color: #1E40AF; }
        .alert-warn    { background: #FFFBEB; border-color: #F59E0B; color: #92400E; }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 80;
            background: rgba(0,0,0,.4);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
        }
        .modal-box {
            background: var(--surface);
            border-radius: 14px;
            width: 100%; max-width: 500px;
            max-height: 88vh; overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,.18);
        }
        .modal-header {
            padding: 18px 22px 14px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-title { font-size: 15px; font-weight: 700; color: var(--text); }
        .modal-body  { padding: 18px 22px; }
        .modal-footer {
            padding: 14px 22px;
            border-top: 1px solid var(--border);
            display: flex; justify-content: flex-end; gap: 9px;
        }

        /* Grid helpers */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* Animations */
        @keyframes fadeUp   { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slideIn  { from { opacity:0; transform:translateX(-12px);} to { opacity:1; transform:translateX(0); } }
        .anim-fade  { animation: fadeUp  .38s ease both; }
        .anim-slide { animation: slideIn .34s ease both; }

        /* ── Responsive ── */
        @media (max-width: 1023px) {
            .sidebar       { display: none; }
            .topbar        { display: none; }
            .topbar-mobile { display: flex; }
            .breadcrumb    { display: none; }
            .page-header   { padding: 14px 18px; }
            .page-content  { padding: 18px 14px; }
            .page-footer   { padding: 11px 18px; }
        }
        @media (max-width: 639px) {
            .page-header   { padding: 12px 14px; }
            .ph-right      { display: none; }
            .page-content  { padding: 14px 12px; }
            .page-footer   { padding: 10px 14px; }
            .grid-4, .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 479px) {
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .page-content { padding: 12px 10px; }
        }
        @media (min-width: 1024px) {
            .mob-overlay { display: none !important; }
        }
        @media (min-width: 1280px) {
            :root { --sidebar-w: 260px; }
        }
    </style>
    @stack('styles')
</head>

<body data-role="{{ $service->role ?? 'hopital' }}">
<div class="shell">

    {{-- ══════════════════════
         SIDEBAR DESKTOP
    ══════════════════════ --}}
    <aside class="sidebar" aria-label="Navigation principale">
        <div class="sidebar-stripe"></div>

        {{-- Brand --}}
        <div class="sb-brand">
            <div class="sb-brand-row">
                <div class="sb-logo-wrap">
                    <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist" class="sb-logo">
                    <div class="sb-logo-badge">
                        <i data-feather="shield" style="width:8px;height:8px;stroke:#fff;stroke-width:3;"></i>
                    </div>
                </div>
                <div style="min-width:0;">
                    <div class="sb-name">{{ Str::limit($service->nom ?? 'CongoAssist', 22) }}</div>
                    <span class="sb-role-pill">{{ ucfirst($service->role ?? 'Service') }}</span>
                    <div class="sb-gov-label">République du Congo</div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="sb-nav">
            @yield('sidebar')
        </nav>

        {{-- Profile --}}
        <div class="sb-profile">
            <div class="sb-profile-inner">
                <div class="sb-avatar-wrap">
                    <img src="{{ $service->photo_profil ?? asset('medias/default.jpg') }}"
                         alt="Profil" class="sb-avatar"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div style="display:none;width:34px;height:34px;border-radius:50%;background:var(--brand-mid);align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--accent);">
                        {{ strtoupper(substr($service->nom ?? 'S', 0, 1)) }}
                    </div>
                    <span class="sb-online"></span>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="sb-pname">{{ Str::limit($service->nom ?? 'Service', 18) }}</div>
                    <div class="sb-pstatus">
                        <span class="sb-pstatus-dot"></span>
                        En ligne
                    </div>
                </div>
                <i data-feather="more-vertical" style="width:13px;height:13px;color:rgba(255,255,255,.25);flex-shrink:0;"></i>
            </div>
        </div>
    </aside>

    {{-- ══════════════════════
         MAIN COLUMN
    ══════════════════════ --}}
    <div class="main-col">

        {{-- Mobile topbar --}}
        <div class="topbar-mobile" role="banner">
            <button class="mob-btn" id="hamburger" aria-label="Ouvrir le menu">
                <i data-feather="menu"></i>
            </button>
            <div class="mob-brand">
                <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist">
                <span class="mob-brand-name">{{ $service->nom ?? 'CongoAssist' }}</span>
            </div>
            <button class="mob-btn" aria-label="Notifications">
                <i data-feather="bell"></i>
            </button>
        </div>

        {{-- Desktop topbar --}}
        <div class="topbar" role="banner">
            <div class="topbar-left">
                <div>
                    <div class="topbar-page-title">@yield('page-title', 'Tableau de bord')</div>
                    <div class="topbar-page-sub">@yield('page-subtitle', 'Bienvenue sur votre espace de gestion')</div>
                </div>
            </div>
            <div class="topbar-right">
                <div class="tb-status">
                    <span class="tb-status-dot"></span>
                    Service actif
                </div>
                <div class="tb-icon-btn" title="Notifications">
                    <i data-feather="bell"></i>
                    <span class="tb-notif-dot"></span>
                </div>
                <div class="tb-icon-btn" title="Aide">
                    <i data-feather="help-circle"></i>
                </div>
                <img src="{{ $service->photo_profil ?? asset('medias/default.jpg') }}"
                     alt="Profil" class="tb-avatar"
                     onerror="this.style.background='var(--brand-mid)';">
            </div>
        </div>

        {{-- Breadcrumb --}}
        <nav class="breadcrumb" aria-label="Fil d'Ariane">
            <i data-feather="home"></i>
            <a href="{{ route('services.compte') }}">Accueil</a>
            <i data-feather="chevron-right"></i>
            <span>@yield('page-title', 'Dashboard')</span>
        </nav>

        {{-- Page header --}}
        <header class="page-header">
            <div class="ph-left">
                <h1>@yield('page-title', 'Tableau de bord')</h1>
                <p>@yield('page-subtitle', 'Vue d\'ensemble de votre activité')</p>
            </div>
            <div class="ph-right">
                @yield('page-actions')
            </div>
        </header>

        {{-- Main content --}}
        <main class="page-content" id="main-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="page-footer">
            <div class="pf-inner">
                <div class="pf-gov">
                    <div class="pf-flag" aria-label="Drapeau Congo"></div>
                    République du Congo — CongoAssist © {{ date('Y') }}
                </div>
                <div class="pf-links">
                    <a href="#">Support</a>
                    <a href="#">Documentation</a>
                    <a href="#">Confidentialité</a>
                    <span>v2.0</span>
                </div>
            </div>
        </footer>
    </div>
</div>

{{-- ══════════════════════
     MOBILE DRAWER
══════════════════════ --}}
<div class="mob-overlay" id="mobOverlay" role="dialog" aria-modal="true" aria-label="Menu de navigation">
    <div class="mob-drawer">
        <div class="mob-drawer::before"></div>
        <div class="mob-header">
            <div class="mob-logo">
                <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist">
                <div>
                    <div class="mob-logo-name">{{ $service->nom ?? 'CongoAssist' }}</div>
                    <div class="mob-logo-role">{{ ucfirst($service->role ?? 'Service') }}</div>
                </div>
            </div>
            <button class="mob-close" id="mobClose" aria-label="Fermer le menu">
                <i data-feather="x"></i>
            </button>
        </div>
        <nav class="mob-nav">
            @yield('sidebar')
        </nav>
        <div class="mob-foot">
            <img src="{{ $service->photo_profil ?? asset('medias/default.jpg') }}" alt="Profil"
                 style="width:32px;height:32px;border-radius:50%;border:2px solid rgba(255,255,255,.12);object-fit:cover;">
            <div>
                <div class="mob-foot-name">{{ $service->nom ?? 'Service' }}</div>
                <div class="mob-foot-status">● En ligne</div>
            </div>
        </div>
    </div>
</div>

<script>
feather.replace({ width: 16, height: 16 });

/* ── Mobile drawer ── */
const overlay  = document.getElementById('mobOverlay');
const ham      = document.getElementById('hamburger');
const mobClose = document.getElementById('mobClose');

function openDrawer()  {
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    overlay.classList.remove('open');
    document.body.style.overflow = '';
}

ham?.addEventListener('click', openDrawer);
mobClose?.addEventListener('click', closeDrawer);
overlay?.addEventListener('click', e => { if (e.target === overlay) closeDrawer(); });

/* Close on nav link tap (mobile) */
document.querySelectorAll('.mob-nav .sidebar-link').forEach(a => {
    a.addEventListener('click', closeDrawer);
});

/* Keyboard: ESC closes drawer */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && overlay.classList.contains('open')) closeDrawer();
});
</script>

@stack('scripts')
</body>
</html>
