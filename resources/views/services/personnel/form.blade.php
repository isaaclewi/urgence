@extends('layouts.portal')

@php $edition = $agent->exists; @endphp

@section('title', $edition ? 'Modifier un agent' : 'Nouvel agent')
@section('page-title', $edition ? 'Modifier un agent' : 'Nouvel agent')
@section('page-subtitle', $edition ? $agent->nom_complet : 'Renseignez la fiche de l\'agent')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.personnel.index') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ $edition ? route('services.personnel.update', $agent->id) : route('services.personnel.store') }}">
        @csrf
        @if($edition) @method('PUT') @endif

        <div class="ds-form-grid">
            <div class="ds-field">
                <label class="ds-label" for="matricule">Matricule *</label>
                <input class="ds-input" id="matricule" name="matricule" value="{{ old('matricule', $agent->matricule) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="statut">Statut *</label>
                <select class="ds-select" id="statut" name="statut" required>
                    @foreach($statuts as $s)
                        <option value="{{ $s }}" @selected(old('statut', $agent->statut) === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="prenom">Prénom *</label>
                <input class="ds-input" id="prenom" name="prenom" value="{{ old('prenom', $agent->prenom) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="nom">Nom *</label>
                <input class="ds-input" id="nom" name="nom" value="{{ old('nom', $agent->nom) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="fonction">Fonction *</label>
                <input class="ds-input" id="fonction" name="fonction" value="{{ old('fonction', $agent->fonction) }}" placeholder="Ex : Infirmier, Sapeur, Brigadier" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="grade">Grade</label>
                <input class="ds-input" id="grade" name="grade" value="{{ old('grade', $agent->grade) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="equipe_id">Équipe</label>
                <select class="ds-select" id="equipe_id" name="equipe_id">
                    <option value="">— Aucune —</option>
                    @foreach($equipes as $e)
                        <option value="{{ $e->id }}" @selected((string) old('equipe_id', $agent->equipe_id) === (string) $e->id)>{{ $e->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="date_embauche">Date d'embauche</label>
                <input class="ds-input" type="date" id="date_embauche" name="date_embauche"
                       value="{{ old('date_embauche', $agent->date_embauche?->format('Y-m-d')) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="telephone">Téléphone</label>
                <input class="ds-input" id="telephone" name="telephone" value="{{ old('telephone', $agent->telephone) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="email">Email</label>
                <input class="ds-input" type="email" id="email" name="email" value="{{ old('email', $agent->email) }}">
            </div>
        </div>

        <div class="ds-field">
            <label class="ds-label" for="competences">Compétences</label>
            <textarea class="ds-textarea" id="competences" name="competences" rows="3"
                      placeholder="Ex : secourisme, conduite d'ambulance, désincarcération">{{ old('competences', $agent->competences) }}</textarea>
        </div>

        <button class="ds-btn ds-btn-primary" type="submit">{{ $edition ? 'Enregistrer' : 'Ajouter l\'agent' }}</button>
    </form>
</x-card>
@endsection
