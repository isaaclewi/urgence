@extends('citoyen')

@section('title', 'Mon profil — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Mon profil</h1>
        <p>Gérez vos informations personnelles et vos préférences.</p>
    </div>
    <a href="{{ route('compteController') }}" class="btn btn-secondary">
        <i data-feather="arrow-left" style="width:14px;height:14px;"></i>
        Retour
    </a>
</div>

@if($errors->any())
<div class="alert alert-error anim-fade">
    <i data-feather="alert-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
    <div>
        @foreach($errors->all() as $err)
        <div>{{ $err }}</div>
        @endforeach
    </div>
</div>
@endif

<div style="max-width:600px;">
    <div class="card anim-fade" style="animation-delay:.06s;">
        <div class="card-body">

            {{-- Photo --}}
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:28px;
                        padding-bottom:24px;border-bottom:1px solid var(--border);">
                <div style="position:relative;">
                    <img id="profilePic"
                         src="{{ asset($citoyen->photo_profil ?? 'medias/default.png') }}"
                         alt="Profil"
                         style="width:72px;height:72px;border-radius:50%;object-fit:cover;
                                border:2.5px solid var(--border);">
                    <label for="photoInput"
                           style="position:absolute;bottom:0;right:0;
                                  width:24px;height:24px;border-radius:50%;
                                  background:var(--accent);color:#fff;
                                  display:flex;align-items:center;justify-content:center;
                                  cursor:pointer;border:2px solid #fff;">
                        <i data-feather="camera" style="width:11px;height:11px;"></i>
                    </label>
                    <input type="file" id="photoInput" name="photo" accept="image/*"
                           style="display:none;"
                           onchange="document.getElementById('profilePic').src=URL.createObjectURL(this.files[0])">
                </div>
                <div>
                    <div style="font-size:15px;font-weight:700;color:var(--text);">{{ $citoyen->nom }} {{ $citoyen->prenom }}</div>
                    <div style="font-size:12px;color:var(--text-sec);margin-top:3px;">Cliquez sur l'icône pour changer la photo</div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">JPG / PNG — max 2 Mo</div>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" action="{{ route('profil.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" name="nom" value="{{ $citoyen->nom }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Adresse e-mail</label>
                    <input type="email" name="email" value="{{ $citoyen->email }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Numéro de téléphone</label>
                    <input type="text" name="telephone" value="{{ $citoyen->telephone }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Adresse complète</label>
                    <input type="text" name="adresse" value="{{ $citoyen->adresse }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nouveau mot de passe <span style="font-weight:400;color:var(--text-muted)">(laisser vide pour conserver)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Minimum 6 caractères">
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--border);">
                    <a href="{{ route('compteController') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" style="width:14px;height:14px;"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
