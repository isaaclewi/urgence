@extends('services.master')

@section('title', 'Vaccination — ' . ($service->nom ?? 'CongoAssist'))

@section('page-title', 'Programmes de vaccination')
@section('page-subtitle', 'Gérez et suivez vos campagnes de vaccination en temps réel')

@section('page-actions')
    <button data-bs-toggle="modal" data-bs-target="#addVaccinationModal" class="btn btn-accent">
        <i data-feather="plus-circle"></i>
        Nouveau programme
    </button>
@endsection

@section('sidebar')
<div class="sb-section-label">Navigation</div>
<a href="{{ route('services.compte') }}" class="sidebar-link">
    <i data-feather="home"></i>
    <span class="sb-lbl">Tableau de bord</span>
</a>
<a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link">
    <i data-feather="bell"></i>
    <span class="sb-lbl">Urgences signalées</span>
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
    <i data-feather="newspaper"></i>
    <span class="sb-lbl">Actualités</span>
</a>
<a href="{{ route('services.profil') }}" class="sidebar-link">
    <i data-feather="settings"></i>
    <span class="sb-lbl">Gestion interne</span>
</a>

<div class="sb-divider"></div>
<div class="sb-section-label">Services Médicaux</div>
<a href="{{ route('services.vaccinationIndex') }}" class="sidebar-link active">
    <i data-feather="calendar"></i>
    <span class="sb-lbl">Programmes Vaccination</span>
</a>
<a href="{{ route('services.vaccinationEnfants') }}" class="sidebar-link">
    <i data-feather="activity"></i>
    <span class="sb-lbl">Vaccins Enfants</span>
</a>
<a href="{{ route('services.citoyensBilan') }}" class="sidebar-link">
    <i data-feather="heart"></i>
    <span class="sb-lbl">Bilan Santé</span>
</a>

<div class="sb-divider"></div>
<a href="{{ route('services.logout') }}" class="sidebar-link danger">
    <i data-feather="log-out"></i>
    <span class="sb-lbl">Déconnexion</span>
