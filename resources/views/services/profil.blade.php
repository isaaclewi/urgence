@extends('layouts.portal')

@section('title', 'Profil')
@section('page-title', 'Gestion interne')
@section('page-subtitle', 'Informations du service et sécurité du compte')

@section('content')
<div class="ds-grid ds-grid-2">

    <x-card title="Informations du service">
        <form method="POST" action="{{ route('services.profil.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="ds-field">
                <label class="ds-label">Photo de profil</label>
                <div style="display:flex;align-items:center;gap:14px">
                    <div class="ds-avatar" style="width:56px;height:56px;font-size:20px">
                        @if($service->photo_profil)
                            <img src="{{ str_starts_with($service->photo_profil, 'data:') || str_starts_with($service->photo_profil, 'http') ? $service->photo_profil : asset($service->photo_profil) }}" alt="">
                        @else
                            {{ strtoupper(substr($service->nom, 0, 1)) }}
                        @endif
                    </div>
                    <input class="ds-input" type="file" name="photo_profil" accept="image/jpeg,image/png,image/webp">
                </div>
                <span class="ds-muted" style="font-size:12px">JPG, PNG ou WebP, 10 Mo maximum.</span>
            </div>

            <div class="ds-field">
                <label class="ds-label" for="nom">Nom du service *</label>
                <input class="ds-input" id="nom" name="nom" value="{{ old('nom', $service->nom) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="email">Email (identifiant de connexion) *</label>
                <input class="ds-input" type="email" id="email" name="email" value="{{ old('email', $service->email) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="adresse">Adresse</label>
                <input class="ds-input" id="adresse" name="adresse" value="{{ old('adresse', $service->adresse) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="telephone">Téléphone</label>
                <input class="ds-input" id="telephone" name="telephone" value="{{ old('telephone', $service->telephone) }}">
            </div>

            <button class="ds-btn ds-btn-primary" type="submit">Enregistrer</button>
        </form>
    </x-card>

    <x-card title="Changer le mot de passe">
        <form method="POST" action="{{ route('services.profil.password') }}" autocomplete="off">
            @csrf
            <div class="ds-field">
                <label class="ds-label" for="mot_de_passe_actuel">Mot de passe actuel *</label>
                <input class="ds-input" type="password" id="mot_de_passe_actuel" name="mot_de_passe_actuel" required autocomplete="current-password">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="nouveau_mot_de_passe">Nouveau mot de passe * (8 caractères minimum)</label>
                <input class="ds-input" type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" minlength="8" required autocomplete="new-password">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="nouveau_mot_de_passe_confirmation">Confirmer le nouveau mot de passe *</label>
                <input class="ds-input" type="password" id="nouveau_mot_de_passe_confirmation" name="nouveau_mot_de_passe_confirmation" minlength="8" required autocomplete="new-password">
            </div>
            <button class="ds-btn ds-btn-secondary" type="submit">Modifier le mot de passe</button>
        </form>
    </x-card>
</div>
@endsection
