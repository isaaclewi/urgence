@extends('services.master')

@section('title', ($service->nom ?? 'CongoAssist') . ' — Urgences signalées')
@section('page-title', 'Urgences signalées')
@section('page-subtitle', 'Gérez et suivez toutes les alertes en temps réel')

{{-- ══════════════════════════════
     SIDEBAR
══════════════════════════════ --}}
@section('sidebar')

<div class="sb-section-label">Principal</div>

<a href="{{ route('services.compte') }}" class="sidebar-link">
    <i data-feather="home"></i>
    <span class="sb-lbl">Tableau de bord</span>
</a>

<a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link active">
    <i data-feather="bell"></i>
    <span class="sb-lbl">Urgences signalées</span>
    @if($alertes->where('statut', 'en attente')->count() > 0)
        <span class="sb-badge" id="badge-attente">{{ $alertes->where('statut', 'en attente')->count() }}</span>
    @endif
</a>

<a href="{{ route('services.citoyens') }}" class="sidebar-link">
    <i data-feather="users"></i>
    <span class="sb-lbl">Citoyens</span>
</a>

<a href="{{ route('services.forum.index') }}" class="sidebar-link">
    <i data-feather="message-square"></i>
    <span class="sb-lbl">Forum</span>
</a>

<a href="{{ route('services.actualite') }}" class="sidebar-link">
    <i data-feather="rss"></i>
    <span class="sb-lbl">Actualités</span>
</a>

<a href="{{ route('services.profil') }}" class="sidebar-link">
    <i data-feather="settings"></i>
    <span class="sb-lbl">Gestion interne</span>
</a>

<div class="sb-divider"></div>

<a href="{{ route('services.logout') }}" class="sidebar-link danger">
    <i data-feather="log-out"></i>
    <span class="sb-lbl">Déconnexion</span>
</a>

@endsection

{{-- ══════════════════════════════
     CONTENT
══════════════════════════════ --}}
@section('content')

@push('styles')
<style>
/* ─── Stats ─── */
.urg-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
@media (max-width: 1023px) { .urg-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 479px)  { .urg-stats { grid-template-columns: 1fr; } }

