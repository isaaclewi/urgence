{{--
    Une publication du fil d'actualités.
    Variables : $actu (Actualite)
                $gerer (bool)        afficher Modifier / Supprimer
                $routeEdit, $routeDestroy (noms de routes, requis si $gerer)
--}}
@php
    $gerer     = $gerer ?? false;
    $date      = ($actu->created_at ?? \Carbon\Carbon::parse($actu->date_publication))->copy()->locale('fr');
    $auteur    = $actu->auteur_nom ?: 'CongoAssist';
    $photo     = $actu->relationLoaded('service') ? $actu->service?->photo_profil : null;
    $estVideo  = $actu->type_media === 'mp4';
    $modifie   = $actu->updated_at && $actu->created_at && $actu->updated_at->gt($actu->created_at->copy()->addMinute());
@endphp
<article class="fa-post" id="post-{{ $actu->id }}">

    <header class="fa-post-head">
        <div class="fa-avatar" aria-hidden="true">
            {{ mb_strtoupper(mb_substr($auteur, 0, 1)) }}
            @if($photo)<img src="{{ $photo }}" alt="" loading="lazy" decoding="async">@endif
        </div>
        <div class="fa-who">
            <div class="fa-name">{{ $auteur }}</div>
            <div class="fa-meta">
                <time datetime="{{ $date->toIso8601String() }}" title="{{ $date->isoFormat('dddd D MMMM YYYY [à] HH:mm') }}">{{ $date->diffForHumans() }}</time>
                <span class="fa-dot">·</span>
                <span>{{ $date->isoFormat('D MMM YYYY') }}</span>
                @if($modifie)<span class="fa-dot">·</span><span>modifié</span>@endif
            </div>
        </div>
    </header>

    @if(filled($actu->contenu))
    <div class="fa-body">
        <p class="fa-text" data-fa-text>{{ $actu->contenu }}</p>
        <button type="button" class="fa-toggle" data-fa-toggle aria-expanded="false" hidden>Voir plus</button>
    </div>
    @endif

    @if($actu->url_media)
        @if($estVideo)
            <div class="fa-media is-video">
                <video controls playsinline preload="metadata">
                    <source src="{{ $actu->url_media }}" type="video/mp4">
                </video>
            </div>
        @else
            <div class="fa-media is-image">
                <img src="{{ $actu->url_media }}" alt="Image de la publication de {{ $auteur }}"
                     loading="lazy" decoding="async" data-fa-zoom>
            </div>
        @endif
    @endif

    <footer class="fa-actions">
        <button type="button" class="fa-btn" data-fa-share="{{ $actu->id }}"
                data-fa-title="{{ $auteur }}" data-fa-text="{{ \Illuminate\Support\Str::limit($actu->contenu, 120) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.6" y1="13.5" x2="15.4" y2="17.5"/><line x1="15.4" y1="6.5" x2="8.6" y2="10.5"/></svg>
            Partager
        </button>

        @if($gerer)
            <a class="fa-btn" href="{{ route($routeEdit, $actu->id) }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                Modifier
            </a>
            <form method="POST" action="{{ route($routeDestroy, $actu->id) }}"
                  onsubmit="return confirm('Supprimer cette actualité ? Cette action est irréversible.')">
                @csrf @method('DELETE')
                <button type="submit" class="fa-btn fa-btn-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    Supprimer
                </button>
            </form>
        @endif
    </footer>
</article>