</a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- Stat cards --}}
    <div class="grid-4">
        <div class="stat-card blue anim-fade">
            <div class="sc-icon" style="background:#EFF6FF;">
                <i data-feather="calendar" style="color:#3B82F6;"></i>
            </div>
            <div class="sc-value">{{ $programmes->total() }}</div>
            <div class="sc-label">Total programmes</div>
            <div class="sc-trend neu"><i data-feather="layers"></i> Gestion globale</div>
        </div>

        <div class="stat-card red anim-fade" style="animation-delay:.06s">
            <div class="sc-icon" style="background:#FEF2F2;">
                <i data-feather="alert-triangle" style="color:#EF4444;"></i>
            </div>
            <div class="sc-value">{{ $programmes->where('categorie', 'pandemie')->count() }}</div>
            <div class="sc-label">Pandémie</div>
            <div class="sc-trend down"><i data-feather="alert-circle"></i> Haute priorité</div>
        </div>

        <div class="stat-card purple anim-fade" style="animation-delay:.12s">
            <div class="sc-icon" style="background:#EDE9FE;">
                <i data-feather="cpu" style="color:#8B5CF6;"></i>
            </div>
            <div class="sc-value">{{ $programmes->sum('nb_vaccins') }}</div>
            <div class="sc-label">Total vaccins distribués</div>
            <div class="sc-trend neu"><i data-feather="package"></i> Distribution</div>
        </div>

        <div class="stat-card orange anim-fade" style="animation-delay:.18s">
            <div class="sc-icon" style="background:#FFF7ED;">
                <i data-feather="activity" style="color:#F97316;"></i>
            </div>
            <div class="sc-value">{{ $programmes->where('categorie', 'epidemie')->count() }}</div>
            <div class="sc-label">Épidémie</div>
            <div class="sc-trend neu"><i data-feather="shield"></i> Surveillance active</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="content-card anim-slide">
        <div class="cc-body" style="padding:16px 20px;">
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <div style="flex:1; min-width:220px; position:relative;">
                    <i data-feather="search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted);"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher un programme, organisme…"
                           class="form-input" style="padding-left:38px;">
                </div>
                <select id="categoryFilter" class="form-input" style="width:auto; min-width:180px;">
                    <option value="">Toutes catégories</option>
                    <option value="pandemie">Pandémie</option>
                    <option value="epidemie">Épidémie</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="content-card anim-fade">
        <div class="cc-header">
            <div>
                <div class="cc-title"><i data-feather="calendar"></i> Programmes de vaccination</div>
                <div class="cc-subtitle">{{ $programmes->total() }} programme(s) enregistré(s)</div>
            </div>
            <button class="btn btn-outline btn-sm">
                <i data-feather="download"></i> Exporter
            </button>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Programme</th>
                        <th>Période</th>
                        <th>Âge cible</th>
                        <th>Vaccins</th>
                        <th>Organisme</th>
                        <th>Catégorie</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @foreach ($programmes as $p)
                    <tr id="row-{{ $p->id }}">
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div class="avatar-initials" style="border-radius:10px;">
                                    <i data-feather="calendar" style="width:16px;height:16px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600; color:var(--text);">{{ $p->nom_programme }}</div>
                                    <div style="font-size:11px; color:var(--text-muted);">#{{ $p->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:12.5px;">
                                <div style="font-weight:600; color:var(--text);">{{ \Carbon\Carbon::parse($p->date_debut)->format('d M Y') }}</div>
                                <div style="color:var(--text-muted); margin-top:2px;">→ {{ \Carbon\Carbon::parse($p->date_fin)->format('d M Y') }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="pill pill-purple">{{ $p->age_cible ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="pill pill-blue">
                                <i data-feather="cpu"></i>
                                {{ $p->nb_vaccins ?? 0 }}
                            </span>
                        </td>
                        <td style="color:var(--text-sec); font-size:12.5px;">{{ $p->organisme_resp ?? '—' }}</td>
                        <td>
                            @if($p->categorie == 'pandemie')
                                <span class="pill pill-red"><i data-feather="alert-triangle"></i> Pandémie</span>
                            @elseif($p->categorie == 'epidemie')
                                <span class="pill pill-yellow"><i data-feather="activity"></i> Épidémie</span>
                            @else
                                <span class="pill pill-gray">{{ $p->categorie ?? '—' }}</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex; justify-content:center; gap:6px;">
                                <button onclick="viewDetails({{ $p->id }})" class="btn btn-outline btn-sm btn-icon" title="Voir détails">
                                    <i data-feather="eye"></i>
                                </button>
                                <form action="{{ route('services.vaccinationDestroy', $p->id) }}" method="POST"
                                      class="deleteForm" data-id="{{ $p->id }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Supprimer">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <span style="font-size:12px; color:var(--text-muted);">
                Affichage de <strong>{{ $programmes->count() }}</strong> sur <strong>{{ $programmes->total() }}</strong> programmes
            </span>
            {!! $programmes->links('pagination::tailwind') !!}
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="addVaccinationModal" tabindex="-1" aria-labelledby="addVaccinationLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:var(--brand-dark); border:none; padding:20px 24px;">
                <div>
                    <h5 class="modal-title" style="color:#F8FAFC; font-family:'Sora',sans-serif; font-size:17px; font-weight:700;">
                        Nouveau programme de vaccination
                    </h5>
                    <p style="color:rgba(255,255,255,.55); font-size:12px; margin-top:3px;">Créez un programme de vaccination</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="addVaccinationForm">
                @csrf
                <div class="modal-body" style="padding:24px; display:flex; flex-direction:column; gap:16px;">
                    <div class="grid-2">
                        <div style="grid-column:1/-1;">
                            <label class="form-label">Nom du programme <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="nom_programme" required class="form-input" placeholder="Ex : Vaccination COVID-19">
                        </div>
                        <div>
                            <label class="form-label">Âge cible</label>
                            <input type="text" name="age_cible" class="form-input" placeholder="Ex : 0–5 ans">
                        </div>
                        <div>
                            <label class="form-label">Nombre de vaccins</label>
                            <input type="number" name="nb_vaccins" class="form-input" placeholder="0">
                        </div>
                        <div>
                            <label class="form-label">Date de début <span style="color:#EF4444;">*</span></label>
                            <input type="date" name="date_debut" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Date de fin <span style="color:#EF4444;">*</span></label>
                            <input type="date" name="date_fin" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Organisme responsable</label>
                            <input type="text" name="organisme_resp" class="form-input" placeholder="Ex : OMS, Ministère Santé">
                        </div>
                        <div>
                            <label class="form-label">Catégorie <span style="color:#EF4444;">*</span></label>
                            <select name="categorie" required class="form-input">
                                <option value="">Sélectionner…</option>
                                <option value="pandemie">Pandémie</option>
                                <option value="epidemie">Épidémie</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:16px 24px; gap:10px;">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline">Annuler</button>
                    <button type="submit" class="btn btn-accent">
                        <i data-feather="check"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    feather.replace({ width: 16, height: 16 });

    $('#searchInput').on('input', function() {
        const q = $(this).val().toLowerCase();
        $('#tableBody tr').each(function() {
            $(this).toggle($(this).text().toLowerCase().includes(q));
        });
    });

    $('#categoryFilter').on('change', function() {
        const cat = $(this).val().toLowerCase();
        $('#tableBody tr').each(function() {
            $(this).toggle(!cat || $(this).find('td:nth-child(6)').text().toLowerCase().includes(cat));
        });
    });

    $('#addVaccinationForm').submit(function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i data-feather="loader"></i> Enregistrement…');
        feather.replace({ width: 16, height: 16 });
        $.ajax({
            url: "{{ route('services.vaccinationStore') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: () => location.reload(),
            error: () => {
                alert('Erreur lors de l\'enregistrement');
                btn.prop('disabled', false).html('<i data-feather="check"></i> Enregistrer');
                feather.replace({ width: 16, height: 16 });
            }
        });
    });

    $('.deleteForm').submit(function(e) {
        e.preventDefault();
        if (!confirm('Supprimer ce programme ? Cette action est irréversible.')) return;
        const id  = $(this).data('id');
        const url = $(this).attr('action');
        $.ajax({
            url, type: 'POST', data: $(this).serialize(),
            success: () => $('#row-' + id).fadeOut(300, function() { $(this).remove(); }),
            error:   () => alert('Erreur lors de la suppression')
        });
    });

    window.viewDetails = function(id) {
        const row = $('#row-' + id);
        alert('Programme : ' + row.find('td:first div div:first').text().trim() +
              '\nPériode   : ' + row.find('td:nth-child(2)').text().trim() +
              '\nÂge cible : ' + row.find('td:nth-child(3)').text().trim() +
              '\nVaccins   : ' + row.find('td:nth-child(4)').text().trim() +
              '\nOrganisme : ' + row.find('td:nth-child(5)').text().trim());
    };
});
</script>
@endpush
@endsection