.icon-red    { background: #FEE2E2; color: #DC2626; }
.icon-yellow { background: #FEF3C7; color: #D97706; }
.icon-green  { background: #D1FAE5; color: #059669; }
.icon-blue   { background: #DBEAFE; color: #2563EB; }

/* ─── Table ─── */
.table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

.data-table .th-icon {
    display: flex; align-items: center; gap: 5px;
}
.data-table .th-icon-center {
    display: flex; align-items: center; justify-content: center; gap: 5px;
}

/* Type badge */
.type-badge {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    background: #DBEAFE;
    color: #1E40AF;
    white-space: nowrap;
}

/* Location cell */
.loc-cell {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--text-sec);
}
.loc-cell i { font-size: 13px; color: #EF4444; flex-shrink: 0; }

/* Date cell */
.date-cell-main { font-size: 13px; font-weight: 600; color: var(--text); }
.date-cell-sub  { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

/* Status select */
.status-select {
    width: 100%;
    padding: 7px 10px;
    border: 1.5px solid var(--border);
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    font-family: inherit;
    color: var(--text);
    background: var(--surface);
    outline: none;
    cursor: pointer;
    transition: border-color .17s, box-shadow .17s;
}
.status-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(29,184,122,.1);
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 52px 20px;
}
.empty-state-icon {
    width: 68px; height: 68px;
    border-radius: 50%;
    background: var(--surface2);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
}
.empty-state-icon i { font-size: 30px; color: var(--text-muted); }
.empty-state p { font-size: 14px; font-weight: 600; color: var(--text-sec); margin-bottom: 4px; }
.empty-state span { font-size: 12.5px; color: var(--text-muted); }

/* ─── Modals ─── */
.modal-backdrop {
    position: fixed; inset: 0; z-index: 80;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center; justify-content: center;
    padding: 16px;
}
.modal-backdrop.open { display: flex; }

/* Modal citoyen */
.cit-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--accent);
    margin: 0 auto 12px;
    display: block;
}
.cit-name {
    font-family: 'Sora', sans-serif;
    font-size: 18px; font-weight: 700;
    color: var(--text); text-align: center;
    margin-bottom: 20px;
}
.cit-info-row {
    display: flex; align-items: center; gap: 12px;
    padding: 11px 13px;
    background: var(--surface2);
    border-radius: 9px;
    margin-bottom: 8px;
}
.cit-info-row:last-child { margin-bottom: 0; }
.cit-info-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cit-info-icon i { font-size: 15px; }
.cit-info-label { font-size: 10.5px; font-weight: 600; color: var(--text-muted); }
.cit-info-value { font-size: 13px; font-weight: 600; color: var(--text); margin-top: 1px; }

/* Modal alerte */
.alerte-titre {
    font-family: 'Sora', sans-serif;
    font-size: 17px; font-weight: 700;
    color: var(--text); margin-bottom: 8px;
}
.alerte-desc {
    font-size: 13.5px; color: var(--text-sec);
    line-height: 1.65; margin-bottom: 16px;
}
.alerte-photo {
    width: 100%; max-height: 240px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid var(--border);
    margin-bottom: 12px;
}
.alerte-audio {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 4px;
}

/* ─── Toast notification nouvelle alerte ─── */
.toast-new-alerte {
    position: fixed;
    top: 20px; right: 20px;
    z-index: 999;
    background: var(--accent);
    color: #fff;
    padding: 14px 18px;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,.25);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    font-weight: 600;
    transform: translateX(120%);
    transition: transform .35s cubic-bezier(.34,1.56,.64,1);
    cursor: pointer;
    max-width: 340px;
}
.toast-new-alerte.show { transform: translateX(0); }
.toast-new-alerte .toast-icon {
    width: 32px; height: 32px;
    background: rgba(255,255,255,.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* ─── Ligne nouvelle alerte (highlight) ─── */
@keyframes rowHighlight {
    0%   { background: rgba(29,184,122,.18); }
    100% { background: transparent; }
}
.tr-new { animation: rowHighlight 3s ease forwards; }

/* ─── Indicateur live ─── */
.live-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    color: #10B981;
    background: rgba(16,185,129,.1);
    padding: 4px 10px;
    border-radius: 100px;
    margin-left: 8px;
}
.live-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #10B981;
    animation: livePulse 1.4s infinite;
}
@keyframes livePulse {
    0%,100% { opacity: 1; transform: scale(1); }
    50%      { opacity: .4; transform: scale(1.6); }
}
</style>
@endpush

{{-- Toast notification --}}
<div id="toastNewAlerte" class="toast-new-alerte" onclick="scrollToNew()">
    <div class="toast-icon"><i data-feather="bell" style="width:16px;height:16px;"></i></div>
    <div>
        <div id="toastTitle">Nouvelle urgence reçue</div>
        <div id="toastSub" style="font-size:11px; opacity:.8; margin-top:2px;"></div>
    </div>
</div>

{{-- Flash success --}}
@if(session('success'))
<div class="alert alert-success anim-fade" style="margin-bottom:16px;">
    <i data-feather="check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

{{-- ── Stat Cards ── --}}
<div class="urg-stats anim-fade">
    <div class="stat-card red">
        <div class="sc-icon icon-red"><i data-feather="alert-circle"></i></div>
        <div class="sc-value" id="stat-attente">{{ $alertes->where('statut', 'en attente')->count() }}</div>
        <div class="sc-label">En attente</div>
    </div>
    <div class="stat-card yellow">
        <div class="sc-icon icon-yellow"><i data-feather="clock"></i></div>
        <div class="sc-value" id="stat-encours">{{ $alertes->where('statut', 'pris en charge')->count() }}</div>
        <div class="sc-label">Pris en charge</div>
    </div>
    <div class="stat-card green">
        <div class="sc-icon icon-green"><i data-feather="check-circle"></i></div>
        <div class="sc-value" id="stat-terminee">{{ $alertes->where('statut', 'terminee')->count() }}</div>
        <div class="sc-label">Terminées</div>
    </div>
    <div class="stat-card blue">
        <div class="sc-icon icon-blue"><i data-feather="list"></i></div>
        <div class="sc-value" id="stat-total">{{ $alertes->count() }}</div>
        <div class="sc-label">Total</div>
    </div>
</div>

{{-- ── Table Card ── --}}
<div class="content-card anim-slide">

    <div class="cc-header">
        <div>
            <div class="cc-title" style="display:flex;align-items:center;flex-wrap:wrap;gap:6px;">
                <i data-feather="alert-triangle"></i>
                Liste des urgences
                <span class="live-indicator">
                    <span class="live-dot"></span>
                    LIVE
                </span>
            </div>
            <div class="cc-subtitle" id="subtitle-count">{{ $alertes->count() }} alerte(s) au total</div>
        </div>
        <a href="{{ route('services.interventions.journalieres') }}" class="btn btn-accent btn-sm">
            <i data-feather="activity"></i>
            Interventions journalières
        </a>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="alertesTable">
            <thead>
                <tr>
                    <th><div class="th-icon"><i data-feather="hash"></i> ID</div></th>
                    <th><div class="th-icon"><i data-feather="file-text"></i> Titre</div></th>
                    <th><div class="th-icon"><i data-feather="tag"></i> Type</div></th>
                    <th><div class="th-icon"><i data-feather="map-pin"></i> Localisation</div></th>
                    <th><div class="th-icon"><i data-feather="calendar"></i> Date</div></th>
                    <th style="text-align:center;"><div class="th-icon-center"><i data-feather="user"></i> Citoyen</div></th>
                    <th style="text-align:center;"><div class="th-icon-center"><i data-feather="info"></i> Statut</div></th>
                    <th style="text-align:center;"><div class="th-icon-center"><i data-feather="eye"></i> Alerte</div></th>
                    <th><div class="th-icon"><i data-feather="sliders"></i> Action</div></th>
                    <th><div class="th-icon"><i data-feather="git-branch"></i> Affectation</div></th>
                </tr>
            </thead>
            <tbody id="alertesTbody">
                @forelse($alertes as $alerte)
                <tr id="alerte-row-{{ $alerte->id }}">
                    {{-- ID --}}
                    <td><span style="font-weight:700;color:var(--text);">#{{ $alerte->id }}</span></td>

                    {{-- Titre --}}
                    <td><span style="font-weight:600;font-size:13px;">{{ $alerte->titre }}</span></td>

                    {{-- Type --}}
                    <td><span class="type-badge">{{ $alerte->type_alerte }}</span></td>

                    {{-- Localisation --}}
                    <td>
                        <div class="loc-cell">
                            <i data-feather="map-pin"></i>
                            {{ $alerte->localisation }}
                        </div>
                    </td>

                    {{-- Date --}}
                    <td>
                        <div class="date-cell-main">{{ \Carbon\Carbon::parse($alerte->created_at)->format('d/m/Y') }}</div>
                        <div class="date-cell-sub">{{ \Carbon\Carbon::parse($alerte->created_at)->format('H:i') }}</div>
                    </td>

                    {{-- Citoyen --}}
                    <td style="text-align:center;">
                        @if($alerte->citoyen)
                            <button class="btn btn-accent btn-sm"
                                onclick="showCitoyenModal(
                                    '{{ addslashes($alerte->citoyen->nom) }}',
                                    '{{ addslashes($alerte->citoyen->prenom) }}',
                                    '{{ addslashes($alerte->citoyen->email) }}',
                                    '{{ addslashes($alerte->citoyen->telephone) }}',
                                    '{{ addslashes($alerte->citoyen->adresse ?? 'Adresse non renseignée') }}',
                                    '{{ asset($alerte->citoyen->photo_profil ?? 'medias/default.jpg') }}'
                                )">
                                <i data-feather="eye"></i>
                                Voir
                            </button>
                        @else
                            <span style="font-size:12px;color:var(--text-muted);font-style:italic;">Anonyme</span>
                        @endif
                    </td>

                    {{-- Statut --}}
                    <td style="text-align:center;">
                        @if($alerte->statut === 'en attente')
                            <span class="pill pill-red"><i data-feather="alert-circle"></i> En attente</span>
                        @elseif($alerte->statut === 'pris en charge')
                            <span class="pill pill-yellow"><i data-feather="clock"></i> Pris en charge</span>
                        @else
                            <span class="pill pill-green"><i data-feather="check-circle"></i> Terminée</span>
                        @endif
                    </td>

                    {{-- Voir alerte --}}
                    <td style="text-align:center;">
                        <button class="btn btn-outline btn-sm"
                            onclick="showAlerteModal(
                                '{{ addslashes($alerte->titre) }}',
                                '{{ addslashes($alerte->description) }}',
                                '{{ $alerte->media_photo ? asset($alerte->media_photo) : '' }}',
                                '{{ $alerte->media_vocal ? asset($alerte->media_vocal) : '' }}'
                            )">
                            <i data-feather="file-text"></i>
                            Détail
                        </button>
                    </td>

                    {{-- Changer statut --}}
                    <td>
                        <form method="POST" action="{{ route('services.urgenceSignaleeUpdate', $alerte->id) }}">
                            @csrf
                            <select name="statut" class="status-select" onchange="this.form.submit()">
                                <option value="en attente"     {{ $alerte->statut === 'en attente'     ? 'selected' : '' }}>En attente</option>
                                <option value="pris en charge" {{ $alerte->statut === 'pris en charge' ? 'selected' : '' }}>Pris en charge</option>
                                <option value="terminee"       {{ $alerte->statut === 'terminee'       ? 'selected' : '' }}>Terminée</option>
                            </select>
                        </form>
                    </td>

                    {{-- Affectation --}}
                    <td style="text-align:center;">
                        <button class="btn btn-warning btn-sm" onclick="openAffectModal({{ $alerte->id }})">
                            <i data-feather="share-2"></i>
                            Affecter
                        </button>
                    </td>
                </tr>
                @empty
                <tr id="empty-row">
                    <td colspan="10">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i data-feather="inbox"></i></div>
                            <p>Aucune alerte signalée</p>
                            <span>Les nouvelles urgences apparaîtront ici automatiquement</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════
     MODAL — Citoyen
