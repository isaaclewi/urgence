<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications — CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Inter,system-ui,sans-serif;background:#F1F5F9;color:#0F1923;padding:1rem}
        .wrap{max-width:760px;margin:0 auto}
        header{display:flex;justify-content:space-between;align-items:center;gap:.5rem;flex-wrap:wrap;margin:.5rem 0 1rem}
        h1{font-size:1.4rem}
        .btn{display:inline-block;padding:.5rem .9rem;border-radius:9px;border:1px solid #CBD5E1;background:#fff;color:#0F1923;font-size:.85rem;font-weight:600;text-decoration:none;cursor:pointer}
        .btn.p{background:#1DB87A;border-color:#1DB87A;color:#fff}
        .outils{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem}
        .ok{background:#DCFCE7;color:#166534;border-radius:10px;padding:.7rem .9rem;font-size:.9rem;margin-bottom:.8rem}
        a.n{display:block;background:#fff;border-radius:12px;border-left:5px solid #1A3E6E;padding:.8rem 1rem;margin-bottom:.6rem;text-decoration:none;color:inherit}
        a.n.important{border-left-color:#F59E0B}
        a.n.urgent{border-left-color:#E53E3E}
        a.n.lue{opacity:.62}
        a.n strong{display:block;font-size:.95rem}
        a.n span{display:block;font-size:.88rem;color:#475569;margin-top:2px}
        a.n small{display:block;font-size:.75rem;color:#94A3B8;margin-top:4px}
        .vide{text-align:center;color:#64748B;padding:2rem}
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <h1>Notifications @if($nonLues)<small style="font-size:.9rem;color:#E53E3E">({{ $nonLues }} non lue{{ $nonLues > 1 ? 's' : '' }})</small>@endif</h1>
        <a class="btn" href="{{ $retour }}">← Retour</a>
    </header>

    @if(session('success'))<div class="ok">{{ session('success') }}</div>@endif

    <div class="outils">
        @if($nonLues)
            <form method="POST" action="{{ route('notifications.lues') }}">@csrf<button class="btn p" type="submit">Tout marquer comme lu</button></form>
        @endif
        <button class="btn" type="button" onclick="window.nwSonnerie && window.nwSonnerie('important')">🔔 Tester la sonnerie</button>
        <button class="btn" type="button" id="btn-notif-nav">Activer les alertes du navigateur</button>
    </div>

    @forelse($notifications as $n)
        <a class="n {{ $n->niveau }} {{ $n->lu_at ? 'lue' : '' }}" href="{{ route('notifications.ouvrir', $n->id) }}">
            <strong>{{ $n->titre }}</strong>
            <span>{{ $n->message }}</span>
            <small>{{ $n->created_at->format('d/m/Y H:i') }}{{ $n->lu_at ? '' : ' · nouvelle' }}</small>
        </a>
    @empty
        <div class="vide">Aucune notification pour le moment.</div>
    @endforelse
</div>

@include('partials.notif-widget')
<script>
    const b = document.getElementById('btn-notif-nav');
    if (!('Notification' in window)) { b.style.display = 'none'; }
    else if (Notification.permission === 'granted') { b.textContent = 'Alertes du navigateur activées ✓'; }
    b.addEventListener('click', () => Notification.requestPermission().then(p => { if (p === 'granted') b.textContent = 'Alertes du navigateur activées ✓'; }));
</script>
</body>
</html>
