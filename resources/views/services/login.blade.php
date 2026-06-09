<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand-dark:   #0B1E3D;
            --brand-mid:    #1A3E6E;
            --accent:       #1DB87A;
            --accent-dark:  #15A066;
            --text-primary: #0F1923;
            --text-muted:   #64748B;
            --border:       #E2E8F0;
            --surface:      #F8FAFC;
            --error:        #E53E3E;
            --white:        #FFFFFF;
        }

        html, body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            background: var(--brand-dark);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px; left: -120px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(29,184,122,0.14) 0%, transparent 65%);
            pointer-events: none;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; right: -80px;
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(26,62,110,0.7) 0%, transparent 65%);
            pointer-events: none;
        }

        .panel-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            position: relative;
            z-index: 1;
        }
        .logo-mark {
            width: 38px; height: 38px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-mark span {
            font-family: 'Sora', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--accent);
        }
        .logo-text {
            font-family: 'Sora', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--white);
        }
        .logo-text em { font-style: normal; color: var(--accent); }

        .panel-content {
            position: relative;
            z-index: 1;
        }
        .panel-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(29,184,122,0.12);
            border: 1px solid rgba(29,184,122,0.25);
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 100px;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .panel-tag::before {
            content: '';
            width: 6px; height: 6px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.5); }
        }
        .panel-content h2 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(1.6rem, 2.5vw, 2.2rem);
            font-weight: 700;
            color: var(--white);
            line-height: 1.25;
            margin-bottom: 1rem;
        }
        .panel-content p {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.75;
            max-width: 340px;
        }

        .service-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 2rem;
        }
        .service-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            width: fit-content;
        }
        .service-pill-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .service-pill span { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 500; }

        .panel-footer {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.25);
            position: relative;
            z-index: 1;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            min-height: 100vh;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
        }

        .form-header {
            margin-bottom: 2.25rem;
        }
        .form-header h1 {
            font-family: 'Sora', sans-serif;
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.4rem;
        }
        .form-header p {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Alert error */
        .alert-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-error svg { width: 16px; height: 16px; stroke: var(--error); stroke-width: 2; fill: none; flex-shrink: 0; margin-top: 1px; }
        .alert-error p { font-size: 0.82rem; color: #B91C1C; line-height: 1.5; }

        /* Form elements */
        .field { margin-bottom: 1.25rem; }
        .field label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.45rem;
        }
        .input-wrap { position: relative; }
        .input-wrap svg.input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            stroke: #94A3B8;
            stroke-width: 2;
            fill: none;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            height: 44px;
            padding: 0 2.5rem 0 2.5rem;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background: var(--white);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-wrap input::placeholder { color: #CBD5E1; }
        .input-wrap input:focus {
            border-color: var(--brand-dark);
            box-shadow: 0 0 0 3px rgba(11,30,61,0.07);
        }
        .input-wrap input.has-error { border-color: var(--error); }
        .input-wrap input.has-error:focus { box-shadow: 0 0 0 3px rgba(229,62,62,0.08); }

        .btn-toggle-pw {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px;
            color: #94A3B8;
            transition: color 0.2s;
        }
        .btn-toggle-pw:hover { color: var(--text-primary); }
        .btn-toggle-pw svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 2; fill: none; display: block; }

        .field-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.82rem;
            color: var(--text-muted);
            cursor: pointer;
        }
        .checkbox-label input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: var(--brand-dark);
            cursor: pointer;
        }
        .forgot-link {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--brand-dark);
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--accent-dark); }

        .btn-submit {
            width: 100%;
            height: 46px;
            background: var(--brand-dark);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 0.925rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-submit:hover { background: var(--brand-mid); }
        .btn-submit:active { transform: scale(0.98); }
        .btn-submit svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 2.5; fill: none; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1.5rem 0;
        }
        .divider hr { flex: 1; border: none; border-top: 1px solid var(--border); }
        .divider span { font-size: 0.78rem; color: #CBD5E1; white-space: nowrap; }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--text-primary); }
        .back-link svg { width: 14px; height: 14px; stroke: currentColor; stroke-width: 2.5; fill: none; }

        .info-note {
            margin-top: 2rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 3px solid var(--brand-dark);
            border-radius: 0 8px 8px 0;
            padding: 0.75rem 1rem;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .info-note svg { width: 15px; height: 15px; stroke: var(--brand-dark); stroke-width: 2; fill: none; flex-shrink: 0; margin-top: 1px; }
        .info-note p { font-size: 0.8rem; color: var(--text-muted); line-height: 1.6; }
        .info-note strong { color: var(--text-primary); font-weight: 600; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 2.5rem 1.5rem; justify-content: flex-start; padding-top: 3rem; }

            /* Show a mini logo on mobile */
            .right-panel::before {
                content: 'CongoAssist';
                display: block;
                font-family: 'Sora', sans-serif;
                font-size: 1.1rem;
                font-weight: 700;
                color: var(--brand-dark);
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>

<!-- ── LEFT PANEL ── -->
<div class="left-panel">
    <a href="{{ route('services.accueil') }}" class="panel-logo">
        <div class="logo-mark"><span>C</span></div>
        <span class="logo-text">Congo<em>Assist</em></span>
    </a>

    <div class="panel-content">
        <div class="panel-tag">Espace administrateur</div>
        <h2>Gérez les urgences depuis un seul tableau de bord.</h2>
        <p>Accédez en temps réel aux signalements, coordonnez les interventions et suivez l'état des services d'urgence.</p>

        <div class="service-list">
            <div class="service-pill">
                <div class="service-pill-dot" style="background:#E83A3A;"></div>
                <span>Urgences médicales</span>
            </div>
            <div class="service-pill">
                <div class="service-pill-dot" style="background:#3B82F6;"></div>
                <span>Sécurité publique</span>
            </div>
            <div class="service-pill">
                <div class="service-pill-dot" style="background:#F5A623;"></div>
                <span>Pannes électriques & urbaines</span>
            </div>
            <div class="service-pill">
                <div class="service-pill-dot" style="background:#7C3AED;"></div>
                <span>Campagnes de vaccination</span>
            </div>
        </div>
    </div>

    <div class="panel-footer">© 2025 CongoAssist. Tous droits réservés.</div>
</div>


<!-- ── RIGHT PANEL ── -->
<div class="right-panel">
    <div class="form-box">

        <div class="form-header">
            <h1>Connexion</h1>
            <p>Entrez vos identifiants pour accéder à votre espace.</p>
        </div>

        {{-- Error messages --}}
        @if ($errors->any())
        <div class="alert-error">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </p>
        </div>
        @endif

        <form action="{{ route('services.login.process') }}" method="post">
            @csrf

            <!-- Email -->
            <div class="field">
                <label for="email">Adresse email</label>
                <div class="input-wrap">
                    <svg class="input-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="exemple@congoassist.com"
                        autocomplete="email"
                        required
                        class="{{ $errors->has('email') ? 'has-error' : '' }}">
                </div>
            </div>

            <!-- Password -->
            <div class="field">
                <label for="password">Mot de passe</label>
                <div class="input-wrap">
                    <svg class="input-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                        class="{{ $errors->has('password') ? 'has-error' : '' }}">
                    <button type="button" class="btn-toggle-pw" id="toggle-pw" aria-label="Afficher le mot de passe">
                        <svg id="pw-icon-show" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg id="pw-icon-hide" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <!-- Remember + Forgot -->
            <div class="field-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    Se souvenir de moi
                </label>
                <a href="#" class="forgot-link">Mot de passe oublié ?</a>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit">
                Se connecter
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </form>

        <div class="divider">
            <hr>
            <span>ou</span>
            <hr>
        </div>

        <a href="{{ route('services.accueil') }}" class="back-link">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Retour à l'accueil
        </a>

        <div class="info-note">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <p><strong>Accès restreint.</strong> Cet espace est réservé au personnel autorisé des services d'urgence. Toute tentative non autorisée est enregistrée.</p>
        </div>

    </div>
</div>

<script>
    // Toggle password visibility
    const toggleBtn = document.getElementById('toggle-pw');
    const pwInput = document.getElementById('password');
    const iconShow = document.getElementById('pw-icon-show');
    const iconHide = document.getElementById('pw-icon-hide');

    toggleBtn.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type = isHidden ? 'text' : 'password';
        iconShow.style.display = isHidden ? 'none' : 'block';
        iconHide.style.display = isHidden ? 'block' : 'none';
    });

    // Autofocus
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('email').focus();
    });
</script>
</body>
</html>