══════════════════════════════ --}}
<div id="citoyenModal" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">
                <span style="display:flex;align-items:center;gap:8px;">
                    <i data-feather="user" style="color:var(--accent);"></i>
                    Informations citoyen
                </span>
            </div>
            <button class="btn btn-outline btn-icon" onclick="closeCitoyenModal()"><i data-feather="x"></i></button>
        </div>
        <div class="modal-body">
            <img id="photoCitoyen" src="" alt="Photo citoyen" class="cit-avatar">
            <div id="nomComplet" class="cit-name"></div>
            <div class="cit-info-row">
                <div class="cit-info-icon icon-blue"><i data-feather="mail"></i></div>
                <div>
                    <div class="cit-info-label">Email</div>
                    <div id="emailCitoyen" class="cit-info-value"></div>
                </div>
            </div>
            <div class="cit-info-row">
                <div class="cit-info-icon icon-green"><i data-feather="phone"></i></div>
                <div>
                    <div class="cit-info-label">Téléphone</div>
                    <div id="telCitoyen" class="cit-info-value"></div>
                </div>
            </div>
            <div class="cit-info-row">
                <div class="cit-info-icon" style="background:#EDE9FE;color:#7C3AED;"><i data-feather="map-pin"></i></div>
                <div>
                    <div class="cit-info-label">Adresse</div>
                    <div id="adresseCitoyen" class="cit-info-value"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-accent" onclick="closeCitoyenModal()">Fermer</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     MODAL — Alerte
