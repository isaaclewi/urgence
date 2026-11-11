@extends('services.master')

@section('page-title', 'Modifier équipe')
@section('page-subtitle', 'Mise à jour des informations')

@section('content')

<div class="content-card">
    <div class="cc-header">
        <div>
            <div class="cc-title">
                <i data-feather="edit"></i>
                Modifier équipe
            </div>
            <div class="cc-subtitle">
                Mise à jour des informations de l’équipe
            </div>
        </div>
    </div>

    <div class="cc-body">

        <form action="{{ route('services.equipes.update', $equipe->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid-2">

                <div>
                    <label class="form-label">Nom</label>
                    <input type="text"
                           name="nom"
                           class="form-input"
                           value="{{ $equipe->nom }}"
                           required>
                </div>

                <div>
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-input"
                           value="{{ $equipe->email }}">
                </div>

                <div>
                    <label class="form-label">Téléphone</label>
                    <input type="text"
                           name="telephone"
                           class="form-input"
                           value="{{ $equipe->telephone }}">
                </div>

                <div>
                    <label class="form-label">Adresse</label>
                    <input type="text"
                           name="adresse"
                           class="form-input"
                           value="{{ $equipe->adresse }}">
                </div>

                <div>
                    <label class="form-label">Nouveau mot de passe <span style="font-weight:400;opacity:.7">(laisser vide pour ne pas changer)</span></label>
                    <input type="password"
                           name="password"
                           class="form-input"
                           minlength="8"
                           autocomplete="new-password">
                </div>

            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button class="btn btn-accent">
                    <i data-feather="save"></i> Mettre à jour
                </button>

                <a href="{{ route('services.equipes.index') }}" class="btn btn-outline">
                    Retour
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
