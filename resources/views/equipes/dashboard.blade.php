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
    position: absolute;
    right: -60px; top: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(249,115,22,.2) 0%, transparent 65%);
    pointer-events: none;
}
.mission-info h2 {
    font-family: 'Sora', sans-serif;
    font-size: 20px; font-weight: 700;
    margin-bottom: 4px;
}
.mission-info p { font-size: 13px; color: rgba(255,255,255,.6); }
.mission-badge {
    background: var(--accent);
    color: #fff;
    font-size: 11px; font-weight: 700;
    padding: 4px 12px;
    border-radius: 100px;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-top: 10px;
    display: inline-block;
}
.mission-icon {
    width: 64px; height: 64px;
    background: rgba(249,115,22,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
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

/* Pulsing marker animation */
@keyframes mapPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.4); opacity: 0.6; }
}
</style>
@endpush

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
        <span class="mission-badge">
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
        <div class="sc-value">{{ $stats['total'] }}</div>
        <div class="sc-label">Total affectées</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon icon-orange"><i data-feather="clock"></i></div>
        <div class="sc-value">{{ $stats['transmise'] }}</div>
        <div class="sc-label">En attente</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon icon-yellow"><i data-feather="activity"></i></div>
        <div class="sc-value">{{ $stats['en_cours'] }}</div>
        <div class="sc-label">En cours</div>
    </div>
    <div class="stat-card">
        <div class="sc-icon icon-green"><i data-feather="check-circle"></i></div>
        <div class="sc-value">{{ $stats['terminee'] }}</div>
        <div class="sc-label">Terminées</div>
    </div>
</div>

{{-- ── Layout 2 colonnes ── --}}
<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">

    {{-- Alertes récentes --}}
    <div class="content-card anim-slide">
        <div class="cc-header">
            <div>
                <div class="cc-title"><i data-feather="bell"></i> Dernières alertes affectées</div>
                <div class="cc-subtitle">{{ $affectations->count() }} au total</div>
            </div>
            <a href="{{ route('equipe.alertes') }}" class="btn btn-outline btn-sm">Voir tout</a>
        </div>
        @forelse($affectations->take(5) as $aff)
        <div class="recent-alerte">
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
        <div class="empty-state">
            <div class="empty-icon"><i data-feather="inbox"></i></div>
            <p>Aucune alerte affectée</p>
            <span>Les nouvelles missions apparaîtront ici</span>
        </div>
        @endforelse
    </div>

    {{-- Carte position urgence ── --}}
    <div class="content-card anim-slide" style="overflow:hidden;">
        <div class="cc-header">
            <div>
                <div class="cc-title"><i data-feather="map"></i> Carte de mission</div>
                <div class="cc-subtitle">
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

@push('scripts')
<script>
feather.replace({ width: 14, height: 14 });

// ── Initialisation carte Leaflet ──
const map = L.map('dashMap', { zoomControl: true });

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 18
}).addTo(map);

// Coordonnées de l'urgence affectée
const alerteLat = {{ $derniereAlerte?->alerte?->latitude ?? 'null' }};
const alerteLng = {{ $derniereAlerte?->alerte?->longitude ?? 'null' }};
const alerteTitre = @json($derniereAlerte?->alerte?->titre ?? 'Urgence');
const alerteLoc   = @json($derniereAlerte?->alerte?->localisation ?? '');

// Icône urgence personnalisée (rouge pulsant)
const iconUrgence = L.divIcon({
    className: '',
    html: `<div style="
        width:18px; height:18px;
        background:#EF4444;
        border-radius:50%;
        border:3px solid #fff;
        box-shadow:0 0 0 4px rgba(239,68,68,.3);
        animation: mapPulse 1.8s infinite;
    "></div>`,
    iconSize: [18, 18],
    iconAnchor: [9, 9]
});

