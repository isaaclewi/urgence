@extends('services.master')

@section('title', 'Profil — ' . ($service->nom ?? 'CongoAssist'))

@section('page-title', 'Mon Profil')
@section('page-subtitle', 'Gérez les informations de votre service')

@section('sidebar')
<div class="sb-section-label">Navigation</div>
<a href="{{ route('services.compte') }}" class="sidebar-link">
    <i data-feather="home"></i><span class="sb-lbl">Tableau de bord</span>
</a>
<a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link">
    <i data-feather="bell"></i><span class="sb-lbl">Urgences signalées</span>
</a>
<a href="{{ route('services.citoyens') }}" class="sidebar-link">
    <i data-feather="users"></i><span class="sb-lbl">Citoyens</span>
</a>
<a href="{{ route('services.forum.index') }}" class="sidebar-link">
    <i data-feather="message-square"></i><span class="sb-lbl">Forum</span>
</a>
<a href="{{ route('services.actualite') }}" class="sidebar-link">
    <i data-feather="newspaper"></i><span class="sb-lbl">Actualités</span>
</a>
<a href="{{ route('services.profil') }}" class="sidebar-link active">
    <i data-feather="settings"></i><span class="sb-lbl">Gestion interne</span>
</a>
<div class="sb-divider"></div>
<a href="{{ route('services.logout') }}" class="sidebar-link danger">
    <i data-feather="log-out"></i><span class="sb-lbl">Déconnexion</span>
</a>
@endsection

