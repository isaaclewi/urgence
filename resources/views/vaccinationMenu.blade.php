@extends('citoyen')

@section('title', 'Vaccinations — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Vaccinations</h1>
        <p>Gérez vos programmes de vaccination et ceux de votre famille.</p>
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:28px;">
    <div class="stat-card anim-fade">
        <div class="stat-icon bg-green-soft"><i data-feather="shield-check" style="width:18px;height:18px;"></i></div>
        <div><div class="stat-value">95%</div><div class="stat-label">Efficacité moyenne</div></div>
    </div>
    <div class="stat-card anim-fade" style="animation-delay:.05s">
        <div class="stat-icon bg-blue-soft"><i data-feather="users" style="width:18px;height:18px;"></i></div>
        <div><div class="stat-value">85%</div><div class="stat-label">Pop. vaccinée</div></div>
    </div>
    <div class="stat-card anim-fade" style="animation-delay:.1s">
        <div class="stat-icon bg-purple-soft"><i data-feather="trending-up" style="width:18px;height:18px;"></i></div>
        <div><div class="stat-value">3M+</div><div class="stat-label">Vies sauvées</div></div>
    </div>
</div>

{{-- Options --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;">
    @php
    $options = [
        [
            'route' => route('programmeVaccinController'),
            'icon' => 'alert-triangle',
            'color' => 'bg-red-soft',
            'title' => 'Pandémies',
            'desc' => 'Programmes de vaccination liés aux pandémies actuelles. Protégez-vous contre les menaces sanitaires mondiales.',
        ],
        [
            'route' => route('programmeVaccinEpidemieController'),
            'icon' => 'alert-circle',
            'color' => 'bg-amber-soft',
            'title' => 'Épidémies',
            'desc' => 'Alertes et programmes contre les épidémies locales pour une protection ciblée.',
        ],
        [
            'route' => route('programmeVaccinEnfantController'),
            'icon' => 'heart',
            'color' => 'bg-purple-soft',
            'title' => 'Vaccination enfant',
            'desc' => 'Calendrier vaccinal pédiatrique. Consultez et suivez les vaccins de vos enfants.',
        ],
        [
            'route' => '#',
            'icon' => 'user-check',
            'color' => 'bg-green-soft',
            'title' => 'Mon programme',
            'desc' => 'Votre calendrier de vaccination personnalisé avec vos rappels et antécédents.',
        ],
    ];
    @endphp

    @foreach($options as $i => $opt)
    <a href="{{ $opt['route'] }}"
       class="card anim-fade"
       style="animation-delay:{{ $i * 0.07 }}s;text-decoration:none;display:block;
              transition:border-color .15s,box-shadow .15s,transform .15s;"
       onmouseover="this.style.borderColor='var(--accent)';this.style.boxShadow='var(--shadow)';this.style.transform='translateY(-3px)'"
       onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='var(--shadow-sm)';this.style.transform='translateY(0)'">
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;height:100%;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="stat-icon {{ $opt['color'] }}" style="width:42px;height:42px;border-radius:10px;flex-shrink:0;">
                    <i data-feather="{{ $opt['icon'] }}" style="width:18px;height:18px;"></i>
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $opt['title'] }}</div>
            </div>
            <p style="font-size:13px;color:var(--text-sec);line-height:1.65;flex:1;">{{ $opt['desc'] }}</p>
            <div style="font-size:12.5px;font-weight:600;color:var(--accent);display:flex;align-items:center;gap:5px;">
                Voir les détails <i data-feather="arrow-right" style="width:13px;height:13px;"></i>
            </div>
        </div>
    </a>
    @endforeach
</div>

@endsection
