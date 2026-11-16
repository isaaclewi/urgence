@extends('layouts.portal')

@section('title', 'Mon espace')
@section('page-title', 'Mon espace')
@section('page-subtitle', $agent->nom_complet . ' · ' . $agent->matricule)

@section('content')
<div class="ds-stats ds-section">
    <x-stat label="Service" :value="$service?->nom ?? '—'" />
    <x-stat label="Équipe" :value="$equipe?->nom ?? 'Aucune'" />
    <x-stat label="Fonction" :value="$agent->fonction" />
    <x-stat label="Garde" :value="$enGarde ? 'En garde' : 'Hors garde'" :type="$enGarde ? 'success' : 'info'" />
</div>

@unless($equipe)
<x-card>
    <p>Vous n'êtes rattaché à aucune équipe terrain : les alertes ne s'affichent ici que pour les équipes. Votre service peut vous affecter à une équipe depuis la fiche du personnel.</p>
</x-card>
@endunless

<x-card>
    <h3 style="margin-bottom:.75rem">Mes gardes à venir</h3>
    @forelse($gardes as $g)
        <div style="padding:.5rem 0;border-bottom:1px solid #E2E8F0">
            <strong>{{ ucfirst($g->type) }}</strong> — du {{ $g->date_debut->format('d/m/Y H:i') }} au {{ $g->date_fin->format('d/m/Y H:i') }}
            @if($g->note)<br><small>{{ $g->note }}</small>@endif
        </div>
    @empty
        <p>Aucune garde planifiée.</p>
    @endforelse
</x-card>
@endsection
