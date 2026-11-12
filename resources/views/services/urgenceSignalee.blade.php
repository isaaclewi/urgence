@extends('layouts.portal')

@section('title', 'Urgences signalées')
@section('page-title', 'Urgences signalées')
@section('page-subtitle', 'Suivez et traitez les alertes en temps réel')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.interventions.journalieres') }}">Interventions</a>
@endsection

@section('content')
<div class="ds-stats" style="margin-bottom:20px">
    <x-stat id="stat-attente"  label="En attente"     :value="$stats['attente']"  type="danger" />
    <x-stat id="stat-encours"  label="Pris en charge" :value="$stats['encours']"  type="info" />
    <x-stat id="stat-terminee" label="Terminées"      :value="$stats['terminee']" type="success" />
    <x-stat id="stat-total"    label="Total"          :value="$stats['total']" />
</div>

<div id="bandeau-nouvelles" class="ds-alert ds-alert-info" style="display:none;margin-bottom:16px">
    Nouvelles urgences reçues. <a href="{{ route('services.urgenceSignalee') }}"><strong>Actualiser la liste</strong></a>
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un titre, un lieu, une description…">
        <select class="ds-select" name="statut">
            <option value="">Tous statuts</option>
            @foreach(['en attente' => 'En attente', 'pris en charge' => 'Pris en charge', 'terminee' => 'Terminées'] as $val => $lab)
                <option value="{{ $val }}" @selected(request('statut') === $val)>{{ $lab }}</option>
            @endforeach
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
        @if(request()->hasAny(['q', 'statut']))
            <a class="ds-btn ds-btn-ghost" href="{{ route('services.urgenceSignalee') }}">Réinitialiser</a>
        @endif
    </form>

    <x-table id="alertesTable">
        <thead>
            <tr>
                <th>Alerte</th>
                <th>Urgence</th>
                <th>Type</th>
                <th>Citoyen</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="alertesTbody">
            @foreach($alertes as $alerte)
                @include('services.partials.urgence-row', ['alerte' => $alerte])
            @endforeach
        </tbody>
    </x-table>

    <div id="vide" @if($alertes->isNotEmpty()) style="display:none" @endif>
        <x-empty title="Aucune urgence">
            {{ request()->hasAny(['q', 'statut']) ? 'Aucun résultat pour ces filtres.' : 'Les nouvelles urgences apparaîtront ici automatiquement.' }}
        </x-empty>
    </div>
</x-card>

<div style="margin-top:16px">
    @include('layouts.partials.pager', ['paginator' => $alertes])
</div>

