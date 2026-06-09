@extends('citoyen')

@section('title', 'Vaccination Enfants — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Vaccination — Enfants</h1>
        <p>Programme de vaccination pédiatrique.</p>
    </div>
    <a href="{{ route('vaccinationMenuController') }}" class="btn btn-secondary">
        <i data-feather="arrow-left" style="width:14px;height:14px;"></i> Retour
    </a>
</div>

{{-- Bannière --}}
<div class="alert alert-info anim-fade" style="margin-bottom:24px;">
    <i data-feather="info" style="width:16px;height:16px;flex-shrink:0;"></i>
    <span>La vaccination protège les enfants contre des maladies graves. Respectez le calendrier vaccinal pour une protection optimale.</span>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px;margin-bottom:28px;" class="anim-fade">
    <div class="stat-card">
        <div class="stat-icon bg-red-soft">
            <i data-feather="alert-circle"></i>
        </div>
        <div>
            <div class="stat-value">{{ collect($vaccins)->where('vaccination_obligatoire', true)->count() }}</div>
            <div class="stat-label">Obligatoires</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-blue-soft">
            <i data-feather="check-circle"></i>
        </div>
        <div>
            <div class="stat-value">{{ collect($vaccins)->where('vaccination_obligatoire', false)->count() }}</div>
            <div class="stat-label">Optionnels</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-green-soft">
            <i data-feather="package"></i>
        </div>
        <div>
            <div class="stat-value">{{ collect($vaccins)->pluck('fabricant')->unique()->count() }}</div>
            <div class="stat-label">Fabricants</div>
        </div>
    </div>
</div>

{{-- Vaccins --}}
<h2 style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
    <i data-feather="grid" style="width:15px;height:15px;color:var(--accent);"></i>
    Vaccins disponibles
</h2>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
    @forelse($vaccins as $index => $vaccin)
    @php
        if (str_contains(strtolower($vaccin->voie_administration), 'orale')) {
            $img = asset('medias/Screenshot 2025-02-15 220828.jpg');
        } elseif (str_contains(strtolower($vaccin->voie_administration), 'injection')) {
            $img = asset('medias/Screenshot 2025-02-15 220915.jpg');
        } else {
            $img = asset('medias/Screenshot 2025-02-15 220719.jpg');
        }
    @endphp
    <div class="card anim-fade" style="animation-delay:{{ $index * 0.08 }}s;overflow:hidden;">
        <div style="height:180px;overflow:hidden;">
            <img src="{{ $img }}" alt="{{ $vaccin->nom_vaccin }}"
                 style="width:100%;height:100%;object-fit:cover;transition:transform .4s ease;"
                 onmouseover="this.style.transform='scale(1.06)'"
                 onmouseout="this.style.transform='scale(1)'">
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:12px;">
                <h3 style="font-size:14px;font-weight:700;color:var(--text);margin:0;">{{ $vaccin->nom_vaccin }}</h3>
                <span class="pill {{ $vaccin->vaccination_obligatoire ? 'pill-red' : 'pill-blue' }}">
                    {{ $vaccin->vaccination_obligatoire ? 'Obligatoire' : 'Optionnel' }}
                </span>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach([
                    ['briefcase','Fabricant',$vaccin->fabricant ?? '—'],
                    ['layers','Doses',$vaccin->nombre_doses ?? '—'],
                    ['droplet','Voie',$vaccin->voie_administration ?? '—'],
                    ['users','Âge cible',$vaccin->age_cible_min.' - '.$vaccin->age_cible_max.' mois'],
                ] as $row)
                <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;">
                    <i data-feather="{{ $row[0] }}" style="width:13px;height:13px;color:var(--text-muted);flex-shrink:0;"></i>
                    <span style="color:var(--text-sec);">{{ $row[1] }} :</span>
                    <span style="font-weight:600;color:var(--text);">{{ $row[2] }}</span>
                </div>
                @endforeach
                <div style="background:var(--surface2);border-radius:var(--radius);padding:10px;margin-top:4px;font-size:12px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="color:var(--text-sec);">Fabrication</span>
                        <span style="font-weight:600;color:var(--text);">{{ \Carbon\Carbon::parse($vaccin->date_fabrication)->format('d/m/Y') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <span style="color:var(--text-sec);">Expiration</span>
                        <span style="font-weight:600;color:var(--text);">{{ \Carbon\Carbon::parse($vaccin->date_expiration)->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;">
        <div class="card" style="padding:60px;text-align:center;">
            <i data-feather="inbox" style="width:36px;height:36px;color:var(--text-muted);margin-bottom:12px;"></i>
            <p style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px;">Aucun vaccin disponible</p>
            <p style="font-size:12.5px;color:var(--text-sec);">Les vaccins pour enfants seront bientôt disponibles.</p>
        </div>
    </div>
    @endforelse
</div>

@endsection
