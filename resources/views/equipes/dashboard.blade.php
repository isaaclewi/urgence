@extends('equipes.master')

@section('title', ($equipe->nom ?? 'Équipe') . ' — Dashboard')
@section('page-icon', 'home')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de vos missions en cours')

@section('content')

@push('styles')
<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
@media (max-width: 1023px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 479px)  { .stats-grid { grid-template-columns: 1fr; } }

.icon-orange { background: #FFF7ED; color: #C2410C; }
.icon-yellow { background: #FEF3C7; color: #92400E; }
.icon-green  { background: #D1FAE5; color: #065F46; }
.icon-blue   { background: #DBEAFE; color: #1E40AF; }

/* Carte mission active */
.mission-card {
    background: linear-gradient(135deg, var(--brand) 0%, #1A3E6E 100%);
    border-radius: var(--radius);
    padding: 24px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    position: relative;
}
.mission-card::before {
    content: '';
    position: absolute; right: -60px; top: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(249,115,22,.2) 0%, transparent 65%);
    pointer-events: none;
}
.mission-info h2 {
    font-family: 'Sora', sans-serif;
    font-size: 20px; font-weight: 700; margin-bottom: 4px;
}
.mission-info p { font-size: 13px; color: rgba(255,255,255,.6); }
.mission-badge {
    background: var(--accent); color: #fff;
    font-size: 11px; font-weight: 700;
    padding: 4px 12px; border-radius: 100px;
    text-transform: uppercase; letter-spacing: .05em;
    margin-top: 10px; display: inline-block;
    transition: all .3s;
}
.mission-icon {
    width: 64px; height: 64px;
    background: rgba(249,115,22,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.mission-icon svg { stroke: var(--accent); width: 28px; height: 28px; }

/* Alerte récente */
.recent-alerte {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    transition: background .12s;
}
.recent-alerte:last-child { border-bottom: none; }
.recent-alerte:hover { background: var(--surface2); }
.ra-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    margin-top: 4px; flex-shrink: 0;
}
.ra-title { font-size: 13px; font-weight: 600; color: var(--text); }
.ra-meta  { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

/* Map */
#dashMap { height: 340px; border-radius: 0 0 var(--radius) var(--radius); }

/* Toast nouvelle affectation */
.toast-affectation {
    position: fixed;
    top: 20px; right: 20px;
    z-index: 999;
    background: #EF4444;
    color: #fff;
    padding: 16px 20px;
    border-radius: 14px;
    box-shadow: 0 8px 40px rgba(239,68,68,.4);
    display: flex; align-items: center; gap: 12px;
    font-size: 13.5px; font-weight: 600;
    transform: translateX(120%);
    transition: transform .4s cubic-bezier(.34,1.56,.64,1);
    cursor: pointer;
    max-width: 360px;
}
.toast-affectation.show { transform: translateX(0); }
.toast-aff-icon {
    width: 38px; height: 38px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* Ligne nouvelle affectation */
@keyframes newAffHighlight {
    0%   { background: rgba(239,68,68,.15); }
    100% { background: transparent; }
}
.ra-new { animation: newAffHighlight 3s ease forwards; }

/* Indicateur live */
.live-indicator {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 11px; font-weight: 700; color: #10B981;
    background: rgba(16,185,129,.1);
    padding: 4px 10px; border-radius: 100px; margin-left: 8px;
}
.live-dot {
    width: 7px; height: 7px; border-radius: 50%; background: #10B981;
    animation: livePulse 1.4s infinite;
}
@keyframes livePulse {
    0%,100% { opacity: 1; transform: scale(1); }
    50%      { opacity: .4; transform: scale(1.6); }
}

@keyframes mapPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.4); opacity: 0.6; }
}
</style>
@endpush

{{-- Toast nouvelle affectation --}}
<div id="toastAff" class="toast-affectation" onclick="goToFirstAff()">
    <div class="toast-aff-icon"><i data-feather="alert-triangle" style="width:18px;height:18px;"></i></div>
    <div>
        <div id="toastAffTitle">Nouvelle mission affectée !</div>
        <div id="toastAffSub" style="font-size:11px;opacity:.8;margin-top:2px;"></div>
    </div>
</div>

{{-- ── Mission banner ── --}}
<div class="mission-card anim-fade">
    <div class="mission-info">
        <h2>{{ $equipe->nom }}</h2>
        <p>
            @if($equipe->parentService ?? false)
                Rattachée à · {{ $equipe->parentService->nom }}
            @elseif(session('equipe_parent_id'))
                @php $parent = \App\Models\Services::find(session('equipe_parent_id')); @endphp
                Rattachée à · {{ $parent->nom ?? '—' }}
            @else
                Équipe de terrain autonome
            @endif
        </p>
        <span class="mission-badge" id="missionBadge">
            {{ $stats['en_cours'] > 0 ? $stats['en_cours'] . ' mission(s) en cours' : 'Aucune mission active' }}
        </span>
    </div>
    <div class="mission-icon">
        <i data-feather="shield"></i>
    </div>
</div>

{{-- ── Stats ── --}}
<div class="stats-grid anim-fade">
    <div class="stat-card">
        <div class="sc-icon icon-blue"><i data-feather="list"></i></div>
        <div class="sc-value" id="st-total">{{ $stats['total'] }}</div>
        <div class="sc-label">Total affectées</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon icon-orange"><i data-feather="clock"></i></div>
        <div class="sc-value" id="st-transmise">{{ $stats['transmise'] }}</div>
        <div class="sc-label">En attente</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon icon-yellow"><i data-feather="activity"></i></div>
        <div class="sc-value" id="st-encours">{{ $stats['en_cours'] }}</div>
        <div class="sc-label">En cours</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon icon-green"><i data-feather="check-circle"></i></div>
        <div class="sc-value" id="st-terminee">{{ $stats['terminee'] }}</div>
        <div class="sc-label">Terminées</div>
    </div>
</div>

{{-- ── Layout 2 colonnes ── --}}
<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">

    {{-- Alertes récentes --}}
    <div class="content-card anim-slide">
        <div class="cc-header">
            <div>
                <div class="cc-title" style="display:flex;align-items:center;flex-wrap:wrap;gap:6px;">
                    <i data-feather="bell"></i> Dernières alertes affectées
                    <span class="live-indicator">
                        <span class="live-dot"></span>
                        LIVE
                    </span>
                </div>
                <div class="cc-subtitle" id="affCount">{{ $affectations->count() }} au total</div>
            </div>
            <a href="{{ route('equipe.alertes') }}" class="btn btn-outline btn-sm">Voir tout</a>
        </div>

        <div id="affList">
        @forelse($affectations->take(5) as $aff)
        <div class="recent-alerte" id="aff-row-{{ $aff->id }}">
            <div class="ra-dot" style="background:
                {{ $aff->statut === 'terminee' ? '#10B981' : ($aff->statut === 'en_cours' ? '#F59E0B' : '#EF4444') }};"></div>
            <div style="flex:1;">
                <div class="ra-title">{{ $aff->alerte->titre ?? 'Alerte #'.$aff->alerte_id }}</div>
                <div class="ra-meta">
                    {{ $aff->alerte->localisation ?? '—' }} ·
                    {{ \Carbon\Carbon::parse($aff->created_at)->diffForHumans() }}
                </div>
            </div>
            <a href="{{ route('equipe.alerte.detail', $aff->id) }}" class="btn btn-outline btn-sm">
                <i data-feather="map-pin"></i>
            </a>
        </div>
        @empty
        <div class="empty-state" id="affEmpty">
            <div class="empty-icon"><i data-feather="inbox"></i></div>
            <p>Aucune alerte affectée</p>
            <span>Les nouvelles missions apparaîtront ici automatiquement</span>
        </div>
        @endforelse
        </div>
    </div>

    {{-- Carte ── --}}
    <div class="content-card anim-slide" style="overflow:hidden;">
        <div class="cc-header">
            <div>
                <div class="cc-title"><i data-feather="map"></i> Carte de mission</div>
                <div class="cc-subtitle" id="mapSubDash">
                    @if($derniereAlerte)
                        Dernière urgence : {{ $derniereAlerte->alerte->titre }}
                    @else
                        Aucune position disponible
                    @endif
                </div>
            </div>
        </div>
        <div id="dashMap"></div>
    </div>

</div>

{{-- Bouton Google Maps --}}
<div id="gmapsBtnContainer" style="display:none; margin-bottom:20px;">
    <a id="gmapsBtn" href="#" target="_blank" class="btn btn-accent">
        <i data-feather="navigation"></i>
        Ouvrir l'itinéraire dans Google Maps
    </a>
</div>

@push('scripts')
<script>
feather.replace({ width: 14, height: 14 });

// ── Compteurs ──────────────────────────────────────────────
let stats = {
    total:    parseInt(document.getElementById('st-total').textContent)    || 0,
    transmise:parseInt(document.getElementById('st-transmise').textContent)|| 0,
    encours:  parseInt(document.getElementById('st-encours').textContent)  || 0,
    terminee: parseInt(document.getElementById('st-terminee').textContent) || 0,
};

// ID de la dernière affectation chargée
let lastAffId = {{ $affectations->isNotEmpty() ? $affectations->max('id') : 0 }};

// ── Compteur max des 5 affichées dans la liste ─────────────
let affCount = {{ $affectations->count() }};

// ════════════════════════════════════════════════════════════
//  SSE — écouter les nouvelles affectations
// ════════════════════════════════════════════════════════════
function startAffStream() {
    const url = `{{ route('equipe.affectations.stream') }}?lastId=${lastAffId}`;
    const es  = new EventSource(url);

    es.onmessage = function(e) {
        const aff = JSON.parse(e.data);

        // Supprimer l'état vide si présent
        const empty = document.getElementById('affEmpty');
        if (empty) empty.remove();

        // Construire et insérer la ligne
        const html = buildAffRow(aff);
        const list = document.getElementById('affList');
        list.insertAdjacentHTML('afterbegin', html);

        // Limiter la liste à 5
        const rows = list.querySelectorAll('.recent-alerte');
        if (rows.length > 5) rows[rows.length - 1].remove();

        feather.replace({ width: 14, height: 14 });

        // Mettre à jour lastId
        lastAffId = Math.max(lastAffId, aff.id);

        // Stats
        stats.total++;
        stats.transmise++;
        updateStats();

        // Mettre à jour le marqueur carte si coordonnées disponibles
        if (aff.alerte?.latitude && aff.alerte?.longitude) {
            updateMapMarker(aff.alerte);
            document.getElementById('mapSubDash').textContent =
                'Dernière urgence : ' + (aff.alerte.titre || '');
        } else if (aff.alerte?.localisation) {
            // Géocodage Nominatim si pas de coordonnées
            geocodeAndUpdateMap(aff.alerte);
        }

        // Alerte sonore
        playUrgenceBeep();

        // Toast
        showAffToast(aff.alerte?.titre || 'Nouvelle mission', aff.alerte?.localisation || '');
    };

    es.onerror = function() {
        es.close();
        setTimeout(startAffStream, 5000);
    };
}

function updateStats() {
    document.getElementById('st-total').textContent    = stats.total;
    document.getElementById('st-transmise').textContent= stats.transmise;
    document.getElementById('st-encours').textContent  = stats.encours;
    document.getElementById('st-terminee').textContent = stats.terminee;
    document.getElementById('affCount').textContent    = stats.total + ' au total';

    // Badge mission
    const badge = document.getElementById('missionBadge');
    if (badge) {
        badge.textContent = stats.encours > 0
            ? stats.encours + ' mission(s) en cours'
            : 'Aucune mission active';
    }
}

function buildAffRow(aff) {
    const dotColor = aff.statut === 'terminee' ? '#10B981'
                   : aff.statut === 'en_cours'  ? '#F59E0B' : '#EF4444';
    const detailUrl = `{{ url('equipe/alertes') }}/${aff.id}`;
    return `
    <div class="recent-alerte ra-new" id="aff-row-${aff.id}">
        <div class="ra-dot" style="background:${dotColor};"></div>
        <div style="flex:1;">
            <div class="ra-title">${escHtml(aff.alerte?.titre || 'Alerte #' + aff.id)}</div>
            <div class="ra-meta">
                ${escHtml(aff.alerte?.localisation || '—')} · ${escHtml(aff.created_at)}
            </div>
        </div>
        <a href="${detailUrl}" class="btn btn-outline btn-sm">
            <i data-feather="map-pin"></i>
        </a>
    </div>`;
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}

let toastAffTimer = null;
function showAffToast(titre, loc) {
    document.getElementById('toastAffTitle').textContent = '🚨 ' + titre;
    document.getElementById('toastAffSub').textContent   = loc;
    const t = document.getElementById('toastAff');
    t.classList.add('show');
    if (toastAffTimer) clearTimeout(toastAffTimer);
    toastAffTimer = setTimeout(() => t.classList.remove('show'), 7000);
}
function goToFirstAff() {
    const first = document.querySelector('.ra-new');
    if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.getElementById('toastAff').classList.remove('show');
}

function playUrgenceBeep() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        [660, 880].forEach((freq, i) => {
            const osc  = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.value = freq;
            osc.type = 'sine';
            const t = ctx.currentTime + i * .22;
            gain.gain.setValueAtTime(.2, t);
            gain.gain.exponentialRampToValueAtTime(.001, t + .3);
            osc.start(t); osc.stop(t + .3);
        });
    } catch(e) {}
}

// ════════════════════════════════════════════════════════════
//  CARTE LEAFLET
// ════════════════════════════════════════════════════════════
const map = L.map('dashMap', { zoomControl: true });
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 18
}).addTo(map);

