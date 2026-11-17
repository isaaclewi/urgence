@extends('layouts.portal')

@section('title', 'Vérifications')
@section('page-title', 'Vérification des comptes')
@section('page-subtitle', 'Contrôlez la pièce d’identité et le selfie, puis validez ou rejetez le dossier')

@section('content')
<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="À vérifier" :value="$compteurs['en_attente']" type="warning" />
    <x-stat label="Identités vérifiées" :value="$compteurs['verifie']" type="success" />
    <x-stat label="Dossiers rejetés" :value="$compteurs['rejete']" type="danger" />
</div>

<x-card flush>
    <form method="GET" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ $q }}" placeholder="Nom, prénom ou matricule…">
        <select class="ds-select" name="statut">
            <option value="en_attente" @selected($statut === 'en_attente')>À vérifier</option>
            <option value="verifie" @selected($statut === 'verifie')>Vérifiés</option>
            <option value="rejete" @selected($statut === 'rejete')>Rejetés</option>
            <option value="" @selected($statut === '')>Tous</option>
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
    </form>

    @if($citoyens->isEmpty())
        <x-empty title="Rien à afficher">{{ $statut === 'en_attente' ? 'Aucun dossier en attente.' : 'Aucun compte ne correspond.' }}</x-empty>
    @else
        <x-table>
            <thead>
                <tr><th>Citoyen</th><th>Inscrit le</th><th>Pièce</th><th>Email</th><th>Statut</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($citoyens as $c)
                    @php
                        $actif = $c->etat_compte === 'actif';
                        $type  = ['verifie' => 'success', 'rejete' => 'danger'][$c->statut_verification] ?? 'warning';
                        $lib   = ['verifie' => 'Vérifié', 'rejete' => 'Rejeté'][$c->statut_verification] ?? 'À vérifier';
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $c->nom }} {{ $c->prenom }}</strong>
                            <div class="ds-muted" style="font-size:12px">{{ $c->matricule }} · {{ $c->email }}</div>
                        </td>
                        <td style="white-space:nowrap">{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ \App\Support\Identite::TYPES[$c->type_piece] ?? '—' }}</td>
                        <td>
                            <x-badge :type="$c->email_verifie_le ? 'success' : 'neutral'">{{ $c->email_verifie_le ? 'Confirmé' : 'Non confirmé' }}</x-badge>
                        </td>
                        <td>
                            <x-badge :type="$type">{{ $lib }}</x-badge>
                            @if($c->statut_verification === 'verifie' && ! $actif)
                                <div class="ds-muted" style="font-size:12px">compte désactivé</div>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            <a class="ds-btn ds-btn-primary ds-btn-sm" href="{{ route('admin.valider.show', $c->id) }}">
                                {{ $c->statut_verification === 'en_attente' ? 'Contrôler' : 'Voir le dossier' }}
                            </a>
                            @if($c->statut_verification === 'verifie')
                                <form method="POST" action="{{ route($actif ? 'admin.desactiver' : 'admin.activer', $c->id) }}" style="display:inline">
                                    @csrf
                                    <button class="ds-btn ds-btn-ghost ds-btn-sm" type="submit">{{ $actif ? 'Désactiver' : 'Réactiver' }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

<div style="margin-top:16px">@include('layouts.partials.pager', ['paginator' => $citoyens])</div>
@endsection