@section('content')
<div style="max-width:860px; margin:0 auto; display:flex; flex-direction:column; gap:24px;">

    {{-- Message de succès --}}
    @if(session('success'))
    <div class="alert alert-success anim-fade">
        <i data-feather="check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Carte profil --}}
    <div class="content-card anim-fade">
        <div class="cc-body" style="text-align:center; padding:36px 24px;">
            <div style="position:relative; display:inline-block; margin-bottom:20px;">
                <img src="{{ asset($service->photo_profil ?? 'medias/default.jpg') }}"
                     alt="Profil"
                     style="width:96px;height:96px;border-radius:50%;object-fit:cover;
                            border:3px solid var(--accent); box-shadow:0 4px 20px rgba(29,184,122,.2);"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div style="display:none;width:96px;height:96px;border-radius:50%;
                             background:var(--brand-mid);align-items:center;justify-content:center;
                             font-size:32px;font-weight:700;color:var(--accent);">
                    {{ strtoupper(substr($service->nom ?? 'S', 0, 1)) }}
                </div>
                <div style="position:absolute;bottom:0;right:0;width:32px;height:32px;
                             border-radius:50%;background:var(--accent);border:3px solid var(--surface);
                             display:flex;align-items:center;justify-content:center;">
                    <i data-feather="shield" style="width:14px;height:14px;color:#fff;"></i>
                </div>
            </div>

            <div style="font-family:'Sora',sans-serif;font-size:20px;font-weight:700;color:var(--text);margin-bottom:4px;">
                {{ $service->nom }}
            </div>
            <div style="font-size:13px;color:var(--text-muted);margin-bottom:14px;">{{ $service->email }}</div>

            <div style="display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
                <span class="pill pill-accent">
                    <i data-feather="briefcase"></i>
                    {{ ucfirst($service->role ?? 'Service') }}
                </span>
                <span class="pill pill-green">
                    <i data-feather="check-circle"></i>
                    {{ $service->etat_compte ?? 'Actif' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid-3 anim-slide">
        <div class="stat-card blue">
            <div class="sc-icon" style="background:#DBEAFE;">
                <i data-feather="activity" style="color:#3B82F6;"></i>
            </div>
            <div class="sc-value">248</div>
            <div class="sc-label">Interventions</div>
        </div>
        <div class="stat-card green">
            <div class="sc-icon" style="background:#D1FAE5;">
                <i data-feather="trending-up" style="color:#10B981;"></i>
            </div>
            <div class="sc-value">98%</div>
            <div class="sc-label">Taux de réussite</div>
        </div>
        <div class="stat-card purple">
            <div class="sc-icon" style="background:#EDE9FE;">
                <i data-feather="clock" style="color:#8B5CF6;"></i>
            </div>
            <div class="sc-value">12min</div>
            <div class="sc-label">Temps moyen</div>
        </div>
    </div>

    {{-- Formulaire modification --}}
    <div class="content-card anim-fade">
        <div class="cc-header">
            <div>
                <div class="cc-title"><i data-feather="edit"></i> Modifier les informations</div>
                <div class="cc-subtitle">Mettez à jour les détails de votre service</div>
            </div>
        </div>
        <div class="cc-body">
            <form action="{{ route('services.profil.update') }}" method="POST"
                  enctype="multipart/form-data"
                  style="display:flex; flex-direction:column; gap:18px;">
                @csrf

                <div class="grid-2">
                    <div>
                        <label class="form-label">
                            <i data-feather="briefcase" style="width:13px;height:13px;color:var(--accent);"></i>
                            Nom du service <span style="color:#EF4444;">*</span>
                        </label>
                        <input type="text" name="nom" value="{{ old('nom', $service->nom) }}"
                               required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">
                            <i data-feather="mail" style="width:13px;height:13px;color:var(--accent);"></i>
                            Adresse email
                        </label>
                        <input type="email" name="email" value="{{ old('email', $service->email) }}"
                               class="form-input">
                    </div>
                    <div>
                        <label class="form-label">
                            <i data-feather="map-pin" style="width:13px;height:13px;color:var(--accent);"></i>
                            Adresse
                        </label>
                        <input type="text" name="adresse" value="{{ old('adresse', $service->adresse) }}"
                               class="form-input">
                    </div>
                    <div>
                        <label class="form-label">
                            <i data-feather="phone" style="width:13px;height:13px;color:var(--accent);"></i>
                            Téléphone
                        </label>
                        <input type="text" name="telephone" value="{{ old('telephone', $service->telephone) }}"
                               class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">
                        <i data-feather="camera" style="width:13px;height:13px;color:var(--accent);"></i>
                        Photo de profil
                    </label>
                    <input type="file" name="photo_profil" accept="image/*"
                           id="photo_profil" style="display:none;"
                           onchange="document.getElementById('fileName').textContent=this.files[0]?.name||'Choisir une photo'">
                    <label for="photo_profil" class="form-input"
                           style="display:flex;align-items:center;justify-content:center;gap:8px;
                                  cursor:pointer;border-style:dashed;color:var(--text-muted);">
                        <i data-feather="upload" style="width:15px;height:15px;"></i>
                        <span id="fileName">Choisir une photo</span>
                    </label>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:5px;">JPG, PNG — max 2 Mo</div>
                </div>

                <div style="display:flex;gap:10px;padding-top:4px;">
                    <button type="submit" class="btn btn-accent" style="flex:1;">
                        <i data-feather="save"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('services.compte') }}" class="btn btn-outline">
                        <i data-feather="arrow-left"></i> Retour
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Sécurité --}}
    <div class="content-card anim-fade">
        <div class="cc-header">
            <div>
                <div class="cc-title">
                    <i data-feather="shield" style="color:#EF4444;"></i>
                    Sécurité du compte
                </div>
                <div class="cc-subtitle">Gérez la sécurité de votre compte</div>
            </div>
        </div>
        <div class="cc-body" style="display:flex;flex-direction:column;gap:10px;">

            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:14px 16px;background:var(--surface2);border-radius:10px;
                        border:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#DBEAFE;
                                display:flex;align-items:center;justify-content:center;">
                        <i data-feather="key" style="width:16px;height:16px;color:#3B82F6;"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);">Mot de passe</div>
                        <div style="font-size:11.5px;color:var(--text-muted);">Dernière modification : il y a 30 jours</div>
                    </div>
                </div>
                <button class="btn btn-outline btn-sm">Modifier</button>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:14px 16px;background:var(--surface2);border-radius:10px;
                        border:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#D1FAE5;
                                display:flex;align-items:center;justify-content:center;">
                        <i data-feather="smartphone" style="width:16px;height:16px;color:#10B981;"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);">Authentification à deux facteurs</div>
                        <div style="font-size:11.5px;color:var(--text-muted);">Sécurisez votre compte avec 2FA</div>
                    </div>
                </div>
                <button class="btn btn-outline btn-sm">Activer</button>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:14px 16px;background:var(--surface2);border-radius:10px;
                        border:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#EDE9FE;
                                display:flex;align-items:center;justify-content:center;">
                        <i data-feather="clock" style="width:16px;height:16px;color:#8B5CF6;"></i>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text);">Historique de connexion</div>
                        <div style="font-size:11.5px;color:var(--text-muted);">Dernière connexion : Aujourd'hui à 14:32</div>
                    </div>
                </div>
                <button class="btn btn-outline btn-sm">Voir</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => feather.replace({ width: 16, height: 16 }));
</script>
@endpush
@endsection
