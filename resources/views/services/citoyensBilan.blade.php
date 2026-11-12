@extends('layouts.portal')

@section('title', 'Bilans de santé')
@section('page-title', 'Bilans de santé')
@section('page-subtitle', 'Créez et consultez les bilans de santé des citoyens')

@section('page-actions')
    <a class="ds-btn ds-btn-primary" href="{{ route('services.citoyensBilanCreate') }}">+ Nouveau bilan</a>
@endsection

@section('content')
<div class="ds-alert ds-alert-info" style="margin-bottom:16px">
    Données de santé confidentielles : chaque consultation, modification ou suppression d'un bilan est enregistrée avec le nom de votre service.
</div>

<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="Bilans" :value="$stats['total']" />
    <x-stat label="Créés par votre service" :value="$stats['miens']" type="info" />
    <x-stat label="Mis à jour (30 j)" :value="$stats['recents']" type="success" />
    <x-stat label="Citoyens sans bilan" :value="$stats['sans']" type="warning" />
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher par nom, prénom ou matricule…">
        <button class="ds-btn ds-btn-secondary" type="submit">Rechercher</button>
        @if(request('q'))
            <a class="ds-btn ds-btn-ghost" href="{{ route('services.citoyensBilan') }}">Réinitialiser</a>
        @endif
    </form>

    @if($bilans->isEmpty())
        <x-empty title="Aucun bilan">
            {{ request('q') ? 'Aucun résultat pour cette recherche.' : 'Créez le premier bilan avec le bouton « Nouveau bilan ».' }}
        </x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Citoyen</th>
                    <th>Groupe sanguin</th>
                    <th>Créé par</th>
                    <th>Dernière mise à jour</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($bilans as $b)
                    <tr>
                        <td>
                            <strong>{{ $b->citoyen?->nom }} {{ $b->citoyen?->prenom }}</strong>
                            <div class="ds-muted" style="font-size:12px">{{ $b->citoyen?->matricule ?? 'Compte supprimé' }}</div>
                        </td>
                        <td>
                            @if($gs = $b->valeur('groupe_sanguin'))
                                <x-badge type="danger">{{ $gs }}</x-badge>
                            @else — @endif
                        </td>
                        <td>
                            @if($b->creePar((int) $service->id))
                                <x-badge type="info">Votre service</x-badge>
                            @else
                                <span class="ds-muted">Autre service</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">{{ $b->updated_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="ds-row-actions">
                                <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.citoyensBilanShow', $b->id) }}">Consulter</a>
                                <a class="ds-btn ds-btn-ghost ds-btn-sm" href="{{ route('services.citoyensBilanEdit', $b->id) }}">Modifier</a>
                                @if($b->creePar((int) $service->id))
                                    <form method="POST" action="{{ route('services.citoyensBilanDestroy', $b->id) }}"
                                          onsubmit="return confirm('Supprimer définitivement ce bilan de santé ?')">
                                        @csrf @method('DELETE')
                                        <button class="ds-btn ds-btn-ghost ds-btn-sm ds-text-danger" type="submit">Supprimer</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

<div style="margin-top:16px">
    @include('layouts.partials.pager', ['paginator' => $bilans])
</div>
@endsection
