@extends('services.master')

@section('title', 'Interventions')
@section('page-title', 'Interventions')
@section('page-subtitle', 'Suivi des interventions de vos équipes')

@section('content')

<x-card title="Interventions des équipes" flush>
    @if($interventions->isEmpty())
        <x-empty title="Aucune intervention">Les interventions de vos équipes apparaîtront ici.</x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Alerte</th>
                    <th>Lieu</th>
                    <th>Équipe</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($interventions as $i)
                    <tr>
                        <td><strong>{{ $i->titre }}</strong></td>
                        <td>{{ $i->localisation ?: '—' }}</td>
                        <td>{{ $i->equipe_nom }}</td>
                        <td><x-badge :status="$i->statut" /></td>
                        <td>{{ \Carbon\Carbon::parse($i->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="ds-row-actions">
                            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.exports.intervention', $i->id) }}">Rapport PDF</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

@endsection
