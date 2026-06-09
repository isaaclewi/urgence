@extends('services.app')

@section('title', ($service->nom ?? 'Service Urgences') . ' — Bilan de Santé')
@section('page-title', 'Bilans de santé')
@section('page-subtitle', 'Créez et consultez les bilans de santé des citoyens')

{{-- ══════════════════════════════
     SIDEBAR
══════════════════════════════ --}}
@section('sidebar')

<div class="sb-section-label">Principal</div>

<a href="{{ route('services.compte') }}" class="sidebar-link">
    <i data-feather="home"></i>
    <span class="sb-lbl">Tableau de bord</span>
</a>

<a href="{{ route('services.citoyens') }}" class="sidebar-link">
    <i data-feather="users"></i>
    <span class="sb-lbl">Citoyens</span>
</a>

<div class="sb-divider"></div>
<div class="sb-section-label">Services médicaux</div>

<a href="{{ route('services.citoyensBilan') }}" class="sidebar-link active">
    <i data-feather="activity"></i>
    <span class="sb-lbl">Bilan de santé</span>
</a>

<a href="{{ route('services.vaccinationIndex') }}" class="sidebar-link">
    <i data-feather="shield"></i>
    <span class="sb-lbl">Vaccination</span>
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
.bilan-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}
@media (max-width: 1023px) { .bilan-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 479px)  { .bilan-stats { grid-template-columns: 1fr; } }

