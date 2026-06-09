@extends('equipes.master')

@section('title', 'Alerte — ' . ($affectation->alerte->titre ?? '#'.$affectation->id))
@section('page-icon', 'map-pin')
@section('page-title', $affectation->alerte->titre ?? 'Détail alerte')
@section('page-subtitle', 'Localisation et itinéraire vers l\'urgence')

@section('content')

@push('styles')
<style>
.detail-grid {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 16px;
    align-items: start;
}
@media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }

.info-row {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.info-row:last-child { border-bottom: none; }
.info-icon {
    width: 36px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.info-label { font-size: 10.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; }
.info-value { font-size: 13.5px; font-weight: 600; color: var(--text); margin-top: 2px; }

#detailMap { height: 480px; border-radius: 0 0 var(--radius) var(--radius); }

.status-select {
    width: 100%; padding: 9px 12px;
    border: 2px solid var(--accent); border-radius: 9px;
    font-size: 13px; font-weight: 600; font-family: inherit;
    color: var(--text); background: var(--surface);
    outline: none; cursor: pointer; margin-top: 8px;
}

.alerte-photo {
    width: 100%; max-height: 180px; object-fit: cover;
    border-radius: 9px; border: 1px solid var(--border); margin-top: 12px;
}

/* ── Live distance bar ── */
#liveBar {
    background: var(--brand);
    border-radius: 10px;
    padding: 14px 18px;
    margin-top: 12px;
    display: none;
}
#liveBar.show { display: block; }

.lb-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px;
}
.lb-dist {
    font-family: 'Sora', sans-serif;
    font-size: 28px; font-weight: 700;
    color: var(--accent);
    transition: all .4s ease;
}
.lb-label { font-size: 11px; color: rgba(255,255,255,.5); margin-top: 2px; }

.lb-pulse {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; color: rgba(255,255,255,.7); font-weight: 600;
}
.lb-dot {
    width: 8px; height: 8px; border-radius: 50%; background: #10B981;
    animation: liveDot 1.2s infinite;
}
@keyframes liveDot {
    0%,100% { opacity: 1; transform: scale(1); }
    50%      { opacity: .4; transform: scale(1.5); }
}

/* Barre de progression (distance restante visuellement) */
.lb-progress-wrap {
    background: rgba(255,255,255,.1);
    border-radius: 100px; height: 6px; overflow: hidden; margin-top: 8px;
}
.lb-progress-bar {
    height: 100%; border-radius: 100px;
    background: linear-gradient(90deg, var(--accent), #10B981);
    transition: width .8s ease;
}

.lb-arrived {
    display: none;
    background: #10B981;
    color: #fff;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 13px; font-weight: 700;
    text-align: center;
    margin-top: 10px;
}
.lb-arrived.show { display: block; }

/* GPS refusé */
#gpsWarning {
    background: #FEF3C7; border: 1px solid #FCD34D;
    border-radius: 9px; padding: 12px 14px;
    font-size: 12.5px; color: #92400E;
    display: none; margin-top: 12px; gap: 8px; align-items: flex-start;
}
#gpsWarning.show { display: flex; }

@keyframes urgPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,.6); }
    70%       { box-shadow: 0 0 0 14px rgba(239,68,68,0); }
}
</style>
@endpush

