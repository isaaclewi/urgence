@extends('layouts.portal')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Activité de ' . $service->nom)

@section('page-actions')
    <a class="ds-btn ds-btn-primary" href="{{ route('services.urgenceSignalee') }}">Voir les urgences</a>
@endsection

@section('content')

<div class="ds-stats ds-section">
    <x-stat label="Urgences en attente" :value="$stats['en_attente']" type="warning" hint="À prendre en charge" />
    <x-stat label="En cours"             :value="$stats['en_cours']"   type="info"    hint="Prises en charge ou transmises" />
    <x-stat label="Terminées"            :value="$stats['terminees']"  type="success" />
    <x-stat label="Délai de réponse moyen" :value="$stats['delai_moyen']" hint="30 derniers jours" />
</div>

<div class="ds-grid ds-grid-aside" style="grid-template-columns: 1fr minmax(240px, 320px)">
    <x-card flush title="Urgences récentes">
        <x-slot:actions>
            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.urgenceSignalee') }}">Tout voir</a>
        </x-slot:actions>

        @if($recentes->isEmpty())
            <x-empty title="Aucune urgence">Les nouvelles alertes apparaîtront ici.</x-empty>
        @else
            <x-table>
                <thead>
                    <tr><th>Alerte</th><th>Lieu</th><th>Statut</th><th>Reçue</th></tr>
                </thead>
                <tbody>
                    @foreach($recentes as $a)
                        <tr>
                            <td><strong>{{ $a->titre ?: 'Alerte sans titre' }}</strong>
                                @if($a->type_alerte)<div class="ds-muted">{{ ucfirst($a->type_alerte) }}</div>@endif</td>
                            <td>{{ $a->localisation ?: '—' }}</td>
                            <td><x-badge :status="$a->statut" /></td>
                            <td>{{ $a->created_at?->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        @endif
    </x-card>

    <div class="ds-stack">
        <x-card title="Votre service">
            <div><strong>{{ $service->nom }}</strong></div>
            <div class="ds-muted">{{ $service->email }}</div>
            @if($service->telephone)<div class="ds-muted">{{ $service->telephone }}</div>@endif
            @if($service->adresse)<div class="ds-muted">{{ $service->adresse }}</div>@endif
            <div style="margin-top:12px">
                <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.profil') }}">Modifier le profil</a>
            </div>
        </x-card>

        <x-card title="Citoyens">
            <div class="ds-stat-value" style="font:700 28px/1 'Sora','Inter',sans-serif">{{ $stats['citoyens_actifs'] }}</div>
            <div class="ds-muted">comptes citoyens actifs</div>
        </x-card>
    </div>
</div>
@endsection
