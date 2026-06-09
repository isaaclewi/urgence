@extends('citoyen')

@section('title', 'Actualités — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Actualités</h1>
        <p>{{ $actualites->count() }} article{{ $actualites->count() > 1 ? 's' : '' }} disponible{{ $actualites->count() > 1 ? 's' : '' }}</p>
    </div>
</div>

<div style="max-width:720px;">
    @forelse($actualites as $index => $actu)
    <article class="card anim-fade" style="margin-bottom:16px;animation-delay:{{ $index * 0.05 }}s;">

        {{-- Barre de couleur en haut --}}
        <div style="height:3px;background:linear-gradient(90deg,var(--accent),var(--green));border-radius:var(--radius-lg) var(--radius-lg) 0 0;"></div>

        {{-- Auteur --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px 12px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:var(--accent);
                            display:flex;align-items:center;justify-content:center;
                            font-weight:700;color:#fff;font-size:16px;flex-shrink:0;">
                    {{ strtoupper(substr($actu->auteur_nom, 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $actu->auteur_nom }}</div>
                    <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-sec);margin-top:2px;">
                        <i data-feather="clock" style="width:11px;height:11px;"></i>
                        {{ \Carbon\Carbon::parse($actu->date_publication)->diffForHumans() }}
                        &nbsp;·&nbsp;
                        {{ \Carbon\Carbon::parse($actu->date_publication)->translatedFormat('d M Y') }}
                    </div>
                </div>
            </div>
            <span class="pill pill-blue">Actualité</span>
        </div>

        {{-- Contenu --}}
        <div style="padding:0 20px 16px;">
            <p id="body-{{ $actu->id }}"
               style="font-size:14px;line-height:1.75;color:#374151;">{{ Str::limit($actu->contenu, 280) }}</p>
            @if(strlen($actu->contenu) > 280)
            <button id="btn-{{ $actu->id }}"
                    data-full="{{ e($actu->contenu) }}"
                    data-short="{{ e(Str::limit($actu->contenu, 280)) }}"
                    data-open="false"
                    onclick="toggleContent({{ $actu->id }})"
                    style="margin-top:8px;background:none;border:none;cursor:pointer;
                           font-size:12.5px;font-weight:600;color:var(--accent);
                           display:inline-flex;align-items:center;gap:4px;font-family:inherit;padding:0;">
                Lire la suite <i data-feather="chevron-down" style="width:13px;height:13px;"></i>
            </button>
            @endif
        </div>

        {{-- Média --}}
        @if($actu->url_media)
        <div style="overflow:hidden;border-top:1px solid var(--border);">
            @if($actu->type_media === 'mp4')
                <video controls style="width:100%;max-height:400px;display:block;background:#000;">
                    <source src="{{ $actu->url_media }}" type="video/mp4">
                </video>
            @else
                <img src="{{ $actu->url_media }}" alt="Illustration"
                     style="width:100%;max-height:400px;object-fit:cover;display:block;">
            @endif
        </div>
        @endif

    </article>
    @empty
    <div class="card" style="text-align:center;padding:60px 24px;">
        <div style="width:60px;height:60px;background:var(--surface2);border-radius:50%;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i data-feather="inbox" style="width:26px;height:26px;color:var(--text-muted);"></i>
        </div>
        <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px;">Aucune actualité pour l'instant</div>
        <div style="font-size:13px;color:var(--text-sec);">Revenez plus tard pour découvrir les dernières nouvelles.</div>
    </div>
    @endforelse
</div>

@push('scripts')
<script>
function toggleContent(id) {
    const body = document.getElementById('body-'+id);
    const btn  = document.getElementById('btn-'+id);
    const open = btn.dataset.open === 'true';
    body.textContent = open ? btn.dataset.short : btn.dataset.full;
    btn.dataset.open = open ? 'false' : 'true';
    btn.innerHTML = open
        ? 'Lire la suite <i data-feather="chevron-down" style="width:13px;height:13px;"></i>'
        : 'Réduire <i data-feather="chevron-up" style="width:13px;height:13px;"></i>';
    feather.replace({ 'stroke-width': 1.75 });
}
</script>
@endpush

@endsection
