@extends('services.master')

@section('title', 'Forum — ' . ($service->nom ?? 'CongoAssist'))

@section('page-title', 'Forum')
@section('page-subtitle', 'Espaces de discussion et échanges communautaires')

@section('sidebar')
<div class="sb-section-label">Navigation</div>
<a href="{{ route('services.compte') }}" class="sidebar-link {{ request()->routeIs('services.compte') ? 'active' : '' }}">
    <i data-feather="home"></i><span class="sb-lbl">Tableau de bord</span>
</a>
<a href="{{ route('services.urgenceSignalee') }}" class="sidebar-link">
    <i data-feather="bell"></i>
    <span class="sb-lbl">Urgences signalées</span>
    @if(($stats['urgences_en_cours'] ?? 0) > 0)
        <span class="sb-badge">{{ $stats['urgences_en_cours'] }}</span>
    @endif
</a>
<a href="{{ route('services.citoyens') }}" class="sidebar-link">
    <i data-feather="users"></i><span class="sb-lbl">Citoyens</span>
</a>
<a href="{{ route('services.forum.index') }}" class="sidebar-link active">
    <i data-feather="message-square"></i><span class="sb-lbl">Forum</span>
</a>
<a href="{{ route('services.actualite') }}" class="sidebar-link">
    <i data-feather="newspaper"></i><span class="sb-lbl">Actualités</span>
</a>
<a href="{{ route('services.profil') }}" class="sidebar-link">
    <i data-feather="settings"></i><span class="sb-lbl">Gestion interne</span>
</a>

@if(($service->role ?? '') === 'hopital')
<div class="sb-divider"></div>
<div class="sb-section-label">Services Médicaux</div>
<a href="{{ route('services.vaccinationIndex') }}" class="sidebar-link">
    <i data-feather="calendar"></i><span class="sb-lbl">Programmes Vaccination</span>
</a>
<a href="{{ route('services.citoyensBilan') }}" class="sidebar-link">
    <i data-feather="heart"></i><span class="sb-lbl">Bilan Santé</span>
</a>
@endif

<div class="sb-divider"></div>
<a href="{{ route('services.logout') }}" class="sidebar-link danger">
    <i data-feather="log-out"></i><span class="sb-lbl">Déconnexion</span>
</a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- Résumé --}}
    <div class="stat-card accent anim-fade">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div class="sc-icon" style="background:rgba(29,184,122,.12); margin:0;">
                    <i data-feather="message-square" style="color:var(--accent);"></i>
                </div>
                <div>
                    <div style="font-family:'Sora',sans-serif; font-size:18px; font-weight:700; color:var(--text);">
                        Espaces de discussion
                    </div>
                    <div style="font-size:12.5px; color:var(--text-muted); margin-top:3px;">
                        Rejoignez la conversation et échangez avec la communauté
                    </div>
                </div>
            </div>
            <div style="text-align:right;">
                <div class="sc-value">{{ count($spaces) }}</div>
                <div class="sc-label">{{ count($spaces) > 1 ? 'Espaces' : 'Espace' }}</div>
            </div>
        </div>
    </div>

    {{-- Liste des espaces --}}
    <div style="display:flex; flex-direction:column; gap:12px;" class="anim-slide">
        @forelse($spaces as $space)
        <a href="{{ route('services.forum.group', $space->id) }}"
           style="text-decoration:none; display:block;">
            <div class="content-card" style="transition:box-shadow .22s, transform .22s; cursor:pointer;"
                 onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 24px rgba(11,30,61,.07)';"
                 onmouseleave="this.style.transform='';this.style.boxShadow='';">
                <div class="cc-body">
                    <div style="display:flex; align-items:center; gap:16px;">

                        {{-- Icône type --}}
                        <div style="width:52px; height:52px; border-radius:14px; flex-shrink:0;
                                    display:flex; align-items:center; justify-content:center; font-size:26px;
                                    background:{{ $space->type === 'public' ? 'linear-gradient(135deg,#34D399,#059669)' : 'linear-gradient(135deg,#60A5FA,#2563EB)' }};">
                            {{ $space->type === 'public' ? '🌐' : '🔒' }}
                        </div>

                        {{-- Info --}}
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                                <div style="font-size:14px; font-weight:700; color:var(--text);">
                                    {{ $space->title }}
                                </div>
                                <i data-feather="arrow-right" style="width:16px;height:16px;color:var(--text-muted); flex-shrink:0;"></i>
                            </div>
                            <div style="font-size:12.5px; color:var(--text-muted); margin-bottom:10px;
                                        overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ \Illuminate\Support\Str::limit($space->description, 120) }}
                            </div>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <span class="pill {{ $space->type === 'public' ? 'pill-green' : 'pill-blue' }}">
                                    <i data-feather="{{ $space->type === 'public' ? 'globe' : 'lock' }}" style="width:11px;height:11px;"></i>
                                    {{ strtoupper($space->type) }}
                                </span>
                                @if($space->service)
                                <span class="pill pill-gray">
                                    <i data-feather="briefcase" style="width:11px;height:11px;"></i>
                                    {{ $space->service->nom }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="content-card">
            <div class="cc-body" style="text-align:center; padding:60px 20px;">
                <div style="width:64px;height:64px;border-radius:50%;background:var(--surface2);
                             display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i data-feather="message-square" style="width:28px;height:28px;color:var(--border-mid);"></i>
                </div>
                <div style="font-size:15px; font-weight:700; color:var(--text); margin-bottom:6px;">Aucun espace disponible</div>
                <div style="font-size:13px; color:var(--text-muted);">Les espaces de discussion seront bientôt disponibles.</div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => feather.replace({ width: 16, height: 16 }));
</script>
@endpush
@endsection