══════════════════════════════ --}}
<div id="alerteModal" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-box" style="max-width:540px;">
        <div class="modal-header">
            <div class="modal-title">
                <span style="display:flex;align-items:center;gap:8px;">
                    <i data-feather="alert-triangle" style="color:var(--accent);"></i>
                    Détail de l'alerte
                </span>
            </div>
            <button class="btn btn-outline btn-icon" onclick="closeAlerteModal()"><i data-feather="x"></i></button>
        </div>
        <div class="modal-body">
            <div id="alerteTitre" class="alerte-titre"></div>
            <p id="alerteDescription" class="alerte-desc"></p>
            <img id="alertePhoto" src="" alt="Photo alerte" class="alerte-photo" style="display:none;">
            <audio id="alerteVocal" controls class="alerte-audio" style="display:none;">
                <source id="alerteVocalSource" src="" type="audio/webm">
            </audio>
        </div>
        <div class="modal-footer">
            <button class="btn btn-accent" onclick="closeAlerteModal()">Fermer</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     MODAL — Affectation
══════════════════════════════ --}}
<div id="affectModal" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Affecter une équipe</div>
            <button onclick="closeAffectModal()" class="btn btn-outline btn-icon"><i data-feather="x"></i></button>
        </div>
        <form id="affectForm" method="POST">
            @csrf
            <div class="modal-body">
                <label>Équipe</label>
                <select name="service_destination_id" class="status-select" required>
                    @foreach(\App\Models\Services::where('parent_service_id', session('service_id'))->get() as $s)
                        <option value="{{ $s->id }}">{{ $s->nom }}</option>
                    @endforeach
                </select>
                <label style="margin-top:10px;">Commentaire</label>
                <textarea name="commentaire" class="status-select"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-accent">Affecter</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
