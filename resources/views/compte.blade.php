@extends('citoyen')

@section('title', 'Tableau de bord — CongoAssist')

@section('content')

{{-- Header --}}
<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Bonjour, {{ $citoyen->prenom ?? $citoyen->nom }} 👋</h1>
        <p>Voici un aperçu de votre espace santé et urgences.</p>
    </div>
    <a href="{{ route('MesAlertesController') }}" class="btn btn-primary">
        <i data-feather="alert-circle" style="width:15px;height:15px;"></i>
        Signaler une urgence
    </a>
</div>

{{-- Stat cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:28px;">
    <div class="stat-card anim-fade">
        <div class="stat-icon bg-blue-soft">
            <i data-feather="bell" style="width:20px;height:20px;"></i>
        </div>
        <div>
            <div class="stat-value">{{ $totalAlertes ?? '—' }}</div>
            <div class="stat-label">Mes alertes</div>
        </div>
    </div>
    <div class="stat-card anim-fade" style="animation-delay:.06s">
        <div class="stat-icon bg-green-soft">
            <i data-feather="check-circle" style="width:20px;height:20px;"></i>
        </div>
        <div>
            <div class="stat-value">{{ $alertesResolues ?? '—' }}</div>
            <div class="stat-label">Résolues</div>
        </div>
    </div>
    <div class="stat-card anim-fade" style="animation-delay:.1s">
        <div class="stat-icon bg-amber-soft">
            <i data-feather="clock" style="width:20px;height:20px;"></i>
        </div>
        <div>
            <div class="stat-value">{{ $alertesEnAttente ?? '—' }}</div>
            <div class="stat-label">En attente</div>
        </div>
    </div>
    <div class="stat-card anim-fade" style="animation-delay:.14s">
        <div class="stat-icon bg-purple-soft">
            <i data-feather="shield" style="width:20px;height:20px;"></i>
        </div>
        <div>
            <div class="stat-value">{{ $vaccinsRecus ?? '—' }}</div>
            <div class="stat-label">Vaccins reçus</div>
        </div>
    </div>
</div>

{{-- Profil + carte --}}
<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:28px;" class="responsive-grid">
    {{-- Carte profil --}}
    <div class="card anim-fade" style="animation-delay:.18s">
        <div class="card-body" style="text-align:center;padding-top:28px;">
            <img src="{{ asset($citoyen->photo_profil ?? 'medias/default.png') }}"
                 alt="Profil"
                 style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border);margin-bottom:12px;">
            <div style="font-weight:700;font-size:15px;color:var(--text);">{{ $citoyen->nom }} {{ $citoyen->prenom }}</div>
            <div style="font-size:12px;color:var(--text-sec);margin-top:3px;">Citoyen actif</div>
            <div style="margin-top:16px;display:flex;flex-direction:column;gap:9px;text-align:left;">
                <div style="display:flex;align-items:center;gap:9px;font-size:13px;color:var(--text-sec);">
                    <i data-feather="mail" style="width:14px;height:14px;flex-shrink:0;"></i>
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $citoyen->email }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:9px;font-size:13px;color:var(--text-sec);">
                    <i data-feather="phone" style="width:14px;height:14px;flex-shrink:0;"></i>
                    {{ $citoyen->telephone }}
                </div>
                <div style="display:flex;align-items:flex-start;gap:9px;font-size:13px;color:var(--text-sec);">
                    <i data-feather="map-pin" style="width:14px;height:14px;flex-shrink:0;margin-top:2px;"></i>
                    {{ $citoyen->adresse }}
                </div>
            </div>
            <a href="{{ route('profilController') }}" class="btn btn-secondary btn-sm btn-full" style="margin-top:18px;">
                <i data-feather="edit-2" style="width:13px;height:13px;"></i> Modifier le profil
            </a>
        </div>
    </div>

    {{-- Accès rapide --}}
    <div class="card anim-fade" style="animation-delay:.22s">
        <div class="card-header">
            <span class="card-title">Accès rapide</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                @php
                $shortcuts = [
                    ['route'=>route('MesAlertesController'), 'icon'=>'alert-circle', 'label'=>'Urgences', 'color'=>'bg-red-soft'],
                    ['route'=>route('bilanController'), 'icon'=>'activity', 'label'=>'Mon bilan', 'color'=>'bg-blue-soft'],
                    ['route'=>route('vaccinationMenuController'), 'icon'=>'shield', 'label'=>'Vaccins', 'color'=>'bg-green-soft'],
                    ['route'=>route('actualitesController'), 'icon'=>'rss', 'label'=>'Actualités', 'color'=>'bg-purple-soft'],
                    ['route'=>route('forumCitoyen'), 'icon'=>'message-square', 'label'=>'Forum', 'color'=>'bg-amber-soft'],
                    ['route'=>route('codeQR'), 'icon'=>'grid', 'label'=>'Code QR', 'color'=>'bg-gray-soft'],
                ];
                @endphp
                @foreach($shortcuts as $sc)
                <a href="{{ $sc['route'] }}"
                   style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 10px;
                          background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-lg);
                          text-decoration:none;transition:border-color .15s,box-shadow .15s;"
                   onmouseover="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,.08)'"
                   onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                    <div class="stat-icon {{ $sc['color'] }}" style="width:36px;height:36px;border-radius:9px;">
                        <i data-feather="{{ $sc['icon'] }}" style="width:16px;height:16px;"></i>
                    </div>
                    <span style="font-size:12px;font-weight:600;color:var(--text);text-align:center;">{{ $sc['label'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Carte Brazzaville --}}
<div class="card anim-fade" style="animation-delay:.26s;margin-bottom:28px;">
    <div class="card-header" style="padding-bottom:14px;">
        <span class="card-title">Carte — Urgences à proximité</span>
        <span class="pill pill-blue">
            <i data-feather="map-pin" style="width:11px;height:11px;"></i>
            Brazzaville
        </span>
    </div>
    <div style="overflow:hidden;border-radius:0 0 var(--radius-lg) var(--radius-lg);">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.246183956974!2d15.258879!3d-4.259204!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a6a63f00cf56721%3A0x2de5d3fa47bb3c21!2sBrazzaville%2C%20Congo!5e0!3m2!1sfr!2scg!4v1695822730456!5m2!1sfr!2scg"
            width="100%" height="280" style="border:0;display:block;"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>

<style>
@media (max-width: 767px) {
    .responsive-grid { grid-template-columns: 1fr !important; }
}
</style>

@endsection
