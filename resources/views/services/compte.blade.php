@extends('services.master')

@section('title', $service->nom ?? 'CongoAssist')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de votre activité en temps réel')

{{-- ══════════════════════════════
     SIDEBAR
══════════════════════════════ --}}
@section('sidebar')

<div class="sb-section-label">Principal</div>

<a href="{{ route('services.compte') }}" class="sidebar-link active">
    <i data-feather="home"></i>
    <span class="sb-lbl">Tableau de bord</span>
</a>

<div class="sb-section-label">Gestion des services</div>

<a href="{{ route('services.equipes.index') }}" class="sidebar-link">
    <i data-feather="list"></i>
    <span class="sb-lbl">Liste des équipes</span>
</a>

<a href="{{ route('services.equipes.create') }}" class="sidebar-link">
    <i data-feather="plus-circle"></i>
    <span class="sb-lbl">Créer une équipe</span>
</a>

<a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link">
    <i data-feather="bell"></i>
    <span class="sb-lbl">Urgences signalées</span>
    @if(($stats['urgences_en_cours'] ?? 0) > 0)
        <span class="sb-badge">{{ $stats['urgences_en_cours'] }}</span>
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

@if(($service->role ?? '') === 'hopital')
<div class="sb-divider"></div>
<div class="sb-section-label">Services médicaux</div>

<a href="{{ route('services.vaccinationIndex') }}" class="sidebar-link">
    <i data-feather="shield"></i>
    <span class="sb-lbl">Vaccinations</span>
</a>

<a href="{{ route('services.citoyensBilan') }}" class="sidebar-link">
    <i data-feather="activity"></i>
    <span class="sb-lbl">Bilan santé</span>
</a>
@endif

<div class="sb-divider"></div>

<a href="{{ route('services.logout') }}" class="sidebar-link danger">
    <i data-feather="log-out"></i>
    <span class="sb-lbl">Déconnexion</span>
</a>


@endsection

{{-- ══════════════════════════════
     PAGE ACTIONS (topbar droite)
══════════════════════════════ --}}
@section('page-actions')
<button class="btn btn-outline btn-sm" onclick="location.reload()">
    <i data-feather="refresh-cw"></i>
    Actualiser
</button>
@if(($service->role ?? '') === 'hopital')
<a href="{{ route('services.vaccinationEnfantsIndex') }}" class="btn btn-accent btn-sm">
    <i data-feather="shield"></i>
    Vaccination enfants
</a>
@endif
@endsection

{{-- ══════════════════════════════
     CONTENT
══════════════════════════════ --}}
@section('content')

@push('styles')
<style>
/* ─── Dashboard-specific ─── */
.dash-grid-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
@media (max-width: 1199px) { .dash-grid-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 479px)  { .dash-grid-stats { grid-template-columns: 1fr; } }

.dash-grid-main {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 14px;
    margin-bottom: 20px;
}
@media (max-width: 1023px) { .dash-grid-main { grid-template-columns: 1fr; } }

