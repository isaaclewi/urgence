@extends('layouts.portal')

@section('title', 'Vaccination — Vaccins enfants')
@section('page-title', 'Vaccination')
@section('page-subtitle', 'Vaccins pour enfants de votre service')

@section('page-actions')
    <a class="ds-btn ds-btn-primary" href="{{ route('services.vaccinationEnfantsCreate') }}">+ Nouveau vaccin</a>
@endsection

@section('content')
@include('services.partials.vaccination-tabs', ['actif' => 'vaccins'])

<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="Vaccins" :value="$stats['total']" />
    <x-stat label="Obligatoires" :value="$stats['obligatoires']" type="info" />
    <x-stat :label="'Expirent sous ' . \App\Models\vaccinEnfant::ALERTE_JOURS . ' j'" :value="$stats['bientot']" type="warning" />
    <x-stat label="Périmés" :value="$stats['perimes']" type="danger" />
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un vaccin ou un fabricant…">
        <select class="ds-select" name="obligatoire">
            <option value="">Obligatoire ou non</option>
            <option value="oui" @selected(request('obligatoire') === 'oui')>Obligatoires</option>
            <option value="non" @selected(request('obligatoire') === 'non')>Optionnels</option>
        </select>
        <select class="ds-select" name="peremption">
            <option value="">Toutes péremptions</option>
            <option value="valide" @selected(request('peremption') === 'valide')>Valides</option>
            <option value="bientot" @selected(request('peremption') === 'bientot')>Expirent bientôt</option>
            <option value="perime" @selected(request('peremption') === 'perime')>Périmés</option>
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
        @if(request()->hasAny(['q', 'obligatoire', 'peremption']))
            <a class="ds-btn ds-btn-ghost" href="{{ route('services.vaccinationEnfantsIndex') }}">Réinitialiser</a>
        @endif
    </form>

    @if($vaccins->isEmpty())
        <x-empty title="Aucun vaccin">
            @if(request()->hasAny(['q', 'obligatoire', 'peremption']))
                Aucun résultat pour ces filtres.
            @else
                Ajoutez votre premier vaccin avec le bouton « Nouveau vaccin ».
            @endif
        </x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Vaccin</th>
                    <th>Programme</th>
                    <th>Doses</th>
                    <th>Âge cible</th>
                    <th>Expiration</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($vaccins as $v)
                    @php $mien = $v->appartientA((int) $service->id); @endphp
                    <tr>
                        <td>
                            <strong>{{ $v->nom_vaccin }}</strong>
                            <div class="ds-muted" style="font-size:12px">
                                {{ $v->fabricant ?: 'Fabricant non renseigné' }}
                                @if($v->voie_administration) · {{ $v->voie_administration }} @endif
                                @unless($mien) · <em>National</em> @endunless
                            </div>
                        </td>
                        <td>{{ $v->programme?->nom_programme ?? '—' }}</td>
                        <td>{{ $v->nombre_doses ?? '—' }}</td>
                        <td style="white-space:nowrap">
                            @if($v->age_cible_min !== null || $v->age_cible_max !== null)
                                {{ $v->age_cible_min ?? 0 }} – {{ $v->age_cible_max ?? '…' }} mois
                            @else — @endif
                        </td>
                        <td style="white-space:nowrap">
                            {{ $v->date_expiration?->format('d/m/Y') ?? '—' }}
                            <div><x-badge :type="$v->peremptionType()">{{ $v->peremption() }}</x-badge></div>
                        </td>
                        <td>
                            <x-badge :type="$v->vaccination_obligatoire ? 'danger' : 'neutral'">
                                {{ $v->vaccination_obligatoire ? 'Obligatoire' : 'Optionnel' }}
                            </x-badge>
                        </td>
                        <td>
                            @if($mien)
                                <div class="ds-row-actions">
                                    <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.vaccinationEnfantsEdit', $v->id) }}">Modifier</a>
                                    <form method="POST" action="{{ route('services.vaccinationEnfantsDestroy', $v->id) }}"
                                          onsubmit="return confirm('Supprimer ce vaccin ?')">
                                        @csrf @method('DELETE')
                                        <button class="ds-btn ds-btn-ghost ds-btn-sm ds-text-danger" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            @else
                                <span class="ds-muted" style="font-size:12px">Lecture seule</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

<div style="margin-top:16px">
    @include('layouts.partials.pager', ['paginator' => $vaccins])
</div>
@endsection