const iconUrgence = L.divIcon({
    className: '',
    html: `<div style="width:18px;height:18px;background:#EF4444;border-radius:50%;
        border:3px solid #fff;box-shadow:0 0 0 4px rgba(239,68,68,.3);
        animation:mapPulse 1.8s infinite;"></div>`,
    iconSize: [18,18], iconAnchor: [9,9]
});
const iconEquipe = L.divIcon({
    className: '',
    html: `<div style="width:16px;height:16px;background:#F97316;border-radius:50%;
        border:3px solid #fff;box-shadow:0 2px 8px rgba(249,115,22,.5);"></div>`,
    iconSize: [16,16], iconAnchor: [8,8]
});

let mapMarkerAlerte = null;
let mapMarkerEquipe = null;
let mapPolyline     = null;

function updateMapMarker(alerte) {
    if (!alerte.latitude || !alerte.longitude) return;

    if (mapMarkerAlerte) {
        mapMarkerAlerte.setLatLng([alerte.latitude, alerte.longitude]);
    } else {
        mapMarkerAlerte = L.marker([alerte.latitude, alerte.longitude], { icon: iconUrgence })
            .addTo(map)
            .bindPopup(`<b style="color:#EF4444;">🚨 ${alerte.titre || ''}</b><br><small>${alerte.localisation || ''}</small>`);
        map.setView([alerte.latitude, alerte.longitude], 14);
    }
}

