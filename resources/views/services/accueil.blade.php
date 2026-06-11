<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CongoAssist — Services d'urgence</title>
    <link rel="icon" href="medias/Clogo.jpg" type="image/png">

    {{-- PWA Meta --}}
    <link rel="manifest" href="/manifest-services.json">
    <meta name="theme-color" content="#0B1E3D">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="CongoAssist">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand-dark:    #0B1E3D;
            --brand-mid:     #1A3E6E;
            --accent-green:  #1DB87A;
            --accent-green2: #15A066;
            --accent-red:    #E83A3A;
            --accent-amber:  #F5A623;
            --text-primary:  #0F1923;
            --text-secondary:#4A5568;
            --text-muted:    #8A97A8;
            --surface:       #F7F9FC;
            --surface2:      #EEF2F8;
            --border:        #DDE3ED;
            --white:         #FFFFFF;
        }

        html, body { overflow-x: hidden; scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            color: var(--text-primary);
            background: var(--white);
        }

        img { max-width: 100%; height: auto; display: block; }

        /* ── TYPOGRAPHY ── */
        .display { font-family: 'Sora', sans-serif; font-weight: 700; }
        h2.section-title {
            font-family: 'Sora', sans-serif;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 700;
            color: var(--brand-dark);
        }
        h3 { font-size: 1.05rem; font-weight: 600; color: var(--brand-dark); }

        /* ── HEADER ── */
        /* ── HEADER ── */
header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: rgba(255,255,255,0.96);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border);
}
.nav-inner {
    max-width: 1140px;
    margin: 0 auto;
    padding: 0 1rem;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.logo {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    flex-shrink: 0;
}
.logo-mark {
    width: 34px;
    height: 34px;
    background: var(--brand-dark);
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.logo-mark span {
    font-family: 'Sora', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--accent-green);
}
.logo-text {
    font-family: 'Sora', sans-serif;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--brand-dark);
}
.logo-text em { font-style: normal; color: var(--accent-green); }

nav.desktop-nav {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}
nav.desktop-nav a {
    font-size: 0.88rem;
    font-weight: 500;
    color: var(--text-secondary);
    text-decoration: none;
    transition: color 0.2s;
    white-space: nowrap;
}
nav.desktop-nav a:hover { color: var(--brand-dark); }

/* Bouton connexion header */
.nav-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--brand-dark);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: background 0.2s, transform 0.15s;
    white-space: nowrap;
}
.btn-primary:hover { background: var(--brand-mid); }
.btn-primary:active { transform: scale(0.98); }

/* Burger */
.burger {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px;
    border-radius: 8px;
    transition: background 0.2s;
    flex-shrink: 0;
}
.burger:hover { background: var(--surface); }
.burger svg {
    width: 22px; height: 22px;
    stroke: var(--brand-dark);
    stroke-width: 2;
    fill: none;
    display: block;
}

/* Mobile nav */
#mobile-nav {
    display: none;
    background: var(--white);
    border-top: 1px solid var(--border);
    padding: 0.75rem 1rem 1.25rem;
}
#mobile-nav.open { display: block; }
#mobile-nav a {
    display: flex;
    align-items: center;
    padding: 0.7rem 0;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-secondary);
    text-decoration: none;
    border-bottom: 1px solid var(--surface2);
    transition: color 0.2s;
}
#mobile-nav a:hover { color: var(--brand-dark); }
#mobile-nav a:last-of-type { border-bottom: none; }
#mobile-nav .btn-primary {
    margin-top: 1rem;
    width: 100%;
    justify-content: center;
    padding: 0.7rem 1rem;
    font-size: 0.9rem;
    border-radius: 10px;
}

/* ── Breakpoints ── */

/* Tablette : masquer les liens nav, garder le bouton connexion */
@media (max-width: 900px) {
    nav.desktop-nav { display: none; }
    .burger { display: block; }
}