// Icône équipe (orange)
const iconEquipe = L.divIcon({
    className: '',
    html: `<div style="
        width:16px; height:16px;
        background:#F97316;
        border-radius:50%;
        border:3px solid #fff;
        box-shadow:0 2px 8px rgba(249,115,22,.5);
    "></div>`,
    iconSize: [16, 16],
    iconAnchor: [8, 8]
});

if (alerteLat && alerteLng) {
    // Marqueur urgence
    const markerAlerte = L.marker([alerteLat, alerteLng], { icon: iconUrgence })
        .addTo(map)
        .bindPopup(`
            <div style="font-family:'Inter',sans-serif; min-width:160px;">
                <div style="font-weight:700; color:#EF4444; margin-bottom:4px;">🚨 ${alerteTitre}</div>
                <div style="font-size:12px; color:#475569;">${alerteLoc}</div>
            </div>
        `);

    // Obtenir position réelle de l'équipe via l'API géolocalisation navigateur
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const equipeLat = pos.coords.latitude;
            const equipeLng = pos.coords.longitude;

            // Marqueur équipe
            const markerEquipe = L.marker([equipeLat, equipeLng], { icon: iconEquipe })
                .addTo(map)
                .bindPopup(`
                    <div style="font-family:'Inter',sans-serif;">
                        <div style="font-weight:700; color:#F97316; margin-bottom:4px;">📍 {{ session('equipe_nom') }}</div>
                        <div style="font-size:12px; color:#475569;">Votre position</div>
                    </div>
                `);

            // Tracer la ligne entre équipe et urgence
            const route = L.polyline(
                [[equipeLat, equipeLng], [alerteLat, alerteLng]],
                { color: '#F97316', weight: 3, dashArray: '6 8', opacity: 0.8 }
            ).addTo(map);

            // Calculer distance à vol d'oiseau
            const distKm = markerEquipe.getLatLng().distanceTo(markerAlerte.getLatLng()) / 1000;

            // Légende distance
            const distLabel = L.divIcon({
                className: '',
                html: `<div style="
                    background:#0B1E3D; color:#fff;
                    padding:3px 8px; border-radius:6px;
                    font-size:11px; font-weight:700;
                    font-family:'Inter',sans-serif;
                    white-space:nowrap;
                ">${distKm.toFixed(1)} km</div>`,
                iconAnchor: [30, 10]
            });
            const midLat = (equipeLat + alerteLat) / 2;
            const midLng = (equipeLng + alerteLng) / 2;
            L.marker([midLat, midLng], { icon: distLabel }).addTo(map);

            // Ajuster le zoom pour voir les deux points
            map.fitBounds([[equipeLat, equipeLng], [alerteLat, alerteLng]], { padding: [40, 40] });

            // Lien Google Maps itinéraire
            const gmLink = `https://www.google.com/maps/dir/${equipeLat},${equipeLng}/${alerteLat},${alerteLng}`;
            document.getElementById('gmapsBtn').href = gmLink;
            document.getElementById('gmapsBtnContainer').style.display = 'block';

        }, function() {
            // Géolocalisation refusée → centrer sur l'urgence
            map.setView([alerteLat, alerteLng], 14);
            markerAlerte.openPopup();
        });
    } else {
        map.setView([alerteLat, alerteLng], 14);
        markerAlerte.openPopup();
    }

} else {
    // Pas de coordonnées → centrer sur Brazzaville
    map.setView([-4.2634, 15.2429], 12);
    L.marker([-4.2634, 15.2429], { icon: iconEquipe })
        .addTo(map)
        .bindPopup('<b>Brazzaville</b><br>Aucune urgence localisée')
        .openPopup();
}
</script>
@endpush

{{-- Bouton Google Maps itinéraire (masqué par défaut, affiché via JS) --}}
<div id="gmapsBtnContainer" style="display:none; margin-bottom:20px;">
    <a id="gmapsBtn" href="#" target="_blank" class="btn btn-accent">
        <i data-feather="navigation"></i>
        Ouvrir l'itinéraire dans Google Maps
    </a>
</div>

@endsection