// Géocodage Nominatim (adresse → lat/lng) si pas de coordonnées GPS
async function geocodeAndUpdateMap(alerte) {
    if (!alerte.localisation) return;
    try {
        const query = encodeURIComponent(alerte.localisation + ', Brazzaville, Congo');
        const res   = await fetch(`https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=1`, {
            headers: { 'Accept-Language': 'fr' }
        });
        const data = await res.json();
        if (data.length > 0) {
            alerte.latitude  = parseFloat(data[0].lat);
            alerte.longitude = parseFloat(data[0].lon);
            updateMapMarker(alerte);
        }
    } catch(e) {
        console.warn('Géocodage échoué pour :', alerte.localisation);
    }
}

// ── Initialisation carte (données serveur existantes) ──
const alerteLat = {{ $derniereAlerte?->alerte?->latitude ?? 'null' }};
const alerteLng = {{ $derniereAlerte?->alerte?->longitude ?? 'null' }};
const alerteTitre = @json($derniereAlerte?->alerte?->titre ?? 'Urgence');
const alerteLoc   = @json($derniereAlerte?->alerte?->localisation ?? '');

if (alerteLat && alerteLng) {
    mapMarkerAlerte = L.marker([alerteLat, alerteLng], { icon: iconUrgence })
        .addTo(map)
        .bindPopup(`<div style="font-family:'Inter',sans-serif;min-width:160px;">
            <div style="font-weight:700;color:#EF4444;margin-bottom:4px;">🚨 ${alerteTitre}</div>
            <div style="font-size:12px;color:#475569;">${alerteLoc}</div>
        </div>`);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const eLat = pos.coords.latitude;
            const eLng = pos.coords.longitude;

            mapMarkerEquipe = L.marker([eLat, eLng], { icon: iconEquipe })
                .addTo(map)
                .bindPopup(`<div style="font-family:'Inter',sans-serif;">
                    <div style="font-weight:700;color:#F97316;margin-bottom:4px;">📍 {{ session('equipe_nom') }}</div>
                    <div style="font-size:12px;color:#475569;">Votre position</div>
                </div>`);

            mapPolyline = L.polyline([[eLat, eLng], [alerteLat, alerteLng]], {
                color: '#F97316', weight: 3, dashArray: '6 8', opacity: 0.8
            }).addTo(map);

            const distKm = L.latLng(eLat, eLng).distanceTo(L.latLng(alerteLat, alerteLng)) / 1000;
            const midLat = (eLat + alerteLat) / 2;
            const midLng = (eLng + alerteLng) / 2;
            L.marker([midLat, midLng], { icon: L.divIcon({
                className:'',
                html:`<div style="background:#0B1E3D;color:#fff;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700;font-family:'Inter',sans-serif;white-space:nowrap;">${distKm.toFixed(1)} km</div>`,
                iconAnchor:[30,10]
            })}).addTo(map);

            map.fitBounds([[eLat, eLng], [alerteLat, alerteLng]], { padding: [40,40] });

            const gmLink = `https://www.google.com/maps/dir/${eLat},${eLng}/${alerteLat},${alerteLng}`;
            document.getElementById('gmapsBtn').href = gmLink;
            document.getElementById('gmapsBtnContainer').style.display = 'block';
            feather.replace({ width: 14, height: 14 });
        }, function() {
            map.setView([alerteLat, alerteLng], 14);
            mapMarkerAlerte.openPopup();
        });
    } else {
        map.setView([alerteLat, alerteLng], 14);
        mapMarkerAlerte.openPopup();
    }
} else if (alerteLoc) {
    // Géocodage de la dernière localisation textuelle connue
    geocodeAndUpdateMap({ titre: alerteTitre, localisation: alerteLoc });
    map.setView([-4.2634, 15.2429], 12);
} else {
    map.setView([-4.2634, 15.2429], 12);
    L.marker([-4.2634, 15.2429], { icon: iconEquipe })
        .addTo(map)
        .bindPopup('<b>Brazzaville</b><br>Aucune urgence localisée')
        .openPopup();
}

// ── Démarrage SSE ──
startAffStream();
</script>
@endpush

@endsection
