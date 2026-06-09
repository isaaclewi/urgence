@extends('services.master')

@section('title', 'Vaccins Enfants — ' . ($service->nom ?? 'CongoAssist'))

@section('page-title', 'Vaccins Enfants')
@section('page-subtitle', 'Suivez et gérez le calendrier vaccinal pédiatrique')

@section('page-actions')
    <button data-bs-toggle="modal" data-bs-target="#addVaccinModal" class="btn btn-accent">
        <i data-feather="plus"></i>
        Ajouter un vaccin
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
<a href="{{ route('services.vaccinationIndex') }}" class="sidebar-link">
    <i data-feather="calendar"></i>
    <span class="sb-lbl">Programmes Vaccination</span>
</a>
<a href="{{ route('services.vaccinationEnfantsIndex') }}" class="sidebar-link active">
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
                <i data-feather="cpu" style="color:#3B82F6;"></i>
            </div>
            <div class="sc-value">{{ $vaccins->total() }}</div>
            <div class="sc-label">Total vaccins</div>
            <div class="sc-trend neu"><i data-feather="layers"></i> Catalogue complet</div>
        </div>

        <div class="stat-card green anim-fade" style="animation-delay:.06s">
            <div class="sc-icon" style="background:#D1FAE5;">
                <i data-feather="check-circle" style="color:#10B981;"></i>
            </div>
            <div class="sc-value">{{ $vaccins->where('vaccination_obligatoire', 1)->count() }}</div>
            <div class="sc-label">Obligatoires</div>
            <div class="sc-trend up"><i data-feather="shield"></i> Vaccins requis</div>
        </div>

        <div class="stat-card purple anim-fade" style="animation-delay:.12s">
            <div class="sc-icon" style="background:#EDE9FE;">
                <i data-feather="droplet" style="color:#8B5CF6;"></i>
            </div>
            <div class="sc-value">{{ $vaccins->sum('nombre_doses') }}</div>
            <div class="sc-label">Doses totales</div>
            <div class="sc-trend neu"><i data-feather="package"></i> Toutes administrations</div>
        </div>

        <div class="stat-card yellow anim-fade" style="animation-delay:.18s">
            <div class="sc-icon" style="background:#FEF3C7;">
                <i data-feather="star" style="color:#F59E0B;"></i>
            </div>
            <div class="sc-value">{{ $vaccins->where('vaccination_obligatoire', 0)->count() }}</div>
            <div class="sc-label">Recommandés</div>
            <div class="sc-trend neu"><i data-feather="info"></i> Optionnels</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="content-card anim-slide">
        <div class="cc-body" style="padding:16px 20px;">
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <div style="flex:1; min-width:220px; position:relative;">
                    <i data-feather="search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted);"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher un vaccin, fabricant…"
                           class="form-input" style="padding-left:38px;">
                </div>
                <select id="filterObligatoire" class="form-input" style="width:auto; min-width:180px;">
                    <option value="">Tous les vaccins</option>
                    <option value="1">Obligatoires</option>
                    <option value="0">Recommandés</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="content-card anim-fade">
        <div class="cc-header">
            <div>
                <div class="cc-title"><i data-feather="activity"></i> Catalogue des vaccins pédiatriques</div>
                <div class="cc-subtitle">{{ $vaccins->total() }} vaccin(s) référencé(s)</div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Vaccin</th>
                        <th>Fabricant</th>
                        <th>Doses</th>
                        <th>Voie</th>
                        <th>Âge cible (mois)</th>
                        <th>Statut</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @foreach ($vaccins as $v)
                    <tr id="row-{{ $v->id }}">
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div class="avatar-initials" style="border-radius:10px;">
                                    <i data-feather="droplet" style="width:16px;height:16px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600; color:var(--text);">{{ $v->nom_vaccin }}</div>
                                    <div style="font-size:11px; color:var(--text-muted);">#{{ $v->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-sec); font-size:12.5px;">{{ $v->fabricant ?? '—' }}</td>
                        <td>
                            <span class="pill pill-blue">
                                {{ $v->nombre_doses ?? 0 }} dose{{ ($v->nombre_doses ?? 0) > 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td>
                            <span class="pill pill-purple">{{ $v->voie_administration ?? '—' }}</span>
                        </td>
                        <td style="font-size:12.5px; color:var(--text-sec);">
                            {{ $v->age_cible_min ?? '0' }} – {{ $v->age_cible_max ?? '0' }}
                        </td>
                        <td>
                            @if($v->vaccination_obligatoire)
                                <span class="pill pill-green"><i data-feather="check"></i> Obligatoire</span>
                            @else
                                <span class="pill pill-gray"><i data-feather="star"></i> Recommandé</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <form action="{{ route('services.vaccinationEnfantsDestroy', $v->id) }}" method="POST"
                                  class="deleteForm" data-id="{{ $v->id }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Supprimer">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <span style="font-size:12px; color:var(--text-muted);">
                Affichage de <strong>{{ $vaccins->count() }}</strong> sur <strong>{{ $vaccins->total() }}</strong> vaccins
            </span>
            {!! $vaccins->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>

{{-- MODAL AJOUT --}}
<div class="modal fade" id="addVaccinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:var(--brand-dark); border:none; padding:20px 24px;">
                <div>
                    <h5 class="modal-title" style="color:#F8FAFC; font-family:'Sora',sans-serif; font-size:17px; font-weight:700;">
                        Ajouter un vaccin
                    </h5>
                    <p style="color:rgba(255,255,255,.55); font-size:12px; margin-top:3px;">Référencez un nouveau vaccin pédiatrique</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="addVaccinForm">
                @csrf
                <div class="modal-body" style="padding:24px; display:flex; flex-direction:column; gap:16px;">
                    <div class="grid-2">
                        <div style="grid-column:1/-1;">
                            <label class="form-label">Nom du vaccin <span style="color:#EF4444;">*</span></label>
                            <input type="text" name="nom_vaccin" required class="form-input" placeholder="Ex : BCG, DTC, Polio…">
                        </div>
                        <div>
                            <label class="form-label">Fabricant</label>
                            <input type="text" name="fabricant" class="form-input" placeholder="Ex : Sanofi, GSK">
                        </div>
                        <div>
                            <label class="form-label">Nombre de doses</label>
                            <input type="number" name="nombre_doses" class="form-input" placeholder="0">
                        </div>
                        <div>
                            <label class="form-label">Voie d'administration</label>
                            <select name="voie_administration" class="form-input">
                                <option value="">Sélectionner…</option>
                                <option value="Intramusculaire">Intramusculaire</option>
                                <option value="Sous-cutanée">Sous-cutanée</option>
                                <option value="Orale">Orale</option>
                                <option value="Intradermique">Intradermique</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Vaccination obligatoire</label>
                            <select name="vaccination_obligatoire" class="form-input">
                                <option value="0">Non — Recommandé</option>
                                <option value="1">Oui — Obligatoire</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Date de fabrication</label>
                            <input type="date" name="date_fabrication" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Date d'expiration</label>
                            <input type="date" name="date_expiration" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Âge cible min (mois)</label>
                            <input type="number" name="age_cible_min" class="form-input" placeholder="0">
                        </div>
                        <div>
                            <label class="form-label">Âge cible max (mois)</label>
                            <input type="number" name="age_cible_max" class="form-input" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border); padding:16px 24px; gap:10px;">
                    <button type="button" data-bs-dismiss="modal" class="btn btn-outline">Annuler</button>
                    <button type="submit" class="btn btn-accent">
                        <i data-feather="save"></i> Enregistrer
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

    $('#filterObligatoire').on('change', function() {
        const f = $(this).val();
        $('#tableBody tr').each(function() {
            if (f === '') { $(this).show(); return; }
            const isObligatoire = $(this).find('td:nth-child(6)').text().includes('Obligatoire');
            $(this).toggle((f === '1' && isObligatoire) || (f === '0' && !isObligatoire));
        });
    });

    $('#addVaccinForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('services.vaccinationEnfantsStore') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: () => { $('#addVaccinModal').modal('hide'); location.reload(); },
            error:   () => alert('Erreur lors de l\'enregistrement')
        });
    });

    $('.deleteForm').submit(function(e) {
        e.preventDefault();
        if (!confirm('Supprimer ce vaccin ?')) return;
        const id  = $(this).data('id');
        const url = $(this).attr('action');
        $.ajax({
            url, type: 'POST', data: $(this).serialize(),
            success: () => $('#row-' + id).fadeOut(300, function() { $(this).remove(); }),
            error:   () => alert('Erreur lors de la suppression')
        });
    });
});
</script>
@endpush
@endsection
