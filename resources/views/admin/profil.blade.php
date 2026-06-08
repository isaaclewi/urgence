@extends('admin.master')

@section('title', 'Profil Administrateur')
@section('profilActive', 'active')

@section('content')

<div class="card profil-card">
    <img src="{{ $admin->photo_profil ? asset($admin->photo_profil) : asset('medias/default-user.jpg') }}" alt="Photo Profil">

    <div class="profil-info">
        <h2>{{ $admin->nom }} {{ $admin->prenom }}</h2>

        <p>Email : <span>{{ $admin->email }}</span></p>
        <p>Adresse : <span>{{ $admin->adresse }}</span></p>
        <p>Téléphone : <span>{{ $admin->telephone }}</span></p>
        <p>Rôle : <span>{{ $admin->role }}</span></p>

        <hr>

        <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <input type="text" name="nom" value="{{ $admin->nom }}" placeholder="Nom">
                <input type="text" name="prenom" value="{{ $admin->prenom }}" placeholder="Prénom">
                <input type="email" name="email" value="{{ $admin->email }}" placeholder="Email">
                <input type="text" name="adresse" value="{{ $admin->adresse }}" placeholder="Adresse">
                <input type="text" name="telephone" value="{{ $admin->telephone }}" placeholder="Téléphone">
                <input type="file" name="photo">
                <input type="password" name="mot_passe" placeholder="Nouveau mot de passe">
            </div>

            <button class="btn-primary">Mettre à jour</button>
        </form>
    </div>
</div>
<style>
            .card {
    background:#fff;
    border-radius:20px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    border-left:4px solid #FF6F61;
}

.profil-card {
    display:flex;
    gap:25px;
    align-items:flex-start;
    flex-wrap:wrap;
}

.profil-card img {
    width:150px;
    height:150px;
    border-radius:50%;
    border:3px solid #1B263B;
    object-fit:cover;
}

.profil-info {
    flex:1;
}

.profil-info h2 {
    color:#0D3B66;
    margin-bottom:10px;
}

.profil-info p {
    margin:6px 0;
    font-weight:500;
}

.profil-info span {
    color:#FF6F61;
    font-weight:600;
}

.form-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:12px;
    margin-top:15px;
}

.form-grid input {
    padding:10px;
    border-radius:10px;
    border:1px solid #A3D2CA;
}

.btn-primary {
    margin-top:15px;
    padding:12px 25px;
    border:none;
    border-radius:12px;
    background:linear-gradient(90deg,#FFAB91,#FF6F61);
    color:#fff;
    font-weight:600;
    cursor:pointer;
}

</style>
@endsection
