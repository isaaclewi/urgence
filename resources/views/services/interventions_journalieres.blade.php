@extends('layouts.portal')

@section('title', 'Interventions')
@section('page-title', 'Interventions')
@section('page-subtitle', 'Suivi des interventions de vos équipes')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.exports.index') }}">Rapports & exports</a>
@endsection

@section('content')
@php
    $statuts = ['en_cours' => 'En cours', 'terminee' => 'Terminée'];
    $typeStatut = fn ($s) => match ($s) { 'en_cours' => 'info', 'terminee' => 'success', default => 'neutral' };
    $filtre = request()->hasAny(['du', 'au', 'equipe', 'statut']);
@endphp

<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="Interventions" :value="$stats['total']" />
    <x-stat label="En cours" :value="$stats['encours']" type="info" />
    <x-stat label="Terminées" :value="$stats['terminee']" type="success" />
    <x-stat label="Équipes mobilisées" :value="$stats['equipes']" />
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="date" name="du" value="{{ request('du') }}" aria-label="Du" style="flex:1 1 150px">
        <input class="ds-input" type="date" name="au" value="{{ request('au') }}" aria-label="Au" style="flex:1 1 150px">
        <select class="ds-select" name="equipe">
            <option value="">Toutes les équipes</option>
            @foreach($equipes as $e)
                <option value="{{ $e->id }}" @selected((string) request('equipe') === (string) $e->id)>{{ $e->nom }}</option>
            @endforeach
        </select>
        <select class="ds-select" name="statut">
            <option value="">Tous statuts</option>
            @foreach($statuts as $val => $lab)
                <option value="{{ $val }}" @selected(request('statut') === $val)>{{ $lab }}</option>
            @endforeach
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
        <a class="ds-btn ds-btn-ghost" href="{{ route('services.interventions.journalieres', ['du' => today()->toDateString(), 'au' => today()->toDateString()]) }}">Aujourd'hui</a>
        <a class="ds-btn ds-btn-ghost" href="{{ route('services.interventions.journalieres', ['du' => today()->subDays(6)->toDateString(), 'au' => today()->toDateString()]) }}">7 jours</a>
        @if($filtre)
            <a class="ds-btn ds-btn-ghost" href="{{ route('services.interventions.journalieres') }}">Tout afficher</a>
        @endif
    </form>

    @if($interventions->isEmpty())
        <x-empty title="Aucune intervention">
            {{ $filtre ? 'Aucun résultat pour ces filtres.' : "Une intervention est enregistrée dès qu'une équipe démarre une mission affectée." }}
        </x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Urgence</th>
                    <th>Équipe</th>
                    <th>Statut</th>
                    <th>Compte rendu</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($interventions as $i)
                    <tr>
                        <td>
                            <strong>{{ $i->titre }}</strong>
                            <div class="ds-muted" style="font-size:12px">{{ $i->localisation ?: 'Lieu non précisé' }}</div>
                        </td>
                        <td>{{ $i->equipe_nom }}</td>
                        <td><x-badge :type="$typeStatut($i->statut)">{{ $statuts[$i->statut] ?? ($i->statut ?: '—') }}</x-badge></td>
                        <td style="max-width:280px">{{ \Illuminate\Support\Str::limit($i->commentaire ?: '—', 90) }}</td>
                        <td style="white-space:nowrap">{{ \Carbon\Carbon::parse($i->created_at)->format('d/m/Y H:i') }}</td>
                        <td>
                            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.exports.intervention', $i->id) }}">Rapport PDF</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

<div style="margin-top:16px">
    @include('layouts.partials.pager', ['paginator' => $interventions])
</div>
@endsection