.icon-blue   { background: #DBEAFE; color: #2563EB; }
.icon-green  { background: #D1FAE5; color: #059669; }
.icon-purple { background: #EDE9FE; color: #7C3AED; }
.icon-orange { background: #FEF3C7; color: #D97706; }

/* ─── Form card ─── */
.form-section-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; font-weight: 700;
    color: var(--text); margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
}
.form-section-title i { font-size: 15px; color: var(--accent); }

.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
@media (max-width: 639px) { .form-grid-2 { grid-template-columns: 1fr; } }

.form-field { margin-bottom: 14px; }
.form-field:last-child { margin-bottom: 0; }

.field-label {
    display: flex; align-items: center; gap: 7px;
    font-size: 12px; font-weight: 600;
    color: var(--text); margin-bottom: 6px;
}
.field-label i { font-size: 13px; }
.field-label .req { color: #EF4444; }

.form-input,
.form-textarea,
.form-select {
    width: 100%;
    padding: 10px 13px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: 13.5px; font-family: inherit;
    color: var(--text); background: var(--surface);
    outline: none;
    transition: border-color .17s, box-shadow .17s;
}
.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(29,184,122,.1);
}
.form-input::placeholder,
.form-textarea::placeholder { color: #CBD5E1; }
.form-textarea { resize: vertical; min-height: 88px; }

/* Search input wrap */
.search-wrap { position: relative; }
.search-wrap i {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%);
    font-size: 14px; color: var(--text-muted);
    pointer-events: none;
}
.search-wrap .form-input { padding-left: 36px; }

/* ─── Table ─── */
.table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

.citoyen-cell { display: flex; align-items: center; gap: 10px; }
.citoyen-initials {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #60A5FA, #34D399);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.citoyen-name  { font-size: 13px; font-weight: 600; color: var(--text); }
.citoyen-matri { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

.blood-badge {
    display: inline-block;
    padding: 3px 9px; border-radius: 999px;
    font-size: 11px; font-weight: 700;
    background: #FEE2E2; color: #991B1B;
}

.empty-state {
    text-align: center; padding: 52px 20px;
}
.empty-state-icon {
    width: 68px; height: 68px; border-radius: 50%;
    background: var(--surface2);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
}
.empty-state-icon i { font-size: 30px; color: var(--text-muted); }
.empty-state p { font-size: 14px; font-weight: 600; color: var(--text-sec); }
</style>
@endpush

{{-- Flash --}}
@if(session('success'))
<div class="alert alert-success anim-fade" style="margin-bottom:16px;">
    <i data-feather="check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

{{-- ── Stat Cards ── --}}
<div class="bilan-stats anim-fade">

    <div class="stat-card blue">
        <div class="sc-icon icon-blue"><i data-feather="file-text"></i></div>
        <div class="sc-value">{{ $bilans->count() }}</div>
        <div class="sc-label">Total bilans</div>
    </div>

    <div class="stat-card green">
        <div class="sc-icon icon-green"><i data-feather="users"></i></div>
        <div class="sc-value">{{ $citoyens->count() }}</div>
        <div class="sc-label">Citoyens</div>
    </div>

    <div class="stat-card purple">
        <div class="sc-icon icon-purple"><i data-feather="alert-circle"></i></div>
        <div class="sc-value">{{ $bilans->whereNotNull('allergies')->count() }}</div>
        <div class="sc-label">Avec allergies</div>
    </div>

    <div class="stat-card orange">
        <div class="sc-icon icon-orange"><i data-feather="trending-up"></i></div>
        <div class="sc-value">{{ $bilans->whereNotNull('maladies_chroniques')->count() }}</div>
        <div class="sc-label">Chroniques</div>
    </div>

</div>

{{-- ── Formulaire ── --}}
<div class="content-card anim-slide" style="margin-bottom:20px;">
    <div class="cc-header">
        <div>
            <div class="cc-title">
                <i data-feather="file-plus"></i>
                Créer un bilan de santé
            </div>
            <div class="cc-subtitle">Remplissez les informations médicales du citoyen</div>
        </div>
    </div>
    <div class="cc-body">
        <form action="{{ route('services.citoyensBilanStore') }}" method="POST">
            @csrf

            {{-- Recherche + sélection citoyen --}}
            <div class="form-section-title">
                <i data-feather="user"></i>
                Identification du citoyen
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <div class="field-label">
                        <i data-feather="search"></i>
                        Rechercher par matricule
                    </div>
                    <div class="search-wrap">
                        <i data-feather="search"></i>
                        <input type="text" id="citoyenSearch" placeholder="Tapez le matricule..."
                            class="form-input">
                    </div>
                </div>

                <div class="form-field">
                    <div class="field-label">
                        <i data-feather="user"></i>
                        Citoyen <span class="req">*</span>
                    </div>
                    <select name="citoyen_id" id="citoyenSelect" required class="form-select">
                        <option value="">-- Sélectionner un citoyen --</option>
                        @foreach($citoyens as $citoyen)
                        <option value="{{ $citoyen->id }}" data-matricule="{{ $citoyen->matricule }}">
                            {{ $citoyen->matricule }} — {{ $citoyen->nom }} {{ $citoyen->prenom }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Informations biométriques --}}
            <div class="form-section-title" style="margin-top:6px;">
                <i data-feather="thermometer"></i>
                Données biométriques
            </div>

            <div class="form-grid-2">
                <div class="form-field">
                    <div class="field-label">
                        <i data-feather="droplet" style="color:#EF4444;"></i>
                        Groupe sanguin
                    </div>
                    <input type="text" name="groupe_sanguin" placeholder="Ex: A+" class="form-input">
                </div>
                <div class="form-field">
                    <div class="field-label">
                        <i data-feather="maximize-2" style="color:#2563EB;"></i>
                        Taille (cm)
                    </div>
                    <input type="number" name="taille" placeholder="Ex: 175" class="form-input">
                </div>
                <div class="form-field">
                    <div class="field-label">
                        <i data-feather="award" style="color:#059669;"></i>
                        Poids (kg)
                    </div>
                    <input type="number" name="poids" placeholder="Ex: 70" class="form-input">
                </div>
                <div class="form-field">
                    <div class="field-label">
                        <i data-feather="alert-triangle" style="color:#D97706;"></i>
                        Allergies
                    </div>
                    <input type="text" name="allergies" placeholder="Ex: Arachides, Pénicilline" class="form-input">
                </div>
            </div>

            {{-- Antécédents --}}
            <div class="form-section-title" style="margin-top:6px;">
                <i data-feather="book-open"></i>
                Antécédents médicaux
            </div>

            <div class="form-field">
                <div class="field-label">
                    <i data-feather="heart" style="color:#EF4444;"></i>
                    Maladies chroniques
                </div>
                <input type="text" name="maladies_chroniques" placeholder="Ex: Diabète, Hypertension" class="form-input">
            </div>

            <div class="form-field">
                <div class="field-label">
                    <i data-feather="clock" style="color:#7C3AED;"></i>
                    Maladies passées importantes
                </div>
                <input type="text" name="maladies_passees_importantes" placeholder="Ex: Tuberculose (2020)" class="form-input">
            </div>

            <div class="form-field">
                <div class="field-label">
                    <i data-feather="scissors" style="color:#0891B2;"></i>
                    Interventions chirurgicales
                </div>
                <textarea name="interventions_chirurgicales" placeholder="Décrivez les opérations subies..." class="form-textarea"></textarea>
            </div>

            <div class="form-field">
                <div class="field-label">
                    <i data-feather="users" style="color:#4F46E5;"></i>
                    Antécédents familiaux
                </div>
                <textarea name="antecedents_familiaux" placeholder="Ex: Père diabétique, Mère cardiaque..." class="form-textarea"></textarea>
            </div>

            <div class="form-field">
                <div class="field-label">
                    <i data-feather="home" style="color:#DB2777;"></i>
                    Antécédents d'hospitalisation
                </div>
                <textarea name="antecedents_hospitalisation" placeholder="Listez les hospitalisations..." class="form-textarea"></textarea>
            </div>

            {{-- Traitement & mode de vie --}}
            <div class="form-section-title" style="margin-top:6px;">
                <i data-feather="package"></i>
                Traitement & mode de vie
            </div>

            <div class="form-field">
                <div class="field-label">
                    <i data-feather="package" style="color:#0891B2;"></i>
                    Médicaments pris actuellement
                </div>
                <textarea name="medicaments_pris_actuellement" placeholder="Ex: Aspirine 100mg/jour..." class="form-textarea"></textarea>
            </div>

            <div class="form-field">
                <div class="field-label">
                    <i data-feather="shield" style="color:#059669;"></i>
                    Liste des vaccins reçus
                </div>
                <textarea name="listez_vaccins_reçus" placeholder="Ex: BCG, DTC, Hépatite B..." class="form-textarea"></textarea>
            </div>

            <div class="form-field">
                <div class="field-label">
                    <i data-feather="coffee" style="color:#D97706;"></i>
                    Mode de vie
                </div>
                <textarea name="mode_de_vie" placeholder="Ex: Non-fumeur, sport régulier..." class="form-textarea"></textarea>
            </div>

            <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center;padding:12px;font-size:14px;margin-top:6px;">
                <i data-feather="save"></i>
                Enregistrer le bilan
            </button>
        </form>
    </div>
</div>

{{-- ── Table bilans ── --}}
<div class="content-card anim-fade">
    <div class="cc-header">
        <div>
            <div class="cc-title">
                <i data-feather="clipboard"></i>
                Bilans enregistrés
            </div>
            <div class="cc-subtitle">{{ $bilans->count() }} bilan(s) au total</div>
        </div>
        <div class="search-wrap" style="width:240px;">
            <i data-feather="search"></i>
            <input type="text" id="tableSearch" placeholder="Rechercher..." class="form-input">
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" id="bilansTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Citoyen</th>
                    <th>Groupe</th>
                    <th>Taille</th>
                    <th>Poids</th>
                    <th>Allergies</th>
                    <th>Chroniques</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bilans as $bilan)
                <tr>
                    <td><span style="font-weight:700;">#{{ $bilan->id }}</span></td>

                    <td>
                        <div class="citoyen-cell">
                            <div class="citoyen-initials">
                                {{ strtoupper(substr($bilan->citoyen->nom ?? '', 0, 1)) }}{{ strtoupper(substr($bilan->citoyen->prenom ?? '', 0, 1)) }}
                            </div>
                            <div>
                                <div class="citoyen-name">{{ $bilan->citoyen->nom ?? '' }} {{ $bilan->citoyen->prenom ?? '' }}</div>
                                <div class="citoyen-matri">{{ $bilan->citoyen->matricule ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="blood-badge">{{ $bilan->groupe_sanguin ?? '—' }}</span>
                    </td>

                    <td style="font-size:13px;color:var(--text-sec);">{{ $bilan->taille ? $bilan->taille.' cm' : '—' }}</td>
                    <td style="font-size:13px;color:var(--text-sec);">{{ $bilan->poids  ? $bilan->poids.' kg'  : '—' }}</td>

                    <td style="font-size:12.5px;color:var(--text-sec);">{{ Str::limit($bilan->allergies ?? '—', 30) }}</td>
                    <td style="font-size:12.5px;color:var(--text-sec);">{{ Str::limit($bilan->maladies_chroniques ?? '—', 30) }}</td>

                    <td style="text-align:center;">
                        <form action="{{ route('services.citoyensBilanDestroy', $bilan->id) }}" method="POST"
                            onsubmit="return confirm('Supprimer ce bilan ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Supprimer" style="margin:0 auto;">
                                <i data-feather="trash-2"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i data-feather="inbox"></i></div>
                            <p>Aucun bilan enregistré</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
feather.replace({ width: 14, height: 14 });

/* Filtre select citoyen par matricule */
document.getElementById('citoyenSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    Array.from(document.getElementById('citoyenSelect').options).forEach(opt => {
        if (!opt.value) return;
        opt.style.display = (opt.dataset.matricule || '').toLowerCase().includes(q) ? '' : 'none';
    });
});

/* Filtre tableau */
document.getElementById('tableSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    Array.from(document.getElementById('bilansTable').tBodies[0].rows).forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
