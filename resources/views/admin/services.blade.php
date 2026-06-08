@extends('admin.master')

@section('title', 'Ajouter un service')
@section('alertesActive', 'active') {{-- Menu sidebar actif --}}

@push('styles')
<style>
/* ===== PROFIL CARD ===== */
.profil-card {
    display:flex;
    gap:25px;
    align-items:center;
    background:#fff;
    border-radius:20px;
    padding:25px;
    box-shadow:0 12px 25px rgba(0,0,0,0.05);
    border-left:4px solid #FF6F61;
    margin-bottom:30px;
}
.profil-card img {
    width:120px;
    height:120px;
    border-radius:50%;
    border:3px solid #1B263B;
    object-fit:cover;
}
.profil-info h2 {
    font-size:22px;
    margin-bottom:6px;
    color:#0D3B66;
}
.profil-info p {
    font-size:16px;
    color:#FF6F61;
}

/* ===== FORMULAIRE SERVICE ===== */
.service-card {
    background:#fff;
    border-radius:20px;
    padding:25px;
    box-shadow:0 12px 25px rgba(0,0,0,0.05);
    border-left:4px solid #FF6F61;
    margin-bottom:30px;
    max-width:900px;
    margin-left:auto;
    margin-right:auto;
}
.service-card h5 {
    font-size:20px;
    color:#0D3B66;
    margin-bottom:20px;
}
.form-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:18px;
    margin-top:15px;
}
.form-grid input,
.form-grid textarea,
.form-grid select {
    padding:14px;
    border-radius:12px;
    border:1px solid #A3D2CA;
    font-size:15px;
    background:#FAFAFA;
}
.form-grid input:focus,
.form-grid textarea:focus,
.form-grid select:focus {
    outline:none;
    border-color:#FF6F61;
    box-shadow:0 0 0 3px rgba(255,111,97,0.15);
    background:#fff;
}
.service-card button {
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
.service-card button:hover {
    transform:translateY(-2px);
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

/* ===== LISTE SERVICES ===== */
.service-list {
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    margin-top:20px;
}
.service-list .card {
    width:220px;
    background:#fff;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
}
.service-list .card img {
    width:100%;
    height:150px;
    object-fit:cover;
}
.service-list .card-body {
    padding:15px;
}
.service-list .card-title {
    font-weight:600;
    margin-bottom:8px;
}
.service-list button {
    width:100%;
    padding:10px;
    border:none;
    background:#ca3d20;
    color:#fff;
    border-radius:6px;
    cursor:pointer;
    transition:0.3s;
}
.service-list button:hover {background:#AB1D02;}

/* ===== ACTION ILLUSTRATIONS ===== */
.action-illustrations {
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    margin-top:20px;
}
.action-illustrations div {
    flex:1 1 150px;
    min-width:150px;
    text-align:center;
    cursor:pointer;
    transition:0.3s;
    padding:15px;
    border-radius:15px;
    background:#afafcc;
    border:1px solid #A3D2CA;
}
.action-illustrations div:hover {
    transform:translateY(-5px);
    background:linear-gradient(135deg,#FFAB91,#FF6F61);
    color:#FFF;
}
.action-illustrations img {width:80px;height:80px;margin-bottom:10px;object-fit:contain;}
.action-illustrations span {display:block;font-weight:600;color:#1B263B;}

/* ===== FOOTER LINKS ===== */
.footer-links {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:15px;
    margin-top:20px;
}
.footer-links a {
    display:block;
    text-align:center;
    padding:15px;
    border-radius:16px;
    background:#FFFFFF;
    transition:0.3s;
    color:#1B263B;
    font-weight:600;
    text-decoration:none;
    border:1px solid #A3D2CA;
}
.footer-links a:hover {
    transform:translateY(-3px);
    background:linear-gradient(90deg,#FFAB91,#FF6F61);
    color:#FFF;
}
</style>
@endpush

@section('content')

<!-- PROFIL CARD -->
<div class="profil-card">
    <img src="{{ asset($admin->photo_profil ?? 'medias/default.jpg') }}" alt="Photo Profil">
    <div class="profil-info">
        <h2>{{ $admin->nom }} {{ $admin->prenom }}</h2>
        <p>Email: {{ $admin->email ?? 'Non renseigné' }}</p>
        <p>Téléphone: {{ $admin->telephone ?? 'Non renseigné' }}</p>
    </div>
</div>

<!-- FORMULAIRE AJOUT SERVICE -->
<div class="service-card">
    <h5><i class="fa fa-plus-circle"></i> Ajouter un nouveau service</h5>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <input type="text" name="nom_service" placeholder="Titre du service" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="text" name="lien" placeholder="Lien du service" required>
            <input type="file" name="image">
        </div>
        <button type="submit"><i class="fa fa-save"></i> Créer Service</button>
    </form>
</div>

<!-- LISTE DES SERVICES -->
<div class="service-list">
    @forelse($services as $service)
        <div class="card">
            <img src="{{ $service->image ?? asset('medias/default-service.jpg') }}" alt="Service">
            <div class="card-body">
                <h5 class="card-title">{{ $service->nom_service }}</h5>
                <p class="card-text">{{ $service->description }}</p>
                <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce service ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"><i class="fa fa-trash"></i> Supprimer</button>
                </form>
            </div>
        </div>
    @empty
        <p>Aucun service créé pour l’instant.</p>
    @endforelse
</div>



<!-- FOOTER LINKS -->
<div class="footer-links">
    <a href="{{ route('admin.bilanSante') }}">Services Vaccination</a>
    <a href="{{ route('admin.services') }}">Nouveau Service Urgence</a>
    <a href="{{ route('admin.login') }}">Déconnexion</a>
</div>

@endsection