feather.replace({ width: 14, height: 14 });

// ════════════════════════════════════════════════════════════
//  COMPTEURS en mémoire (synchronisés avec les stats affichées)
// ════════════════════════════════════════════════════════════
let counts = {
    attente:  parseInt(document.getElementById('stat-attente').textContent)  || 0,
    encours:  parseInt(document.getElementById('stat-encours').textContent)  || 0,
    terminee: parseInt(document.getElementById('stat-terminee').textContent) || 0,
    total:    parseInt(document.getElementById('stat-total').textContent)    || 0,
};

// ID de la dernière alerte chargée côté serveur
let lastAlerteId = {{ $alertes->isNotEmpty() ? $alertes->first()->id : 0 }};

// ════════════════════════════════════════════════════════════
//  CONSTRUCTION D'UNE LIGNE HTML pour une nouvelle alerte
// ════════════════════════════════════════════════════════════
function buildRow(a) {
    const citoyenBtn = a.citoyen
        ? `<button class="btn btn-accent btn-sm"
                onclick="showCitoyenModal(
                    '${escJs(a.citoyen.nom)}','${escJs(a.citoyen.prenom)}',
                    '${escJs(a.citoyen.email)}','${escJs(a.citoyen.telephone)}',
                    '${escJs(a.citoyen.adresse)}','${escJs(a.citoyen.photo)}'
                )">
                <i data-feather="eye"></i> Voir
           </button>`
        : `<span style="font-size:12px;color:var(--text-muted);font-style:italic;">Anonyme</span>`;

    const statusPill = a.statut === 'en attente'
        ? `<span class="pill pill-red"><i data-feather="alert-circle"></i> En attente</span>`
        : a.statut === 'pris en charge'
        ? `<span class="pill pill-yellow"><i data-feather="clock"></i> Pris en charge</span>`
        : `<span class="pill pill-green"><i data-feather="check-circle"></i> Terminée</span>`;

    return `
    <tr id="alerte-row-${a.id}" class="tr-new">
        <td><span style="font-weight:700;color:var(--text);">#${a.id}</span></td>
        <td><span style="font-weight:600;font-size:13px;">${escHtml(a.titre)}</span></td>
        <td><span class="type-badge">${escHtml(a.type_alerte || '')}</span></td>
        <td>
            <div class="loc-cell">
                <i data-feather="map-pin"></i>
                ${escHtml(a.localisation || '')}
            </div>
        </td>
        <td>
            <div class="date-cell-main">${a.created_at.split(' ')[0]}</div>
            <div class="date-cell-sub">${a.created_at.split(' ')[1] || ''}</div>
        </td>
        <td style="text-align:center;">${citoyenBtn}</td>
        <td style="text-align:center;">${statusPill}</td>
        <td style="text-align:center;">
            <button class="btn btn-outline btn-sm"
                onclick="showAlerteModal(
                    '${escJs(a.titre)}','',
                    '${escJs(a.media_photo || '')}','${escJs(a.media_vocal || '')}'
                )">
                <i data-feather="file-text"></i> Détail
            </button>
        </td>
        <td>
            <form method="POST" action="/services/urgences/${a.id}/update">
                @csrf
                <select name="statut" class="status-select" onchange="this.form.submit()">
                    <option value="en attente" selected>En attente</option>
                    <option value="pris en charge">Pris en charge</option>
                    <option value="terminee">Terminée</option>
                </select>
            </form>
        </td>
        <td style="text-align:center;">
            <button class="btn btn-warning btn-sm" onclick="openAffectModal(${a.id})">
                <i data-feather="share-2"></i> Affecter
            </button>
        </td>
    </tr>`;
}

