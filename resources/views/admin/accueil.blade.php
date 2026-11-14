<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail d'administration — CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    <style>
        .lp-wrap { max-width: 1120px; margin: 0 auto; padding: 0 20px; }
        .lp-head { background: var(--ds-brand); color: #fff; position: relative; }
        .lp-head::before { content: ''; position: absolute; inset: 0 0 auto 0; height: 4px; background: linear-gradient(90deg, var(--ds-flag-g) 0 33.3%, var(--ds-flag-y) 33.3% 66.6%, var(--ds-flag-r) 66.6% 100%); }
        .lp-nav { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 0; }
        .lp-logo { display: flex; align-items: center; gap: 10px; color: #fff; text-decoration: none; font: 700 18px 'Sora', sans-serif; }
        .lp-logo img { width: 38px; height: 38px; border-radius: 9px; object-fit: cover; }
        .lp-logo em { font-style: normal; color: var(--ds-accent); }
        .lp-hero { padding: 48px 0 64px; max-width: 720px; }
        .lp-hero h1 { font: 700 clamp(26px, 4vw, 38px)/1.2 'Sora', sans-serif; margin: 0 0 14px; }
        .lp-hero p { color: rgba(255,255,255,.75); font-size: 16px; margin: 0 0 24px; }
        .lp-stats { margin-top: -36px; position: relative; }
        .lp-section { padding: 48px 0; }
        .lp-section h2 { font: 700 22px 'Sora', sans-serif; margin: 0 0 6px; }
        .lp-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-top: 20px; }
        .lp-card { background: var(--ds-surface); border: 1px solid var(--ds-border); border-radius: var(--ds-radius); padding: 20px; box-shadow: var(--ds-shadow); }
        .lp-card h3 { font: 700 15px 'Sora', sans-serif; margin: 0 0 6px; }
        .lp-card p { margin: 0; font-size: 13px; color: var(--ds-text-sec); }
        .lp-foot { border-top: 1px solid var(--ds-border); padding: 22px 0; font-size: 13px; color: var(--ds-text-mute); }
        .lp-foot a { color: var(--ds-text-sec); }
    </style>
</head>
<body class="ds-body">
<header class="lp-head">
    <div class="lp-wrap">
        <div class="lp-nav">
            <a class="lp-logo" href="{{ route('admin.accueil') }}"><img src="{{ asset('medias/Clogo.jpg') }}" alt="">Congo<em>Assist</em></a>
            <a class="ds-btn ds-btn-primary" href="{{ route('admin.login') }}">Connexion admin</a>
        </div>
        <div class="lp-hero">
            <h1>Administration et coordination des urgences</h1>
            <p>Centralisez les signalements citoyens, les services d'urgence, les campagnes de vaccination et les communications officielles sur une seule plateforme.</p>
            <a class="ds-btn ds-btn-primary" href="{{ route('admin.login') }}">Accéder au panneau d'administration</a>
        </div>
    </div>
</header>

<main>
    <div class="lp-wrap lp-stats">
        <div class="ds-stats">
            <x-stat label="Alertes reçues" :value="$stats['alertes'] ?? '—'" />
            <x-stat label="En attente" :value="$stats['en_attente'] ?? '—'" type="warning" />
            <x-stat label="Services disponibles" :value="$stats['services'] ?? '—'" type="success" />
        </div>
    </div>

    <section class="lp-wrap lp-section">
        <h2>Ce que vous pouvez gérer</h2>
        <p class="ds-muted" style="margin:0">Un seul espace pour superviser toute la plateforme.</p>
        <div class="lp-cards">
            <div class="lp-card"><h3>Urgences</h3><p>Suivi des signalements en temps réel et orientation vers le bon service.</p></div>
            <div class="lp-card"><h3>Services d'urgence</h3><p>Comptes des pompiers, de la police et des hôpitaux, disponibilité et accès.</p></div>
            <div class="lp-card"><h3>Citoyens</h3><p>Vérification des comptes et des pièces d'identité.</p></div>
            <div class="lp-card"><h3>Santé et vaccination</h3><p>Bilans de santé et programmes de vaccination.</p></div>
            <div class="lp-card"><h3>Actualités</h3><p>Diffusion des informations officielles auprès des citoyens.</p></div>
            <div class="lp-card"><h3>Messagerie</h3><p>Groupes de discussion entre citoyens, services et administration.</p></div>
        </div>
    </section>
</main>

<footer class="lp-foot">
    <div class="lp-wrap" style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <span>© {{ date('Y') }} CongoAssist — Tous droits réservés</span>
        <span>Contact : <a href="mailto:lombaisaac8@gmail.com">lombaisaac8@gmail.com</a> · <a href="tel:+242050520146">+242 05 052 0146</a></span>
    </div>
</footer>
</body>
</html>
