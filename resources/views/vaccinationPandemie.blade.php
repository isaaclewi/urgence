@extends('citoyen')

@section('title', 'Programmes Épidémies — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Programmes — Épidémies</h1>
        <p>Campagnes de vaccination liées aux épidémies.</p>
    </div>
    <a href="{{ route('vaccinationMenuController') }}" class="btn btn-secondary">
        <i data-feather="arrow-left" style="width:14px;height:14px;"></i> Retour
    </a>
</div>

<div class="alert alert-warning anim-fade" style="margin-bottom:24px;">
    <i data-feather="alert-octagon" style="width:16px;height:16px;flex-shrink:0;"></i>
    <span>Ces programmes visent à protéger rapidement les populations à risque et à contenir la propagation des maladies infectieuses.</span>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;margin-bottom:28px;" class="anim-fade">
    <div class="stat-card">
        <div class="stat-icon bg-blue-soft"><i data-feather="list"></i></div>
        <div>
            <div class="stat-value">{{ count($programmes) }}</div>
            <div class="stat-label">Programmes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-green-soft"><i data-feather="shield"></i></div>
        <div>
            <div class="stat-value">{{ collect($programmes)->sum('nb_vaccins') }}</div>
            <div class="stat-label">Vaccins administrés</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple-soft"><i data-feather="briefcase"></i></div>
        <div>
            <div class="stat-value">{{ collect($programmes)->pluck('organisme_resp')->unique()->count() }}</div>
            <div class="stat-label">Organismes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-amber-soft"><i data-feather="tag"></i></div>
        <div>
            <div class="stat-value">{{ collect($programmes)->pluck('categorie')->unique()->count() }}</div>
            <div class="stat-label">Catégories</div>
        </div>
    </div>
</div>

{{-- Programmes --}}
<h2 style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
    <i data-feather="grid" style="width:15px;height:15px;color:var(--accent);"></i>
    Programmes actifs
</h2>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
    @forelse($programmes as $index => $programme)
    @php
        if (str_contains(strtolower($programme->age_cible), 'enfant')) {
            $img = asset('medias/Joyeux bébé dans un studio _ Photo Premium.jpeg');
        } elseif (str_contains(strtolower($programme->age_cible), 'adulte')) {
            $img = asset('medias/Face student portrait and black woman in university ready for learning goals or targets education scholarship and happy female learner from south africa with books for studying and knowledge _ Premium Photo.jpg');
        } elseif (str_contains(strtolower($programme->age_cible), 'senior') || str_contains(strtolower($programme->age_cible), 'personne âgée')) {
            $img = asset('medias/Free Photo _ Medium shot health worker with mask.jpeg');
        } else {
            $img = asset('medias/Famille.jpeg');
        }
    @endphp
    <div class="card anim-fade" style="animation-delay:{{ $index * 0.08 }}s;overflow:hidden;">
        <div style="height:180px;overflow:hidden;">
            <img src="{{ $img }}" alt="{{ $programme->nom_programme }}"
                 style="width:100%;height:100%;object-fit:cover;transition:transform .4s ease;"
                 onmouseover="this.style.transform='scale(1.06)'"
                 onmouseout="this.style.transform='scale(1)'">
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:12px;">
                <h3 style="font-size:14px;font-weight:700;color:var(--text);margin:0;flex:1;">
                    {{ $programme->nom_programme }}
                </h3>
                <span class="pill pill-red" style="flex-shrink:0;">{{ ucfirst($programme->categorie) }}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:12.5px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <i data-feather="users" style="width:13px;height:13px;color:var(--text-muted);flex-shrink:0;"></i>
                    <span style="color:var(--text-sec);">Âge cible :</span>
                    <span style="font-weight:600;color:var(--text);">{{ $programme->age_cible }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <i data-feather="calendar" style="width:13px;height:13px;color:var(--text-muted);flex-shrink:0;"></i>
                    <span style="color:var(--text-sec);">Période :</span>
                    <span style="font-weight:600;color:var(--text);">
                        {{ $programme->date_debut->format('d/m/Y') }} → {{ $programme->date_fin->format('d/m/Y') }}
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <i data-feather="activity" style="width:13px;height:13px;color:var(--text-muted);flex-shrink:0;"></i>
                    <span style="color:var(--text-sec);">Vaccins :</span>
                    <span style="font-weight:600;color:var(--text);">{{ $programme->nb_vaccins }} administrés</span>
                </div>
                <div style="background:var(--surface2);border-radius:var(--radius);padding:10px;margin-top:4px;">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                        <i data-feather="briefcase" style="width:12px;height:12px;color:var(--text-muted);"></i>
                        <span style="font-size:11px;color:var(--text-sec);">Organisme</span>
                    </div>
                    <span style="font-weight:600;color:var(--text);font-size:13px;">{{ $programme->organisme_resp }}</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;">
        <div class="card" style="padding:60px;text-align:center;">
            <i data-feather="inbox" style="width:36px;height:36px;color:var(--text-muted);margin-bottom:12px;"></i>
            <p style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px;">Aucun programme disponible</p>
            <p style="font-size:12.5px;color:var(--text-sec);">Il n'y a actuellement aucun programme actif.</p>
        </div>
    </div>
    @endforelse
</div>

@endsection
