{{-- Cloche de notifications + sonnerie, commune aux espaces citoyen, service, équipe et agent. --}}
@php $nwIdentites = \App\Support\Notifier::identites(); @endphp
@if($nwIdentites)
<style>
    #nw-bell{position:fixed;right:18px;bottom:18px;z-index:9998;width:52px;height:52px;border-radius:50%;background:#0B1E3D;color:#fff;border:0;cursor:pointer;box-shadow:0 6px 20px rgba(0,0,0,.28);display:flex;align-items:center;justify-content:center;text-decoration:none}
    #nw-bell:hover{background:#1A3E6E}
    #nw-bell svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    #nw-bell.nw-sonne{animation:nw-secoue .6s ease 3}
    #nw-badge{position:absolute;top:-4px;right:-4px;min-width:20px;height:20px;padding:0 5px;border-radius:10px;background:#E53E3E;color:#fff;font:700 11px/20px Inter,system-ui,sans-serif;text-align:center;display:none}
    #nw-toasts{position:fixed;top:14px;right:14px;z-index:9999;display:flex;flex-direction:column;gap:10px;width:min(360px,calc(100vw - 28px))}
    .nw-toast{background:#fff;border-left:5px solid #1A3E6E;border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,.22);padding:12px 14px;cursor:pointer;font-family:Inter,system-ui,sans-serif;animation:nw-entre .25s ease}
    .nw-toast.important{border-left-color:#F59E0B}
    .nw-toast.urgent{border-left-color:#E53E3E;background:#FFF5F5}
    .nw-toast strong{display:block;font-size:14px;color:#0F1923;margin-bottom:2px}
    .nw-toast span{font-size:13px;color:#475569;display:block}
    .nw-toast button{float:right;border:0;background:none;font-size:18px;line-height:1;color:#94A3B8;cursor:pointer}
    @keyframes nw-entre{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:none}}
    @keyframes nw-secoue{0%,100%{transform:rotate(0)}25%{transform:rotate(-14deg)}75%{transform:rotate(14deg)}}
</style>

<a id="nw-bell" href="{{ route('notifications.index') }}" aria-label="Notifications" title="Notifications">
    <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>
    <span id="nw-badge">0</span>
</a>
<div id="nw-toasts" aria-live="polite"></div>

<script>
(function () {
    const FLUX   = @json(route('notifications.flux'));
    const OUVRIR = @json(url('/notifications'));
    const CLE    = 'nw_dernier_{{ md5(json_encode($nwIdentites)) }}';
    const titreInitial = document.title;

    let dernier = sessionStorage.getItem(CLE);
    dernier = dernier === null ? null : parseInt(dernier, 10);

    /* ── Audio : autorisé par le navigateur après le premier clic/toucher sur la page ── */
    let ctx = null;
    function preparerAudio() {
        try {
            if (!ctx) ctx = new (window.AudioContext || window.webkitAudioContext)();
            if (ctx.state === 'suspended') ctx.resume();
        } catch (e) {}
    }
    ['click', 'keydown', 'touchstart'].forEach(ev => document.addEventListener(ev, preparerAudio, { passive: true }));

    function bip(freq, debut, duree, vol) {
        if (!ctx || ctx.state !== 'running') return;
        const o = ctx.createOscillator(), g = ctx.createGain(), t = ctx.currentTime + debut;
        o.type = 'sine'; o.frequency.value = freq;
        g.gain.setValueAtTime(0, t);
        g.gain.linearRampToValueAtTime(vol, t + 0.02);
        g.gain.linearRampToValueAtTime(0, t + duree);
        o.connect(g); g.connect(ctx.destination);
        o.start(t); o.stop(t + duree + 0.05);
    }

    function sonnerie(niveau) {
        if (niveau === 'urgent') {            // sirène à deux tons, 5 fois
            for (let i = 0; i < 5; i++) { bip(988, i * 0.7, 0.3, 0.28); bip(740, i * 0.7 + 0.33, 0.3, 0.28); }
        } else if (niveau === 'important') {  // trois notes
            bip(880, 0, 0.18, 0.22); bip(880, 0.28, 0.18, 0.22); bip(1175, 0.56, 0.3, 0.22);
        } else {                              // double bip discret
            bip(660, 0, 0.15, 0.16); bip(880, 0.2, 0.2, 0.16);
        }
    }
    window.nwSonnerie = function (niveau) { preparerAudio(); setTimeout(() => sonnerie(niveau), 200); };   // bouton « Tester la sonnerie »

    const rang = n => ({ info: 0, important: 1, urgent: 2 }[n] || 0);
    const muet = n => (window.NOTIF_TYPES_MUETS || []).indexOf(n.categorie) !== -1;

    /* ── Affichage ── */
    function majBadge(n) {
        const b = document.getElementById('nw-badge');
        b.textContent = n > 99 ? '99+' : n;
        b.style.display = n > 0 ? 'block' : 'none';
        document.title = n > 0 ? '(' + n + ') ' + titreInitial : titreInitial;
    }

    function toast(n) {
        const el = document.createElement('div');
        el.className = 'nw-toast ' + n.niveau;
        el.innerHTML = '<button type="button" aria-label="Fermer">×</button><strong></strong><span></span>';
        el.querySelector('strong').textContent = n.titre;
        el.querySelector('span').textContent = n.message;
        el.querySelector('button').addEventListener('click', e => { e.stopPropagation(); el.remove(); });
        el.addEventListener('click', () => { window.location.href = OUVRIR + '/' + n.id + '/ouvrir'; });
        document.getElementById('nw-toasts').appendChild(el);
        setTimeout(() => el.remove(), n.niveau === 'urgent' ? 25000 : 10000);

        if (document.hidden && 'Notification' in window && Notification.permission === 'granted') {
            try { new Notification(n.titre, { body: n.message }); } catch (e) {}
        }
    }

    async function interroger() {
        try {
            const r = await fetch(FLUX + '?apres=' + (dernier === null ? -1 : dernier), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin', cache: 'no-store'
            });
            if (!r.ok) return;
            const d = await r.json();
            majBadge(d.non_lues);

            if (dernier === null) {           // première visite : on ne sonne pas pour l'existant
                dernier = d.dernier_id;
                sessionStorage.setItem(CLE, dernier);
                return;
            }

            if (d.items.length) {
                let niveau = 'info', sonore = false;
                d.items.forEach(n => {
                    toast(n);
                    if (!muet(n)) { sonore = true; if (rang(n.niveau) > rang(niveau)) niveau = n.niveau; }
                });
                if (sonore) {
                    sonnerie(niveau);
                    const c = document.getElementById('nw-bell');
                    c.classList.remove('nw-sonne'); void c.offsetWidth; c.classList.add('nw-sonne');
                }
                dernier = d.items[d.items.length - 1].id;
                sessionStorage.setItem(CLE, dernier);
            }
        } catch (e) {}
    }

    interroger();
    setInterval(interroger, 6000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) interroger(); });
})();
</script>
@endif
