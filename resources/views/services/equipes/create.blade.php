@extends('layouts.portal')

@section('title', 'Nouvelle équipe')
@section('page-title', 'Nouvelle équipe')
@section('page-subtitle', 'Un mot de passe temporaire sera généré et affiché une seule fois')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.equipes.index') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ route('services.equipes.store') }}">
        @csrf
        <div class="ds-form-grid">
            <div class="ds-field">
                <label class="ds-label" for="nom">Nom de l'équipe *</label>
                <input class="ds-input" id="nom" name="nom" value="{{ old('nom') }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="email">E-mail (identifiant de connexion) *</label>
                <input class="ds-input" type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="telephone">Téléphone</label>
                <input class="ds-input" id="telephone" name="telephone" value="{{ old('telephone') }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="adresse">Adresse</label>
                <input class="ds-input" id="adresse" name="adresse" value="{{ old('adresse') }}">
            </div>
        </div>

        <div style="margin-top:20px;display:flex;gap:10px">
            <button class="ds-btn ds-btn-primary" type="submit">Créer l'équipe</button>
            <a class="ds-btn ds-btn-ghost" href="{{ route('services.equipes.index') }}">Annuler</a>
        </div>
    </form>
</x-card>
@endsection
