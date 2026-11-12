@extends('layouts.portal')

@section('title', 'Nouveau bilan de santé')
@section('page-title', 'Nouveau bilan de santé')
@section('page-subtitle', $citoyen ? 'Bilan de ' . $citoyen->nom . ' ' . $citoyen->prenom : 'Choisissez d\'abord le citoyen')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.citoyensBilan') }}">← Retour</a>
@endsection

@section('content')
@if($citoyen)
    <x-card>
        <div style="margin-bottom:16px">
            <strong>{{ $citoyen->nom }} {{ $citoyen->prenom }}</strong>
            <span class="ds-muted"> — {{ $citoyen->matricule }}</span>
            <a class="ds-btn ds-btn-ghost ds-btn-sm" href="{{ route('services.citoyensBilanCreate') }}">Changer de citoyen</a>
        </div>
        <form method="POST" action="{{ route('services.citoyensBilanStore') }}">
            @csrf
            <input type="hidden" name="citoyen_id" value="{{ $citoyen->id }}">
            @include('services.partials.bilan-form')
        </form>
    </x-card>
@else
    <x-card title="Rechercher le citoyen">
        <form method="GET" action="{{ route('services.citoyensBilanCreate') }}" class="ds-filters" style="padding:0 0 12px;border:0">
            <input class="ds-input" type="search" name="q" value="{{ request('q') }}" autofocus
                   placeholder="Matricule, nom, prénom ou téléphone (2 caractères minimum)">
            <button class="ds-btn ds-btn-primary" type="submit">Rechercher</button>
        </form>

        @if($resultats === null)
            <p class="ds-muted" style="margin:0">Seuls les comptes citoyens validés par l'administration peuvent avoir un bilan.</p>
        @elseif($resultats->isEmpty())
            <x-empty title="Aucun citoyen trouvé">Vérifiez l'orthographe ou le matricule. Le compte doit être actif.</x-empty>
        @else
            <x-table>
                <tbody>
                    @foreach($resultats as $c)
                        <tr>
                            <td><strong>{{ $c->nom }} {{ $c->prenom }}</strong><div class="ds-muted" style="font-size:12px">{{ $c->matricule }}</div></td>
                            <td style="text-align:right">
                                @if($c->bilan_id)
                                    <x-badge type="info">A déjà un bilan</x-badge>
                                    <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.citoyensBilanEdit', $c->bilan_id) }}">Modifier</a>
                                @else
                                    <a class="ds-btn ds-btn-primary ds-btn-sm" href="{{ route('services.citoyensBilanCreate', ['citoyen_id' => $c->id]) }}">Créer le bilan</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
            @if($resultats->count() === 15)
                <p class="ds-muted" style="margin:10px 0 0;font-size:12px">Résultats limités à 15 : précisez la recherche.</p>
            @endif
        @endif
    </x-card>
@endif
@endsection
