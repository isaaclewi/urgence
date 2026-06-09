@extends('citoyen')

@section('title', 'Urgences — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Gestion d'Alertes</h1>
        <p>Sélectionnez un service d'urgence pour signaler un incident.</p>
    </div>
</div>

{{-- Info Banner --}}
<div class="alert alert-info anim-fade" style="margin-bottom:28px;">
    <i data-feather="info" style="width:16px;height:16px;flex-shrink:0;"></i>
    <span>Choisissez le type de service d'urgence ci-dessous, puis remplissez le formulaire de signalement. Votre alerte sera immédiatement transmise aux autorités compétentes.</span>
</div>

{{-- Services d'urgence --}}
<div style="margin-bottom:32px;">
    <h2 style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <i data-feather="grid" style="width:16px;height:16px;color:var(--accent);"></i>
        Services d'urgence disponibles
    </h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
        @forelse($services as $service)
        <a href="{{ $service->lien }}"
           style="display:flex;align-items:center;gap:14px;padding:18px 16px;
                  background:var(--surface);border:1.5px solid var(--border);
                  border-radius:var(--radius-lg);text-decoration:none;
                  transition:border-color .15s,box-shadow .15s,transform .15s;"
           onmouseover="this.style.borderColor='var(--accent)';this.style.boxShadow='var(--shadow)';this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none';this.style.transform='translateY(0)'">
            @if($service->image)
            <img src="{{ $service->image ?? asset('medias/default-service.jpg') }}"
                 style="width:52px;height:52px;border-radius:10px;object-fit:cover;flex-shrink:0;">
            @else
            <div style="width:52px;height:52px;border-radius:10px;background:var(--accent-light);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i data-feather="alert-triangle" style="width:22px;height:22px;color:var(--accent);"></i>
            </div>
            @endif
            <div style="min-width:0;">
                <div style="font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $service->nom_service }}
                </div>
                @if($service->description)
                <div style="font-size:12px;color:var(--text-sec);margin-top:3px;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $service->description }}
                </div>
                @endif
                <div style="font-size:12px;color:var(--accent);margin-top:4px;font-weight:600;">
                    Signaler →
                </div>
            </div>
        </a>
        @empty
        <div class="alert alert-warning">
            <i data-feather="alert-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
            <span>Aucun service d'urgence disponible pour le moment.</span>
        </div>
        @endforelse
    </div>
</div>

{{-- Numéros d'urgence --}}
<div class="card anim-fade" style="animation-delay:.1s;margin-bottom:28px;">
    <div class="card-header" style="padding-bottom:14px;border-bottom:1px solid var(--border);">
        <span class="card-title" style="font-size:15px;">
            <i data-feather="phone" style="width:15px;height:15px;display:inline;margin-right:6px;vertical-align:middle;"></i>
            Numéros d'urgence nationaux
        </span>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px;">
            @foreach([
                ['15','SAMU','Urgences médicales','bg-red-soft'],
                ['18','Pompiers','Incendie & secours','bg-amber-soft'],
                ['17','Police','Forces de l\'ordre','bg-blue-soft'],
                ['112','Urgence univ.','Tout type','bg-green-soft']
            ] as $num)
            <a href="tel:{{ $num[0] }}"
               style="padding:16px;background:var(--surface2);border:1px solid var(--border);
                      border-radius:var(--radius-lg);text-align:center;text-decoration:none;
                      transition:box-shadow .15s,transform .15s;display:block;"
               onmouseover="this.style.boxShadow='var(--shadow)';this.style.transform='translateY(-2px)'"
               onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)'">
                <div style="font-size:28px;font-weight:800;color:var(--text);line-height:1;">{{ $num[0] }}</div>
                <div style="font-size:12px;font-weight:700;color:var(--text);margin-top:4px;">{{ $num[1] }}</div>
                <div style="font-size:11px;color:var(--text-sec);margin-top:2px;">{{ $num[2] }}</div>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Conseils d'urgence --}}
<div class="card anim-fade" style="animation-delay:.18s;">
    <div class="card-header" style="padding-bottom:14px;border-bottom:1px solid var(--border);">
        <span class="card-title" style="font-size:15px;">
            <i data-feather="alert-octagon" style="width:15px;height:15px;display:inline;margin-right:6px;vertical-align:middle;color:var(--red);"></i>
            Conseils en cas d'urgence
        </span>
    </div>
    <div class="card-body">
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach([
                'Gardez votre calme et évaluez la situation avant d\'agir.',
                'Fournissez des informations précises sur votre localisation.',
                'Restez en sécurité en attendant l\'intervention des secours.',
                'Ne déplacez pas les personnes blessées sauf danger immédiat.',
            ] as $conseil)
            <div style="display:flex;align-items:flex-start;gap:10px;">
                <div style="width:20px;height:20px;border-radius:50%;background:var(--green-light);
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                    <i data-feather="check" style="width:11px;height:11px;color:var(--green);"></i>
                </div>
                <span style="font-size:13.5px;color:var(--text-sec);line-height:1.5;">{{ $conseil }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }
</style>
@endpush

@endsection
