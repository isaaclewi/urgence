<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé — CongoAssist</title>
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
</head>
<body class="ds-body" style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;">
    <div class="ds-card" style="max-width:460px;text-align:center;padding:32px;">
        <div style="font-size:42px;line-height:1;">🔒</div>
        <h1 style="margin:12px 0 8px;font-size:1.4rem;">Accès refusé</h1>
        <p style="color:#64748b;margin:0 0 20px;">
            {{ $exception->getMessage() ?: "Vous n'avez pas l'autorisation d'accéder à cette page." }}
        </p>
        <a href="javascript:history.back()" class="ds-btn">Retour</a>
    </div>
</body>
</html>
