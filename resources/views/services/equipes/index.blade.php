@extends('layouts.portal')

@section('title', 'Équipes')
@section('page-title', 'Équipes terrain')
@section('page-subtitle', 'Créez les comptes de vos équipes et suivez leur activité')

@section('page-actions')
    <a href="{{ route('services.equipes.create') }}" class="ds-btn ds-btn-primary">+ Nouvelle équipe</a>
@endsection

@section('content')
<div class="ds-stats" style="margin-bottom:20px">
    <x-stat label="Équipes" :value="$equipes->count()" />
    <x-stat label="Actives" :value="$equipes->where('etat_compte', 'actif')->count()" type="success" />
    <x-stat label="Désactivées" :value="$equipes->where('etat_compte', '!=', 'actif')->count()" type="neutral" />
    <x-stat label="Agents rattachés" :value="$agents->sum()" type="info" />
</div>

<x-card flush>
    @if($equipes->isEmpty())
        <x-empty title="Aucune équipe">Créez votre première équipe avec le bouton « Nouvelle équipe ».</x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Équipe</th>
                    <th>Contact</th>
                    <th>Agents</th>
                    <th>Interventions</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipes as $equipe)
                    @php $actif = $equipe->etat_compte === 'actif'; @endphp
                    <tr>
                        <td>
                            <strong>{{ $equipe->nom }}</strong>
                            @if($equipe->adresse)<div class="ds-muted" style="font-size:12px">{{ $equipe->adresse }}</div>@endif
                        </td>
                        <td>
                            {{ $equipe->email ?? '—' }}
                            @if($equipe->telephone)<div class="ds-muted" style="font-size:12px">{{ $equipe->telephone }}</div>@endif
                        </td>
                        <td>{{ $agents[$equipe->id] ?? 0 }}</td>
                        <td>{{ $interventions[$equipe->id] ?? 0 }}</td>
                        <td><x-badge :type="$actif ? 'success' : 'neutral'">{{ $actif ? 'Active' : 'Désactivée' }}</x-badge></td>
                        <td>
                            <div class="ds-row-actions">
                                <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.equipes.edit', $equipe->id) }}">Modifier</a>

                                <form method="POST" action="{{ route('services.equipes.statut', $equipe->id) }}"
                                      onsubmit="return confirm('{{ $actif ? 'Désactiver cette équipe ? Elle ne pourra plus se connecter.' : 'Réactiver cette équipe ?' }}')">
                                    @csrf
                                    <button class="ds-btn ds-btn-ghost ds-btn-sm" type="submit">{{ $actif ? 'Désactiver' : 'Réactiver' }}</button>
                                </form>

                                @if(($interventions[$equipe->id] ?? 0) === 0)
                                    <form method="POST" action="{{ route('services.equipes.destroy', $equipe->id) }}"
                                          onsubmit="return confirm('Supprimer définitivement cette équipe ? Ses agents seront détachés.')">
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

<p class="ds-muted" style="margin-top:12px;font-size:12.5px">
    Une équipe qui a déjà des interventions ne peut pas être supprimée (son historique serait effacé) : désactivez-la.
</p>
@endsection
