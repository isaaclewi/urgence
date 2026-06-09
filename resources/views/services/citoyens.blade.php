@extends('services.master')

@section('title', 'Citoyens — ' . ($service->nom ?? 'CongoAssist'))

@section('page-title', 'Citoyens actifs')
@section('page-subtitle', 'Liste des utilisateurs inscrits sur la plateforme')

@section('page-actions')
    <button class="btn btn-outline btn-sm">
        <i data-feather="filter"></i> Filtrer
    </button>
    <button class="btn btn-accent btn-sm">
        <i data-feather="download"></i> Exporter
    </button>
@endsection

@section('sidebar')
<div class="sb-section-label">Navigation</div>
<a href="{{ route('services.compte') }}" class="sidebar-link">
    <i data-feather="home"></i><span class="sb-lbl">Tableau de bord</span>
</a>
<a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link">
    <i data-feather="bell"></i><span class="sb-lbl">Urgences signalées</span>
</a>
<a href="{{ route('services.citoyens') }}" class="sidebar-link active">
    <i data-feather="users"></i><span class="sb-lbl">Citoyens</span>
</a>
<a href="{{ route('services.forum.index') }}" class="sidebar-link">
    <i data-feather="message-square"></i><span class="sb-lbl">Forum</span>
</a>
<a href="{{ route('services.actualite') }}" class="sidebar-link">
    <i data-feather="newspaper"></i><span class="sb-lbl">Actualités</span>
</a>
<a href="{{ route('services.profil') }}" class="sidebar-link">
    <i data-feather="settings"></i><span class="sb-lbl">Gestion interne</span>
</a>
<div class="sb-divider"></div>
<a href="{{ route('services.logout') }}" class="sidebar-link danger">
    <i data-feather="log-out"></i><span class="sb-lbl">Déconnexion</span>
</a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- Stat cards --}}
    <div class="grid-4 anim-fade">
        <div class="stat-card green">
            <div class="sc-icon" style="background:#D1FAE5;">
                <i data-feather="users" style="color:#10B981;"></i>
            </div>
            <div class="sc-value">{{ $citoyens->total() }}</div>
            <div class="sc-label">Total citoyens</div>
        </div>
        <div class="stat-card blue">
            <div class="sc-icon" style="background:#DBEAFE;">
                <i data-feather="user-check" style="color:#3B82F6;"></i>
            </div>
            <div class="sc-value">{{ $citoyens->where('etat_compte', 'actif')->count() }}</div>
            <div class="sc-label">Comptes actifs</div>
        </div>
        <div class="stat-card purple">
            <div class="sc-icon" style="background:#EDE9FE;">
                <i data-feather="user" style="color:#8B5CF6;"></i>
            </div>
            <div class="sc-value">{{ $citoyens->where('sexe', 'M')->count() }}</div>
            <div class="sc-label">Hommes</div>
        </div>
        <div class="stat-card accent">
            <div class="sc-icon" style="background:rgba(29,184,122,.1);">
                <i data-feather="user" style="color:var(--accent);"></i>
            </div>
            <div class="sc-value">{{ $citoyens->where('sexe', 'F')->count() }}</div>
            <div class="sc-label">Femmes</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="content-card anim-slide">
        <div class="cc-header">
            <div>
                <div class="cc-title"><i data-feather="users"></i> Liste des citoyens</div>
                <div class="cc-subtitle">{{ $citoyens->where('etat_compte', 'actif')->count() }} comptes actifs</div>
            </div>
        </div>

        <div class="table-wrap">
            <table id="citoyensTable" class="data-table">
                <thead>
                    <tr>
                        <th><i data-feather="hash" style="width:13px;height:13px;"></i> ID</th>
                        <th><i data-feather="user" style="width:13px;height:13px;"></i> Noms et Prénoms</th>
                        <th><i data-feather="mail" style="width:13px;height:13px;"></i> Email</th>
                        <th><i data-feather="map-pin" style="width:13px;height:13px;"></i> Adresse</th>
                        <th><i data-feather="phone" style="width:13px;height:13px;"></i> Contact</th>
                        <th><i data-feather="credit-card" style="width:13px;height:13px;"></i> Matricule</th>
                        <th style="text-align:center;">État</th>
                        <th style="text-align:center;">Genre</th>
                        <th style="text-align:center;">Inscription</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($citoyens->where('etat_compte', 'actif') as $c)
                    <tr>
                        <td><span style="font-weight:600;">#{{ $c->id }}</span></td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div class="avatar-initials">
                                    {{ strtoupper(substr($c->nom,0,1)) }}{{ strtoupper(substr($c->prenom,0,1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;">{{ $c->nom }} {{ $c->prenom }}</div>
                                    <div style="font-size:11px; color:var(--text-muted);">Citoyen</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-sec);">
                                <i data-feather="mail" style="width:13px;height:13px;color:var(--border-mid);"></i>
                                {{ $c->email }}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-sec);">
                                <i data-feather="map-pin" style="width:13px;height:13px;color:var(--border-mid);"></i>
                                {{ $c->adresse }}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-sec);">
                                <i data-feather="phone" style="width:13px;height:13px;color:var(--border-mid);"></i>
                                {{ $c->telephone }}
                            </div>
                        </td>
                        <td>
                            <span class="pill pill-gray" style="font-family:monospace;">{{ $c->matricule }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span class="pill pill-green"><i data-feather="check"></i> Actif</span>
                        </td>
                        <td style="text-align:center;">
                            @if($c->sexe === 'M')
                                <span class="pill pill-blue">M</span>
                            @else
                                <span class="pill" style="background:#FCE7F3; color:#9D174D;">F</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <div style="font-size:12.5px; font-weight:600; color:var(--text);">{{ $c->created_at->format('d/m/Y') }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $c->created_at->format('H:i') }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px; border-top:1px solid var(--border);">
            {!! $citoyens->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    /* Intégration DataTables avec le design système */
    .dataTables_wrapper { font-size: 13px; color: var(--text-sec); }
    .dataTables_filter input,
    .dataTables_length select {
        border: 1.5px solid var(--border);
        border-radius: 7px;
        padding: 6px 10px;
        font-family: inherit;
        font-size: 13px;
        color: var(--text);
        outline: none;
        background: var(--surface);
    }
    .dataTables_filter input:focus,
    .dataTables_length select:focus { border-color: var(--accent); }
    table.dataTable thead th { font-size: 10.5px !important; }
    .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        padding: 4px 10px !important;
        font-size: 12.5px !important;
    }
    .dataTables_paginate .paginate_button.current {
        background: var(--accent) !important;
        color: #fff !important;
        border: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#citoyensTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 15, 25, 50],
        responsive: true,
        autoWidth: false,
        order: [[0, 'desc']],
        language: {
            search:     "Rechercher :",
            lengthMenu: "Afficher _MENU_ citoyens",
            paginate:   { first:"Début", last:"Fin", next:"Suivant", previous:"Précédent" },
            info:           "Affichage _START_ à _END_ sur _TOTAL_ citoyens",
            infoEmpty:      "Aucun citoyen",
            infoFiltered:   "(filtré depuis _MAX_ total)",
            zeroRecords:    "Aucun citoyen trouvé"
        },
        columnDefs: [{ orderable: false, targets: [6, 7] }],
        drawCallback: () => feather.replace({ width: 16, height: 16 })
    });
    feather.replace({ width: 16, height: 16 });
});
</script>
@endpush
@endsection
