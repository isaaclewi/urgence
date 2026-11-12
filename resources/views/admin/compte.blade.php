@extends('layouts.portal')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d’ensemble de la plateforme')

@section('content')
<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="Citoyens" :value="$stats['citoyens']" />
    <x-stat label="Comptes à vérifier" :value="$stats['a_valider']" type="warning" />
    <x-stat label="Services" :value="$stats['services']" :hint="$stats['equipes'] . ' équipes terrain'" type="info" />
    <x-stat label="Urgences en attente" :value="$stats['alertes_attente']" :hint="$stats['alertes_24h'] . ' dans les dernières 24 h'" type="danger" />
</div>

<div class="ds-grid-aside">
    <x-card title="Urgences récentes" flush>
        <x-slot:actions><a class="ds-btn ds-btn-ghost ds-btn-sm" href="{{ route('admin.alertes') }}">Tout voir</a></x-slot:actions>
        @if($recentes->isEmpty())
            <x-empty title="Aucune urgence">Les alertes envoyées par les citoyens apparaîtront ici.</x-empty>
        @else
            <x-table>
                <thead><tr><th>Date</th><th>Alerte</th><th>Citoyen</th><th>Statut</th></tr></thead>
                <tbody>
                    @foreach($recentes as $a)
                        <tr>
                            <td style="white-space:nowrap">{{ $a->created_at?->format('d/m H:i') }}</td>
                            <td><strong>{{ $a->titre }}</strong>@if($a->type_alerte)<div class="ds-muted" style="font-size:12px">{{ ucfirst($a->type_alerte) }}</div>@endif</td>
                            <td>{{ $a->citoyen ? $a->citoyen->nom . ' ' . $a->citoyen->prenom : 'Anonyme' }}</td>
                            <td><x-badge :status="$a->statut">{{ $a->statut ? ucfirst($a->statut) : 'Non défini' }}</x-badge></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        @endif
    </x-card>

    <x-card title="Comptes à vérifier">
        <x-slot:actions><a class="ds-btn ds-btn-ghost ds-btn-sm" href="{{ route('admin.valider') }}">Ouvrir</a></x-slot:actions>
        @forelse($aValider as $c)
            <div style="display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid #F1F5F9">
                <div>
                    <strong>{{ $c->nom }} {{ $c->prenom }}</strong>
                    <div class="ds-muted" style="font-size:12px">{{ $c->matricule }}</div>
                </div>
                <span class="ds-muted" style="font-size:12px;white-space:nowrap">{{ $c->created_at?->format('d/m/Y') }}</span>
            </div>
        @empty
            <x-empty title="Rien à vérifier">Tous les comptes sont traités.</x-empty>
        @endforelse
    </x-card>
</div>
@endsection