/* Mobile : réduire encore le logo */
@media (max-width: 400px) {
    .logo-text { font-size: 0.95rem; }
    .logo-mark { width: 30px; height: 30px; }
    .logo-mark span { font-size: 14px; }
    .nav-inner { padding: 0 0.75rem; height: 56px; }
    .btn-primary { padding: 0.45rem 0.85rem; font-size: 0.78rem; }
    .btn-primary svg { display: none; } /* masquer la flèche sur très petit */
}

        /* ── HERO ── */
        .hero {
            background: var(--brand-dark);
            color: var(--white);
            padding: 5rem 1.5rem 4rem;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 480px; height: 480px;
            background: radial-gradient(circle, rgba(29,184,122,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(26,62,110,0.6) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(29,184,122,0.12);
            border: 1px solid rgba(29,184,122,0.3);
            color: var(--accent-green);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 100px;
            margin-bottom: 1.25rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .hero-badge::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.4); }
        }
        .hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 1.25rem;
            color: var(--white);
        }
        .hero h1 .accent { color: var(--accent-green); }
        .hero p {
            font-size: 1.05rem;
            line-height: 1.75;
            color: rgba(255,255,255,0.65);
            margin-bottom: 2rem;
            max-width: 480px;
        }
        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .hero-actions .btn-hero-primary {
            background: var(--accent-green);
            color: var(--white);
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            text-decoration: none;
        }
        .hero-actions .btn-hero-primary:hover { background: var(--accent-green2); }
        .hero-actions .btn-hero-secondary {
            background: transparent;
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            border: 1.5px solid rgba(255,255,255,0.2);
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s;
            text-decoration: none;
        }
        .hero-actions .btn-hero-secondary:hover { border-color: rgba(255,255,255,0.5); color: #fff; }

        /* Hero visual — urgence badges */
        .hero-visual {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: flex-end;
        }
        .urgence-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 280px;
            transition: transform 0.3s;
        }
        .urgence-card:hover { transform: translateX(-4px); }
        .urgence-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .urgence-icon svg { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2; }
        .urgence-card-info { flex: 1; }
        .urgence-card-info .label { font-size: 0.8rem; font-weight: 600; color: var(--white); }
        .urgence-card-info .sub { font-size: 0.73rem; color: rgba(255,255,255,0.45); margin-top: 2px; }
        .status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent-green);
        }

        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; }
            .hero-visual { display: none; }
        }
        @media (max-width: 640px) {
            .hero { padding: 3.5rem 1.25rem 3rem; }
            .hero-actions .btn-hero-primary,
            .hero-actions .btn-hero-secondary { width: 100%; text-align: center; justify-content: center; }
        }

        /* ── STATS BAND ── */
        .stats-band {
            background: var(--surface);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 2rem 1.5rem;
        }
        .stats-inner {
            max-width: 1140px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        .stat-item {
            text-align: center;
            padding: 0.5rem;
        }
        .stat-item .stat-val {
            font-family: 'Sora', sans-serif;
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--brand-dark);
            line-height: 1;
        }
        .stat-item .stat-val span { color: var(--accent-green); }
        .stat-item .stat-label {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }
        @media (max-width: 640px) {
            .stats-inner { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        }

        /* ── SECTION WRAPPER ── */
        .section { padding: 5rem 1.5rem; }
        .section-inner { max-width: 1140px; margin: 0 auto; }
        .section-header { margin-bottom: 3rem; }
        .eyebrow {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--accent-green);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.6rem;
        }
        .section-title { margin-bottom: 0.6rem; }
        .section-subtitle {
            font-size: 1rem;
            color: var(--text-secondary);
            max-width: 520px;
            line-height: 1.7;
        }

        /* ── SERVICE GRID ── */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }
        @media (max-width: 900px) { .services-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 560px) { .services-grid { grid-template-columns: 1fr; } }

        .service-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
            cursor: default;
        }
        .service-card:hover {
            box-shadow: 0 8px 32px rgba(11,30,61,0.08);
            transform: translateY(-3px);
            border-color: transparent;
        }
        .service-card-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
        }
        .service-card-icon svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        .icon-red    { background: #FEF0F0; color: #C0392B; }
        .icon-blue   { background: #EEF4FF; color: #2563EB; }
        .icon-purple { background: #F5F0FF; color: #7C3AED; }
        .icon-orange { background: #FFF4EC; color: #D97706; }
        .icon-amber  { background: #FFFBEB; color: #B45309; }
        .icon-green  { background: #F0FBF4; color: #15803D; }

        .service-card h3 { font-size: 1rem; }
        .service-card p {
            font-size: 0.88rem;
            color: var(--text-secondary);
            line-height: 1.65;
            flex: 1;
        }
        .service-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.83rem;
            font-weight: 600;
            color: var(--brand-dark);
            text-decoration: none;
            transition: gap 0.2s;
        }
        .service-link:hover { gap: 8px; }
        .service-link svg { width: 14px; height: 14px; stroke: currentColor; stroke-width: 2.5; fill: none; }

        /* ── CTA BAND ── */
        .cta-band {
            background: var(--brand-dark);
            padding: 4.5rem 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .cta-band::before {
            content: '';
            position: absolute;
            right: -100px; top: -100px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(29,184,122,0.12) 0%, transparent 60%);
            pointer-events: none;
        }
        .cta-inner {
            max-width: 640px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .cta-band h2 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(1.6rem, 3vw, 2rem);
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.75rem;
        }
        .cta-band p {
            font-size: 1rem;
            color: rgba(255,255,255,0.6);
            line-height: 1.75;
            margin-bottom: 2rem;
        }
        .cta-band .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent-green);
            color: var(--white);
            padding: 0.85rem 2rem;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
        }
        .cta-band .btn-cta:hover { background: var(--accent-green2); }
        .cta-band .btn-cta:active { transform: scale(0.98); }
        .cta-band .btn-cta svg { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2.5; }

        /* ── FOOTER ── */
        footer {
            background: #060E1A;
            color: rgba(255,255,255,0.55);
            padding: 3.5rem 1.5rem 2rem;
        }
        .footer-inner { max-width: 1140px; margin: 0 auto; }
        .footer-top {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1fr;
            gap: 2.5rem;
            margin-bottom: 2.5rem;
        }
        .footer-brand .logo-text { color: var(--white); font-size: 1rem; }
        .footer-brand p { font-size: 0.85rem; line-height: 1.7; margin-top: 0.6rem; }
        .footer-col h4 {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 0.9rem;
            letter-spacing: 0.03em;
        }
        .footer-col a {
            display: block;
            font-size: 0.83rem;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            margin-bottom: 0.5rem;
            transition: color 0.2s;
        }
        .footer-col a:hover { color: var(--accent-green); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.07);
            padding-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .footer-bottom p { font-size: 0.8rem; }
        .social-links { display: flex; gap: 8px; }
        .social-link {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .social-link:hover { border-color: var(--accent-green); background: rgba(29,184,122,0.08); }
        .social-link svg { width: 15px; height: 15px; fill: none; stroke: rgba(255,255,255,0.55); stroke-width: 2; }
        @media (max-width: 900px) { .footer-top { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 560px) { .footer-top { grid-template-columns: 1fr; } }

        /* ══════════════════════════════════════
           PWA FAB
        ══════════════════════════════════════ */
        #pwa-fab {
            display: none;
            position: fixed;
            bottom: max(1.5rem, env(safe-area-inset-bottom));
            right: 1.5rem;
            z-index: 9990;
            align-items: center;
            gap: .6rem;
            background: var(--accent-green);
            color: var(--white);
            font-family: 'Inter', sans-serif;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .75rem 1.4rem;
            border-radius: 100px;
            border: none;
            box-shadow: 0 8px 28px rgba(29,184,122,.45);
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            -webkit-tap-highlight-color: transparent;
        }
        #pwa-fab.visible { display: flex; }
        #pwa-fab:hover   { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(29,184,122,.55); }
        #pwa-fab:active  { transform: scale(.97); }
        #pwa-fab svg {
            width: 16px; height: 16px;
            fill: none; stroke: currentColor;
            stroke-width: 2.5;
            stroke-linecap: round; stroke-linejoin: round;
        }

        /* ══════════════════════════════════════
           PWA Modal overlay
        ══════════════════════════════════════ */
        #pwa-modal-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 9995;
            background: rgba(0,0,0,.55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            align-items: flex-end;
            justify-content: center;
            padding: 1rem;
        }
        #pwa-modal-overlay.open { display: flex; }

        #pwa-modal-box {
            background: var(--white);
            border-radius: 24px 24px 0 0;
            padding: 2rem 1.8rem max(2rem, env(safe-area-inset-bottom));
            width: 100%; max-width: 480px;
        }
        #pwa-modal-handle {
            width: 36px; height: 4px;
            background: var(--border); border-radius: 2px;
            margin: 0 auto 1.4rem;
        }
        #pwa-modal-header {
            display: flex; align-items: center;
            gap: 12px; margin-bottom: .75rem;
        }
        #pwa-modal-app-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--brand-dark);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        #pwa-modal-app-icon svg {
            width: 22px; height: 22px;
            fill: none; stroke: var(--accent-green); stroke-width: 2;
        }
        #pwa-modal-title {
            font-family: 'Sora', sans-serif;
            font-size: 1rem; font-weight: 700;
            color: var(--brand-dark);
        }
        #pwa-modal-body {
            font-size: .85rem; color: var(--text-secondary);
            line-height: 1.7; margin-bottom: 1.2rem;
        }
        .pwa-chip {
            display: inline-block;
            background: #ecfdf5; color: #065f46;
            border-radius: 6px; padding: .08rem .5rem;
            font-weight: 700; font-size: .8rem;
        }
        #pwa-modal-steps {
            display: flex; flex-direction: column;
            gap: .6rem; margin-bottom: 1.4rem;
        }
        .pwa-step {
            display: flex; align-items: flex-start;
            gap: 10px; font-size: .82rem; color: var(--text-secondary);
        }
        .pwa-step-num {
            width: 22px; height: 22px; border-radius: 50%;
            background: var(--accent-green); color: var(--white);
            font-size: .62rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }
        #pwa-modal-close {
            display: block; width: 100%;
            background: var(--brand-dark); color: var(--white);
            font-family: 'Inter', sans-serif;
            font-weight: 700; font-size: .78rem;
            letter-spacing: .08em; text-transform: uppercase;
            padding: .9rem; border-radius: 100px;
            border: none; cursor: pointer;
            transition: background .2s;
            -webkit-tap-highlight-color: transparent;
        }
        #pwa-modal-close:hover { background: var(--brand-mid); }
    </style>