{{-- Détail d'une urgence --}}
<dialog id="dlg-detail">
    <div class="dlg-head"><strong id="d-titre"></strong><button type="button" class="ds-btn ds-btn-ghost ds-btn-sm" data-close>✕</button></div>
    <div class="dlg-body">
        <p class="ds-muted" id="d-meta" style="margin:0 0 10px"></p>
        <p id="d-desc" style="white-space:pre-line;margin:0 0 12px"></p>
        <p id="d-lieu" style="margin:0 0 12px"></p>
        <img id="d-photo" alt="Photo jointe à l'alerte" style="display:none;max-width:100%;border-radius:8px;margin-bottom:12px">
        <audio id="d-vocal" controls style="display:none;width:100%;margin-bottom:12px"></audio>
        <div id="d-citoyen" style="border-top:1px solid var(--ds-border);padding-top:12px"></div>
    </div>
</dialog>

{{-- Affectation --}}
<dialog id="dlg-affecter">
    <form method="POST" id="form-affecter">
        @csrf
        <div class="dlg-head"><strong>Affecter une équipe</strong><button type="button" class="ds-btn ds-btn-ghost ds-btn-sm" data-close>✕</button></div>
        <div class="dlg-body">
            @if($equipes->isEmpty())
                <p class="ds-muted" style="margin:0">Aucune équipe active. Créez ou activez une équipe depuis « Équipes ».</p>
            @else
                <div class="ds-field">
                    <label class="ds-label" for="equipe-select">Équipe</label>
                    <select class="ds-select" id="equipe-select" name="service_destination_id" required>
                        @foreach($equipes as $e)<option value="{{ $e->id }}">{{ $e->nom }}</option>@endforeach
                    </select>
                </div>
                <div class="ds-field">
                    <label class="ds-label" for="commentaire">Consigne pour l'équipe (facultatif)</label>
                    <textarea class="ds-textarea" id="commentaire" name="commentaire" rows="3" maxlength="1000"></textarea>
                </div>
                <button class="ds-btn ds-btn-primary" type="submit">Affecter</button>
            @endif
        </div>
    </form>
</dialog>
@endsection

@push('styles')
<style>
    dialog { border: 1px solid var(--ds-border); border-radius: 12px; padding: 0; width: min(560px, 92vw); background: var(--ds-surface, #fff); color: inherit; }
    dialog::backdrop { background: rgba(15, 23, 42, .5); }
    .dlg-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid var(--ds-border); }
    .dlg-body { padding: 18px; max-height: 70vh; overflow: auto; }
    #toast-urgence { position: fixed; right: 16px; bottom: 16px; z-index: 50; background: #b91c1c; color: #fff; padding: 12px 16px; border-radius: 10px; box-shadow: 0 6px 24px rgba(0,0,0,.25); display: none; max-width: 320px; }
</style>
@endpush

@push('scripts')
<div id="toast-urgence" role="status"></div>
<script>
(function () {
    const tbody   = document.getElementById('alertesTbody');
    const LIVE    = @json($live);
    const STREAM  = @json(route('services.urgences.stream'));
    const AFFECT  = @json(route('services.urgence.affecter', '__ID__'));
    let lastId    = {{ (int) $lastId }};
    let busy      = false;

    const esc = (t) => { const d = document.createElement('div'); d.textContent = t ?? ''; return d.innerHTML; };

    /* ── Fenêtres ── */
    document.querySelectorAll('dialog [data-close]').forEach(b => b.addEventListener('click', () => b.closest('dialog').close()));

    tbody.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        if (btn.dataset.action === 'affecter') {
            document.getElementById('form-affecter').action = AFFECT.replace('__ID__', btn.dataset.id);
            document.getElementById('dlg-affecter').showModal();
            return;
        }

        const d = JSON.parse(btn.dataset.detail);
        document.getElementById('d-titre').textContent = d.titre || 'Urgence';
        document.getElementById('d-meta').textContent  = [d.type, d.date, d.equipe ? 'Équipe : ' + d.equipe : null].filter(Boolean).join(' · ');
        document.getElementById('d-desc').textContent  = d.description || 'Aucune description.';

        const lieu = document.getElementById('d-lieu');
        lieu.innerHTML = '📍 ' + esc(d.lieu || 'Lieu non précisé');
        if (d.lat && d.lng) {
            const a = document.createElement('a');
            a.href = 'https://www.openstreetmap.org/?mlat=' + encodeURIComponent(d.lat) + '&mlon=' + encodeURIComponent(d.lng) + '#map=17/' + d.lat + '/' + d.lng;
            a.target = '_blank'; a.rel = 'noopener'; a.textContent = ' Voir sur la carte';
            lieu.appendChild(a);
        }

        const photo = document.getElementById('d-photo'); photo.style.display = d.photo ? 'block' : 'none'; photo.src = d.photo || '';
        const vocal = document.getElementById('d-vocal'); vocal.style.display = d.vocal ? 'block' : 'none'; vocal.src = d.vocal || '';

        const c = d.citoyen, box = document.getElementById('d-citoyen');
        box.innerHTML = c
            ? '<strong>' + esc(c.nom) + '</strong><br>'
              + (c.telephone ? '📞 <a href="tel:' + esc(c.telephone) + '">' + esc(c.telephone) + '</a><br>' : '')
              + (c.email ? '✉️ ' + esc(c.email) + '<br>' : '')
              + (c.adresse ? '🏠 ' + esc(c.adresse) : '')
            : '<span class="ds-muted">Alerte envoyée sans compte citoyen (anonyme).</span>';

        document.getElementById('dlg-detail').showModal();
    });

    /* ── Changement de statut (confirmation avant clôture) ── */
    tbody.addEventListener('change', (e) => {
        const select = e.target.closest('[data-statut-form] select');
        if (!select) return;
        if (select.value === 'terminee' && !confirm('Marquer cette urgence comme terminée ? Elle ne pourra plus être rouverte.')) {
            select.form.reset();
            return;
        }
        select.form.submit();
    });

    /* ── Temps réel (interrogation toutes les 5 s, aucune connexion longue) ── */
    function bip() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const o = ctx.createOscillator(), g = ctx.createGain();
            o.connect(g); g.connect(ctx.destination); o.frequency.value = 880; g.gain.value = .08;
            o.start(); o.stop(ctx.currentTime + .25);
        } catch (e) {}
    }

    function toast(texte) {
        const t = document.getElementById('toast-urgence');
        t.textContent = '🚨 ' + texte; t.style.display = 'block';
        setTimeout(() => { t.style.display = 'none'; }, 6000);
    }

    function majStats(s) {
        const set = (id, v) => { const el = document.querySelector('#' + id + ' .ds-stat-value'); if (el) el.textContent = v; };
        set('stat-attente', s.attente); set('stat-encours', s.encours); set('stat-terminee', s.terminee); set('stat-total', s.total);
    }

    async function interroger() {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const r = await fetch(STREAM + '?lastId=' + lastId, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', cache: 'no-store' });
            if (r.ok) {
                const data = await r.json();
                majStats(data.stats);
                const items = data.items || [];
                items.forEach((a) => {
                    lastId = Math.max(lastId, a.id);
                    if (LIVE && !document.getElementById('alerte-row-' + a.id)) {
                        tbody.insertAdjacentHTML('afterbegin', a.html);
                        document.getElementById('vide').style.display = 'none';
                    }
                });
                if (items.length) {
                    bip();
                    toast(items[items.length - 1].titre + (items[items.length - 1].localisation ? ' — ' + items[items.length - 1].localisation : ''));
                    if (!LIVE) document.getElementById('bandeau-nouvelles').style.display = 'block';
                }
            }
        } catch (e) { /* réseau coupé : nouvel essai au prochain cycle */ }
        busy = false;
    }

    setInterval(interroger, 5000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) interroger(); });
})();
</script>
@endpush
