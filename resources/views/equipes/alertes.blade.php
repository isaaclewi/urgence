@extends('equipes.master')

@section('title', 'Mes alertes — ' . ($equipe->nom ?? 'Équipe'))
@section('page-icon', 'bell')
@section('page-title', 'Mes alertes affectées')
@section('page-subtitle', 'Toutes les urgences transmises à votre équipe')

@section('content')

@push('styles')
<style>
.table-wrap { overflow-x: auto; }

.type-badge {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 100px;
    font-size: 10.5px; font-weight: 700;
    background: #DBEAFE; color: #1E40AF;
    white-space: nowrap;
}
.status-select {
    width: 100%; padding: 7px 10px;
    border: 1.5px solid var(--border);
    border-radius: 7px;
    font-size: 12px; font-weight: 600;
    font-family: inherit;
    color: var(--text);
    background: var(--surface);
    outline: none; cursor: pointer;
    transition: border-color .17s;
}
.status-select:focus { border-color: var(--accent); }

.source-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11px; font-weight: 600;
    color: var(--text-sec);
}
</style>
@endpush

{{-- Filtres rapides --}}
<div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
    <button onclick="filterTable('all')"
        class="btn btn-outline btn-sm filter-btn active" data-filter="all">
        Toutes ({{ $affectations->count() }})
    </button>
    <button onclick="filterTable('transmise')"
        class="btn btn-outline btn-sm filter-btn" data-filter="transmise">
        En attente ({{ $affectations->where('statut','transmise')->count() }})
    </button>
    <button onclick="filterTable('en_cours')"
        class="btn btn-outline btn-sm filter-btn" data-filter="en_cours">
        En cours ({{ $affectations->where('statut','en_cours')->count() }})
    </button>
    <button onclick="filterTable('terminee')"
        class="btn btn-outline btn-sm filter-btn" data-filter="terminee">
        Terminées ({{ $affectations->where('statut','terminee')->count() }})
    </button>
</div>

<div class="content-card anim-slide">
    <div class="cc-header">
        <div>
            <div class="cc-title"><i data-feather="alert-triangle"></i> Alertes reçues</div>
            <div class="cc-subtitle">{{ $affectations->count() }} alerte(s) au total</div>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="alertesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre alerte</th>
                    <th>Type</th>
                    <th>Localisation</th>
                    <th>Source</th>
                    <th>Date affectation</th>
                    <th>Commentaire</th>
                    <th style="text-align:center;">Statut</th>
                    <th style="text-align:center;">Action</th>
                    <th style="text-align:center;">Carte</th>
                </tr>
            </thead>
            <tbody>
                @forelse($affectations as $aff)
                <tr data-statut="{{ $aff->statut }}">
                    {{-- ID --}}
                    <td><span style="font-weight:700; color:var(--text);">#{{ $aff->alerte_id }}</span></td>

                    {{-- Titre --}}
                    <td>
                        <span style="font-weight:600; font-size:13px;">
                            {{ $aff->alerte->titre ?? '—' }}
                        </span>
                        @if($aff->alerte?->description)
                        <div style="font-size:11px; color:var(--text-muted); margin-top:2px; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $aff->alerte->description }}
                        </div>
                        @endif
                    </td>

                    {{-- Type --}}
                    <td><span class="type-badge">{{ $aff->alerte->type_alerte ?? '—' }}</span></td>

                    {{-- Localisation --}}
                    <td>
                        <div style="display:flex; align-items:center; gap:5px; font-size:13px; color:var(--text-sec);">
                            <i data-feather="map-pin" style="stroke:#EF4444; flex-shrink:0;"></i>
                            {{ $aff->alerte->localisation ?? '—' }}
                        </div>
                    </td>

                    {{-- Service source --}}
                    <td>
                        @if($aff->serviceSource)
                        <span class="source-chip">
                            <i data-feather="send"></i>
                            {{ $aff->serviceSource->nom }}
                        </span>
                        @else
                        <span style="font-size:12px; color:var(--text-muted);">—</span>
                        @endif
                    </td>

                    {{-- Date --}}
                    <td>
                        <div style="font-size:13px; font-weight:600;">
                            {{ \Carbon\Carbon::parse($aff->created_at)->format('d/m/Y') }}
                        </div>
                        <div style="font-size:11px; color:var(--text-muted);">
                            {{ \Carbon\Carbon::parse($aff->created_at)->format('H:i') }}
                        </div>
                    </td>

                    {{-- Commentaire --}}
                    <td>
                        <span style="font-size:12px; color:var(--text-sec); font-style:italic;">
                            {{ $aff->commentaire ? \Str::limit($aff->commentaire, 40) : '—' }}
                        </span>
                    </td>

                    {{-- Statut badge --}}
                    <td style="text-align:center;">
                        @if($aff->statut === 'terminee')
                            <span class="pill pill-green"><i data-feather="check-circle"></i> Terminée</span>
                        @elseif($aff->statut === 'en_cours')
                            <span class="pill pill-yellow"><i data-feather="activity"></i> En cours</span>
                        @else
                            <span class="pill pill-red"><i data-feather="clock"></i> En attente</span>
                        @endif
                    </td>

                    {{-- Changer statut --}}
                    <td style="min-width:140px;">
                        <form method="POST" action="{{ route('equipe.affectation.statut', $aff->id) }}">
                            @csrf
                            <select name="statut" class="status-select" onchange="this.form.submit()">
                                <option value="transmise" {{ $aff->statut === 'transmise' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours"  {{ $aff->statut === 'en_cours'  ? 'selected' : '' }}>En cours</option>
                                <option value="terminee"  {{ $aff->statut === 'terminee'  ? 'selected' : '' }}>Terminée</option>
                            </select>
                        </form>
                    </td>

                    {{-- Voir sur carte --}}
                    <td style="text-align:center;">
                        @if($aff->alerte?->latitude)
                        <a href="{{ route('equipe.alerte.detail', $aff->id) }}" class="btn btn-accent btn-sm">
                            <i data-feather="map"></i>
                            Carte
                        </a>
                        @else
                        <span style="font-size:11px; color:var(--text-muted); font-style:italic;">Pas de GPS</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <div class="empty-icon"><i data-feather="inbox"></i></div>
                            <p>Aucune alerte affectée</p>
                            <span>Les urgences transmises apparaîtront ici</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
feather.replace({ width: 14, height: 14 });

// ── Filtrage côté client ──
function filterTable(statut) {
    const rows = document.querySelectorAll('#alertesTable tbody tr[data-statut]');
    rows.forEach(row => {
        row.style.display = (statut === 'all' || row.dataset.statut === statut) ? '' : 'none';
    });

    // Style boutons
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.filter === statut);
        b.style.background = b.dataset.filter === statut ? 'var(--accent)' : '';
        b.style.color = b.dataset.filter === statut ? '#fff' : '';
        b.style.borderColor = b.dataset.filter === statut ? 'var(--accent)' : '';
    });
}
</script>
@endpush

@endsection
