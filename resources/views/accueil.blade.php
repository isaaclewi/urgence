<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="MV6linsONY2-JJ_AcRM1kpei_58RFHjlG-jI5TYCk-w" />
    <title>CongoAssist — Signalement des urgences</title>

    {{-- PWA Meta --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#10b981">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="CongoAssist">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }

        /* ── Animations ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .5; transform: scale(1.4); }
        }
        .animate-fade-in   { animation: fadeInUp .65s ease-out both; }
        .delay-1 { animation-delay: .1s; }
        .delay-2 { animation-delay: .2s; }
        .delay-3 { animation-delay: .3s; }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

        /* ── Nav ── */
        .nav-link { position: relative; padding-bottom: 3px; }
        .nav-link::after {
            content: ''; position: absolute; bottom: 0; left: 0;
            width: 0; height: 2px; background: #10b981;
            transition: width .25s ease;
        }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }

        /* ── Cards ── */
        .service-card {
            transition: transform .3s ease, box-shadow .3s ease;
            border: 1px solid #e5e7eb;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(0,0,0,.09);
            border-color: #10b981;
        }
        .hero-card {
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .hero-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 48px rgba(0,0,0,.18);
        }

        /* ── Image overlay ── */
        .img-wrap { position: relative; overflow: hidden; }
        .img-wrap img { transition: transform .4s ease; }
        .img-wrap:hover img { transform: scale(1.04); }
        .img-wrap::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(to bottom, transparent 30%, rgba(0,0,0,.55));
            pointer-events: none;
        }

        /* ── Form fields ── */
        .form-field {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 14px;
            color: #1f2937;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
            appearance: none;
        }
        .form-field:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,.12);
        }

        /* ── Section divider ── */
        .section-label {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 11px; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; color: #10b981;
            margin-bottom: 12px;
        }
        .section-label::before {
            content: ''; display: block;
            width: 24px; height: 2px; background: #10b981;
            border-radius: 2px;
        }

        /* ── Stats bar ── */
        .stat-item { border-right: 1px solid #e5e7eb; }
        .stat-item:last-child { border-right: none; }

        /* ── Urgence badge ── */
        .urgence-badge {
            position: absolute; top: 14px; left: 14px; z-index: 20;
            padding: 5px 12px; border-radius: 6px;
            font-size: 10px; font-weight: 800;
            letter-spacing: .1em; text-transform: uppercase;
        }

        /* ── Record button ── */
        #recordBtn.recording {
            background: #dc2626 !important;
            animation: pulse-dot 1.5s ease-in-out infinite;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        /* ── Trust strip ── */
        .trust-icon-wrap {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
        }

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
            background: #10b981;
            color: #fff;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .75rem 1.4rem;
            border-radius: 100px;
            border: none;
            box-shadow: 0 8px 28px rgba(16,185,129,.45);
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            -webkit-tap-highlight-color: transparent;
        }
        #pwa-fab.visible { display: flex; }
        #pwa-fab:hover   { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(16,185,129,.55); }
        #pwa-fab:active  { transform: scale(.97); }

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

        /* Modal box */
        #pwa-modal-box {
            background: #fff;
            border-radius: 24px 24px 0 0;
            padding: 2rem 1.8rem max(2rem, env(safe-area-inset-bottom));
            width: 100%; max-width: 480px;
        }

        /* Poignée */
        #pwa-modal-handle {
            width: 36px; height: 4px;
            background: #e5e7eb; border-radius: 2px;
            margin: 0 auto 1.4rem;
        }

        /* En-tête modal */
        #pwa-modal-header {
            display: flex; align-items: center;
            gap: 12px; margin-bottom: .75rem;
        }
        #pwa-modal-app-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: #10b981;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        #pwa-modal-title {
            font-size: 1rem; font-weight: 800; color: #0f172a;
        }

        /* Corps modal */
        #pwa-modal-body {
            font-size: .85rem; color: #64748b;
            line-height: 1.7; margin-bottom: 1.2rem;
        }

        /* Chips inline */
        .pwa-chip {
            display: inline-block;
            background: #ecfdf5; color: #065f46;
            border-radius: 6px; padding: .08rem .5rem;
            font-weight: 700; font-size: .8rem;
        }

        /* Étapes numérotées */
        #pwa-modal-steps {
            display: flex; flex-direction: column;
            gap: .6rem; margin-bottom: 1.4rem;
        }
        .pwa-step {
            display: flex; align-items: flex-start;
            gap: 10px; font-size: .82rem; color: #475569;
        }
        .pwa-step-num {
            width: 22px; height: 22px; border-radius: 50%;
            background: #10b981; color: #fff;
            font-size: .62rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }

        /* Bouton fermer */
        #pwa-modal-close {
            display: block; width: 100%;
            background: #10b981; color: #fff;
            font-weight: 700; font-size: .78rem;
            letter-spacing: .08em; text-transform: uppercase;
            padding: .9rem; border-radius: 100px;
            border: none; cursor: pointer;
            transition: background .2s;
            -webkit-tap-highlight-color: transparent;
        }
        #pwa-modal-close:hover { background: #059669; }
    </style>
