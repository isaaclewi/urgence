@extends('layouts.portal')

@section('title', 'Citoyens')
@section('page-title', 'Citoyens')
@section('page-subtitle', 'Utilisateurs inscrits sur la plateforme')

@section('content')

<div class="ds-stats ds-section">
    <x-stat label="Citoyens inscrits" :value="$stats['total']" />
    <x-stat label="Comptes actifs"    :value="$stats['actifs']" type="success" />
    <x-stat label="Hommes"            :value="$stats['hommes']" type="info" />
    <x-stat label="Femmes"            :value="$stats['femmes']" type="info" />
</div>

<x-card flush>
    <form method="GET" action="{{ route('services.citoyens') }}" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ request('q') }}" placeholder="Nom, email, téléphone, matricule, adresse…">
        <select class="ds-select" name="sexe">
            <option value="">Tous les genres</option>
            <option value="M" @selected(request('sexe') === 'M')>Hommes</option>
            <option value="F" @selected(request('sexe') === 'F')>Femmes</option>
        </select>
        <select class="ds-select" name="etat">
            <option value="">Tous les états</option>
            <option value="actif" @selected(request('etat') === 'actif')>Actifs</option>
            <option value="inactif" @selected(request('etat') === 'inactif')>Inactifs</option>
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
        @if(request()->hasAny(['q', 'sexe', 'etat']))
            <a class="ds-btn ds-btn-ghost" href="{{ route('services.citoyens') }}">Réinitialiser</a>
        @endif
    </form>

    @if($citoyens->isEmpty())
        <x-empty title="Aucun citoyen trouvé">Modifiez votre recherche ou vos filtres.</x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Citoyen</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Adresse</th>
                    <th>Genre</th>
                    <th>État</th>
                    <th>Inscription</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citoyens as $c)
                    <tr>
                        <td>{{ $c->matricule ?: '—' }}</td>
                        <td><strong>{{ $c->nom }} {{ $c->prenom }}</strong></td>
                        <td>{{ $c->email }}</td>
                        <td>{{ $c->telephone ?: '—' }}</td>
                        <td>{{ $c->adresse ?: '—' }}</td>
                        <td>{{ $c->sexe === 'M' ? 'Homme' : ($c->sexe === 'F' ? 'Femme' : '—') }}</td>
                        <td><x-badge :status="$c->etat_compte ?: 'inactif'" /></td>
                        <td>{{ $c->created_at?->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

@include('layouts.partials.pager', ['paginator' => $citoyens])

@endsection