<div class="detail-grid">

    {{-- ── Panel gauche ── --}}
    <div>
        {{-- Infos alerte --}}
        <div class="content-card anim-fade" style="margin-bottom:16px;">
            <div class="cc-header">
                <div class="cc-title"><i data-feather="alert-triangle"></i> Informations urgence</div>
            </div>
            <div class="cc-body">

                @if($affectation->alerte?->media_photo)
                <img src="{{ asset($affectation->alerte->media_photo) }}" class="alerte-photo" alt="Photo urgence">
                @endif

                <div class="info-row">
                    <div class="info-icon" style="background:#FEE2E2;"><i data-feather="alert-circle" style="stroke:#EF4444;"></i></div>
                    <div>
                        <div class="info-label">Titre</div>
                        <div class="info-value">{{ $affectation->alerte->titre ?? '—' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-icon" style="background:#DBEAFE;"><i data-feather="tag" style="stroke:#3B82F6;"></i></div>
                    <div>
                        <div class="info-label">Type</div>
                        <div class="info-value">{{ $affectation->alerte->type_alerte ?? '—' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-icon" style="background:#FEF3C7;"><i data-feather="map-pin" style="stroke:#D97706;"></i></div>
                    <div>
                        <div class="info-label">Localisation déclarée</div>
                        <div class="info-value">{{ $affectation->alerte->localisation ?? '—' }}</div>
                    </div>
                </div>

                @if($affectation->alerte?->latitude)
                <div class="info-row">
                    <div class="info-icon" style="background:#D1FAE5;"><i data-feather="crosshair" style="stroke:#10B981;"></i></div>
                    <div>
                        <div class="info-label">Coordonnées GPS</div>
                        <div class="info-value">
                            {{ number_format($affectation->alerte->latitude, 6) }},
                            {{ number_format($affectation->alerte->longitude, 6) }}
                        </div>
                    </div>
                </div>
                @endif

                <div class="info-row">
                    <div class="info-icon" style="background:#F3E8FF;"><i data-feather="calendar" style="stroke:#7C3AED;"></i></div>
                    <div>
                        <div class="info-label">Signalée le</div>
                        <div class="info-value">
                            {{ $affectation->alerte ? \Carbon\Carbon::parse($affectation->alerte->created_at)->format('d/m/Y à H:i') : '—' }}
                        </div>
                    </div>
                </div>

                @if($affectation->alerte?->description)
                <div class="info-row">
                    <div class="info-icon" style="background:#F1F5F9;"><i data-feather="file-text" style="stroke:#64748B;"></i></div>
                    <div>
                        <div class="info-label">Description</div>
                        <div class="info-value" style="font-weight:400; line-height:1.6;">
                            {{ $affectation->alerte->description }}
                        </div>
                    </div>
                </div>
                @endif

                @if($affectation->commentaire)
                <div class="info-row">
                    <div class="info-icon" style="background:#FFF7ED;"><i data-feather="message-circle" style="stroke:#F97316;"></i></div>
                    <div>
                        <div class="info-label">Note du service</div>
                        <div class="info-value" style="font-weight:400; color:var(--text-sec); font-style:italic;">
                            {{ $affectation->commentaire }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Citoyen --}}
        @if($affectation->alerte?->citoyen)
        <div class="content-card anim-fade" style="margin-bottom:16px;">
            <div class="cc-header">
                <div class="cc-title"><i data-feather="user"></i> Citoyen concerné</div>
            </div>
            <div class="cc-body">
                <div class="info-row">
                    <div class="info-icon" style="background:#DBEAFE;"><i data-feather="user" style="stroke:#3B82F6;"></i></div>
                    <div>
                        <div class="info-label">Nom complet</div>
                        <div class="info-value">{{ $affectation->alerte->citoyen->nom }} {{ $affectation->alerte->citoyen->prenom }}</div>
                    </div>
                </div>
                <div class="info-row" style="border:none;">
                    <div class="info-icon" style="background:#D1FAE5;"><i data-feather="phone" style="stroke:#10B981;"></i></div>
                    <div>
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">
                            <a href="tel:{{ $affectation->alerte->citoyen->telephone }}"
                               style="color:var(--accent); text-decoration:none; font-size:15px;">
                                📞 {{ $affectation->alerte->citoyen->telephone }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Statut --}}
        <div class="content-card anim-fade">
            <div class="cc-header">
                <div class="cc-title"><i data-feather="sliders"></i> Statut de l'intervention</div>
            </div>
            <div class="cc-body">
                <form method="POST" action="{{ route('equipe.affectation.statut', $affectation->id) }}">
                    @csrf
                    <label style="font-size:12px; font-weight:600; color:var(--text-muted);">Mettre à jour</label>
                    <select name="statut" class="status-select" onchange="this.form.submit()">
                        <option value="transmise" {{ $affectation->statut === 'transmise' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="en_cours"  {{ $affectation->statut === 'en_cours'  ? 'selected' : '' }}>🔥 En cours d'intervention</option>
                        <option value="terminee"  {{ $affectation->statut === 'terminee'  ? 'selected' : '' }}>✅ Mission terminée</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Colonne carte ── --}}
    <div>

        {{-- Carte --}}
        <div class="content-card anim-slide" style="overflow:hidden;">
            <div class="cc-header">
                <div>
                    <div class="cc-title"><i data-feather="map"></i> Carte de mission en direct</div>
                    <div class="cc-subtitle" id="mapSubtitle">Chargement de la carte…</div>
                </div>
                <a id="gmapsBtn" href="#" target="_blank" class="btn btn-accent btn-sm" style="display:none;">
                    <i data-feather="navigation"></i>
                    Itinéraire
                </a>
            </div>
            <div id="detailMap"></div>
        </div>

        {{-- Barre de distance live --}}
        <div id="liveBar">
            <div class="lb-header">
                <div>
                    <div class="lb-dist" id="lbDist">—</div>
                    <div class="lb-label">Distance jusqu'à l'urgence</div>
                </div>
                <div class="lb-pulse">
                    <div class="lb-dot"></div>
                    Suivi en direct
                </div>
            </div>
            <div class="lb-progress-wrap">
                <div class="lb-progress-bar" id="lbBar" style="width:100%;"></div>
            </div>
            <div id="lbArrived" class="lb-arrived">
                ✅ Vous êtes arrivé sur les lieux
            </div>
        </div>

        {{-- Warning GPS refusé --}}
        <div id="gpsWarning">
            <i data-feather="alert-triangle" style="stroke:#D97706; flex-shrink:0; margin-top:1px;"></i>
            <div>
                <strong>GPS non disponible.</strong> Activez la géolocalisation dans votre navigateur pour le suivi en temps réel et le calcul de distance.
            </div>
        </div>

        {{-- Audio --}}
        @if($affectation->alerte?->media_vocal)
        <div class="content-card" style="margin-top:16px;">
            <div class="cc-header"><div class="cc-title"><i data-feather="mic"></i> Message vocal</div></div>
            <div class="cc-body">
                <audio controls style="width:100%; border-radius:8px;">
                    <source src="{{ asset($affectation->alerte->media_vocal) }}" type="audio/webm">
                </audio>
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
feather.replace({ width: 14, height: 14 });

// ── Données urgence (PHP → JS) ──────────────────────────────
const ALERTE_LAT  = {{ $affectation->alerte?->latitude  ?? 'null' }};
const ALERTE_LNG  = {{ $affectation->alerte?->longitude ?? 'null' }};
const ALERTE_TITRE = @json($affectation->alerte?->titre       ?? 'Urgence');
const ALERTE_LOC   = @json($affectation->alerte?->localisation ?? '');
const ALERTE_TYPE  = @json($affectation->alerte?->type_alerte  ?? '');
const CITOYEN_TEL  = @json($affectation->alerte?->citoyen?->telephone ?? '');
const EQUIPE_NOM   = @json(session('equipe_nom') ?? 'Équipe');

// ── Init carte Leaflet ──────────────────────────────────────
const map = L.map('detailMap');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19
}).addTo(map);

