@extends('layouts.portal')

@section('title', 'Modifier une équipe')
@section('page-title', 'Modifier une équipe')
@section('page-subtitle', $equipe->nom)

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.equipes.index') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ route('services.equipes.update', $equipe->id) }}">
        @csrf @method('PUT')
        <div class="ds-form-grid">
            <div class="ds-field">
                <label class="ds-label" for="nom">Nom *</label>
                <input class="ds-input" id="nom" name="nom" value="{{ old('nom', $equipe->nom) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="email">E-mail (identifiant de connexion) *</label>
                <input class="ds-input" type="email" id="email" name="email" value="{{ old('email', $equipe->email) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="telephone">Téléphone</label>
                <input class="ds-input" id="telephone" name="telephone" value="{{ old('telephone', $equipe->telephone) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="adresse">Adresse</label>
                <input class="ds-input" id="adresse" name="adresse" value="{{ old('adresse', $equipe->adresse) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="password">Nouveau mot de passe <span class="ds-muted">(laisser vide pour ne pas le changer)</span></label>
                <input class="ds-input" type="password" id="password" name="password" minlength="8" autocomplete="new-password">
            </div>
        </div>

        <div style="margin-top:20px;display:flex;gap:10px">
            <button class="ds-btn ds-btn-primary" type="submit">Enregistrer</button>
            <a class="ds-btn ds-btn-ghost" href="{{ route('services.equipes.index') }}">Annuler</a>
        </div>
    </form>
</x-card>
@endsection
