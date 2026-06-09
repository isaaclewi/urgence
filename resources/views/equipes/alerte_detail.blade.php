@extends('equipes.master')

@section('title', 'Alerte — ' . ($affectation->alerte->titre ?? '#'.$affectation->id))
@section('page-icon', 'map-pin')
@section('page-title', $affectation->alerte->titre ?? 'Détail alerte')
@section('page-subtitle', 'Localisation et itinéraire vers l\'urgence')

@section('content')

@push('styles')
<style>
/* ── Layout ── */
.detail-grid {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 16px;
    align-items: start;
}
@media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }

/* ── Info panel ── */
.info-row {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.info-row:last-child { border-bottom: none; }
.info-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.info-label { font-size: 10.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .04em; }
.info-value { font-size: 13.5px; font-weight: 600; color: var(--text); margin-top: 2px; }

/* ── Map ── */
#detailMap {
    height: 480px;
    border-radius: 0 0 var(--radius) var(--radius);
}

/* ── Statut changer ── */
.status-select {
    width: 100%; padding: 9px 12px;
    border: 2px solid var(--accent);
    border-radius: 9px;
    font-size: 13px; font-weight: 600;
    font-family: inherit;
    color: var(--text); background: var(--surface);
    outline: none; cursor: pointer;
    margin-top: 8px;
}

/* ── Media ── */
.alerte-photo {
    width: 100%; max-height: 180px;
    object-fit: cover;
    border-radius: 9px;
    border: 1px solid var(--border);
    margin-top: 12px;
}

/* ── Route info box ── */
#routeInfo {
    background: var(--brand);
    color: #fff;
    border-radius: 9px;
    padding: 12px 16px;
    font-size: 13px;
    display: none;
    margin-top: 12px;
    gap: 16px;
    align-items: center;
}
#routeInfo.show { display: flex; }
#routeInfo .ri-val { font-family: 'Sora', sans-serif; font-size: 22px; font-weight: 700; color: var(--accent); }
#routeInfo .ri-label { font-size: 11px; color: rgba(255,255,255,.6); }

/* Pulsing urgence dot */
@keyframes urgPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,.6); }
    70% { box-shadow: 0 0 0 12px rgba(239,68,68,0); }
}
</style>
@endpush

<div class="detail-grid">

    {{-- ── Panel info ── --}}
    <div>
        {{-- Carte info alerte --}}
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
                        <div class="info-label">Note du service source</div>
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
                <div class="info-row">
                    <div class="info-icon" style="background:#D1FAE5;"><i data-feather="phone" style="stroke:#10B981;"></i></div>
                    <div>
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">
                            <a href="tel:{{ $affectation->alerte->citoyen->telephone }}"
                               style="color:var(--accent); text-decoration:none;">
                                {{ $affectation->alerte->citoyen->telephone }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Changer statut --}}
        <div class="content-card anim-fade">
            <div class="cc-header">
                <div class="cc-title"><i data-feather="sliders"></i> Mettre à jour le statut</div>
            </div>
            <div class="cc-body">
                <form method="POST" action="{{ route('equipe.affectation.statut', $affectation->id) }}">
                    @csrf
                    <label style="font-size:12px; font-weight:600; color:var(--text-muted);">Statut de l'intervention</label>
                    <select name="statut" class="status-select" onchange="this.form.submit()">
                        <option value="transmise" {{ $affectation->statut === 'transmise' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="en_cours"  {{ $affectation->statut === 'en_cours'  ? 'selected' : '' }}>🔥 En cours d'intervention</option>
                        <option value="terminee"  {{ $affectation->statut === 'terminee'  ? 'selected' : '' }}>✅ Mission terminée</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Carte ── --}}
    <div>
        <div class="content-card anim-slide" style="overflow:hidden;">
            <div class="cc-header">
                <div>
                    <div class="cc-title"><i data-feather="map"></i> Cartographie de la mission</div>
                    <div class="cc-subtitle" id="mapSubtitle">Localisation de l'urgence</div>
                </div>
                <a id="gmapsBtn" href="#" target="_blank" class="btn btn-accent btn-sm" style="display:none;">
                    <i data-feather="navigation"></i>
                    Google Maps
                </a>
            </div>
            <div id="detailMap"></div>
        </div>

        {{-- Distance & durée estimée --}}
        <div id="routeInfo" class="anim-fade">
            <div>
                <div class="ri-val" id="riDist">—</div>
                <div class="ri-label">Distance vol d'oiseau</div>
            </div>
            <div style="width:1px; background:rgba(255,255,255,.15); height:40px;"></div>
            <div style="flex:1; font-size:12px; color:rgba(255,255,255,.7); line-height:1.6;">
                <i data-feather="info" style="display:inline; width:12px; height:12px;"></i>
                Activez GPS pour voir votre position et le trajet en temps réel
            </div>
        </div>

        @if($affectation->alerte?->media_vocal)
        <div class="content-card" style="margin-top:16px;">
            <div class="cc-header"><div class="cc-title"><i data-feather="mic"></i> Message vocal du citoyen</div></div>
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

// ── Données urgence depuis PHP ──
const alerteLat = {{ $affectation->alerte?->latitude ?? 'null' }};
const alerteLng = {{ $affectation->alerte?->longitude ?? 'null' }};
const alerteTitre  = @json($affectation->alerte?->titre ?? 'Urgence');
const alerteLoc    = @json($affectation->alerte?->localisation ?? '');
const alerteType   = @json($affectation->alerte?->type_alerte ?? '');
const citoyenTel   = @json($affectation->alerte?->citoyen?->telephone ?? '');

