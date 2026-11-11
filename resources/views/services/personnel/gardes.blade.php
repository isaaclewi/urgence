@extends('layouts.portal')

@section('title', 'Planning des gardes')
@section('page-title', 'Planning des gardes')
@section('page-subtitle', 'Gardes en cours et à venir')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.personnel.index') }}">← Personnel</a>
@endsection

@section('content')

<div class="ds-grid ds-grid-2 ds-grid-aside">

    <x-card title="Planifier une garde">
        @if($agents->isEmpty())
            <x-empty title="Aucun agent actif">Ajoutez des agents avant de planifier des gardes.</x-empty>
        @else
            <form method="POST" action="{{ route('services.personnel.gardes.store') }}">
                @csrf
                <div class="ds-field">
                    <label class="ds-label" for="agent_id">Agent *</label>
                    <select class="ds-select" id="agent_id" name="agent_id" required>
                        <option value="">— Choisir —</option>
                        @foreach($agents as $a)
                            <option value="{{ $a->id }}" @selected((string) old('agent_id') === (string) $a->id)>{{ $a->nom_complet }} — {{ $a->fonction }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ds-field">
                    <label class="ds-label" for="type">Type *</label>
                    <select class="ds-select" id="type" name="type" required>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected(old('type', 'jour') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ds-field">
                    <label class="ds-label" for="date_debut">Début *</label>
                    <input class="ds-input" type="datetime-local" id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required>
                </div>
                <div class="ds-field">
                    <label class="ds-label" for="date_fin">Fin *</label>
                    <input class="ds-input" type="datetime-local" id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required>
                </div>
                <div class="ds-field">
                    <label class="ds-label" for="note">Note</label>
                    <input class="ds-input" id="note" name="note" value="{{ old('note') }}" maxlength="255">
                </div>
                <button class="ds-btn ds-btn-primary" type="submit">Planifier</button>
            </form>
        @endif
    </x-card>

    <x-card title="Gardes à venir et en cours" flush>
        @if($gardes->isEmpty())
            <x-empty title="Aucune garde planifiée">Les gardes planifiées apparaîtront ici.</x-empty>
        @else
            <x-table>
                <thead>
                    <tr><th>Agent</th><th>Type</th><th>Période</th><th>État</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($gardes as $g)
                        <tr>
                            <td><strong>{{ $g->agent?->nom_complet ?? '—' }}</strong>@if($g->note)<div class="ds-muted">{{ $g->note }}</div>@endif</td>
                            <td>{{ ucfirst($g->type) }}</td>
                            <td>{{ $g->date_debut->format('d/m H:i') }} → {{ $g->date_fin->format('d/m H:i') }}</td>
                            <td>
                                @if($g->estEnCours())
                                    <x-badge type="success">En cours</x-badge>
                                @else
                                    <x-badge type="info">Planifiée</x-badge>
                                @endif
                            </td>
                            <td class="ds-row-actions">
                                <form method="POST" action="{{ route('services.personnel.gardes.destroy', $g->id) }}"
                                      onsubmit="return confirm('Supprimer cette garde ?')">
                                    @csrf @method('DELETE')
                                    <button class="ds-btn ds-btn-ghost ds-btn-sm ds-text-danger" type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        @endif
    </x-card>

</div>
@endsection
