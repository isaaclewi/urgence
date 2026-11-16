@extends('layouts.portal')

@section('title', 'Suivi des inscriptions')
@section('page-title', 'Suivi des inscriptions')
@section('page-subtitle', 'Citoyens et personnel inscrits à vos programmes de vaccination')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.vaccinationIndex') }}">Mes programmes</a>
@endsection

@section('content')

<div class="ds-stats ds-section">
    <x-stat label="Inscriptions" :value="$stats['total']" />
    <x-stat label="En cours" :value="$stats['en_cours']" type="info" />
    <x-stat label="Terminés" :value="$stats['termine']" type="success" />
    <x-stat label="Personnel" :value="$stats['personnel']" />
    <x-stat label="Doses à venir (7 j)" :value="$stats['rappels']" type="warning" />
</div>

<x-card>
    <h3 style="margin-bottom:.75rem">Inscrire du personnel à un programme</h3>
    <form method="POST" action="{{ route('services.vaccinationSuivi.personnel') }}">
        @csrf
        <div class="ds-form-grid">
            <div class="ds-field">
                <label class="ds-label" for="programme_id">Programme *</label>
                <select class="ds-select" id="programme_id" name="programme_id" required>
                    <option value="">— Choisir —</option>
                    @foreach($programmes as $p)
                        <option value="{{ $p->id }}">{{ $p->nom_programme }}{{ $p->service_id ? '' : ' (national)' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-field">
                <label class="ds-label">Agents</label>
                <label style="display:block;margin-bottom:.4rem"><input type="checkbox" name="tous" value="1" id="tous"> Tout le personnel actif</label>
                <div id="liste-agents" style="max-height:150px;overflow:auto;border:1px solid #E2E8F0;border-radius:8px;padding:.5rem">
                    @forelse($agents as $a)
                        <label style="display:block"><input type="checkbox" name="agent_ids[]" value="{{ $a->id }}"> {{ $a->nom_complet }} <span class="ds-muted">({{ $a->matricule }})</span></label>
                    @empty
                        <span class="ds-muted">Aucun agent enregistré.</span>
                    @endforelse
                </div>
            </div>
        </div>
        <button class="ds-btn ds-btn-primary" type="submit">Inscrire et notifier</button>
    </form>
</x-card>

<x-card flush>
    <form method="GET" action="{{ route('services.vaccinationSuivi.index') }}" class="ds-filters">
        <input class="ds-input" type="search" name="q" value="{{ request('q') }}" placeholder="Nom, matricule…">
        <select class="ds-select" name="programme_id">
            <option value="">Tous les programmes</option>
            @foreach($programmes as $p)
                <option value="{{ $p->id }}" @selected((string) request('programme_id') === (string) $p->id)>{{ $p->nom_programme }}</option>
            @endforeach
        </select>
        <select class="ds-select" name="statut">
            <option value="">Tous les statuts</option>
            @foreach($statuts as $k => $l)
                <option value="{{ $k }}" @selected(request('statut') === $k)>{{ $l }}</option>
            @endforeach
        </select>
        <select class="ds-select" name="type">
            <option value="">Citoyens et personnel</option>
            <option value="citoyen" @selected(request('type') === 'citoyen')>Citoyens</option>
            <option value="personnel" @selected(request('type') === 'personnel')>Personnel</option>
        </select>
        <button class="ds-btn ds-btn-secondary" type="submit">Filtrer</button>
    </form>

    @if($inscriptions->isEmpty())
        <x-empty title="Aucune inscription">Les inscriptions apparaîtront ici dès qu'un citoyen ou un agent suivra l'un de vos programmes.</x-empty>
    @else
        <x-table>
            <thead>
                <tr>
                    <th>Personne</th>
                    <th>Programme</th>
                    <th>Avancement</th>
                    <th>Prochaine dose</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inscriptions as $i)
                    <tr>
                        <td><strong>{{ $i->sujetNom() }}</strong><div class="ds-muted">{{ $i->sujetType() }}</div></td>
                        <td>{{ $i->programme->nom_programme }}</td>
                        <td style="min-width:130px">
                            {{ $i->libelleDoses() }}
                            @if($i->programme->nb_vaccins)
                                <div style="height:6px;background:#E5E7EB;border-radius:99px;overflow:hidden;margin-top:4px">
                                    <div style="height:100%;width:{{ $i->progression() }}%;background:#1DB87A"></div>
                                </div>
                            @endif
                        </td>
                        <td>{{ $i->prochaine_dose?->format('d/m/Y') ?? '—' }}</td>
                        <td><x-badge :type="$i->badgeType()">{{ $i->libelleStatut() }}</x-badge></td>
                        <td>
                            @if(in_array($i->statut, ['inscrit', 'en_cours']))
                            <form method="POST" action="{{ route('services.vaccinationSuivi.dose', $i->id) }}" style="display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;margin-bottom:.4rem">
                                @csrf
                                <input class="ds-input" type="date" name="prochaine_dose" min="{{ today()->addDay()->format('Y-m-d') }}" title="Date de la prochaine dose (facultatif)" style="width:auto">
                                <button class="ds-btn ds-btn-primary ds-btn-sm" type="submit" onclick="return confirm('Enregistrer une dose reçue ? La personne sera notifiée.')">+ Dose</button>
                            </form>
                            <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                                <form method="POST" action="{{ route('services.vaccinationSuivi.rappel', $i->id) }}">@csrf
                                    <button class="ds-btn ds-btn-secondary ds-btn-sm" type="submit">Rappel</button>
                                </form>
                                <form method="POST" action="{{ route('services.vaccinationSuivi.statut', $i->id) }}" onsubmit="return confirm('Marquer ce suivi comme abandonné ?')">@csrf
                                    <input type="hidden" name="statut" value="abandonne">
                                    <button class="ds-btn ds-btn-ghost ds-btn-sm" type="submit">Abandon</button>
                                </form>
                            </div>
                            @else
                                <span class="ds-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>

@include('layouts.partials.pager', ['paginator' => $inscriptions])

<script>
    document.getElementById('tous')?.addEventListener('change', function () {
        document.querySelectorAll('#liste-agents input[type=checkbox]').forEach(c => { c.disabled = this.checked; });
    });
</script>
@endsection
