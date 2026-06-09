<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', $service->nom ?? 'CongoAssist') — Plateforme Nationale</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        /* ─── Variables & Reset ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 260px;
            --topbar-h: 64px;

            /* Role colors */
            --c-accent:  16, 185, 129;
            --c-accent2: 5, 150, 105;
            --c-light:   209, 250, 229;

            /* Neutrals */
            --c-bg:      #F0F4F8;
            --c-surface: #FFFFFF;
            --c-border:  #E2E8F0;
            --c-text:    #0F172A;
            --c-muted:   #64748B;

            /* Govt flag stripe */
            --flag-green: #009E60;
            --flag-yellow: #FBDE4A;
            --flag-red:   #CE1126;
        }

        /* Role overrides */
        body[data-role="pompier"]     { --c-accent: 239,68,68;    --c-accent2: 185,28,28;   --c-light: 254,226,226; }
        body[data-role="police"]      { --c-accent: 37,99,235;    --c-accent2: 29,78,216;   --c-light: 219,234,254; }
        body[data-role="hopital"]     { --c-accent: 16,185,129;   --c-accent2: 5,150,105;   --c-light: 209,250,229; }
        body[data-role="electricite"] { --c-accent: 217,119,6;    --c-accent2: 180,83,9;    --c-light: 254,243,199; }

        html { -webkit-text-size-adjust: 100%; scroll-behavior: smooth; }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: var(--c-bg);
            color: var(--c-text);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        img { max-width: 100%; height: auto; display: block; }

        /* ─── Utility classes ─── */
        .accent-bg      { background: rgb(var(--c-accent)); }
        .accent-text    { color: rgb(var(--c-accent)); }
        .accent-border  { border-color: rgb(var(--c-accent)); }
        .accent-light   { background: rgba(var(--c-accent), .08); }
        .accent-gradient { background: linear-gradient(135deg, rgb(var(--c-accent)), rgb(var(--c-accent2))); }

        /* ─── Animations ─── */
        @keyframes fadeUp   { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slideRight { from { opacity:0; transform:translateX(-16px); } to { opacity:1; transform:translateX(0); } }
        @keyframes pulse    { 0%,100% { opacity:1; } 50% { opacity:.4; } }
        @keyframes shimmer  { 0% { background-position:-400px 0; } 100% { background-position:400px 0; } }

        .anim-fade  { animation: fadeUp   .45s ease both; }
        .anim-slide { animation: slideRight .4s ease both; }
        .pulse-dot  { animation: pulse 2s ease-in-out infinite; }

        /* ─── Layout shell ─── */
        .shell { display: flex; min-height: 100vh; }

        /* ════════════════════════════════
           SIDEBAR
        ════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: #0F172A;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            transition: transform .3s ease;
            z-index: 50;
        }

        /* Congo flag stripe */
        .sidebar::before {
            content: '';
            display: block;
            height: 4px;
            background: linear-gradient(90deg,
                var(--flag-green)  0% 33.3%,
                var(--flag-yellow) 33.3% 66.6%,
                var(--flag-red)    66.6% 100%);
            flex-shrink: 0;
        }

        /* Brand */
        .sb-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }
        .sb-brand-row { display: flex; align-items: center; gap: 12px; }
        .sb-logo-wrap { position: relative; flex-shrink: 0; }
        .sb-logo {
            width: 44px; height: 44px;
            border-radius: 10px; object-fit: cover;
            border: 2px solid rgba(255,255,255,.12);
        }
        .sb-logo-badge {
            position: absolute; top: -4px; right: -4px;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #0F172A;
        }
        .sb-logo-badge svg { width: 9px; height: 9px; color: #fff; }
        .sb-name { font-size: 14px; font-weight: 700; color: #F8FAFC; line-height: 1.3; letter-spacing: -.01em; }
        .sb-role {
            display: inline-block; margin-top: 3px;
            padding: 2px 8px; border-radius: 4px;
            font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            background: rgba(var(--c-accent), .18);
            color: rgb(var(--c-accent));
        }
        .sb-gov-label {
            margin-top: 6px;
            font-size: 9px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
            color: rgba(255,255,255,.3);
        }

        /* Nav */
        .sb-nav { flex: 1; padding: 12px 10px; overflow-y: auto; }
        .sb-nav::-webkit-scrollbar { width: 4px; }
        .sb-nav::-webkit-scrollbar-track { background: transparent; }
        .sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .sb-section-label {
            padding: 12px 12px 4px;
            font-size: 10px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            color: rgba(255,255,255,.25);
        }

        .sidebar-link {
            position: relative;
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            color: rgba(255,255,255,.55);
            transition: background .18s, color .18s, padding-left .2s;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-link svg { width: 16px; height: 16px; flex-shrink: 0; opacity: .7; transition: opacity .18s; }
        .sidebar-link .sb-lbl { flex: 1; overflow: hidden; text-overflow: ellipsis; }

        .sidebar-link::before {
            content: '';
            position: absolute; left: 0; top: 50%;
            transform: translateY(-50%) scaleY(0);
            width: 3px; height: 60%;
            background: rgb(var(--c-accent));
            border-radius: 0 3px 3px 0;
            transition: transform .22s ease;
        }
        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255,255,255,.06);
            color: #F8FAFC;
            padding-left: 16px;
        }
        .sidebar-link:hover svg,
        .sidebar-link.active svg { opacity: 1; }
        .sidebar-link:hover::before,
        .sidebar-link.active::before { transform: translateY(-50%) scaleY(1); }
        .sidebar-link.active { background: rgba(var(--c-accent), .12); color: rgb(var(--c-accent)); font-weight: 600; }
        .sidebar-link.active svg { color: rgb(var(--c-accent)); }

        .sb-badge {
            margin-left: auto;
            min-width: 20px; height: 20px;
            padding: 0 6px;
            border-radius: 10px;
            background: rgb(var(--c-accent));
            color: #fff;
            font-size: 10px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sb-divider { height: 1px; background: rgba(255,255,255,.06); margin: 8px 10px; }

        /* Profile strip */
        .sb-profile {
            flex-shrink: 0;
            padding: 12px 14px;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .sb-profile-inner {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            background: rgba(255,255,255,.04);
            cursor: pointer;
            transition: background .18s;
        }
        .sb-profile-inner:hover { background: rgba(255,255,255,.08); }
        .sb-avatar-wrap { position: relative; flex-shrink: 0; }
        .sb-avatar {
            width: 36px; height: 36px;
            border-radius: 50%; object-fit: cover;
            border: 2px solid rgba(255,255,255,.15);
        }
        .sb-online {
            position: absolute; top: 0; right: 0;
            width: 10px; height: 10px;
            background: #10b981; border-radius: 50%;
            border: 2px solid #0F172A;
        }
        .sb-pname { font-size: 13px; font-weight: 600; color: #F8FAFC; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .sb-pstatus { font-size: 11px; color: #10b981; margin-top: 1px; display: flex; align-items: center; gap: 4px; }

        /* ════════════════════════════════
           MAIN COLUMN
        ════════════════════════════════ */
        .main-col { flex: 1; display: flex; flex-direction: column; min-width: 0; min-height: 100vh; }

        /* Top bar (mobile) */
        .topbar {
            display: none;
            position: sticky; top: 0; z-index: 40;
            height: var(--topbar-h);
            background: #0F172A;
            align-items: center;
            padding: 0 16px;
            gap: 12px;
        }
        /* Congo stripe on mobile topbar */
        .topbar::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg,
                var(--flag-green)  0% 33.3%,
                var(--flag-yellow) 33.3% 66.6%,
                var(--flag-red)    66.6% 100%);
        }
        .topbar-btn {
            background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,.7); padding: 6px; border-radius: 6px;
            transition: background .18s; flex-shrink: 0;
        }
        .topbar-btn:hover { background: rgba(255,255,255,.1); }
        .topbar-btn svg { width: 22px; height: 22px; display: block; }
        .topbar-brand {
            flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .topbar-brand img { width: 30px; height: 30px; border-radius: 6px; object-fit: cover; }
        .topbar-brand span { font-size: 15px; font-weight: 700; color: #F8FAFC; letter-spacing: -.01em; }

        /* Page header */
        .page-header {
            background: var(--c-surface);
            border-bottom: 1px solid var(--c-border);
            padding: 20px 32px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .ph-left h1 {
            font-size: clamp(18px, 3vw, 24px);
            font-weight: 700; color: var(--c-text);
            letter-spacing: -.02em; line-height: 1.2;
        }
        .ph-left p { font-size: 13px; color: var(--c-muted); margin-top: 3px; }
        .ph-right { display: flex; align-items: center; gap: 10px; }

        .ph-badge {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 6px;
            font-size: 12px; font-weight: 600;
            background: rgba(var(--c-accent), .08);
            color: rgb(var(--c-accent));
            border: 1px solid rgba(var(--c-accent), .2);
        }
        .ph-dot { width: 7px; height: 7px; border-radius: 50%; background: rgb(var(--c-accent)); }
        .ph-avatar {
            width: 38px; height: 38px;
            border-radius: 50%; object-fit: cover;
            border: 2px solid var(--c-border);
        }

        /* Breadcrumb */
        .breadcrumb {
            background: var(--c-bg);
            border-bottom: 1px solid var(--c-border);
            padding: 8px 32px;
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: var(--c-muted);
        }
        .breadcrumb a { color: var(--c-muted); text-decoration: none; transition: color .18s; }
        .breadcrumb a:hover { color: var(--c-text); }
        .breadcrumb svg { width: 12px; height: 12px; }

        /* Content */
        .page-content { flex: 1; padding: 28px 32px; }

        /* Footer */
        .page-footer {
            background: var(--c-surface);
            border-top: 1px solid var(--c-border);
            padding: 14px 32px;
        }
        .pf-inner {
            display: flex; flex-wrap: wrap;
            align-items: center; justify-content: space-between;
            gap: 10px;
            font-size: 12px; color: var(--c-muted);
        }
        .pf-gov {
            display: flex; align-items: center; gap: 8px;
            font-weight: 600; color: var(--c-text);
        }
        .pf-flag {
            width: 24px; height: 14px;
            background: linear-gradient(90deg,
                var(--flag-green)  0% 33.3%,
                var(--flag-yellow) 33.3% 66.6%,
                var(--flag-red)    66.6% 100%);
            border-radius: 2px;
        }
        .pf-links { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .pf-links a { color: var(--c-muted); text-decoration: none; transition: color .18s; }
        .pf-links a:hover { color: var(--c-text); }

        /* ════════════════════════════════
           MOBILE DRAWER
        ════════════════════════════════ */
        .mob-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 60;
            background: rgba(0,0,0,.55);
            backdrop-filter: blur(3px);
        }
        .mob-overlay.open { display: block; }

        .mob-drawer {
            position: absolute; top: 0; left: 0;
            width: min(var(--sidebar-w), 88vw);
            height: 100%;
            background: #0F172A;
            display: flex; flex-direction: column;
            transform: translateX(-100%);
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }
        .mob-drawer::before {
            content: '';
            display: block; height: 4px; flex-shrink: 0;
            background: linear-gradient(90deg,
                var(--flag-green)  0% 33.3%,
                var(--flag-yellow) 33.3% 66.6%,
                var(--flag-red)    66.6% 100%);
        }
        .mob-overlay.open .mob-drawer { transform: translateX(0); }

        .mob-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }
        .mob-brand { display: flex; align-items: center; gap: 10px; }
        .mob-brand img { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; }
        .mob-brand-name { font-size: 14px; font-weight: 700; color: #F8FAFC; }
        .mob-brand-role { font-size: 10px; font-weight: 700; color: rgb(var(--c-accent)); letter-spacing: .05em; text-transform: uppercase; margin-top: 1px; }
        .mob-close {
            background: none; border: none; cursor: pointer;
            color: rgba(255,255,255,.5); padding: 6px; border-radius: 6px;
            transition: background .18s;
        }
        .mob-close:hover { background: rgba(255,255,255,.1); }
        .mob-close svg { width: 20px; height: 20px; display: block; }

        .mob-nav { flex: 1; padding: 10px 10px; overflow-y: auto; }
        .mob-nav::-webkit-scrollbar { width: 4px; }
        .mob-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .mob-profile {
            flex-shrink: 0;
            padding: 12px 14px;
            border-top: 1px solid rgba(255,255,255,.06);
            display: flex; align-items: center; gap: 10px;
        }
        .mob-profile img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.15); }
        .mob-profile-name   { font-size: 13px; font-weight: 600; color: #F8FAFC; }
        .mob-profile-status { font-size: 11px; color: #10b981; }

        /* ════════════════════════════════
           SHARED COMPONENTS (child views)
        ════════════════════════════════ */

        /* Stat card */
        .stat-card {
            background: var(--c-surface);
            border-radius: 12px;
            padding: 22px 24px;
            border: 1px solid var(--c-border);
            transition: box-shadow .25s, transform .25s;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.08); transform: translateY(-2px); }
        .stat-card::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
        }
        .stat-card.accent::after { background: rgb(var(--c-accent)); }
        .stat-card.blue::after  { background: #3B82F6; }
        .stat-card.green::after { background: #10B981; }
        .stat-card.red::after   { background: #EF4444; }
        .stat-card.yellow::after{ background: #F59E0B; }
        .stat-card.purple::after{ background: #8B5CF6; }

        .stat-card .sc-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
        }
        .stat-card .sc-icon svg { width: 22px; height: 22px; }
        .stat-card .sc-value {
            font-size: 28px; font-weight: 800; letter-spacing: -.03em;
            color: var(--c-text); line-height: 1;
        }
        .stat-card .sc-label { font-size: 13px; font-weight: 500; color: var(--c-muted); margin-top: 4px; }
        .stat-card .sc-trend { font-size: 12px; font-weight: 600; margin-top: 8px; display: flex; align-items: center; gap: 4px; }

        /* Content card */
        .content-card {
            background: var(--c-surface);
            border-radius: 12px;
            border: 1px solid var(--c-border);
            overflow: hidden;
        }
        .content-card .cc-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--c-border);
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            flex-wrap: wrap;
        }
        .content-card .cc-title {
            font-size: 15px; font-weight: 700; color: var(--c-text);
            display: flex; align-items: center; gap: 8px;
        }
        .content-card .cc-title svg { width: 18px; height: 18px; color: rgb(var(--c-accent)); }
        .content-card .cc-sub { font-size: 12px; color: var(--c-muted); margin-top: 2px; }
        .content-card .cc-body { padding: 20px 22px; }

        /* Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead tr { background: #F8FAFC; border-bottom: 2px solid var(--c-border); }
        .data-table thead th {
            padding: 11px 16px;
            text-align: left;
            font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            color: var(--c-muted);
            white-space: nowrap;
        }
        .data-table tbody tr {
            border-bottom: 1px solid var(--c-border);
            transition: background .15s;
        }
        .data-table tbody tr:hover { background: #F8FAFC; }
        .data-table tbody td { padding: 13px 16px; font-size: 13.5px; color: var(--c-text); }
        .data-table tbody tr:last-child { border-bottom: none; }

        /* Pill / badge */
        .pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: .02em;
            white-space: nowrap;
        }
        .pill svg { width: 11px; height: 11px; }
        .pill-green  { background: #D1FAE5; color: #065F46; }
        .pill-red    { background: #FEE2E2; color: #991B1B; }
        .pill-yellow { background: #FEF3C7; color: #92400E; }
        .pill-blue   { background: #DBEAFE; color: #1E40AF; }
        .pill-gray   { background: #F1F5F9; color: #475569; }
        .pill-accent { background: rgba(var(--c-accent),.12); color: rgb(var(--c-accent)); }

        /* Button */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; border: none; transition: all .18s;
            font-family: inherit; text-decoration: none;
            white-space: nowrap;
        }
        .btn svg { width: 15px; height: 15px; }
        .btn-accent { background: rgb(var(--c-accent)); color: #fff; }
        .btn-accent:hover { background: rgb(var(--c-accent2)); box-shadow: 0 4px 12px rgba(var(--c-accent),.35); transform: translateY(-1px); }
        .btn-ghost  { background: transparent; color: var(--c-muted); border: 1.5px solid var(--c-border); }
        .btn-ghost:hover  { background: #F8FAFC; color: var(--c-text); }
        .btn-danger { background: #EF4444; color: #fff; }
        .btn-danger:hover { background: #DC2626; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon { padding: 7px; border-radius: 7px; }

        /* Avatar */
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%; object-fit: cover;
            border: 2px solid var(--c-border);
            flex-shrink: 0;
        }
        .user-initials {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(var(--c-accent), .12);
            color: rgb(var(--c-accent));
            font-size: 13px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* Form inputs */
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--c-border);
            border-radius: 8px;
            font-size: 14px; font-family: inherit;
            color: var(--c-text);
            background: var(--c-surface);
            transition: border-color .18s, box-shadow .18s;
            outline: none;
        }
        .form-input:focus {
            border-color: rgb(var(--c-accent));
            box-shadow: 0 0 0 3px rgba(var(--c-accent), .12);
        }
        .form-label {
            display: block; font-size: 12px; font-weight: 600;
            color: var(--c-text); margin-bottom: 6px;
        }

        /* Alert banners */
        .alert {
            padding: 12px 16px; border-radius: 8px;
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 13.5px;
            border-left: 4px solid transparent;
        }
        .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: #F0FDF4; border-color: #10B981; color: #065F46; }
        .alert-error   { background: #FEF2F2; border-color: #EF4444; color: #991B1B; }
        .alert-info    { background: #EFF6FF; border-color: #3B82F6; color: #1E40AF; }
        .alert-warn    { background: #FFFBEB; border-color: #F59E0B; color: #92400E; }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 80;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
        }
        .modal-box {
            background: var(--c-surface);
            border-radius: 14px;
            width: 100%; max-width: 500px;
            max-height: 90vh; overflow-y: auto;
            box-shadow: 0 24px 64px rgba(0,0,0,.2);
        }
        .modal-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--c-border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-title { font-size: 16px; font-weight: 700; color: var(--c-text); }
        .modal-body  { padding: 20px 24px; }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--c-border);
            display: flex; justify-content: flex-end; gap: 10px;
        }

        /* ─── Responsive ─── */
        @media (max-width: 1023px) {
            .sidebar     { display: none; }
            .topbar      { display: flex; }
            .page-header { padding: 16px 20px; }
            .page-content { padding: 20px 16px; }
            .page-footer  { padding: 12px 20px; }
            .breadcrumb  { padding: 8px 20px; display: none; }
        }
        @media (max-width: 639px) {
            .page-header { padding: 14px 16px; }
            .ph-right    { display: none; }
            .page-content { padding: 16px 12px; }
            .page-footer  { padding: 10px 16px; }
        }
        @media (max-width: 374px) {
            .page-content { padding: 12px 10px; }
        }
        @media (min-width: 1024px) {
            .mob-overlay { display: none !important; }
        }
        @media (min-width: 1280px) {
            :root { --sidebar-w: 272px; }
        }

        /* Scrollbar global */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* Responsive table wrapper */
        .table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* Grid utilities */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }

        @media (max-width: 1023px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 639px) {
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>

<body data-role="{{ $service->role ?? 'hopital' }}">
<div class="shell">

    {{-- ══════════════════════════════
         DESKTOP SIDEBAR
    ══════════════════════════════ --}}
    <aside class="sidebar">

        {{-- Brand --}}
        <div class="sb-brand">
            <div class="sb-brand-row">
                <div class="sb-logo-wrap">
                    <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist" class="sb-logo">
                    <div class="sb-logo-badge accent-gradient">
                        <i data-feather="shield"></i>
                    </div>
                </div>
                <div style="min-width:0;">
                    <div class="sb-name">{{ Str::limit($service->nom ?? 'CongoAssist', 22) }}</div>
                    <span class="sb-role">{{ ucfirst($service->role ?? 'Service') }}</span>
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
                         alt="Profil" class="sb-avatar">
                    <span class="sb-online"></span>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="sb-pname">{{ Str::limit($service->nom ?? 'Service', 20) }}</div>
                    <div class="sb-pstatus">
                        <span style="width:6px;height:6px;border-radius:50%;background:#10b981;flex-shrink:0;" class="pulse-dot"></span>
                        En ligne
                    </div>
                </div>
                <i data-feather="more-vertical" style="width:14px;height:14px;color:rgba(255,255,255,.3);flex-shrink:0;"></i>
            </div>
        </div>
    </aside>

    {{-- ══════════════════════════════
         MAIN COLUMN
    ══════════════════════════════ --}}
    <div class="main-col">

        {{-- Mobile topbar --}}
        <div class="topbar">
            <button class="topbar-btn" id="hamburger" aria-label="Menu">
                <i data-feather="menu"></i>
            </button>
            <div class="topbar-brand">
                <img src="{{ asset('medias/Clogo.jpg') }}" alt="Logo">
                <span>{{ $service->nom ?? 'CongoAssist' }}</span>
            </div>
            <button class="topbar-btn" aria-label="Notifications">
                <i data-feather="bell"></i>
            </button>
        </div>

        {{-- Page header --}}
        <header class="page-header">
            <div class="ph-left">
                <h1>@yield('page-title', 'Tableau de bord')</h1>
                <p>@yield('page-subtitle', 'Bienvenue sur votre espace de gestion')</p>
            </div>
            <div class="ph-right">
                <div class="ph-badge">
                    <span class="ph-dot pulse-dot"></span>
                    Service actif
                </div>
                <img src="{{ $service->photo_profil ?? asset('medias/default.jpg') }}"
                     alt="Profil" class="ph-avatar">
            </div>
        </header>

        {{-- Breadcrumb --}}
        <div class="breadcrumb">
            <i data-feather="home"></i>
            <a href="{{ route('services.compte') }}">Accueil</a>
            <i data-feather="chevron-right"></i>
            <span>@yield('page-title', 'Dashboard')</span>
        </div>

        {{-- Content --}}
        <main class="page-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="page-footer">
            <div class="pf-inner">
                <div class="pf-gov">
                    <div class="pf-flag"></div>
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

    </div>{{-- /main-col --}}
</div>{{-- /shell --}}

{{-- ══════════════════════════════
     MOBILE DRAWER
══════════════════════════════ --}}
<div class="mob-overlay" id="mobOverlay">
    <div class="mob-drawer">
        <div class="mob-header">
            <div class="mob-brand">
                <img src="{{ asset('medias/Clogo.jpg') }}" alt="Logo">
                <div>
                    <div class="mob-brand-name">{{ $service->nom ?? 'CongoAssist' }}</div>
                    <div class="mob-brand-role">{{ ucfirst($service->role ?? 'Service') }}</div>
                </div>
            </div>
            <button class="mob-close" id="mobClose" aria-label="Fermer">
                <i data-feather="x"></i>
            </button>
        </div>
        <nav class="mob-nav">
            @yield('sidebar')
        </nav>
        <div class="mob-profile">
            <img src="{{ $service->photo_profil ?? asset('medias/default.jpg') }}" alt="Profil">
            <div>
                <div class="mob-profile-name">{{ $service->nom ?? 'Service' }}</div>
                <div class="mob-profile-status">● En ligne</div>
            </div>
        </div>
    </div>
</div>

<script>
feather.replace();

/* Mobile drawer */
const overlay  = document.getElementById('mobOverlay');
const ham      = document.getElementById('hamburger');
const mobClose = document.getElementById('mobClose');

function open()  { overlay.classList.add('open');    document.body.style.overflow = 'hidden'; }
function close() { overlay.classList.remove('open'); document.body.style.overflow = ''; }

ham?.addEventListener('click',   open);
mobClose?.addEventListener('click', close);
overlay?.addEventListener('click', e => { if (e.target === overlay) close(); });

/* Close drawer on link tap */
document.querySelectorAll('.mob-nav .sidebar-link').forEach(a => {
    a.addEventListener('click', close);
});
</script>

@stack('scripts')
</body>
</html>
