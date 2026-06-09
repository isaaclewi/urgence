@extends('services.master')

@section('page-title', 'Créer une équipe')
@section('page-subtitle', 'Ajouter une nouvelle équipe terrain')

@section('sidebar')
<a href="{{ route('services.compte') }}" class="sidebar-link">
    <i data-feather="home"></i> Dashboard
</a>

<a href="{{ route('services.equipes.index') }}" class="sidebar-link">
    <i data-feather="users"></i> Équipes
</a>

<a href="{{ route('services.equipes.create') }}" class="sidebar-link active">
    <i data-feather="plus"></i> Nouvelle équipe
</a>
@endsection

@section('content')

<div class="content-card">
    <div class="cc-header">
        <div>
            <div class="cc-title">
                <i data-feather="user-plus"></i>
                Création d’équipe
            </div>
            <div class="cc-subtitle">
                Remplissez les informations de l’équipe
            </div>
        </div>
    </div>

    <div class="cc-body">

        <form action="{{ route('services.equipes.store') }}" method="POST">
            @csrf

            <div class="grid-2">

                <div>
                    <label class="form-label">Nom de l’équipe</label>
                    <input type="text" name="nom" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input">
                </div>

                <div>
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-input">
                </div>

                <div>
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-input">
                </div>

            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button class="btn btn-accent">
                    <i data-feather="save"></i> Créer l’équipe
                </button>

                <a href="{{ route('services.equipes.index') }}" class="btn btn-outline">
                    Annuler
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