// ════════════════════════════════════════════════════════════
//  SERVER-SENT EVENTS — écouter les nouvelles alertes
// ════════════════════════════════════════════════════════════
function startAlerteStream() {
    const url = `{{ route('services.urgences.stream') }}?lastId=${lastAlerteId}`;
    const es  = new EventSource(url);

    es.onmessage = function(e) {
        const alerte = JSON.parse(e.data);

        // Supprimer la ligne "vide" si elle existe
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();

        // Insérer la nouvelle ligne EN HAUT du tableau
        const tbody = document.getElementById('alertesTbody');
        tbody.insertAdjacentHTML('afterbegin', buildRow(alerte));
        feather.replace({ width: 14, height: 14 });

        // Mettre à jour lastId
        lastAlerteId = Math.max(lastAlerteId, alerte.id);

        // Mettre à jour les compteurs
        counts.total++;
        if (alerte.statut === 'en attente') counts.attente++;
        updateCounters();

        // Badge sidebar
        const badge = document.getElementById('badge-attente');
        if (badge) {
            badge.textContent = counts.attente;
            badge.style.display = counts.attente > 0 ? '' : 'none';
        }

        // Notification sonore (bip court)
        playBeep();

        // Toast
        showToast(alerte.titre, alerte.localisation || alerte.type_alerte || '');
    };

    es.onerror = function() {
        // Reconnexion automatique après 5s si la connexion SSE tombe
        es.close();
        setTimeout(startAlerteStream, 5000);
    };
}

// ════════════════════════════════════════════════════════════
//  HELPERS
// ════════════════════════════════════════════════════════════
function updateCounters() {
    document.getElementById('stat-attente').textContent  = counts.attente;
    document.getElementById('stat-encours').textContent  = counts.encours;
    document.getElementById('stat-terminee').textContent = counts.terminee;
    document.getElementById('stat-total').textContent    = counts.total;
    document.getElementById('subtitle-count').textContent = counts.total + ' alerte(s) au total';
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}
function escJs(str) {
    return String(str).replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'\\"');
}

let toastTimer = null;
function showToast(titre, sub) {
    document.getElementById('toastTitle').textContent = '🚨 ' + titre;
    document.getElementById('toastSub').textContent   = sub;
    const t = document.getElementById('toastNewAlerte');
    t.classList.add('show');
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 6000);
}
function scrollToNew() {
    const first = document.querySelector('.tr-new');
    if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.getElementById('toastNewAlerte').classList.remove('show');
}

function playBeep() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = 880;
        osc.type = 'sine';
        gain.gain.setValueAtTime(.18, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + .35);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + .35);
    } catch(e) {}
}

// ════════════════════════════════════════════════════════════
//  MODALS
// ════════════════════════════════════════════════════════════
function showCitoyenModal(nom, prenom, email, tel, adresse, photo) {
    document.getElementById('nomComplet').textContent     = nom + ' ' + prenom;
    document.getElementById('emailCitoyen').textContent   = email;
    document.getElementById('telCitoyen').textContent     = tel;
    document.getElementById('adresseCitoyen').textContent = adresse;
    document.getElementById('photoCitoyen').src           = photo;
    document.getElementById('citoyenModal').classList.add('open');
    feather.replace({ width: 14, height: 14 });
}
function closeCitoyenModal() { document.getElementById('citoyenModal').classList.remove('open'); }

function showAlerteModal(titre, description, photo, vocal) {
    document.getElementById('alerteTitre').textContent      = titre;
    document.getElementById('alerteDescription').textContent = description;
    const photoEl = document.getElementById('alertePhoto');
    if (photo) { photoEl.src = photo; photoEl.style.display = 'block'; }
    else        { photoEl.style.display = 'none'; }
    const vocalEl     = document.getElementById('alerteVocal');
    const vocalSource = document.getElementById('alerteVocalSource');
    if (vocal) { vocalSource.src = vocal; vocalEl.load(); vocalEl.style.display = 'block'; }
    else        { vocalEl.style.display = 'none'; }
    document.getElementById('alerteModal').classList.add('open');
    feather.replace({ width: 14, height: 14 });
}
function closeAlerteModal() {
    document.getElementById('alerteModal').classList.remove('open');
    const v = document.getElementById('alerteVocal');
    v.pause(); v.currentTime = 0;
}

document.querySelectorAll('.modal-backdrop').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-backdrop.open').forEach(m => m.classList.remove('open'));
});

function openAffectModal(id) {
    document.getElementById('affectForm').action = '/services/urgence/affecter/' + id;
    document.getElementById('affectModal').classList.add('open');
}
function closeAffectModal() { document.getElementById('affectModal').classList.remove('open'); }

// ════════════════════════════════════════════════════════════
//  DÉMARRAGE
// ════════════════════════════════════════════════════════════
startAlerteStream();
</script>
@endpush
