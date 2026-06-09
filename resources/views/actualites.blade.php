<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualités — CongoAssist</title>
    <link rel="icon" href="medias/Clogo.jpg" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:    #1a56db;
            --green:   #10b981;
            --text:    #0f172a;
            --muted:   #64748b;
            --border:  #e2e8f0;
            --bg:      #f4f6f9;
            --card:    #ffffff;
            --nav-h:   64px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ═══════════════════════════════════
           TOP NAV
        ═══════════════════════════════════ */
        .topnav {
            position: sticky;
            top: 0; z-index: 100;
            height: var(--nav-h);
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            box-shadow: 0 1px 8px rgba(15,23,42,0.06);
        }

        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; flex-shrink: 0; margin-right: 28px;
        }
        .nav-logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #1a56db 0%, #10b981 100%);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: 18px;
        }
        .nav-logo-text {
            font-size: 17px; font-weight: 700;
            color: var(--text); letter-spacing: -0.02em;
        }
        .nav-logo-text span { color: var(--green); }

        .nav-links {
            display: flex; align-items: center; gap: 2px; flex: 1;
        }
        .nav-link {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 13px; border-radius: 8px;
            text-decoration: none; font-size: 13.5px; font-weight: 500;
            color: var(--muted); transition: background 0.18s, color 0.18s;
            white-space: nowrap;
        }
        .nav-link svg { width: 15px; height: 15px; flex-shrink: 0; }
        .nav-link:hover { background: #f1f5f9; color: var(--text); }
        .nav-link.active { background: #eff6ff; color: var(--blue); font-weight: 600; }

        .nav-logout {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px;
            border: 1.5px solid #fecaca; text-decoration: none;
            font-size: 13px; font-weight: 600; color: #ef4444;
            transition: background 0.18s; margin-left: 16px; flex-shrink: 0;
        }
        .nav-logout:hover { background: #fef2f2; }
        .nav-logout svg { width: 14px; height: 14px; }

        .nav-hamburger {
            display: none; background: none; border: none;
            cursor: pointer; color: var(--text);
            margin-left: auto; padding: 6px;
        }
        .nav-hamburger svg { width: 22px; height: 22px; }

        /* ═══════════════════════════════════
           MOBILE DRAWER
        ═══════════════════════════════════ */
        .mobile-drawer {
            display: none; position: fixed; inset: 0; z-index: 200;
            background: rgba(15,23,42,0.45); backdrop-filter: blur(2px);
        }
        .mobile-drawer.open { display: block; }

        .drawer-panel {
            position: absolute; top: 0; right: 0;
            width: min(300px, 82vw); height: 100%;
            background: #fff; padding: 20px 18px;
            display: flex; flex-direction: column; gap: 2px;
            transform: translateX(100%);
            transition: transform 0.28s ease;
        }
        .mobile-drawer.open .drawer-panel { transform: translateX(0); }

        .drawer-close {
            align-self: flex-end; background: none; border: none;
            cursor: pointer; color: var(--muted); margin-bottom: 12px;
        }
        .drawer-close svg { width: 22px; height: 22px; }

        .drawer-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 14px; border-radius: 10px;
            text-decoration: none; font-size: 15px; font-weight: 500;
            color: var(--text); transition: background 0.18s;
        }
        .drawer-link svg { width: 18px; height: 18px; color: var(--muted); }
        .drawer-link:hover { background: #f1f5f9; }
        .drawer-link.active { background: #eff6ff; color: var(--blue); font-weight: 600; }
        .drawer-link.active svg { color: var(--blue); }

        .drawer-sep { height: 1px; background: var(--border); margin: 10px 0; }

        .drawer-logout {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 14px; border-radius: 10px;
            text-decoration: none; font-size: 15px; font-weight: 600;
            color: #ef4444; transition: background 0.18s;
        }
        .drawer-logout:hover { background: #fef2f2; }
        .drawer-logout svg { width: 18px; height: 18px; }

        /* ═══════════════════════════════════
           PAGE LAYOUT
        ═══════════════════════════════════ */
        .page-container {
            max-width: 720px;
            margin: 0 auto;
            padding: 36px 16px 64px;
        }

        @keyframes riseIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .page-heading {
            margin-bottom: 28px;
            animation: riseIn 0.5s ease forwards;
        }
        .page-heading h1 {
            font-family: 'Lora', serif;
            font-size: clamp(26px, 5vw, 34px);
            font-weight: 600; color: var(--text);
            letter-spacing: -0.02em; line-height: 1.15;
        }
        .page-heading p {
            margin-top: 5px; font-size: 14px;
            color: var(--muted); font-weight: 400;
        }

        /* ═══════════════════════════════════
           POST CARD
        ═══════════════════════════════════ */
        .post-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            opacity: 0;
            animation: riseIn 0.45s ease forwards;
            transition: box-shadow 0.25s ease, transform 0.25s ease;
        }
        .post-card:hover {
            box-shadow: 0 8px 32px rgba(15,23,42,0.09);
            transform: translateY(-2px);
        }

        /* Author strip */
        .card-author {
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 18px 20px 14px;
        }
        .author-left { display: flex; align-items: center; gap: 12px; }

        .avatar {
            width: 46px; height: 46px; border-radius: 50%;
            background: linear-gradient(135deg, var(--blue), var(--green));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: 18px; flex-shrink: 0;
        }
        .author-name { font-size: 15px; font-weight: 600; color: var(--text); }
        .author-meta {
            display: flex; align-items: center; gap: 5px;
            font-size: 12px; color: var(--muted);
            margin-top: 3px; font-weight: 400;
        }
        .author-meta svg { width: 11px; height: 11px; }

        .tag {
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.07em; text-transform: uppercase;
            padding: 4px 11px; border-radius: 999px;
            background: #eff6ff; color: var(--blue);
            border: 1px solid #bfdbfe; flex-shrink: 0;
        }

        /* Body */
        .card-body { padding: 0 20px 18px; }
        .post-text {
            font-size: 15.5px; line-height: 1.82;
            color: #2d3748; font-family: 'Lora', serif;
            white-space: pre-line;
        }
        .read-more {
            display: inline-flex; align-items: center; gap: 4px;
            margin-top: 8px; font-size: 13px; font-weight: 600;
            color: var(--blue); font-family: 'Sora', sans-serif;
            cursor: pointer; background: none; border: none;
            padding: 0; transition: color 0.18s;
        }
        .read-more:hover { color: var(--green); }
        .read-more svg { width: 13px; height: 13px; }

        /* Media */
        .card-media { background: #f8fafc; overflow: hidden; }
        .card-media img {
            width: 100%; max-height: 480px;
            object-fit: cover; display: block;
            transition: transform 0.4s ease;
        }
        .post-card:hover .card-media img { transform: scale(1.025); }
        .card-media video {
            width: 100%; max-height: 460px;
            display: block; background: #000;
        }
        .card-accent {
            height: 4px;
            background: linear-gradient(90deg, var(--blue) 0%, var(--green) 100%);
        }

        /* Actions */
        .card-actions {
            display: flex; align-items: center;
            justify-content: flex-end; gap: 6px;
            padding: 12px 16px;
            border-top: 1px solid var(--border);
        }
        .act-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            cursor: pointer; border: 1px solid transparent;
            transition: all 0.18s; text-decoration: none;
            font-family: 'Sora', sans-serif;
        }
        .act-btn svg { width: 14px; height: 14px; }
        .act-edit { color: var(--blue); background: #eff6ff; border-color: #bfdbfe; }
        .act-edit:hover { background: #dbeafe; }
        .act-delete { color: #ef4444; background: #fef2f2; border-color: #fecaca; }
        .act-delete:hover { background: #fee2e2; }

        /* Empty state */
        .empty {
            text-align: center; padding: 80px 24px;
            background: var(--card);
            border: 1.5px dashed var(--border);
            border-radius: 16px;
        }
        .empty-icon {
            width: 72px; height: 72px; border-radius: 50%;
            background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .empty-icon svg { width: 30px; height: 30px; color: #94a3b8; }
        .empty h3 {
            font-family: 'Lora', serif; font-size: 22px;
            font-weight: 600; color: var(--text); margin-bottom: 8px;
        }
        .empty p { font-size: 14px; color: var(--muted); }

        /* Load more */
        .load-more-wrap { text-align: center; margin-top: 12px; }
        .load-more-btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 28px; border-radius: 10px;
            border: 1.5px solid var(--border);
            background: var(--card); color: var(--muted);
            font-size: 14px; font-weight: 600;
            cursor: pointer; font-family: 'Sora', sans-serif;
            transition: all 0.2s;
        }
        .load-more-btn svg { width: 15px; height: 15px; }
        .load-more-btn:hover {
            border-color: var(--blue); color: var(--blue);
            box-shadow: 0 4px 16px rgba(26,86,219,0.10);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links, .nav-logout { display: none; }
            .nav-hamburger { display: flex; }
            .page-container { padding: 24px 12px 48px; }
        }
        @media (min-width: 769px) {
            .mobile-drawer { display: none !important; }
        }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════
     TOPNAV
══════════════════════════════════════ -->
<nav class="topnav">
    <a href="#" class="nav-logo">
        <div class="nav-logo-icon">C</div>
        <span class="nav-logo-text">Congo<span>Assist</span></span>
    </a>

    <div class="nav-links">
        <a href="{{ route('bilanController') }}" class="nav-link">
            <i data-feather="activity"></i> Mon bilan
        </a>
        <a href="{{ route('vaccinationMenuController') }}" class="nav-link">
            <i data-feather="shield"></i> Vaccinations
        </a>
        <a href="{{ route('actualitesController') }}" class="nav-link active">
            <i data-feather="newspaper"></i> Actualités
        </a>
        <a href="{{ route('MesAlertesController') }}" class="nav-link">
            <i data-feather="bell"></i> Urgences
        </a>
        <a href="{{ route('compteController') }}" class="nav-link">
            <i data-feather="grid"></i> Tableau de bord
        </a>
        <a href="{{ route('codeQR') }}" class="nav-link">
            <i data-feather="maximize"></i> Code QR
        </a>
    </div>

    <a href="{{ route('accueil') }}" class="nav-logout">
        <i data-feather="log-out"></i> Déconnexion
    </a>

    <button class="nav-hamburger" id="hamburger" aria-label="Menu">
        <i data-feather="menu"></i>
    </button>
</nav>

<!-- ══════════════════════════════════════
     MOBILE DRAWER
══════════════════════════════════════ -->
<div class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-panel" id="drawerPanel">
        <button class="drawer-close" id="drawerClose"><i data-feather="x"></i></button>

        <a href="{{ route('bilanController') }}" class="drawer-link"><i data-feather="activity"></i> Mon bilan</a>
        <a href="{{ route('vaccinationMenuController') }}" class="drawer-link"><i data-feather="shield"></i> Vaccinations</a>
        <a href="{{ route('actualitesController') }}" class="drawer-link active"><i data-feather="newspaper"></i> Actualités</a>
        <a href="{{ route('MesAlertesController') }}" class="drawer-link"><i data-feather="bell"></i> Urgences</a>
        <a href="{{ route('compteController') }}" class="drawer-link"><i data-feather="grid"></i> Tableau de bord</a>
        <a href="{{ route('codeQR') }}" class="drawer-link"><i data-feather="maximize"></i> Code QR</a>

        <div class="drawer-sep"></div>
        <a href="{{ route('accueil') }}" class="drawer-logout"><i data-feather="log-out"></i> Déconnexion</a>
    </div>
</div>

<!-- ══════════════════════════════════════
     FEED
══════════════════════════════════════ -->
<div class="page-container">

    <div class="page-heading">
        <h1>Actualités</h1>
        <p>{{ $actualites->count() }} article{{ $actualites->count() > 1 ? 's' : '' }} disponible{{ $actualites->count() > 1 ? 's' : '' }}</p>
    </div>

    @forelse($actualites as $index => $actu)

    <article class="post-card" style="animation-delay: {{ $index * 0.07 }}s;">

        <div class="card-author">
            <div class="author-left">
                <div class="avatar">{{ strtoupper(substr($actu->auteur_nom, 0, 1)) }}</div>
                <div>
                    <div class="author-name">{{ $actu->auteur_nom }}</div>
                    <div class="author-meta">
                        <i data-feather="clock"></i>
                        {{ \Carbon\Carbon::parse($actu->date_publication)->diffForHumans() }}
                        &nbsp;·&nbsp;
                        {{ \Carbon\Carbon::parse($actu->date_publication)->translatedFormat('d M Y') }}
                    </div>
                </div>
            </div>
            <span class="tag">Actualité</span>
        </div>

        <div class="card-body">
            <p class="post-text" id="body-{{ $actu->id }}">{{ Str::limit($actu->contenu, 300) }}</p>
            @if(strlen($actu->contenu) > 300)
                <button class="read-more" id="btn-{{ $actu->id }}"
                        data-full="{{ $actu->contenu }}"
                        data-short="{{ Str::limit($actu->contenu, 300) }}"
                        data-open="false"
                        onclick="toggleContent({{ $actu->id }})">
                    Lire la suite <i data-feather="chevron-down"></i>
                </button>
            @endif
        </div>

        @if($actu->url_media)
            <div class="card-media">
                @if($actu->type_media === 'mp4')
                    <video controls>
                        <source src="{{ $actu->url_media }}" type="video/mp4">
                    </video>
                @else
                    <img src="{{ $actu->url_media }}" alt="Illustration">
                @endif
            </div>
        @else
            <div class="card-accent"></div>
        @endif

        <div class="card-actions">

        </div>

    </article>

    @empty

    <div class="empty">
        <div class="empty-icon"><i data-feather="inbox"></i></div>
        <h3>Aucune actualité pour l'instant</h3>
        <p>Revenez plus tard pour découvrir les dernières nouvelles.</p>
    </div>

    @endforelse

    @if($actualites->count() > 0)
    <div class="load-more-wrap">
        <button class="load-more-btn">
            <i data-feather="refresh-cw"></i> Charger plus
        </button>
    </div>
    @endif

</div>

<script>
feather.replace();

function toggleContent(id) {
    const body = document.getElementById('body-' + id);
    const btn  = document.getElementById('btn-' + id);
    const open = btn.dataset.open === 'true';
    body.textContent = open ? btn.dataset.short : btn.dataset.full;
    btn.dataset.open = open ? 'false' : 'true';
    btn.innerHTML    = open
        ? 'Lire la suite <i data-feather="chevron-down"></i>'
        : 'Réduire <i data-feather="chevron-up"></i>';
    feather.replace();
}

const hamburger   = document.getElementById('hamburger');
const drawer      = document.getElementById('mobileDrawer');
const drawerClose = document.getElementById('drawerClose');

hamburger.addEventListener('click',   () => { drawer.classList.add('open');    document.body.style.overflow = 'hidden'; });
drawerClose.addEventListener('click', () => { drawer.classList.remove('open'); document.body.style.overflow = ''; });
drawer.addEventListener('click', e => {
    if (e.target === drawer) { drawer.classList.remove('open'); document.body.style.overflow = ''; }
});
</script>
</body>
</html>