// ── Icônes ──────────────────────────────────────────────────
const iconUrgence = L.divIcon({
    className: '',
    html: `<div style="
        width:24px; height:24px; background:#EF4444; border-radius:50%;
        border:3px solid #fff;
        box-shadow:0 0 0 4px rgba(239,68,68,.35);
        animation:urgPulse 1.6s infinite;
    "></div>`,
    iconSize: [24, 24], iconAnchor: [12, 12]
});

const iconEquipe = L.divIcon({
    className: '',
    html: `<div style="
        width:20px; height:20px; background:#F97316; border-radius:50%;
        border:3px solid #fff;
        box-shadow:0 2px 10px rgba(249,115,22,.7);
    "></div>`,
    iconSize: [20, 20], iconAnchor: [10, 10]
});

// ── Variables globales ───────────────────────────────────────
let markerAlerte  = null;
let markerEquipe  = null;
let polylinePath  = null;
let labelDist     = null;
let distanceInitiale = null; // pour la barre de progression

// Distance entre deux LatLng Leaflet (en km)
function distanceKm(lat1, lng1, lat2, lng2) {
    return L.latLng(lat1, lng1).distanceTo(L.latLng(lat2, lng2)) / 1000;
}

// Formater km en texte lisible
function formatDist(km) {
    if (km < 1) return Math.round(km * 1000) + ' m';
    return km.toFixed(2) + ' km';
}

