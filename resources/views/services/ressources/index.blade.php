@extends('layouts.portal')

@section('title', 'Ressources')
@section('page-title', 'Ressources')
@section('page-subtitle', 'Véhicules, matériel et stocks de votre service')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.ressources.stocks.create') }}">+ Article de stock</a>
    <a class="ds-btn ds-btn-primary" href="{{ route('services.ressources.vehicules.create') }}">+ Véhicule</a>
@endsection

@section('content')

<div class="ds-stats ds-section">
    <x-stat label="Véhicules"          :value="$stats['vehicules']" />
    <x-stat label="Disponibles"        :value="$stats['disponibles']" type="success" />
    <x-stat label="Maintenance à suivre" :value="$stats['maintenance']" type="warning" />
    <x-stat label="Stocks sous le seuil" :value="$stats['sous_seuil']" type="warning" />
    <x-stat label="Périmés"            :value="$stats['expires']" type="danger" />
    <x-stat label="Expirent sous 30 j" :value="$stats['expire_bientot']" type="warning" />
</div>

<x-card title="Véhicules" flush class="ds-section">
    @if($vehicules->isEmpty())
        <x-empty title="Aucun véhicule">Ajoutez vos ambulances, camions ou véhicules d'intervention.</x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Immatriculation</th><th>Type</th><th>Modèle</th><th>Équipe</th>
                    <th>Km</th><th>Prochaine maintenance</th><th>Statut</th><th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicules as $v)
                    <tr>
                        <td><strong>{{ $v->immatriculation }}</strong></td>
                        <td>{{ ucfirst($v->type) }}</td>
                        <td>{{ $v->marque_modele ?: '—' }}</td>
                        <td>{{ $v->equipe?->nom ?? '—' }}</td>
                        <td>{{ $v->kilometrage !== null ? number_format($v->kilometrage, 0, ',', ' ') : '—' }}</td>
                        <td>
                            @if($v->prochaine_maintenance)
                                {{ $v->prochaine_maintenance->format('d/m/Y') }}
                                @if($v->maintenanceProche())<x-badge type="warning">À prévoir</x-badge>@endif
                            @else — @endif
                        </td>
                        <td><x-badge :status="$v->statut" /></td>
                        <td class="ds-row-actions">
                            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.ressources.vehicules.edit', $v->id) }}">Modifier</a>
                            <form method="POST" action="{{ route('services.ressources.vehicules.destroy', $v->id) }}"
                                  onsubmit="return confirm('Supprimer ce véhicule ?')">
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

<x-card title="Stocks (médicaments, vaccins, matériel)" flush>
    @if($stocks->isEmpty())
        <x-empty title="Aucun article en stock">Suivez vos quantités et les dates d'expiration.</x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Désignation</th><th>Catégorie</th><th>Lot</th><th>Quantité</th>
                    <th>Seuil</th><th>Expiration</th><th>Alertes</th><th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $s)
                    <tr>
                        <td><strong>{{ $s->designation }}</strong></td>
                        <td>{{ ucfirst($s->categorie) }}</td>
                        <td>{{ $s->lot ?: '—' }}</td>
                        <td>{{ $s->quantite }} {{ $s->unite }}</td>
                        <td>{{ $s->seuil_alerte ?: '—' }}</td>
                        <td>{{ $s->date_expiration?->format('d/m/Y') ?? '—' }}</td>
                        <td>
                            @if($s->estExpire())<x-badge type="danger">Périmé</x-badge>
                            @elseif($s->expireBientot())<x-badge type="warning">Expire bientôt</x-badge>@endif
                            @if($s->estSousSeuil())<x-badge type="warning">Sous le seuil</x-badge>@endif
                        </td>
                        <td class="ds-row-actions">
                            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.ressources.stocks.edit', $s->id) }}">Modifier</a>
                            <form method="POST" action="{{ route('services.ressources.stocks.destroy', $s->id) }}"
                                  onsubmit="return confirm('Supprimer cet article ?')">
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

@endsection
