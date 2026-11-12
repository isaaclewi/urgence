@extends('layouts.portal')

@section('title', 'Citoyens')
@section('page-title', 'Citoyens')
@section('page-subtitle', 'Tous les comptes citoyens de la plateforme')

@section('content')
<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ $q }}" placeholder="Nom, prénom, matricule, e-mail, téléphone…">
        <select class="ds-select" name="statut">
            <option value="">Tous les statuts</option>
            <option value="actif" @selected($statut === 'actif')>Actifs</option>
            <option value="inactif" @selected($statut === 'inactif')>Non actifs</option>
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
        @if($q || $statut)<a class="ds-btn ds-btn-ghost" href="{{ route('admin.users') }}">Réinitialiser</a>@endif
    </form>

    @if($citoyens->isEmpty())
        <x-empty title="Aucun citoyen">Aucun compte ne correspond à cette recherche.</x-empty>
    @else
        <x-table>
            <thead>
                <tr><th>Citoyen</th><th>Contact</th><th>Adresse</th><th>Statut</th><th>Inscrit le</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($citoyens as $c)
                    <tr>
                        <td>
                            <strong>{{ $c->nom }} {{ $c->prenom }}</strong>
                            <div class="ds-muted" style="font-size:12px">{{ $c->matricule }}@if($c->sexe) · {{ $c->sexe }}@endif</div>
                        </td>
                        <td>{{ $c->email }}@if($c->telephone)<div class="ds-muted" style="font-size:12px">{{ $c->telephone }}</div>@endif</td>
                        <td>{{ $c->adresse ?: '—' }}</td>
                        <td><x-badge :type="$c->etat_compte === 'actif' ? 'success' : 'neutral'">{{ $c->etat_compte === 'actif' ? 'Actif' : 'Non actif' }}</x-badge></td>
                        <td style="white-space:nowrap">{{ $c->created_at?->format('d/m/Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.delete', $c->id) }}"
                                  onsubmit="return confirm('Supprimer définitivement ce compte ? Son bilan de santé et sa pièce d’identité seront aussi supprimés. Cette action est irréversible.')">
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

<div style="margin-top:16px">@include('layouts.partials.pager', ['paginator' => $citoyens])</div>
@endsection
