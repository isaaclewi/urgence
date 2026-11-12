@extends($layout)

@section('title', 'Messagerie')
@section('page-title', 'Messagerie')

@section('content')
@include('messagerie._styles')

<div class="mg-page">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
        <div>
            <h1>Messagerie</h1>
            <p class="mg-sub">
                @if($actor->isAdmin())
                    Vous voyez tous les groupes. Vous seul pouvez les créer et choisir quels services y participent.
                @elseif($actor->isService())
                    Vos groupes de coordination. L'accès à un groupe est accordé par l'administration.
                @else
                    Les espaces d'échange ouverts aux citoyens.
                @endif
            </p>
        </div>
        @if($actor->isAdmin())
            <a class="mg-btn primary" href="{{ route('admin.messagerie.gestion') }}">Gérer les groupes</a>
        @endif
    </div>

    @php
        $mes     = $spaces->filter(fn ($s) => $s->acces);
        $autres  = $spaces->reject(fn ($s) => $s->acces);
    @endphp

    @if($mes->isEmpty() && $autres->isEmpty())
        <div class="mg-list"><div class="mg-empty">
            @if($actor->isService())
                Aucun groupe pour le moment. L'administration doit vous ajouter à un groupe pour que vous puissiez y participer.
            @elseif($actor->isAdmin())
                Aucun groupe créé. <a href="{{ route('admin.messagerie.gestion') }}">Créer le premier groupe</a>.
            @else
                Aucun espace de discussion n'est ouvert pour le moment.
            @endif
        </div></div>
    @endif

    @if($mes->isNotEmpty())
        <div class="mg-list">
            @foreach($mes as $s)
                @php $d = $s->dernier; $da = $s->dernier_auteur; @endphp
                <a class="mg-row" href="{{ route($base . 'show', $s->id) }}">
                    <div class="mg-av lg tone-{{ $s->type === 'public' ? 'hopital' : 'admin' }}">{{ mb_strtoupper(mb_substr($s->title, 0, 1)) }}</div>
                    <div class="mg-row-main">
                        <div class="mg-row-title">
                            <span>{{ $s->title }}</span>
                            <span class="mg-tag">{{ $s->type === 'public' ? 'Public' : 'Privé' }}</span>
                            @unless($s->is_active)<span class="mg-tag warn">Archivé</span>@endunless
                        </div>
                        <div class="mg-row-prev">
                            @if($d)
                                {{ $d->is_deleted ? 'Message supprimé' : ($da['name'] . ' : ' . \App\Support\Messagerie\Presenter::preview($d, 70)) }}
                            @else
                                {{ $s->description ?: 'Aucun message pour le moment' }}
                            @endif
                        </div>
                    </div>
                    <div class="mg-row-side">
                        <span>{{ $d ? $d->created_at->format('d/m H:i') : '' }}</span>
                        @if($s->non_lus > 0)<span class="mg-unread">{{ $s->non_lus > 99 ? '99+' : $s->non_lus }}</span>@endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if($actor->isService() && $autres->isNotEmpty())
        <div class="mg-h2">Groupes ouverts aux demandes d'accès</div>
        <div class="mg-list">
            @foreach($autres as $s)
                <div class="mg-row">
                    <div class="mg-av lg tone-citoyen">{{ mb_strtoupper(mb_substr($s->title, 0, 1)) }}</div>
                    <div class="mg-row-main">
                        <div class="mg-row-title"><span>{{ $s->title }}</span></div>
                        <div class="mg-row-prev">{{ $s->description ?: 'Groupe de coordination' }}</div>
                    </div>
                    <div class="mg-row-side">
                        @if($s->adhesion && $s->adhesion->status === 'pending')
                            <span class="mg-tag warn">Demande en attente</span>
                        @else
                            <form method="POST" action="{{ route('services.messagerie.acces', $s->id) }}">
                                @csrf
                                <button class="mg-btn" type="submit">Demander l'accès</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