// ── Placer le marqueur urgence ───────────────────────────────
if (ALERTE_LAT && ALERTE_LNG) {

    markerAlerte = L.marker([ALERTE_LAT, ALERTE_LNG], { icon: iconUrgence })
        .addTo(map)
        .bindPopup(`
            <div style="font-family:'Inter',sans-serif; min-width:190px;">
                <div style="font-weight:700;color:#EF4444;font-size:13px;margin-bottom:6px;">
                    🚨 ${ALERTE_TITRE}
                </div>
                <div style="font-size:12px;color:#475569;margin-bottom:3px;"><b>Type :</b> ${ALERTE_TYPE}</div>
                <div style="font-size:12px;color:#475569;margin-bottom:3px;"><b>Lieu :</b> ${ALERTE_LOC}</div>
                ${CITOYEN_TEL ? `
                <a href="tel:${CITOYEN_TEL}" style="
                    display:inline-flex;align-items:center;gap:5px;
                    margin-top:8px;padding:6px 12px;
                    background:#F97316;color:#fff;
                    border-radius:7px;font-size:12px;font-weight:700;
                    text-decoration:none;">
                    📞 Appeler le citoyen
                </a>` : ''}
            </div>
        `);

    map.setView([ALERTE_LAT, ALERTE_LNG], 14);
    markerAlerte.openPopup();
    document.getElementById('mapSubtitle').textContent = 'Urgence localisée — En attente de votre position GPS';

    // ── Suivi GPS en temps réel ──────────────────────────────
    if (navigator.geolocation) {

        // watchPosition = mise à jour continue (chaque déplacement)
        navigator.geolocation.watchPosition(
            function onSuccess(pos) {
                const eLat = pos.coords.latitude;
                const eLng = pos.coords.longitude;
                const distKm = distanceKm(eLat, eLng, ALERTE_LAT, ALERTE_LNG);

                // ── Marqueur équipe : créer ou déplacer ──
                if (!markerEquipe) {
                    markerEquipe = L.marker([eLat, eLng], { icon: iconEquipe })
                        .addTo(map)
                        .bindPopup(`
                            <div style="font-family:'Inter',sans-serif;">
                                <div style="font-weight:700;color:#F97316;margin-bottom:4px;">📍 ${EQUIPE_NOM}</div>
                                <div style="font-size:12px;color:#475569;">Votre position en direct</div>
                            </div>
                        `);

                    // Distance initiale de référence pour la barre de progression
                    distanceInitiale = distKm;

                    // Ajuster la vue pour voir les deux points
                    map.fitBounds(
                        [[eLat, eLng], [ALERTE_LAT, ALERTE_LNG]],
                        { padding: [50, 50], maxZoom: 16 }
                    );

                } else {
                    // Déplacer le marqueur sans recréer
                    markerEquipe.setLatLng([eLat, eLng]);
                }

                // ── Ligne trajet : recréer à chaque mise à jour ──
                if (polylinePath) map.removeLayer(polylinePath);
                polylinePath = L.polyline(
                    [[eLat, eLng], [ALERTE_LAT, ALERTE_LNG]],
                    { color: '#F97316', weight: 3, dashArray: '8 6', opacity: 0.9 }
                ).addTo(map);

                // ── Label distance au milieu de la ligne ──
                if (labelDist) map.removeLayer(labelDist);
                const midLat = (eLat + ALERTE_LAT) / 2;
                const midLng = (eLng + ALERTE_LNG) / 2;
                labelDist = L.marker([midLat, midLng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="
                            background:#0B1E3D;color:#fff;
                            padding:4px 10px;border-radius:8px;
                            font-size:12px;font-weight:700;
                            font-family:'Inter',sans-serif;
                            white-space:nowrap;
                            box-shadow:0 2px 8px rgba(0,0,0,.35);
                        ">📏 ${formatDist(distKm)}</div>`,
                        iconAnchor: [42, 12]
                    })
                }).addTo(map);

                // ── Barre de distance live ──
                const liveBar = document.getElementById('liveBar');
                liveBar.classList.add('show');
                document.getElementById('lbDist').textContent = formatDist(distKm);

                // Barre de progression : 100% = distance initiale, 0% = arrivé
                const pct = distanceInitiale > 0
                    ? Math.max(0, Math.min(100, (distKm / distanceInitiale) * 100))
                    : 100;
                document.getElementById('lbBar').style.width = pct + '%';

                // ── Seuil d'arrivée : ≤ 50 mètres ──
                if (distKm <= 0.05) {
                    document.getElementById('lbArrived').classList.add('show');
                    document.getElementById('lbDist').textContent = '🎯 Sur place';
                    document.getElementById('lbBar').style.width = '0%';
                } else {
                    document.getElementById('lbArrived').classList.remove('show');
                }

                // ── Sous-titre carte ──
                document.getElementById('mapSubtitle').textContent =
                    'En direct · ' + EQUIPE_NOM + ' → ' + formatDist(distKm) + ' de l\'urgence';

                // ── Bouton Google Maps (itinéraire dynamique) ──
                const gmLink = `https://www.google.com/maps/dir/${eLat},${eLng}/${ALERTE_LAT},${ALERTE_LNG}`;
                const btn = document.getElementById('gmapsBtn');
                btn.href = gmLink;
                btn.style.display = 'inline-flex';
                feather.replace({ width: 14, height: 14 });
            },

            function onError(err) {
                // GPS refusé ou indisponible
                map.setView([ALERTE_LAT, ALERTE_LNG], 14);
                document.getElementById('mapSubtitle').textContent = 'GPS indisponible — urgence localisée';
                document.getElementById('gpsWarning').classList.add('show');

                // Bouton Maps centré sur l'urgence uniquement
                const gmLink = `https://www.google.com/maps/search/?api=1&query=${ALERTE_LAT},${ALERTE_LNG}`;
                const btn = document.getElementById('gmapsBtn');
                btn.href  = gmLink;
                btn.style.display = 'inline-flex';
                feather.replace({ width: 14, height: 14 });
            },

            {
                enableHighAccuracy: true,   // GPS haute précision
                maximumAge: 5000,           // accepter position vieille de 5s max
                timeout: 15000             // timeout 15s
            }
        );

    } else {
        // Navigateur sans géolocalisation
        map.setView([ALERTE_LAT, ALERTE_LNG], 14);
        document.getElementById('mapSubtitle').textContent = 'Géolocalisation non supportée';
        document.getElementById('gpsWarning').classList.add('show');
    }

} else {
    // Aucune coordonnée GPS sur l'alerte → carte Brazzaville
    map.setView([-4.2634, 15.2429], 12);
    L.marker([-4.2634, 15.2429], { icon: iconUrgence })
        .addTo(map)
        .bindPopup('<b>Brazzaville</b><br>Aucune coordonnée GPS pour cette urgence')
        .openPopup();
    document.getElementById('mapSubtitle').textContent = 'Aucune coordonnée GPS — localisation textuelle uniquement';
}
</script>
@endpush

@endsection
