@extends('citoyen')

@section('title', 'Calendrier de ' . $enfant->prenom . ' — CongoAssist')

@section('content')

@php
    $nb = fn ($etat) => $lignes->where('etat', $etat)->count();
@endphp

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>{{ $enfant->nomComplet() }}</h1>
        <p>{{ $enfant->libelleAge() }} · né(e) le {{ $enfant->date_naissance->format('d/m/Y') }} — calendrier de vaccination</p>
    </div>
    <a href="{{ route('enfants.index') }}" class="btn btn-secondary">
        <i data-feather="arrow-left" style="width:14px;height:14px;"></i> Mes enfants
    </a>
</div>

@if(session('success'))<div class="alert alert-success anim-fade" style="margin-bottom:16px;"><span>{{ session('success') }}</span></div>@endif
@if(session('error'))<div class="alert alert-warning anim-fade" style="margin-bottom:16px;"><span>{{ session('error') }}</span></div>@endif
@if($errors->any())
<div class="alert alert-error anim-fade" style="margin-bottom:16px;">
    <div>@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
</div>
@endif

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-bottom:24px;">
    <div class="stat-card"><div><div class="stat-value">{{ $nb('en_retard') }}</div><div class="stat-label">En retard</div></div></div>
    <div class="stat-card"><div><div class="stat-value">{{ $nb('a_faire') }}</div><div class="stat-label">À faire</div></div></div>
    <div class="stat-card"><div><div class="stat-value">{{ $nb('a_venir') }}</div><div class="stat-label">À venir</div></div></div>
    <div class="stat-card"><div><div class="stat-value">{{ $nb('complet') }}</div><div class="stat-label">Terminés</div></div></div>
</div>

@if($lignes->isEmpty())
    <div class="card"><div class="card-body" style="text-align:center;color:var(--text-sec);">
        Aucun vaccin n'est encore enregistré dans le calendrier national.
    </div></div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
    @foreach($lignes as $l)
    @php $v = $l['vaccin']; $pct = (int) round($l['recues'] * 100 / $l['total']); @endphp
    <div class="card anim-fade">
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;gap:8px;align-items:flex-start;">
                <strong style="font-size:15px;">{{ $v->nom_vaccin }}</strong>
                <span class="pill {{ $l['pill'] }}" style="white-space:nowrap;">{{ $l['libelle'] }}</span>
            </div>

            <div style="font-size:12px;color:var(--text-sec);margin-top:4px;">
                {{ $v->vaccination_obligatoire ? 'Obligatoire' : 'Optionnel' }}
                @if($v->age_cible_min !== null || $v->age_cible_max !== null)
                    · Âge recommandé : {{ $v->age_cible_min ?? 0 }}@if($v->age_cible_max !== null) à {{ $v->age_cible_max }}@endif mois
                @endif
            </div>

            <div style="margin-top:14px;">
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-sec);margin-bottom:5px;">
                    <span>{{ $l['recues'] }} / {{ $l['total'] }} dose(s)</span><span>{{ $pct }}%</span>
                </div>
                <div style="height:8px;background:#E5E7EB;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:var(--accent);border-radius:99px;"></div>
                </div>
            </div>

            <div style="font-size:12px;color:var(--text-sec);margin-top:12px;line-height:1.6;">
                @if($l['suivi']?->derniere_dose)Dernière dose : {{ $l['suivi']->derniere_dose->format('d/m/Y') }}<br>@endif
                @if($l['etat'] !== 'complet' && $l['echeance'])
                    {{ $l['etat'] === 'a_venir' ? 'Prévu à partir du' : 'Prochaine dose prévue le' }}
                    <strong>{{ $l['echeance']->format('d/m/Y') }}</strong>
                @endif
            </div>

            @if($l['etat'] !== 'complet')
            <details style="margin-top:14px;">
                <summary class="btn btn-primary btn-sm" style="cursor:pointer;display:inline-flex;">Enregistrer une dose</summary>
                <form method="POST" action="{{ route('enfants.dose', [$enfant->id, $v->id]) }}" style="margin-top:12px;">
                    @csrf
                    <label class="form-label">Date de la dose</label>
                    <input class="form-control" type="date" name="date_dose" value="{{ today()->toDateString() }}"
                           min="{{ $enfant->date_naissance->toDateString() }}" max="{{ today()->toDateString() }}" required>
                    @if($l['recues'] + 1 < $l['total'])
                    <label class="form-label" style="margin-top:10px;">Prochaine dose (si connue)</label>
                    <input class="form-control" type="date" name="prochaine_dose" min="{{ today()->addDay()->toDateString() }}">
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top:12px;">Valider</button>
                </form>
            </details>
            @endif

            @if($l['recues'] > 0)
            <form method="POST" action="{{ route('enfants.dose.retirer', [$enfant->id, $v->id]) }}" style="margin-top:10px;"
                  onsubmit="return confirm('Retirer la dernière dose enregistrée ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-secondary btn-sm">Corriger : retirer la dernière dose</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

<p style="font-size:12px;color:var(--text-sec);margin-top:24px;line-height:1.6;">
    Ces informations sont déclarées par vous : elles ne remplacent pas le carnet de santé de l'enfant ni l'avis d'un professionnel de santé.
</p>

@endsection
