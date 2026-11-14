<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion administrateur — CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
    <style>
        .auth { min-height: 100vh; display: grid; grid-template-columns: 1fr 1fr; }
        .auth-side { background: var(--ds-brand); color: #fff; padding: 40px; display: flex; flex-direction: column; justify-content: space-between; position: relative; }
        .auth-side::before { content: ''; position: absolute; inset: 0 0 auto 0; height: 4px; background: linear-gradient(90deg, var(--ds-flag-g) 0 33.3%, var(--ds-flag-y) 33.3% 66.6%, var(--ds-flag-r) 66.6% 100%); }
        .auth-logo { display: flex; align-items: center; gap: 10px; color: #fff; text-decoration: none; font: 700 18px 'Sora', sans-serif; }
        .auth-logo img { width: 38px; height: 38px; border-radius: 9px; object-fit: cover; }
        .auth-logo em { font-style: normal; color: var(--ds-accent); }
        .auth-side h2 { font: 700 28px/1.25 'Sora', sans-serif; margin: 0 0 12px; }
        .auth-side p { color: rgba(255,255,255,.72); max-width: 420px; margin: 0; }
        .auth-side small { color: rgba(255,255,255,.45); }
        .auth-main { display: flex; align-items: center; justify-content: center; padding: 32px 20px; background: var(--ds-bg); }
        .auth-box { width: 100%; max-width: 400px; }
        .auth-box h1 { font: 700 24px 'Sora', sans-serif; margin: 0 0 4px; }
        .auth-box .ds-muted { margin: 0 0 24px; }
        .auth-back { display: inline-block; margin-top: 18px; font-size: 13px; color: var(--ds-text-mute); text-decoration: none; }
        .auth-back:hover { color: var(--ds-text); }
        @media (max-width: 800px) { .auth { grid-template-columns: 1fr; } .auth-side { display: none; } }
    </style>
</head>
<body class="ds-body">
<div class="auth">
    <aside class="auth-side">
        <a class="auth-logo" href="{{ route('admin.accueil') }}"><img src="{{ asset('medias/Clogo.jpg') }}" alt="">Congo<em>Assist</em></a>
        <div>
            <h2>Pilotez la plateforme d'urgence nationale.</h2>
            <p>Gérez les comptes citoyens, les services d'urgence, les actualités et la messagerie depuis un seul espace sécurisé.</p>
        </div>
        <small>© {{ date('Y') }} CongoAssist — Accès réservé aux administrateurs</small>
    </aside>

    <main class="auth-main">
        <div class="auth-box">
            <h1>Connexion administrateur</h1>
            <p class="ds-muted">Saisissez vos identifiants pour accéder au panneau d'administration.</p>

            @if(session('error'))   <div class="ds-alert ds-alert-error">{{ session('error') }}</div>@endif
            @if(session('success')) <div class="ds-alert ds-alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())
                <div class="ds-alert ds-alert-error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.login.process') }}" method="POST" novalidate>
                @csrf
                <div class="ds-field">
                    <label class="ds-label" for="email">Adresse email</label>
                    <input class="ds-input" type="email" id="email" name="email" value="{{ old('email') }}"
                           placeholder="admin@exemple.com" autocomplete="username" autofocus required>
                </div>
                <div class="ds-field">
                    <label class="ds-label" for="mot_passe">Mot de passe</label>
                    <input class="ds-input" type="password" id="mot_passe" name="mot_passe"
                           placeholder="Votre mot de passe" autocomplete="current-password" required>
                </div>
                <button type="submit" class="ds-btn ds-btn-primary" style="width:100%;min-height:44px">Se connecter</button>
            </form>
            <a class="auth-back" href="{{ route('admin.accueil') }}">← Retour à l'accueil</a>
        </div>
    </main>
</div>
</body>
</html>