</head>

<body>

<!-- ── HEADER ── -->
<header>
    <div class="nav-inner">
        <a href="#" class="logo">
            <div class="logo-mark"><span>C</span></div>
            <span class="logo-text">Congo<em>Assist</em></span>
        </a>

        <nav class="desktop-nav">
            <a href="#medical">Médical</a>
            <a href="#police">Sécurité</a>
            <a href="#vaccination">Vaccination</a>
            <a href="#urbaine">Urbain</a>
            <a href="#electricite">Électricité</a>
        </nav>

        {{-- ← remplacez l'ancienne div style="display:flex..." par celle-ci --}}
        <div class="nav-actions">
            <a href="{{ route('services.login') }}" class="btn-primary">
                Connexion
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <button class="burger" id="burger-btn" aria-label="Menu">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>

    <div id="mobile-nav">
        <a href="#medical">Urgence médicale</a>
        <a href="#police">Sécurité / Police</a>
        <a href="#vaccination">Vaccination</a>
        <a href="#urbaine">Urgence urbaine</a>
        <a href="#electricite">Électricité</a>
        <a href="{{ route('services.login') }}" class="btn-primary">Se connecter</a>
    </div>
</header>


<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-inner">
        <div>
            <div class="hero-badge">Service actif 24h/24</div>
            <h1 class="display">
                Réagissez vite.<br>
                <span class="accent">CongoAssist</span> est là.
            </h1>
            <p>Accédez aux services d'urgence essentiels — médical, sécurité, infrastructure — en quelques secondes, depuis n'importe où au Congo.</p>
            <div class="hero-actions">
                <a href="{{ route('services.login') }}" class="btn-hero-primary">Créer un compte</a>
                <a href="#medical" class="btn-hero-secondary">Voir les services</a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="urgence-card">
                <div class="urgence-icon" style="background:rgba(232,58,58,0.15);color:#E83A3A;">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div class="urgence-card-info">
                    <div class="label">Urgence médicale</div>
                    <div class="sub">Hôpitaux à proximité</div>
                </div>
                <div class="status-dot"></div>
            </div>
            <div class="urgence-card">
                <div class="urgence-icon" style="background:rgba(37,99,235,0.15);color:#60A5FA;">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="urgence-card-info">
                    <div class="label">Sécurité publique</div>
                    <div class="sub">Signalement en temps réel</div>
                </div>
                <div class="status-dot"></div>
            </div>
            <div class="urgence-card">
                <div class="urgence-icon" style="background:rgba(245,166,35,0.15);color:#F5A623;">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div class="urgence-card-info">
                    <div class="label">Panne électrique</div>
                    <div class="sub">Suivi des réparations</div>
                </div>
                <div class="status-dot"></div>
            </div>
        </div>
    </div>
</section>


<!-- ── STATS ── -->
<div class="stats-band">
    <div class="stats-inner">
        <div class="stat-item">
            <div class="stat-val">24<span>/7</span></div>
            <div class="stat-label">Service disponible</div>
        </div>
        <div class="stat-item">
            <div class="stat-val">10<span>K+</span></div>
            <div class="stat-label">Utilisateurs actifs</div>
        </div>
        <div class="stat-item">
            <div class="stat-val">5</div>
            <div class="stat-label">Services d'urgence</div>
        </div>
        <div class="stat-item">
            <div class="stat-val">95<span>%</span></div>
            <div class="stat-label">Satisfaction</div>
        </div>
    </div>
</div>


<!-- ── SERVICES ── -->
<section class="section" id="medical">
    <div class="section-inner">
        <div class="section-header">
            <div class="eyebrow">Nos services</div>
            <h2 class="section-title">Des solutions pour chaque urgence</h2>
            <p class="section-subtitle">Rapides, fiables, accessibles depuis votre téléphone ou votre ordinateur.</p>
        </div>

        <div class="services-grid">

            <!-- Médical -->
            <div class="service-card">
                <div class="service-card-icon icon-red">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div><h3>Urgence médicale</h3></div>
                <p>Localisez les hôpitaux les plus proches et signalez une urgence médicale en temps réel pour une intervention rapide.</p>
                <a href="#" class="service-link">
                    En savoir plus
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Sécurité -->
            <div class="service-card" id="police">
                <div class="service-card-icon icon-blue">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div><h3>Sécurité publique</h3></div>
                <p>Signalez les incidents, collaborez avec les forces de l'ordre et contribuez à un environnement plus sûr pour tous.</p>
                <a href="#" class="service-link">
                    En savoir plus
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Vaccination -->
            <div class="service-card" id="vaccination">
                <div class="service-card-icon icon-purple">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 3l4 4m10 10l4 4M9.5 9.5l5 5M7.5 7.5l9 9m-9-9L4 4m12 12 4 4M12 8v8m-4-4h8"/></svg>
                </div>
                <div><h3>Vaccination</h3></div>
                <p>Suivez le calendrier vaccinal de votre famille, recevez des rappels et accédez aux centres de vaccination.</p>
                <a href="#" class="service-link">
                    En savoir plus
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Urbain -->
            <div class="service-card" id="urbaine">
                <div class="service-card-icon icon-orange">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div><h3>Urgence urbaine</h3></div>
                <p>Signalez les routes dégradées, les inondations ou tout autre problème d'infrastructure pour améliorer votre cadre de vie.</p>
                <a href="#" class="service-link">
                    En savoir plus
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Électricité -->
            <div class="service-card" id="electricite">
                <div class="service-card-icon icon-amber">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div><h3>Pannes électriques</h3></div>
                <p>Signalez les pannes de courant dans votre quartier et suivez l'avancement des réparations en temps réel.</p>
                <a href="#" class="service-link">
                    En savoir plus
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Alertes -->
            <div class="service-card">
                <div class="service-card-icon icon-green">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                </div>
                <div><h3>Alertes & actualités</h3></div>
                <p>Restez informé des alertes sanitaires, des épidémies et des informations importantes pour votre région.</p>
                <a href="#" class="service-link">
                    En savoir plus
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

        </div>
    </div>
</section>


<!-- ── CTA ── -->
<section class="cta-band">
    <div class="cta-inner">
        <h2>Prêt en cas d'urgence ?</h2>
        <p>Rejoignez des milliers de Congolais qui font confiance à CongoAssist pour leur sécurité et celle de leurs proches.</p>
        <a href="{{ route('services.login') }}" class="btn-cta">
            Créer un compte gratuitement
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>


<!-- ── FOOTER ── -->
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="#" class="logo" style="margin-bottom:0.6rem;">
                    <div class="logo-mark"><span>C</span></div>
                    <span class="logo-text">Congo<em>Assist</em></span>
                </a>
                <p>Votre partenaire de confiance en situations d'urgence au Congo.</p>
            </div>
            <div class="footer-col">
                <h4>Services</h4>
                <a href="#medical">Urgence médicale</a>
                <a href="#police">Sécurité publique</a>
                <a href="#vaccination">Vaccination</a>
                <a href="#urbaine">Urgence urbaine</a>
                <a href="#electricite">Électricité</a>
            </div>
            <div class="footer-col">
                <h4>À propos</h4>
                <a href="#">Notre mission</a>
                <a href="#">L'équipe</a>
                <a href="#">Partenaires</a>
                <a href="#">Contact</a>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <a href="mailto:lombaisaac8@gmail.com">lombaisaac8@gmail.com</a>
                <a href="#">Brazzaville, Congo</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 CongoAssist. Tous droits réservés.</p>
            <div class="social-links">
                <a href="#" class="social-link" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                </a>
                <a href="#" class="social-link" aria-label="Twitter">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M23 3a10.9 10.9 0 01-3.14 1.53A4.48 4.48 0 0022.43 1a9 9 0 01-2.88 1.1A4.52 4.52 0 0016.11 0c-2.5 0-4.52 2.02-4.52 4.52 0 .35.04.7.11 1.03A12.84 12.84 0 011.64.89a4.52 4.52 0 001.4 6.03A4.49 4.49 0 011 6.43v.06a4.52 4.52 0 003.63 4.43 4.54 4.54 0 01-2.04.08 4.52 4.52 0 004.22 3.14A9.06 9.06 0 010 15.54a12.8 12.8 0 006.92 2.03c8.3 0 12.85-6.88 12.85-12.85 0-.2 0-.39-.01-.58A9.17 9.17 0 0023 3z"/></svg>
                </a>
                <a href="#" class="social-link" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                </a>
            </div>
        </div>
    </div>
</footer>


<!-- ══════════════════════════════════════════════
     PWA — Bouton flottant d'installation
══════════════════════════════════════════════ -->
<button id="pwa-fab" aria-label="Installer l'application">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
    Installer l'app
</button>

<!-- ══════════════════════════════════════════════
     PWA — Modal d'installation (bottom sheet)
══════════════════════════════════════════════ -->
<div id="pwa-modal-overlay">
    <div id="pwa-modal-box">
        <div id="pwa-modal-handle"></div>
        <div id="pwa-modal-header">
            <div id="pwa-modal-app-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <div id="pwa-modal-title"></div>
        </div>
        <div id="pwa-modal-body"></div>
        <div id="pwa-modal-steps"></div>
        <button id="pwa-modal-close">Fermer</button>
    </div>
</div>


<script>
    // ── Mobile menu burger ──
    const burger = document.getElementById('burger-btn');
    const mobileNav = document.getElementById('mobile-nav');
    burger.addEventListener('click', () => {
        mobileNav.classList.toggle('open');
    });
    mobileNav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => mobileNav.classList.remove('open'));
    });
