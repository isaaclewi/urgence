@extends('layouts.portal')

@section('title', 'Vaccination — Programmes')
@section('page-title', 'Vaccination')
@section('page-subtitle', 'Programmes de vaccination de votre service')

@section('page-actions')
    <a class="ds-btn ds-btn-primary" href="{{ route('services.vaccinationCreate') }}">+ Nouveau programme</a>
@endsection

@section('content')
@include('services.partials.vaccination-tabs', ['actif' => 'programmes'])

<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="Programmes" :value="$stats['total']" />
    <x-stat label="En cours" :value="$stats['en_cours']" type="success" />
    <x-stat label="À venir" :value="$stats['a_venir']" type="info" />
    <x-stat label="Terminés" :value="$stats['termines']" />
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un programme ou un organisme…">
        <select class="ds-select" name="categorie">
            <option value="">Toutes catégories</option>
            @foreach($categories as $cle => $label)
                <option value="{{ $cle }}" @selected(request('categorie') === $cle)>{{ $label }}</option>
            @endforeach
        </select>
        <select class="ds-select" name="statut">
            <option value="">Tous statuts</option>
            <option value="en_cours" @selected(request('statut') === 'en_cours')>En cours</option>
            <option value="a_venir" @selected(request('statut') === 'a_venir')>À venir</option>
            <option value="termine" @selected(request('statut') === 'termine')>Terminés</option>
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
        @if(request()->hasAny(['q', 'categorie', 'statut']))
            <a class="ds-btn ds-btn-ghost" href="{{ route('services.vaccinationIndex') }}">Réinitialiser</a>
        @endif
    </form>

    @if($programmes->isEmpty())
        <x-empty title="Aucun programme">
            @if(request()->hasAny(['q', 'categorie', 'statut']))
                Aucun résultat pour ces filtres.
            @else
                Créez votre premier programme avec le bouton « Nouveau programme ».
            @endif
        </x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Programme</th>
                    <th>Catégorie</th>
                    <th>Âge cible</th>
                    <th>Période</th>
                    <th>Vaccins</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($programmes as $p)
                    @php $mien = $p->appartientA((int) $service->id); @endphp
                    <tr>
                        <td>
                            <strong>{{ $p->nom_programme }}</strong>
                            <div class="ds-muted" style="font-size:12px">
                                {{ $p->organisme_resp ?: 'Organisme non renseigné' }}
                                @unless($mien) · <em>Programme national</em> @endunless
                            </div>
                        </td>
                        <td>{{ $categories[$p->categorie] ?? ($p->categorie ?: '—') }}</td>
                        <td>{{ $p->age_cible ?: '—' }}</td>
                        <td style="white-space:nowrap">
                            {{ $p->date_debut?->format('d/m/Y') ?? '—' }} → {{ $p->date_fin?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td>
                            {{ $p->vaccins_count }}@if($p->nb_vaccins !== null) <span class="ds-muted">/ {{ $p->nb_vaccins }} prévus</span>@endif
                        </td>
                        <td><x-badge :type="$p->statutType()">{{ $p->statut() }}</x-badge></td>
                        <td>
                            @if($mien)
                                <div class="ds-row-actions">
                                    <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.vaccinationEdit', $p->id) }}">Modifier</a>
                                    <form method="POST" action="{{ route('services.vaccinationDestroy', $p->id) }}"
                                          onsubmit="return confirm('Supprimer ce programme ?{{ $p->vaccins_count ? ' Ses ' . $p->vaccins_count . ' vaccin(s) seront conservés sans programme.' : '' }}')">
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
    @include('layouts.partials.pager', ['paginator' => $programmes])
</div>
@endsection