// ── Init carte ──
const map = L.map('detailMap');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap', maxZoom: 18
}).addTo(map);

// Icône urgence (rouge pulsant)
const iconUrgence = L.divIcon({
    className: '',
    html: `<div style="
        width:22px; height:22px;
        background:#EF4444; border-radius:50%;
        border:3px solid #fff;
        box-shadow:0 0 0 4px rgba(239,68,68,.4);
        animation: urgPulse 1.8s infinite;
    "></div>`,
    iconSize: [22, 22], iconAnchor: [11, 11]
});

// Icône équipe (orange)
const iconEquipe = L.divIcon({
    className: '',
    html: `<div style="
        width:18px; height:18px;
        background:#F97316; border-radius:50%;
        border:3px solid #fff;
        box-shadow:0 2px 10px rgba(249,115,22,.6);
    "></div>`,
    iconSize: [18, 18], iconAnchor: [9, 9]
});

let markerAlerte = null;

if (alerteLat && alerteLng) {
    markerAlerte = L.marker([alerteLat, alerteLng], { icon: iconUrgence })
        .addTo(map)
        .bindPopup(`
            <div style="font-family:'Inter',sans-serif; min-width:180px;">
                <div style="font-weight:700; color:#EF4444; font-size:13px; margin-bottom:6px;">
                    🚨 ${alerteTitre}
                </div>
                <div style="font-size:12px; color:#475569; margin-bottom:4px;">
                    <b>Type :</b> ${alerteType}
                </div>
                <div style="font-size:12px; color:#475569; margin-bottom:4px;">
                    <b>Lieu :</b> ${alerteLoc}
                </div>
                ${citoyenTel ? `<a href="tel:${citoyenTel}" style="
                    display:inline-flex; align-items:center; gap:4px;
                    margin-top:8px; padding:5px 10px;
                    background:#F97316; color:#fff;
                    border-radius:6px; font-size:11px; font-weight:700;
                    text-decoration:none;
                ">📞 Appeler le citoyen</a>` : ''}
            </div>
        `)
        .openPopup();

    document.getElementById('mapSubtitle').textContent = 'Urgence géolocalisée — En attente de votre position GPS';

    // Tenter géolocalisation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const eLat = pos.coords.latitude;
            const eLng = pos.coords.longitude;

            // Marqueur équipe
            const markerEquipe = L.marker([eLat, eLng], { icon: iconEquipe })
                .addTo(map)
                .bindPopup(`
                    <div style="font-family:'Inter',sans-serif;">
                        <div style="font-weight:700; color:#F97316; margin-bottom:4px;">📍 {{ session('equipe_nom') }}</div>
                        <div style="font-size:12px; color:#475569;">Votre position actuelle</div>
                    </div>
                `);

            // Ligne trajet
            const polyline = L.polyline(
                [[eLat, eLng], [alerteLat, alerteLng]],
                { color: '#F97316', weight: 3, dashArray: '8 6', opacity: 0.9 }
            ).addTo(map);

            // Distance
            const distM  = markerEquipe.getLatLng().distanceTo(markerAlerte.getLatLng());
            const distKm = distM / 1000;

            // Label distance sur la ligne
            const midLat = (eLat + alerteLat) / 2;
            const midLng = (eLng + alerteLng) / 2;
            L.marker([midLat, midLng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="
                        background:#0B1E3D; color:#fff;
                        padding:4px 10px; border-radius:8px;
                        font-size:12px; font-weight:700;
                        font-family:'Inter',sans-serif;
                        white-space:nowrap;
                        box-shadow:0 2px 8px rgba(0,0,0,.3);
                    ">📏 ${distKm.toFixed(2)} km</div>`,
                    iconAnchor: [40, 12]
                })
            }).addTo(map);

            // Ajustement vue
            map.fitBounds([[eLat, eLng], [alerteLat, alerteLng]], { padding: [50, 50] });

            // Route info box
            document.getElementById('riDist').textContent = distKm.toFixed(2) + ' km';
            document.getElementById('routeInfo').classList.add('show');
            document.getElementById('mapSubtitle').textContent =
                'Votre position et l\'urgence — Distance : ' + distKm.toFixed(2) + ' km';

            // Bouton Google Maps
            const gmLink = `https://www.google.com/maps/dir/${eLat},${eLng}/${alerteLat},${alerteLng}`;
            const btn = document.getElementById('gmapsBtn');
            btn.href = gmLink;
            btn.style.display = 'inline-flex';
            feather.replace({ width: 14, height: 14 });

        }, function() {
            // Géoloc refusée
            map.setView([alerteLat, alerteLng], 14);
            document.getElementById('mapSubtitle').textContent = 'Position GPS indisponible — Urgence localisée';

            // Bouton Google Maps sans position équipe (juste destination)
            const gmLink = `https://www.google.com/maps/search/?api=1&query=${alerteLat},${alerteLng}`;
            const btn = document.getElementById('gmapsBtn');
            btn.href = gmLink;
            btn.style.display = 'inline-flex';
            feather.replace({ width: 14, height: 14 });
        });
    } else {
        map.setView([alerteLat, alerteLng], 14);
    }

} else {
    // Pas de GPS → carte Brazzaville + message
    map.setView([-4.2634, 15.2429], 12);
    L.marker([-4.2634, 15.2429], { icon: iconEquipe })
        .addTo(map)
        .bindPopup('<b>Brazzaville</b><br>Aucune coordonnée GPS pour cette urgence')
        .openPopup();
    document.getElementById('mapSubtitle').textContent = 'Aucune coordonnée GPS — localisation textuelle uniquement';
}
</script>
@endpush

@endsection
