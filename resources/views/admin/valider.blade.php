@extends('layouts.portal')

@section('title', 'Vérifications')
@section('page-title', 'Vérification des comptes')
@section('page-subtitle', 'Contrôlez la pièce d’identité puis activez le compte')

@section('content')
<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="À vérifier" :value="$compteurs['inactif']" type="warning" />
    <x-stat label="Comptes actifs" :value="$compteurs['actif']" type="success" />
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ $q }}" placeholder="Nom, prénom ou matricule…">
        <select class="ds-select" name="statut">
            <option value="inactif" @selected($statut === 'inactif')>À vérifier</option>
            <option value="actif" @selected($statut === 'actif')>Actifs</option>
            <option value="" @selected($statut === '')>Tous</option>
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
    </form>

    @if($citoyens->isEmpty())
        <x-empty title="Rien à afficher">{{ $statut === 'inactif' ? 'Tous les comptes sont vérifiés.' : 'Aucun compte ne correspond.' }}</x-empty>
    @else
        <x-table>
            <thead>
                <tr><th>Citoyen</th><th>Inscrit le</th><th>Pièce d’identité</th><th>Statut</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($citoyens as $c)
                    @php $actif = $c->etat_compte === 'actif'; @endphp
                    <tr>
                        <td>
                            <strong>{{ $c->nom }} {{ $c->prenom }}</strong>
                            <div class="ds-muted" style="font-size:12px">{{ $c->matricule }} · {{ $c->email }}</div>
                        </td>
                        <td style="white-space:nowrap">{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($c->piece_identite)
                                {{-- Le fichier est chiffré sur Supabase : on ne le déchiffre qu'à la demande --}}
                                <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('citoyen.piece', $c->id) }}" target="_blank" rel="noopener">Voir la pièce</a>
                            @else
                                <span class="ds-muted">Aucune</span>
                            @endif
                        </td>
                        <td><x-badge :type="$actif ? 'success' : 'warning'">{{ $actif ? 'Actif' : 'À vérifier' }}</x-badge></td>
                        <td>
                            <form method="POST" action="{{ route($actif ? 'admin.desactiver' : 'admin.activer', $c->id) }}">
                                @csrf
                                <button class="ds-btn {{ $actif ? 'ds-btn-ghost' : 'ds-btn-primary' }} ds-btn-sm" type="submit">{{ $actif ? 'Désactiver' : 'Activer' }}</button>
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
