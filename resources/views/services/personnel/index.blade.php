@extends('layouts.portal')

@section('title', 'Personnel')
@section('page-title', 'Personnel')
@section('page-subtitle', 'Agents, fonctions et rattachement aux équipes')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.personnel.gardes') }}">Planning des gardes</a>
    <a class="ds-btn ds-btn-primary" href="{{ route('services.personnel.create') }}">+ Nouvel agent</a>
@endsection

@section('content')

<div class="ds-stats ds-section">
    <x-stat label="Agents"        :value="$stats['total']" />
    <x-stat label="Actifs"        :value="$stats['actifs']"  type="success" />
    <x-stat label="En congé"      :value="$stats['conges']"  type="warning" />
    <x-stat label="De garde maintenant" :value="$stats['en_garde']" type="info" />
</div>

<x-card flush>
    <form method="GET" action="{{ route('services.personnel.index') }}" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ request('q') }}" placeholder="Nom, matricule, fonction…">
        <select class="ds-select" name="statut">
            <option value="">Tous les statuts</option>
            @foreach($statuts as $s)
                <option value="{{ $s }}" @selected(request('statut') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select class="ds-select" name="equipe_id">
            <option value="">Toutes les équipes</option>
            @foreach($equipes as $e)
                <option value="{{ $e->id }}" @selected((string) request('equipe_id') === (string) $e->id)>{{ $e->nom }}</option>
            @endforeach
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
    </form>

    @if($agents->isEmpty())
        <x-empty title="Aucun agent">Ajoutez votre premier agent pour constituer vos équipes.</x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Agent</th>
                    <th>Fonction</th>
                    <th>Équipe</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($agents as $a)
                    <tr>
                        <td>{{ $a->matricule }}</td>
                        <td><strong>{{ $a->nom_complet }}</strong>@if($a->grade)<div class="ds-muted">{{ $a->grade }}</div>@endif</td>
                        <td>{{ $a->fonction }}</td>
                        <td>{{ $a->equipe?->nom ?? '—' }}</td>
                        <td>{{ $a->telephone ?: '—' }}</td>
                        <td><x-badge :status="$a->statut" /></td>
                        <td class="ds-row-actions">
                            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.personnel.edit', $a->id) }}">Modifier</a>
                            <form method="POST" action="{{ route('services.personnel.destroy', $a->id) }}"
                                  onsubmit="return confirm('Supprimer cet agent ?')">
                                @csrf @method('DELETE')
                                <button class="ds-btn ds-btn-ghost ds-btn-sm ds-text-danger" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

@include('layouts.partials.pager', ['paginator' => $agents])

@endsection
