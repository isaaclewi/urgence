@extends('admin.master')

@section('title', 'Ajouter un service')
@section('serviceActive', 'active')

@section('content')

<div class="card service-card">

    <div class="service-info">
        <h2>
            <i class="fa fa-plus-circle"></i> Ajouter un service d’urgence
        </h2>

        {{-- Messages --}}
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.serviceStore') }}" method="POST">
            @csrf

            <div class="form-grid">
                <input type="text" name="nom" placeholder="Nom du service" required>
                <input type="text" name="adresse" placeholder="Adresse" required>

                <select name="role" required>
                    <option value="">Type de service</option>
                    <option value="pompier">Sapeur-pompier</option>
                    <option value="police">Police</option>
                    <option value="hopital">Hôpital</option>
                </select>

                <select name="disponible" required>
                    <option value="1">Disponible</option>
                    <option value="0">Non disponible</option>
                </select>

                <input type="email" name="email" placeholder="Email (optionnel)">
                <input type="text" name="telephone" placeholder="Téléphone (optionnel)">
                <input type="text" name="etat_compte" placeholder="État du compte" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>

            <button class="btn-primary">
                <i class="fa fa-save"></i> Enregistrer le service
            </button>
        </form>
    </div>
</div>

<style>
/* CARD */
.card {
    background:#fff;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    border-left:4px solid #FF6F61;
    max-width:1200px;
    margin:auto;
}

/* TITRE */
.service-info h2 {
    color:#0D3B66;
    margin-bottom:20px;
}

/* GRID FORM (comme profil) */
.form-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:18px;
    margin-top:20px;
}

/* INPUTS */
.form-grid input,
.form-grid select {
    padding:14px;
    border-radius:12px;
    border:1px solid #A3D2CA;
    font-size:15px;
    background:#FAFAFA;
}

/* FOCUS */
.form-grid input:focus,
.form-grid select:focus {
    outline:none;
    border-color:#FF6F61;
    box-shadow:0 0 0 3px rgba(255,111,97,0.15);
    background:#fff;
}

/* BUTTON */
.btn-primary {
    margin-top:25px;
    padding:14px 30px;
    border:none;
    border-radius:14px;
    background:linear-gradient(90deg,#FFAB91,#FF6F61);
    color:#fff;
    font-weight:600;
    font-size:16px;
    cursor:pointer;
}

/* ALERTS */
.alert-success {
    padding:12px;
    background:#D4EDDA;
    color:#155724;
    border-radius:10px;
    margin-bottom:15px;
}

.alert-error {
    padding:12px;
    background:#F8D7DA;
    color:#721C24;
    border-radius:10px;
    margin-bottom:15px;
}
</style>

@endsection