</head>

<body class="bg-white text-gray-900 min-h-screen antialiased">

    {{-- ═══════════════════════════ NAVBAR ═══════════════════════════ --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">

            {{-- Logo --}}
            <a href="{{ route('accueil') }}" class="flex items-center gap-3 shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-700 to-green-500 flex items-center justify-center shadow-sm">
                    <span class="text-white font-bold text-lg leading-none">C</span>
                </div>
                <span class="text-xl font-bold tracking-tight">
                    <span class="text-blue-800">Congo</span><span class="text-green-500">Assist</span>
                </span>
            </a>

            {{-- Desktop links --}}
            <ul class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <li><a href="{{ route('accueil') }}" class="nav-link active hover:text-gray-900 transition">Accueil</a></li>
                <li><a href="{{ route('avis') }}"    class="nav-link hover:text-gray-900 transition">Avis</a></li>
                <li><a href="{{ route('regle') }}"   class="nav-link hover:text-gray-900 transition">Règlement</a></li>
            </ul>

            {{-- CTA --}}
            <div class="hidden md:flex items-center gap-3">
                <span class="flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full pulse-dot"></span>
                    Service actif
                </span>
                <a href="{{ route('login') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm hover:shadow-md">
                    Connexion
                </a>
            </div>

            {{-- Burger --}}
            <button id="menu-btn" class="md:hidden text-gray-600 hover:text-gray-900 transition">
                <i data-feather="menu" class="w-5 h-5"></i>
            </button>
        </nav>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 px-6 py-4 space-y-3">
            <a href="{{ route('accueil') }}" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-green-600 transition py-1">
                <i data-feather="home" class="w-4 h-4"></i> Accueil
            </a>
            <a href="{{ route('avis') }}"    class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-green-600 transition py-1">
                <i data-feather="star" class="w-4 h-4"></i> Avis
            </a>
            <a href="{{ route('regle') }}"   class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-green-600 transition py-1">
                <i data-feather="book-open" class="w-4 h-4"></i> Règlement
            </a>
            <div class="pt-2 border-t border-gray-100">
                <a href="{{ route('login') }}" class="block w-full bg-green-600 text-white text-center py-2.5 rounded-xl text-sm font-semibold">
                    Connexion
                </a>
            </div>
        </div>
    </header>

    <main>

        {{-- ═══════════════════════════ HERO ═══════════════════════════ --}}
        <section class="max-w-7xl mx-auto px-6 pt-12 pb-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                {{-- Left text --}}
                <div class="animate-fade-in">
                    <div class="section-label">Plateforme citoyenne · Brazzaville</div>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
                        Signalez une urgence,<br>
                        <span class="text-green-600">protégez votre communauté</span>
                    </h1>
                    <p class="text-gray-500 text-lg leading-relaxed mb-8 max-w-lg">
                        CongoAssist connecte les citoyens aux services d'urgence — ambulances, pompiers, police — pour des interventions plus rapides et une ville plus sûre.
                    </p>
                    <div class="flex flex-wrap gap-3 mb-10">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-7 py-3.5 rounded-xl font-semibold text-sm transition shadow-md hover:shadow-lg">
                            <i data-feather="send" class="w-4 h-4"></i>
                            Faire un signalement
                        </a>
                        <a href="{{ route('regle') }}"
                           class="inline-flex items-center gap-2 border border-gray-200 hover:border-gray-300 text-gray-700 hover:bg-gray-50 px-7 py-3.5 rounded-xl font-semibold text-sm transition">
                            <i data-feather="info" class="w-4 h-4"></i>
                            En savoir plus
                        </a>
                    </div>

                    {{-- Stats strip --}}
                    <div class="flex divide-x divide-gray-200 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100">
                        <div class="flex-1 px-5 py-4 text-center">
                            <div class="text-2xl font-extrabold text-gray-900">1 248</div>
                            <div class="text-xs text-gray-500 font-medium mt-1">Signalements traités</div>
                        </div>
                        <div class="flex-1 px-5 py-4 text-center">
                            <div class="text-2xl font-extrabold text-green-600">98%</div>
                            <div class="text-xs text-gray-500 font-medium mt-1">Taux de réponse</div>
                        </div>
                        <div class="flex-1 px-5 py-4 text-center">
                            <div class="text-2xl font-extrabold text-gray-900">~8 min</div>
                            <div class="text-xs text-gray-500 font-medium mt-1">Délai moyen</div>
                        </div>
                    </div>
                </div>

                {{-- Right image --}}
                <div class="animate-fade-in delay-2 relative">
                    <div class="relative rounded-2xl overflow-hidden shadow-xl border border-gray-100">
                        <img src="medias/télécharger (8).jfif"
                             alt="Urgence et sécurité"
                             class="w-full h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    </div>
                    {{-- Floating live badge --}}
                    <div class="absolute -bottom-4 left-6 bg-white rounded-xl shadow-lg border border-gray-100 px-4 py-3 flex items-center gap-3">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full pulse-dot shrink-0"></span>
                        <div>
                            <div class="text-xs font-bold text-gray-900">Urgence médicale — Bacongo</div>
                            <div class="text-xs text-gray-400">Prise en charge il y a 3 min</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ FEATURED CARDS ═══════════════════════ --}}
        <section class="max-w-7xl mx-auto px-6 pb-16">
            <div class="mb-8">
                <div class="section-label">Services prioritaires</div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Urgences principales</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-14">

                {{-- Urgence médicale --}}
                <div class="hero-card rounded-2xl overflow-hidden h-72 relative cursor-pointer">
                    <div class="img-wrap h-full">
                        <img src="medias/UE.jpeg" alt="Urgence médicale" class="w-full h-full object-cover">
                    </div>
                    <span class="urgence-badge bg-red-500 text-white">Urgence médicale</span>
                    <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                        <h3 class="text-white text-xl font-bold mb-1">Ambulance & Secours</h3>
                        <p class="text-white/80 text-sm leading-relaxed">Signalez toute situation nécessitant une intervention médicale immédiate.</p>
                        <div class="mt-3 inline-flex items-center gap-1.5 text-white/90 text-xs font-semibold">
                            Signaler maintenant <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </div>
                    </div>
                </div>

                {{-- Pompiers --}}
                <div class="hero-card rounded-2xl overflow-hidden h-72 relative cursor-pointer">
                    <div class="img-wrap h-full">
                        <img src="medias/dd038250-b88a-4d0a-ba6a-0b484559d8bd.jpeg" alt="Pompiers" class="w-full h-full object-cover">
                    </div>
                    <span class="urgence-badge bg-orange-500 text-white">Pompiers</span>
                    <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                        <h3 class="text-white text-xl font-bold mb-1">Incendie & Accident grave</h3>
                        <p class="text-white/80 text-sm leading-relaxed">Alertez les sapeurs-pompiers pour tout incendie ou sinistre majeur.</p>
                        <div class="mt-3 inline-flex items-center gap-1.5 text-white/90 text-xs font-semibold">
                            Signaler maintenant <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Other services grid --}}
            <div class="mb-8">
                <div class="section-label">Autres catégories</div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Tous les services</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                {{-- Police --}}
                <div class="service-card bg-white rounded-2xl overflow-hidden cursor-pointer">
                    <div class="img-wrap h-44">
                        <img src="medias/912a6016-81c1-4f66-b02b-1639e12e9bae.jpeg" alt="Police" class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span class="inline-block bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full mb-3 border border-blue-100">Police</span>
                        <h3 class="text-gray-900 font-bold text-base mb-2">Police & Sécurité</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Signalez toute activité suspecte nécessitant l'intervention des forces de l'ordre.</p>
                        <div class="mt-4 flex items-center gap-1 text-green-600 text-xs font-semibold hover:gap-2 transition-all">
                            Faire un signalement <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </div>
                    </div>
                </div>

                {{-- Routes --}}
                <div class="service-card bg-white rounded-2xl overflow-hidden cursor-pointer">
                    <div class="img-wrap h-44">
                        <img src="https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=600" alt="Routes" class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span class="inline-block bg-yellow-50 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full mb-3 border border-yellow-100">Voirie</span>
                        <h3 class="text-gray-900 font-bold text-base mb-2">Routes Urbaines</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Alertez sur les dangers routiers, nids-de-poule et infrastructures défectueuses.</p>
                        <div class="mt-4 flex items-center gap-1 text-green-600 text-xs font-semibold hover:gap-2 transition-all">
                            Faire un signalement <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </div>
                    </div>
                </div>

                {{-- Propreté --}}
                <div class="service-card bg-white rounded-2xl overflow-hidden cursor-pointer">
                    <div class="img-wrap h-44">
                        <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=600" alt="Déchets" class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span class="inline-block bg-green-50 text-green-700 text-xs font-bold px-3 py-1 rounded-full mb-3 border border-green-100">Propreté</span>
                        <h3 class="text-gray-900 font-bold text-base mb-2">Déchets & Hygiène</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Signalez les problèmes d'hygiène publique et les dépôts sauvages dans votre quartier.</p>
                        <div class="mt-4 flex items-center gap-1 text-green-600 text-xs font-semibold hover:gap-2 transition-all">
                            Faire un signalement <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </div>
                    </div>
                </div>

                {{-- Électricité --}}
                <div class="service-card bg-white rounded-2xl overflow-hidden cursor-pointer">
                    <div class="img-wrap h-44">
                        <img src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=600" alt="Électricité" class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span class="inline-block bg-amber-50 text-amber-700 text-xs font-bold px-3 py-1 rounded-full mb-3 border border-amber-100">Électricité</span>
                        <h3 class="text-gray-900 font-bold text-base mb-2">Pannes Électriques</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Signalez les pannes de courant, câbles exposés ou infrastructures électriques dangereuses.</p>
                        <div class="mt-4 flex items-center gap-1 text-green-600 text-xs font-semibold hover:gap-2 transition-all">
                            Faire un signalement <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </div>
                    </div>
                </div>

                {{-- Carte --}}
                <div class="service-card bg-white rounded-2xl overflow-hidden cursor-pointer">
                    <div class="img-wrap h-44">
                        <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?w=600" alt="Carte" class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span class="inline-block bg-purple-50 text-purple-700 text-xs font-bold px-3 py-1 rounded-full mb-3 border border-purple-100">Carte</span>
                        <h3 class="text-gray-900 font-bold text-base mb-2">Carte des Urgences</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Visualisez en temps réel tous les signalements actifs dans votre ville.</p>
                        <div class="mt-4 flex items-center gap-1 text-green-600 text-xs font-semibold hover:gap-2 transition-all">
                            Voir la carte <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </div>
                    </div>
                </div>

                {{-- Infos --}}
                <div class="service-card bg-white rounded-2xl overflow-hidden cursor-pointer">
                    <div class="img-wrap h-44">
                        <img src="https://images.unsplash.com/photo-1516321497487-e288fb19713f?w=600" alt="Informations" class="w-full h-full object-cover">
                    </div>
                    <div class="p-5">
                        <span class="inline-block bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full mb-3 border border-indigo-100">Infos</span>
                        <h3 class="text-gray-900 font-bold text-base mb-2">Informations Utiles</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Consultez les numéros d'urgence nationaux et les consignes de sécurité.</p>
                        <div class="mt-4 flex items-center gap-1 text-green-600 text-xs font-semibold hover:gap-2 transition-all">
                            Consulter <i data-feather="arrow-right" class="w-3 h-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════ TRUST STRIP ══════════════════════ --}}
        <section class="bg-gray-50 border-y border-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="flex gap-4 items-start">
                        <div class="trust-icon-wrap bg-green-50 border border-green-100">
                            <i data-feather="zap" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Intervention rapide</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">Les alertes sont transmises instantanément aux équipes compétentes, réduisant les délais d'intervention.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="trust-icon-wrap bg-blue-50 border border-blue-100">
                            <i data-feather="users" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Plateforme citoyenne</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">Chaque Congolais contribue à la sécurité collective en partageant ses observations en temps réel.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="trust-icon-wrap bg-purple-50 border border-purple-100">
                            <i data-feather="lock" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Signalement confidentiel</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">Vos informations sont protégées et transmises uniquement aux services habilités à intervenir.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ QUICK REPORT FORM ═══════════════════════ --}}
        <section class="max-w-4xl mx-auto px-6 py-16">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-lg overflow-hidden">

                {{-- Form header --}}
                <div class="bg-gradient-to-r from-green-600 to-green-500 px-8 py-7">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <i data-feather="alert-circle" class="w-5 h-5 text-white"></i>
                        </div>
                        <h2 class="text-white text-xl font-bold">Signalement Rapide</h2>
                    </div>
                    <p class="text-green-100 text-sm ml-12">Témoin d'une urgence ? Alertez les services compétents en quelques secondes.</p>
                </div>

                <div class="px-8 py-8">

                    @if (session('success'))
                        <div class="mb-6 flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl animate-fade-in">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-600 shrink-0 mt-0.5"></i>
                            <p class="text-green-800 text-sm font-semibold">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form id="alertForm" enctype="multipart/form-data" method="POST"
                          action="{{ route('enregistrerAlerte') }}" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Type --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Type d'urgence <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="type_alerte" required class="form-field pr-10">
                                        <option value="">-- Sélectionnez un type --</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->nom_service }}">{{ $service->nom_service }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                        <i data-feather="chevron-down" class="w-4 h-4 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Localisation --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Localisation précise <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="localisation"
                                           placeholder="Ex : Avenue de l'O.U.A, Brazzaville"
                                           class="form-field pl-10" required>
                                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                        <i data-feather="map-pin" class="w-4 h-4 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description de la situation</label>
                            <textarea name="description" rows="4"
                                      placeholder="Décrivez brièvement ce que vous observez — nombre de personnes impliquées, nature du danger, évolution..."
                                      class="form-field resize-none"></textarea>
                        </div>

                        {{-- Voice message --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Message vocal
                                <span class="ml-2 text-xs font-normal text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Optionnel</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <button type="button" id="recordBtn"
                                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
                                    <i data-feather="mic" class="w-4 h-4"></i> Enregistrer
                                </button>
                                <button type="button" id="stopBtn" disabled
                                        class="inline-flex items-center gap-2 bg-gray-100 text-gray-400 px-5 py-2.5 rounded-xl text-sm font-semibold cursor-not-allowed transition">
                                    <i data-feather="square" class="w-4 h-4"></i> Arrêter
                                </button>
                                <div id="recordingStatus" class="hidden items-center gap-2 text-sm text-red-600 font-medium">
                                    <span class="w-2 h-2 bg-red-500 rounded-full pulse-dot"></span>
                                    Enregistrement en cours…
                                </div>
                            </div>
                            <audio id="audioPreview" controls class="mt-4 hidden w-full rounded-xl"></audio>
                            <input type="file" name="audio" id="audioInput" class="hidden" accept="audio/*">
                        </div>

                        {{-- Submit --}}
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <p class="text-xs text-gray-400 flex items-center gap-1.5">
                                <i data-feather="shield" class="w-3.5 h-3.5"></i>
                                Signalement transmis de façon confidentielle
                            </p>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-xl text-sm transition shadow-md hover:shadow-lg">
                                <i data-feather="send" class="w-4 h-4"></i>
                                Envoyer l'alerte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        {{-- ══════════════════════ NUMÉROS URGENCE ══════════════════════ --}}
        <section class="max-w-7xl mx-auto px-6 pb-16">
            <div class="mb-8">
                <div class="section-label">À portée de main</div>
                <h2 class="text-2xl font-bold text-gray-900">Numéros d'urgence nationaux</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-red-50 border border-red-100 rounded-2xl p-5 text-center">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i data-feather="heart" class="w-5 h-5 text-red-600"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-red-600 mb-1">15</div>
                    <div class="text-xs font-semibold text-gray-700">SAMU</div>
                    <div class="text-xs text-gray-500 mt-1">Urgences médicales</div>
                </div>
                <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5 text-center">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i data-feather="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-orange-600 mb-1">18</div>
                    <div class="text-xs font-semibold text-gray-700">Pompiers</div>
                    <div class="text-xs text-gray-500 mt-1">Incendie & secours</div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 text-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i data-feather="shield" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-blue-600 mb-1">17</div>
                    <div class="text-xs font-semibold text-gray-700">Police</div>
                    <div class="text-xs text-gray-500 mt-1">Forces de l'ordre</div>
                </div>
                <div class="bg-green-50 border border-green-100 rounded-2xl p-5 text-center">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i data-feather="phone-call" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div class="text-2xl font-extrabold text-green-600 mb-1">112</div>
                    <div class="text-xs font-semibold text-gray-700">Urgence universelle</div>
                    <div class="text-xs text-gray-500 mt-1">Tout type d'urgence</div>
                </div>
            </div>
        </section>

    </main>

    {{-- ═══════════════════════════ FOOTER ═══════════════════════════ --}}
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-6 pt-12 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">

                {{-- Brand --}}
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-700 to-green-500 flex items-center justify-center">
                            <span class="text-white font-bold text-lg leading-none">C</span>
                        </div>
                        <span class="text-xl font-bold">
                            <span class="text-blue-400">Congo</span><span class="text-green-400">Assist</span>
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
                        Plateforme communautaire de signalement des urgences, pour des interventions plus rapides et une ville de Brazzaville plus sûre.
                    </p>
                    <div class="mt-5 flex items-center gap-2 text-xs font-medium text-green-400 bg-green-900/30 border border-green-800/50 px-3 py-2 rounded-full w-fit">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full pulse-dot"></span>
                        Plateforme opérationnelle 24h/24
                    </div>
                </div>

                {{-- Links --}}
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4 uppercase tracking-wide">Navigation</h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('accueil') }}" class="text-gray-400 hover:text-green-400 text-sm transition flex items-center gap-2"><i data-feather="chevron-right" class="w-3 h-3"></i>Accueil</a></li>
                        <li><a href="{{ route('avis') }}"    class="text-gray-400 hover:text-green-400 text-sm transition flex items-center gap-2"><i data-feather="chevron-right" class="w-3 h-3"></i>Les avis</a></li>
                        <li><a href="{{ route('regle') }}"   class="text-gray-400 hover:text-green-400 text-sm transition flex items-center gap-2"><i data-feather="chevron-right" class="w-3 h-3"></i>Règlement</a></li>
                        <li><a href="{{ route('login') }}"   class="text-gray-400 hover:text-green-400 text-sm transition flex items-center gap-2"><i data-feather="chevron-right" class="w-3 h-3"></i>Connexion</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4 uppercase tracking-wide">Contact</h4>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-2.5 text-sm text-gray-400">
                            <i data-feather="mail" class="w-4 h-4 text-green-500 shrink-0"></i>
                            lombaisaac8@gmail.com
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-400">
                            <i data-feather="phone" class="w-4 h-4 text-green-500 shrink-0"></i>
                            +242 05 052 0146
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-400">
                            <i data-feather="phone" class="w-4 h-4 text-green-500 shrink-0"></i>
                            +242 06 839 8249
                        </li>
                        <li class="flex items-center gap-2.5 text-sm text-gray-400">
                            <i data-feather="map-pin" class="w-4 h-4 text-green-500 shrink-0"></i>
                            Brazzaville, Congo
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-gray-500 text-xs">© 2026 CongoAssist. Tous droits réservés.</p>
                <p class="text-gray-500 text-xs">Fait avec <span class="text-green-500">♥</span> à Brazzaville, République du Congo</p>
            </div>
        </div>
    </footer>

    {{-- ══════════════════════════════════════════════
         PWA — Bouton flottant d'installation
    ══════════════════════════════════════════════ --}}
    <button id="pwa-fab" aria-label="Installer l'application">
        <i data-feather="download" class="w-4 h-4"></i>
        Installer l'app
    </button>

    {{-- ══════════════════════════════════════════════
         PWA — Modal d'installation (bottom sheet)
    ══════════════════════════════════════════════ --}}
    <div id="pwa-modal-overlay">
        <div id="pwa-modal-box">
            <div id="pwa-modal-handle"></div>
            <div id="pwa-modal-header">
                <div id="pwa-modal-app-icon">
                    <i data-feather="shield" style="color:#fff;width:22px;height:22px;"></i>
                </div>
                <div id="pwa-modal-title"></div>
            </div>
            <div id="pwa-modal-body"></div>
            <div id="pwa-modal-steps"></div>
            <button id="pwa-modal-close">Fermer</button>
        </div>
    </div>

    {{-- ══ Scripts principaux ══ --}}
    <script>
        feather.replace();

        // ── Mobile menu ──
        document.getElementById('menu-btn').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // ── Audio recording ──
        let mediaRecorder;
        let audioChunks = [];
        const recordBtn       = document.getElementById('recordBtn');
        const stopBtn         = document.getElementById('stopBtn');
        const audioPreview    = document.getElementById('audioPreview');
        const audioInput      = document.getElementById('audioInput');
        const recordingStatus = document.getElementById('recordingStatus');

        recordBtn.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks   = [];

                mediaRecorder.addEventListener('dataavailable', e => audioChunks.push(e.data));
                mediaRecorder.addEventListener('stop', () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    audioPreview.src = URL.createObjectURL(audioBlob);
                    audioPreview.classList.remove('hidden');

                    const file = new File([audioBlob], 'alerte_audio.webm', { type: 'audio/webm' });
                    const dt   = new DataTransfer();
                    dt.items.add(file);
                    audioInput.files = dt.files;

                    recordBtn.disabled = false;
                    stopBtn.disabled   = true;
                    recordBtn.classList.remove('recording');
                    recordingStatus.classList.add('hidden');
                    recordingStatus.classList.remove('flex');
                    feather.replace();
                });

                mediaRecorder.start();
                recordBtn.disabled = true;
                stopBtn.disabled   = false;
                recordBtn.classList.add('recording');
                recordingStatus.classList.remove('hidden');
                recordingStatus.classList.add('flex');
                feather.replace();
            } catch {
                alert("Erreur : impossible d'accéder au microphone.");
            }
        });

        stopBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') mediaRecorder.stop();
        });
    </script>

    {{-- ══ PWA Script ══ --}}
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

        /* ── Chrome / Edge / Android : prompt natif disponible ── */
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            fab.classList.add('visible');
            feather.replace();
        });

        /* ── iOS Safari : pas de beforeinstallprompt, on affiche quand même ── */
        if (isIOS) {
            fab.classList.add('visible');
            feather.replace();
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

        /* ── Construction des étapes numérotées ── */
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

        /* ── Fermeture de la modal ── */
        closeBtn.addEventListener('click', () => overlay.classList.remove('open'));
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.classList.remove('open');
        });

    })();
    </script>

</body>
</html>
