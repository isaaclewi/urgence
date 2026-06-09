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
        <span class="sb-badge">{{ $alertes->where('statut', 'en attente')->count() }}</span>
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

/* Modal citoyen — avatar + info rows */
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

/* Modal alerte — media */
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
</style>
@endpush

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
        <div class="sc-icon icon-red">
            <i data-feather="alert-circle"></i>
        </div>
        <div class="sc-value">{{ $alertes->where('statut', 'en attente')->count() }}</div>
        <div class="sc-label">En attente</div>
    </div>

    <div class="stat-card yellow">
        <div class="sc-icon icon-yellow">
            <i data-feather="clock"></i>
        </div>
        <div class="sc-value">{{ $alertes->where('statut', 'pris en charge')->count() }}</div>
        <div class="sc-label">Pris en charge</div>
    </div>

    <div class="stat-card green">
        <div class="sc-icon icon-green">
            <i data-feather="check-circle"></i>
        </div>
        <div class="sc-value">{{ $alertes->where('statut', 'terminee')->count() }}</div>
        <div class="sc-label">Terminées</div>
    </div>

    <div class="stat-card blue">
        <div class="sc-icon icon-blue">
            <i data-feather="list"></i>
        </div>
        <div class="sc-value">{{ $alertes->count() }}</div>
        <div class="sc-label">Total</div>
    </div>

</div>

{{-- ── Table Card ── --}}
<div class="content-card anim-slide">

    <div class="cc-header">
        <div>
            <div class="cc-title">
                <i data-feather="alert-triangle"></i>
                Liste des urgences
            </div>
            <div class="cc-subtitle">{{ $alertes->count() }} alerte(s) au total</div>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
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
                </tr>
            </thead>
            <tbody>
                @forelse($alertes as $alerte)
                <tr>
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
                                <option value="en attente"    {{ $alerte->statut === 'en attente'    ? 'selected' : '' }}>En attente</option>
                                <option value="pris en charge" {{ $alerte->statut === 'pris en charge' ? 'selected' : '' }}>Pris en charge</option>
                                <option value="terminee"      {{ $alerte->statut === 'terminee'      ? 'selected' : '' }}>Terminée</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i data-feather="inbox"></i>
                            </div>
                            <p>Aucune alerte signalée</p>
                            <span>Les nouvelles urgences apparaîtront ici</span>
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
<div id="citoyenModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="cit-modal-title">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="cit-modal-title">
                <span style="display:flex;align-items:center;gap:8px;">
                    <i data-feather="user" style="color:var(--accent);"></i>
                    Informations citoyen
                </span>
            </div>
            <button class="btn btn-outline btn-icon" onclick="closeCitoyenModal()" aria-label="Fermer">
                <i data-feather="x"></i>
            </button>
        </div>
        <div class="modal-body">
            <img id="photoCitoyen" src="" alt="Photo citoyen" class="cit-avatar">
            <div id="nomComplet" class="cit-name"></div>

            <div class="cit-info-row">
                <div class="cit-info-icon icon-blue">
                    <i data-feather="mail"></i>
                </div>
                <div>
                    <div class="cit-info-label">Email</div>
                    <div id="emailCitoyen" class="cit-info-value"></div>
                </div>
            </div>

            <div class="cit-info-row">
                <div class="cit-info-icon icon-green">
                    <i data-feather="phone"></i>
                </div>
                <div>
                    <div class="cit-info-label">Téléphone</div>
                    <div id="telCitoyen" class="cit-info-value"></div>
                </div>
            </div>

            <div class="cit-info-row">
                <div class="cit-info-icon icon-purple" style="background:#EDE9FE;color:#7C3AED;">
                    <i data-feather="map-pin"></i>
                </div>
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
<div id="alerteModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="alerte-modal-title">
    <div class="modal-box" style="max-width:540px;">
        <div class="modal-header">
            <div class="modal-title" id="alerte-modal-title">
                <span style="display:flex;align-items:center;gap:8px;">
                    <i data-feather="alert-triangle" style="color:var(--accent);"></i>
                    Détail de l'alerte
                </span>
            </div>
            <button class="btn btn-outline btn-icon" onclick="closeAlerteModal()" aria-label="Fermer">
                <i data-feather="x"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="alerteTitre" class="alerte-titre"></div>
            <p id="alerteDescription" class="alerte-desc"></p>
            <img id="alertePhoto" src="" alt="Photo alerte" class="alerte-photo" style="display:none;">
            <audio id="alerteVocal" controls class="alerte-audio" style="display:none;">
                <source id="alerteVocalSource" src="" type="audio/webm">
                Votre navigateur ne supporte pas la lecture audio.
            </audio>
        </div>
        <div class="modal-footer">
            <button class="btn btn-accent" onclick="closeAlerteModal()">Fermer</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
feather.replace({ width: 14, height: 14 });

/* ── Modal Citoyen ── */
function showCitoyenModal(nom, prenom, email, tel, adresse, photo) {
    document.getElementById('nomComplet').textContent    = nom + ' ' + prenom;
    document.getElementById('emailCitoyen').textContent  = email;
    document.getElementById('telCitoyen').textContent    = tel;
    document.getElementById('adresseCitoyen').textContent = adresse;
    document.getElementById('photoCitoyen').src          = photo;
    document.getElementById('citoyenModal').classList.add('open');
    feather.replace({ width: 14, height: 14 });
}
function closeCitoyenModal() {
    document.getElementById('citoyenModal').classList.remove('open');
}

/* ── Modal Alerte ── */
function showAlerteModal(titre, description, photo, vocal) {
    document.getElementById('alerteTitre').textContent      = titre;
    document.getElementById('alerteDescription').textContent = description;

    const photoEl = document.getElementById('alertePhoto');
    if (photo) { photoEl.src = photo; photoEl.style.display = 'block'; }
    else        { photoEl.style.display = 'none'; }

    const vocalEl     = document.getElementById('alerteVocal');
    const vocalSource = document.getElementById('alerteVocalSource');
    if (vocal) {
        vocalSource.src = vocal;
        vocalEl.load();
        vocalEl.style.display = 'block';
    } else {
        vocalEl.style.display = 'none';
    }

    document.getElementById('alerteModal').classList.add('open');
    feather.replace({ width: 14, height: 14 });
}
function closeAlerteModal() {
    document.getElementById('alerteModal').classList.remove('open');
    const v = document.getElementById('alerteVocal');
    v.pause(); v.currentTime = 0;
}

/* Fermer modals avec ESC ou clic backdrop */
document.querySelectorAll('.modal-backdrop').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-backdrop.open').forEach(m => m.classList.remove('open'));
});
</script>
@endpush
