@php
    $a       = \App\Support\Messagerie\Presenter::author($auteurs, $m->sender_type, $m->sender_id);
    $mine    = $actor->owns($m);
    $canEdit = \App\Support\Messagerie\Rules::canEdit($actor, $m);
    $canDel  = \App\Support\Messagerie\Rules::canDelete($actor, $space, $m);
    $canRep  = ! $m->is_deleted && \App\Support\Messagerie\Rules::canPost($actor, $space);
    $q       = $m->replyTo;
    $qa      = $q ? \App\Support\Messagerie\Presenter::author($auteurs, $q->sender_type, $q->sender_id) : null;
    $parMod  = $m->is_deleted && ($m->deleted_by_type !== $m->sender_type || (int) $m->deleted_by_id !== (int) $m->sender_id);
@endphp
<div class="mg-msg tone-{{ $a['tone'] }} {{ $mine ? 'mine' : '' }} {{ $m->is_deleted ? 'deleted' : '' }}"
     data-id="{{ $m->id }}" data-sender="{{ $m->sender_type }}:{{ $m->sender_id }}"
     data-day="{{ $m->created_at->format('Y-m-d') }}" data-ts="{{ $m->created_at->timestamp }}"
     data-name="{{ $mine ? 'Vous' : $a['name'] }}"
     data-excerpt="{{ $m->is_deleted ? '' : \App\Support\Messagerie\Presenter::excerpt($m->message) }}"
     data-raw="{{ $canEdit ? $m->message : '' }}">
    <div class="mg-av">{{ $a['initials'] }}</div>
    <div class="mg-bubble">
        <div class="mg-head">
            <span class="mg-name">{{ $mine ? 'Vous' : $a['name'] }}</span>
            @if($a['badge'])<span class="mg-badge">{{ $a['badge'] }}</span>@endif
        </div>

        @if($q && ! $m->is_deleted)
            <div class="mg-quote tone-{{ $qa['tone'] }}" data-act="goto" data-target="{{ $q->id }}">
                <b>{{ $qa['name'] }}</b>
                <span>{{ $q->is_deleted ? 'Message supprimé' : \App\Support\Messagerie\Presenter::excerpt($q->message, 80) }}</span>
            </div>
        @endif

        <div class="mg-text">
            @if($m->is_deleted)
                {{ $parMod ? 'Message supprimé par un modérateur' : 'Ce message a été supprimé' }}
            @else
                {!! \App\Support\Messagerie\Presenter::text($m->message) !!}
            @endif
        </div>

        <div class="mg-meta">
            @if($m->edited_at && ! $m->is_deleted)<span>modifié</span>@endif
            <span>{{ $m->created_at->format('H:i') }}</span>
            @if($canRep || $canEdit || $canDel)
                <button type="button" class="mg-more" data-act="menu" aria-label="Actions sur le message">⋯</button>
                <div class="mg-menu" hidden>
                    @if($canRep)<button type="button" data-act="reply">Répondre</button>@endif
                    @if($canEdit)<button type="button" data-act="edit">Modifier</button>@endif
                    @if($canDel)<button type="button" class="del" data-act="del">Supprimer pour tous</button>@endif
                </div>
            @endif
        </div>
    </div>
</div>
