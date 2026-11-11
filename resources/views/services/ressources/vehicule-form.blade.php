@extends('layouts.portal')

@php $edition = $vehicule->exists; @endphp

@section('title', $edition ? 'Modifier un véhicule' : 'Nouveau véhicule')
@section('page-title', $edition ? 'Modifier un véhicule' : 'Nouveau véhicule')
@section('page-subtitle', $edition ? $vehicule->immatriculation : 'Renseignez la fiche du véhicule')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.ressources.index') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ $edition ? route('services.ressources.vehicules.update', $vehicule->id) : route('services.ressources.vehicules.store') }}">
        @csrf
        @if($edition) @method('PUT') @endif

        <div class="ds-form-grid">
            <div class="ds-field">
                <label class="ds-label" for="type">Type *</label>
                <select class="ds-select" id="type" name="type" required>
                    @foreach($types as $t)
                        <option value="{{ $t }}" @selected(old('type', $vehicule->type) === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="immatriculation">Immatriculation *</label>
                <input class="ds-input" id="immatriculation" name="immatriculation" value="{{ old('immatriculation', $vehicule->immatriculation) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="marque_modele">Marque / modèle</label>
                <input class="ds-input" id="marque_modele" name="marque_modele" value="{{ old('marque_modele', $vehicule->marque_modele) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="statut">Statut *</label>
                <select class="ds-select" id="statut" name="statut" required>
                    @foreach($statuts as $s)
                        <option value="{{ $s }}" @selected(old('statut', $vehicule->statut) === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="equipe_id">Équipe affectée</label>
                <select class="ds-select" id="equipe_id" name="equipe_id">
                    <option value="">— Aucune —</option>
                    @foreach($equipes as $e)
                        <option value="{{ $e->id }}" @selected((string) old('equipe_id', $vehicule->equipe_id) === (string) $e->id)>{{ $e->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="kilometrage">Kilométrage</label>
                <input class="ds-input" type="number" min="0" id="kilometrage" name="kilometrage" value="{{ old('kilometrage', $vehicule->kilometrage) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="prochaine_maintenance">Prochaine maintenance</label>
                <input class="ds-input" type="date" id="prochaine_maintenance" name="prochaine_maintenance"
                       value="{{ old('prochaine_maintenance', $vehicule->prochaine_maintenance?->format('Y-m-d')) }}">
            </div>
        </div>

        <button class="ds-btn ds-btn-primary" type="submit">{{ $edition ? 'Enregistrer' : 'Ajouter le véhicule' }}</button>
    </form>
</x-card>
@endsection
