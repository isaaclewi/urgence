@extends('layouts.portal')

@section('title', 'Urgences')
@section('page-title', 'Urgences signalées')
@section('page-subtitle', 'Toutes les alertes envoyées par les citoyens, tous services confondus')

@section('content')
<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="Total" :value="$stats['total']" />
    <x-stat label="En attente" :value="$stats['attente']" type="warning" />
    <x-stat label="Pris en charge" :value="$stats['en_cours']" type="info" />
    <x-stat label="Terminées" :value="$stats['terminees']" type="success" />
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ $q }}" placeholder="Titre, lieu ou nom du citoyen…">
        <select class="ds-select" name="statut">
            <option value="">Tous les statuts</option>
            @foreach($statuts as $s)<option value="{{ $s }}" @selected($statut === $s)>{{ ucfirst($s) }}</option>@endforeach
        </select>
        <select class="ds-select" name="type">
            <option value="">Tous les types</option>
            @foreach($types as $t)<option value="{{ $t }}" @selected($type === $t)>{{ ucfirst($t) }}</option>@endforeach
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
        @if($q || $statut || $type)<a class="ds-btn ds-btn-ghost" href="{{ route('admin.alertes') }}">Réinitialiser</a>@endif
    </form>

    @if($alertes->isEmpty())
        <x-empty title="Aucune urgence">Aucune alerte ne correspond à ces filtres.</x-empty>
    @else
        <x-table>
            <thead>
                <tr><th>Date</th><th>Alerte</th><th>Lieu</th><th>Citoyen</th><th>Service</th><th>Statut</th></tr>
            </thead>
            <tbody>
                @foreach($alertes as $a)
                    <tr>
                        <td style="white-space:nowrap">{{ $a->created_at?->format('d/m/Y') }}<div class="ds-muted" style="font-size:12px">{{ $a->created_at?->format('H:i') }}</div></td>
                        <td>
                            <strong>{{ $a->titre }}</strong>
                            @if($a->type_alerte)<div class="ds-muted" style="font-size:12px">{{ ucfirst($a->type_alerte) }}</div>@endif
                        </td>
                        <td>{{ $a->localisation ?: '—' }}</td>
                        <td>
                            @if($a->citoyen)
                                {{ $a->citoyen->nom }} {{ $a->citoyen->prenom }}
                            @else
                                <span class="ds-muted">Anonyme</span>
                            @endif
                        </td>
                        <td>{{ $a->service?->nom ?? '—' }}</td>
                        <td><x-badge :status="$a->statut">{{ $a->statut ? ucfirst($a->statut) : 'Non défini' }}</x-badge></td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

<div style="margin-top:16px">@include('layouts.partials.pager', ['paginator' => $alertes])</div>
@endsection
