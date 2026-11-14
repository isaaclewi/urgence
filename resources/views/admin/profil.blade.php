@extends('layouts.portal')

@section('title', 'Mon profil')
@section('page-title', 'Mon profil')
@section('page-subtitle', 'Vos informations de compte administrateur')

@section('content')
<div class="ds-grid ds-grid-aside">
    <x-card>
        <div style="text-align:center">
            <img src="{{ $admin->photo_profil ? asset($admin->photo_profil) : asset('medias/default-user.jpg') }}"
                 alt="Photo de profil" style="width:112px;height:112px;border-radius:50%;object-fit:cover;border:3px solid var(--ds-border)">
            <h2 style="font:700 18px 'Sora','Inter',sans-serif;margin:12px 0 2px">{{ $admin->nom }} {{ $admin->prenom }}</h2>
            <x-badge type="info">{{ $admin->role }}</x-badge>
        </div>
        <dl style="margin:16px 0 0;font-size:13px;display:grid;gap:8px">
            <div><dt class="ds-muted">Email</dt><dd style="margin:0;word-break:break-all">{{ $admin->email }}</dd></div>
            <div><dt class="ds-muted">Téléphone</dt><dd style="margin:0">{{ $admin->telephone }}</dd></div>
            <div><dt class="ds-muted">Adresse</dt><dd style="margin:0">{{ $admin->adresse }}</dd></div>
        </dl>
    </x-card>

    <x-card title="Modifier mes informations">
        <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="ds-form-grid">
                <div class="ds-field"><label class="ds-label" for="nom">Nom *</label>
                    <input class="ds-input" id="nom" type="text" name="nom" value="{{ old('nom', $admin->nom) }}" required></div>
                <div class="ds-field"><label class="ds-label" for="prenom">Prénom *</label>
                    <input class="ds-input" id="prenom" type="text" name="prenom" value="{{ old('prenom', $admin->prenom) }}" required></div>
                <div class="ds-field"><label class="ds-label" for="email">Email *</label>
                    <input class="ds-input" id="email" type="email" name="email" value="{{ old('email', $admin->email) }}" required></div>
                <div class="ds-field"><label class="ds-label" for="telephone">Téléphone *</label>
                    <input class="ds-input" id="telephone" type="text" name="telephone" value="{{ old('telephone', $admin->telephone) }}" required></div>
                <div class="ds-field"><label class="ds-label" for="adresse">Adresse *</label>
                    <input class="ds-input" id="adresse" type="text" name="adresse" value="{{ old('adresse', $admin->adresse) }}" required></div>
                <div class="ds-field"><label class="ds-label" for="photo">Photo (jpg, png — 2 Mo max)</label>
                    <input class="ds-input" id="photo" type="file" name="photo" accept=".jpg,.jpeg,.png"></div>
                <div class="ds-field"><label class="ds-label" for="mot_passe">Nouveau mot de passe</label>
                    <input class="ds-input" id="mot_passe" type="password" name="mot_passe" minlength="6" autocomplete="new-password" placeholder="Laisser vide pour ne pas changer"></div>
            </div>
            <button class="ds-btn ds-btn-primary" type="submit">Mettre à jour</button>
        </form>
    </x-card>
</div>
@endsection
