@extends('citoyen')

@section('title', 'Forum — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Espaces de discussion</h1>
        <p>Rejoignez les conversations de la communauté CongoAssist.</p>
    </div>
    <span class="pill pill-green" style="font-size:13px;padding:6px 14px;">
        <i data-feather="grid" style="width:13px;height:13px;"></i>
        {{ count($spaces) }} {{ count($spaces) > 1 ? 'espaces' : 'espace' }}
    </span>
</div>

{{-- Grille des espaces --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
    @forelse($spaces as $index => $space)
    <a href="{{ route('groupeDiscussion', $space->id) }}"
       class="card anim-fade"
       style="animation-delay:{{ $index * 0.08 }}s;text-decoration:none;display:block;
              transition:border-color .15s,box-shadow .15s,transform .15s;overflow:hidden;"
       onmouseover="this.style.borderColor='var(--accent)';this.style.boxShadow='var(--shadow)';this.style.transform='translateY(-3px)'"
       onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none';this.style.transform='translateY(0)'">

        {{-- Barre colorée en haut --}}
        <div style="height:4px;background:{{ $space->type === 'public' ? 'linear-gradient(90deg,var(--green),#34d399)' : 'linear-gradient(90deg,var(--accent),#60a5fa)' }};"></div>

        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:flex-start;gap:14px;">
                <div style="width:48px;height:48px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:22px;
                            background:{{ $space->type === 'public' ? 'var(--green-light)' : 'var(--accent-light)' }};">
                    {{ $space->type === 'public' ? '🌐' : '🔒' }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px;">
                        <h3 style="font-size:14px;font-weight:700;color:var(--text);line-height:1.3;margin:0;">
                            {{ $space->title }}
                        </h3>
                        <i data-feather="arrow-right" style="width:15px;height:15px;color:var(--text-muted);flex-shrink:0;margin-top:2px;"></i>
                    </div>
                    <p style="font-size:12.5px;color:var(--text-sec);margin:0 0 10px;line-height:1.5;
                               display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $space->description ?? 'Aucune description disponible.' }}
                    </p>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <span class="pill {{ $space->type === 'public' ? 'pill-green' : 'pill-blue' }}">
                            <i data-feather="{{ $space->type === 'public' ? 'globe' : 'lock' }}" style="width:10px;height:10px;"></i>
                            {{ strtoupper($space->type) }}
                        </span>
                        @if($space->service)
                        <span class="pill pill-blue">
                            <i data-feather="briefcase" style="width:10px;height:10px;"></i>
                            {{ $space->service->nom }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </a>
    @empty
    <div style="grid-column:1/-1;">
        <div class="card" style="padding:60px;text-align:center;">
            <div style="width:64px;height:64px;border-radius:50%;background:var(--surface2);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <i data-feather="message-square" style="width:28px;height:28px;color:var(--text-muted);"></i>
            </div>
            <p style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:6px;">Aucun espace disponible</p>
            <p style="font-size:13px;color:var(--text-sec);">Les espaces de discussion seront bientôt disponibles.</p>
        </div>
    </div>
    @endforelse
</div>

@endsection
