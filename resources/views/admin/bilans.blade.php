@extends('layouts.portal')

@section('title', 'Bilans de santé')
@section('page-title', 'Bilans de santé — supervision')
@section('page-subtitle', 'Qui a créé et qui a consulté les bilans. Le contenu médical n’est pas affiché ici.')

@section('content')
@php
    $actions = [
        'consultation' => ['Consultation', 'info'],
        'creation'     => ['Création', 'success'],
        'modification' => ['Modification', 'warning'],
        'suppression'  => ['Suppression', 'danger'],
    ];
@endphp

<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="Bilans enregistrés" :value="$stats['total']" />
    <x-stat label="Consultations (7 jours)" :value="$stats['acces_7j']" type="info" />
    <x-stat label="Citoyens actifs sans bilan" :value="$stats['sans_bilan']" type="warning" />
    <x-stat label="Sans service créateur" :value="$stats['sans_auteur']" hint="Bilans antérieurs au suivi" type="neutral" />
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ $q }}" placeholder="Rechercher par nom, prénom ou matricule…">
        <button class="ds-btn ds-btn-secondary" type="submit">Rechercher</button>
        @if($q)
            <a class="ds-btn ds-btn-ghost" href="{{ route('admin.bilanSante') }}">Réinitialiser</a>
        @endif
    </form>

    @if($bilans->isEmpty())
        <x-empty title="Aucun bilan">{{ $q ? 'Aucun résultat pour cette recherche.' : 'Aucun bilan n’a encore été créé par les services.' }}</x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Citoyen</th>
                    <th>Créé par</th>
                    <th>Dernière modification</th>
                    <th>Actions tracées</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bilans as $b)
                    @php $a = $acces->get($b->id); @endphp
                    <tr>
                        <td>
                            <strong>{{ $b->citoyen?->nom }} {{ $b->citoyen?->prenom }}</strong>
                            <div class="ds-muted" style="font-size:12px">{{ $b->citoyen?->matricule ?? 'Compte supprimé' }}</div>
                        </td>
                        <td>{{ $b->service?->nom ?? '—' }}</td>
                        <td style="white-space:nowrap">
                            {{ $b->updated_at?->format('d/m/Y H:i') }}
                            @if($b->modifiePar)
                                <div class="ds-muted" style="font-size:12px">par {{ $b->modifiePar->nom }}</div>
                            @endif
                        </td>
                        <td>
                            @if($a)
                                {{ $a->total }}
                                <div class="ds-muted" style="font-size:12px">dernier : {{ \Illuminate\Support\Carbon::parse($a->dernier)->format('d/m/Y H:i') }}</div>
                            @else
                                <span class="ds-muted">Aucune</span>
                            @endif
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

<x-card title="Dernières actions sur les bilans" flush style="margin-top:20px">
    @if($journal->isEmpty())
        <x-empty title="Journal vide">Aucune action enregistrée pour l’instant.</x-empty>
    @else
        <x-table>
            <thead>
                <tr><th>Date</th><th>Action</th><th>Service</th><th>Citoyen concerné</th></tr>
            </thead>
            <tbody>
                @foreach($journal as $j)
                    @php $c = $citoyens->get($j->citoyen_id); [$libelle, $type] = $actions[$j->action] ?? [ucfirst($j->action), 'neutral']; @endphp
                    <tr>
                        <td style="white-space:nowrap">{{ $j->created_at?->format('d/m/Y H:i') }}</td>
                        <td><x-badge :type="$type">{{ $libelle }}</x-badge></td>
                        <td>{{ $j->service?->nom ?? 'Service supprimé' }}</td>
                        <td>
                            @if($c) {{ $c->nom }} {{ $c->prenom }} <span class="ds-muted">({{ $c->matricule }})</span>
                            @else <span class="ds-muted">Compte supprimé</span> @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>
@endsection