/* Stat card icon colors */
.icon-accent  { background: rgba(29,184,122,.1);  color: var(--accent); }
.icon-green   { background: #D1FAE5; color: #059669; }
.icon-blue    { background: #DBEAFE; color: #2563EB; }
.icon-purple  { background: #EDE9FE; color: #7C3AED; }

/* Alert item */
.alert-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 13px 16px;
    border-radius: 10px;
    background: var(--surface2);
    border-left: 3px solid var(--accent);
    transition: background .15s;
    margin-bottom: 8px;
}
.alert-item:last-child { margin-bottom: 0; }
.alert-item:hover { background: #EEF2F8; }
.alert-item-icon {
    width: 38px; height: 38px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.alert-item-icon i { font-size: 18px; }
.alert-item-body { flex: 1; min-width: 0; }
.alert-item-title {
    font-size: 13.5px; font-weight: 600;
    color: var(--text); white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.alert-item-meta {
    display: flex; align-items: center; gap: 6px;
    font-size: 11.5px; color: var(--text-muted);
    margin-top: 3px; flex-wrap: wrap;
}
.alert-item-meta i { font-size: 11px; }
.alert-meta-sep { color: var(--border-mid); }

/* Map placeholder */
.map-placeholder {
    height: 180px;
    background: linear-gradient(135deg, #EEF6FF 0%, #F0FBF4 100%);
    border: 1px solid var(--border);
    border-radius: 10px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 8px; color: var(--text-muted);
}
.map-placeholder i { font-size: 32px; color: #60A5FA; }
.map-placeholder p { font-size: 13px; font-weight: 600; color: var(--text-sec); }
.map-placeholder span { font-size: 11.5px; }

/* Quick stats list */
.qs-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 0;
    border-bottom: 1px solid var(--border);
    font-size: 13px;
}
.qs-item:last-child { border-bottom: none; padding-bottom: 0; }
.qs-label { color: var(--text-sec); }
.qs-value { font-weight: 700; color: var(--text); }
.qs-value.green { color: #059669; }

/* Chart placeholder */
.chart-placeholder {
    height: 200px;
    background: linear-gradient(135deg, #F5F0FF 0%, #EFF6FF 100%);
    border: 1px solid var(--border);
    border-radius: 10px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 8px; color: var(--text-muted);
}
.chart-placeholder i { font-size: 32px; color: #A78BFA; }
.chart-placeholder p { font-size: 13px; font-weight: 600; color: var(--text-sec); }
.chart-placeholder span { font-size: 11.5px; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 48px 20px;
}
.empty-state-icon {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: var(--surface2);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
}
.empty-state-icon i { font-size: 28px; color: var(--text-muted); }
.empty-state p { font-size: 14px; font-weight: 600; color: var(--text-sec); margin-bottom: 4px; }
.empty-state span { font-size: 12.5px; color: var(--text-muted); }

/* Refresh info */
.refresh-note {
    display: flex; align-items: center; gap: 6px;
    font-size: 11px; color: var(--text-muted);
    padding: 8px 10px;
    background: var(--surface2);
    border-radius: 7px;
    border: 1px solid var(--border);
    margin-top: 10px;
}
.refresh-note i { font-size: 11px; }
</style>
@endpush

{{-- ── Stat Cards ── --}}
<div class="dash-grid-stats anim-fade">

    {{-- Urgences en cours --}}
    <div class="stat-card accent">
        <div class="sc-icon icon-accent">
            <i data-feather="alert-triangle"></i>
        </div>
        <div class="sc-value">{{ $stats['urgences_en_cours'] ?? 0 }}</div>
        <div class="sc-label">Urgences en cours</div>
        <div class="sc-trend up">
            <i data-feather="trending-up"></i>
            +12% vs hier
        </div>
    </div>

    {{-- Urgences résolues --}}
    <div class="stat-card green">
        <div class="sc-icon icon-green">
            <i data-feather="check-circle"></i>
        </div>
        <div class="sc-value">{{ $stats['urgences_resolues'] ?? 0 }}</div>
        <div class="sc-label">Urgences résolues</div>
        <div class="sc-trend up">
            <i data-feather="check"></i>
            95% de résolution
        </div>
    </div>

    {{-- Citoyens actifs --}}
    <div class="stat-card blue">
        <div class="sc-icon icon-blue">
            <i data-feather="users"></i>
        </div>
        <div class="sc-value">{{ $stats['citoyens_actifs'] ?? 0 }}</div>
        <div class="sc-label">Citoyens actifs</div>
        <div class="sc-trend up">
            <i data-feather="user-plus"></i>
            +8 cette semaine
        </div>
    </div>

    {{-- Temps de réponse --}}
    <div class="stat-card purple">
        <div class="sc-icon icon-purple">
            <i data-feather="clock"></i>
        </div>
        <div class="sc-value">{{ $stats['temps_reponse'] ?? '—' }}</div>
        <div class="sc-label">Minutes (moy. réponse)</div>
        <div class="sc-trend up">
            <i data-feather="trending-down"></i>
            −2 min d'amélioration
        </div>
    </div>

</div>

{{-- ── Main grid : Signalements + Carte ── --}}
<div class="dash-grid-main">

    {{-- Derniers signalements --}}
    <div class="content-card anim-slide">
        <div class="cc-header">
            <div>
                <div class="cc-title">
                    <i data-feather="bell"></i>
                    Derniers signalements
                </div>
                <div class="cc-subtitle">Activité en temps réel</div>
            </div>
            <button class="btn btn-outline btn-sm">
                <i data-feather="filter"></i>
                Filtrer
            </button>
        </div>
        <div class="cc-body" style="padding:16px 18px;">

            @forelse($recentAlerts ?? [] as $alert)
            <div class="alert-item">
                <div class="alert-item-icon icon-accent">
                    <i data-feather="alert-circle"></i>
                </div>
                <div class="alert-item-body">
                    <div class="alert-item-title">{{ $alert['title'] }}</div>
                    <div class="alert-item-meta">
                        <i data-feather="tag"></i>
                        {{ $alert['type'] }}
                        <span class="alert-meta-sep">·</span>
                        <i data-feather="clock"></i>
                        {{ $alert['time'] }}
                        <span class="alert-meta-sep">·</span>
                        <i data-feather="map-pin"></i>
                        {{ $alert['location'] }}
                    </div>
                </div>
                <button class="btn btn-accent btn-sm">Traiter</button>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i data-feather="inbox"></i>
                </div>
                <p>Aucun signalement récent</p>
                <span>Les nouvelles alertes apparaîtront ici</span>
            </div>
            @endforelse

        </div>
    </div>

    {{-- Colonne droite : Carte + Quick stats + Actions --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Carte --}}
        <div class="content-card">
            <div class="cc-header">
                <div class="cc-title">
                    <i data-feather="map"></i>
                    Localisation
                </div>
            </div>
            <div class="cc-body">
                <div class="map-placeholder">
                    <i data-feather="map-pin"></i>
                    <p>Carte interactive</p>
                    <span>Leaflet / Google Maps</span>
                </div>
                <div class="refresh-note">
                    <i data-feather="refresh-cw"></i>
                    Mis à jour : {{ now()->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="content-card">
            <div class="cc-header">
                <div class="cc-title">
                    <i data-feather="bar-chart-2"></i>
                    Statistiques rapides
                </div>
            </div>
            <div class="cc-body">
                <div class="qs-item">
                    <span class="qs-label">Interventions aujourd'hui</span>
                    <span class="qs-value">24</span>
                </div>
                <div class="qs-item">
                    <span class="qs-label">Temps moyen</span>
                    <span class="qs-value">{{ $stats['temps_reponse'] ?? '—' }} min</span>
                </div>
                <div class="qs-item">
                    <span class="qs-label">Taux de satisfaction</span>
                    <span class="qs-value green">98%</span>
                </div>
            </div>
        </div>

        {{-- Actions rapides --}}
        @if(($service->role ?? '') === 'hopital')
        <div class="content-card">
            <div class="cc-header">
                <div class="cc-title">
                    <i data-feather="zap"></i>
                    Actions rapides
                </div>
            </div>
            <div class="cc-body" style="display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('services.vaccinationEnfantsIndex') }}" class="btn btn-accent" style="justify-content:center;">
                    <i data-feather="shield"></i>
                    Vaccination enfants
                </a>
                <a href="{{ route('services.citoyensBilan') }}" class="btn btn-outline" style="justify-content:center;">
                    <i data-feather="activity"></i>
                    Bilan santé
                </a>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ── Graphique d'activité ── --}}
<div class="content-card anim-fade">
    <div class="cc-header">
        <div>
            <div class="cc-title">
                <i data-feather="trending-up"></i>
                Activité des derniers jours
            </div>
            <div class="cc-subtitle">Signalements traités par jour</div>
        </div>
        <div style="display:flex;gap:8px;">
            <button class="btn btn-outline btn-sm">7 jours</button>
            <button class="btn btn-outline btn-sm">30 jours</button>
        </div>
    </div>
    <div class="cc-body">
        <div class="chart-placeholder">
            <i data-feather="bar-chart-2"></i>
            <p>Graphique d'activité</p>
            <span>Chart.js / Recharts</span>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
feather.replace({ width: 15, height: 15 });

setInterval(() => {
    // AJAX refresh — à implémenter
}, 30000);
</script>
@endpush