</script>

<!-- ══ PWA Script ══ -->
<script>
(function () {

    /* ── Service Worker ── */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(reg  => console.log('[SW] enregistré :', reg.scope))
                .catch(err => console.warn('[SW] erreur :', err));
        });
    }

    /* ── Ne pas afficher si déjà installée ── */
    const isStandalone = window.navigator.standalone === true
        || window.matchMedia('(display-mode: standalone)').matches
        || window.matchMedia('(display-mode: fullscreen)').matches;
    if (isStandalone) return;

    /* ── Détection plateforme ── */
    const ua        = navigator.userAgent.toLowerCase();
    const isIOS     = /iphone|ipad|ipod/.test(ua);
    const isAndroid = /android/.test(ua);
    const isSamsung = /samsungbrowser/.test(ua);

    /* ── Éléments DOM ── */
    const fab      = document.getElementById('pwa-fab');
    const overlay  = document.getElementById('pwa-modal-overlay');
    const title    = document.getElementById('pwa-modal-title');
    const body     = document.getElementById('pwa-modal-body');
    const steps    = document.getElementById('pwa-modal-steps');
    const closeBtn = document.getElementById('pwa-modal-close');

    let deferredPrompt = null;

    /* ── Chrome / Edge / Android : prompt natif ── */
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        fab.classList.add('visible');
    });

    /* ── iOS Safari ── */
    if (isIOS) {
        fab.classList.add('visible');
    }

    /* ── Masquer après installation ── */
    window.addEventListener('appinstalled', () => {
        fab.classList.remove('visible');
        deferredPrompt = null;
    });

    /* ── Clic sur le FAB ── */
    fab.addEventListener('click', async () => {
        if (deferredPrompt) {
            try {
                await deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') fab.classList.remove('visible');
            } catch {
                showGuide();
            }
            deferredPrompt = null;
        } else {
            showGuide();
        }
    });

    /* ── Étapes numérotées ── */
    function makeSteps(list) {
        return list.map((s, i) =>
            `<div class="pwa-step">
                <span class="pwa-step-num">${i + 1}</span>
                <span>${s}</span>
            </div>`
        ).join('');
    }

    /* ── Guide manuel selon plateforme ── */
    function showGuide() {
        body.innerHTML = '';

        if (isIOS) {
            title.textContent = 'Installer sur iPhone / iPad';
            steps.innerHTML = makeSteps([
                'Ouvrez ce site dans <strong>Safari</strong>',
                'Appuyez sur <span class="pwa-chip">⎙ Partager</span> en bas de l\'écran',
                'Sélectionnez <span class="pwa-chip">Sur l\'écran d\'accueil</span>',
                'Appuyez sur <span class="pwa-chip">Ajouter</span> pour confirmer'
            ]);
        } else if (isSamsung) {
            title.textContent = 'Installer sur Samsung';
            steps.innerHTML = makeSteps([
                'Ouvrez ce site dans <strong>Samsung Internet</strong>',
                'Appuyez sur <span class="pwa-chip">⋮ Menu</span> en bas de l\'écran',
                'Sélectionnez <span class="pwa-chip">Ajouter page à</span>',
                'Choisissez <span class="pwa-chip">Écran d\'accueil</span>'
            ]);
        } else if (isAndroid) {
            title.textContent = 'Installer sur Android';
            steps.innerHTML = makeSteps([
                'Ouvrez ce site dans <strong>Chrome</strong>',
                'Appuyez sur le menu <span class="pwa-chip">⋮</span> en haut à droite',
                'Sélectionnez <span class="pwa-chip">Installer l\'application</span>',
                'Confirmez en appuyant sur <span class="pwa-chip">Installer</span>'
            ]);
        } else {
            title.textContent = 'Installer CongoAssist';
            steps.innerHTML = makeSteps([
                'Ouvrez ce site dans <strong>Chrome</strong> ou <strong>Edge</strong>',
                'Cliquez sur l\'icône <span class="pwa-chip">⊕</span> dans la barre d\'adresse',
                'Ou menu <span class="pwa-chip">⋮</span> → <span class="pwa-chip">Installer l\'application</span>',
                'Confirmez l\'installation'
            ]);
        }

        overlay.classList.add('open');
    }

    /* ── Fermeture ── */
    closeBtn.addEventListener('click', () => overlay.classList.remove('open'));
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.remove('open');
    });

})();
</script>

</body>
</html>
