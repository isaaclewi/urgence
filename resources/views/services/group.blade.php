@extends('services.master')

@section('title', $space->title . ' — Forum')

@section('page-title', $space->title)
@section('page-subtitle', $space->description ?? 'Discussion de groupe')

@section('sidebar')
<div class="sb-section-label">Navigation</div>
<a href="{{ route('services.compte') }}" class="sidebar-link">
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
<div style="display:flex; flex-direction:column; gap:20px;">

    {{-- Header du groupe --}}
    <div class="content-card anim-fade" style="border-left:3px solid var(--accent);">
        <div class="cc-body">
            <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                <div style="width:56px; height:56px; border-radius:14px; flex-shrink:0;
                            display:flex; align-items:center; justify-content:center; font-size:28px;
                            background:{{ $space->type === 'public' ? 'linear-gradient(135deg,#34D399,#059669)' : 'linear-gradient(135deg,#60A5FA,#2563EB)' }};">
                    {{ $space->type === 'public' ? '🌐' : '🔒' }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-family:'Sora',sans-serif; font-size:17px; font-weight:700; color:var(--text);">
                        {{ $space->title }}
                    </div>
                    <div style="font-size:12.5px; color:var(--text-muted); margin-top:3px;">
                        {{ $space->description ?? 'Discussion de groupe' }}
                    </div>
                    <div style="display:flex; gap:8px; margin-top:8px; flex-wrap:wrap;">
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
                <a href="{{ route('services.forum.index') }}" class="btn btn-outline btn-sm">
                    <i data-feather="arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    {{-- Zone de messages --}}
    <div class="content-card anim-slide" style="overflow:hidden;">
        <div class="cc-header">
            <div class="cc-title"><i data-feather="message-circle"></i> Messages</div>
        </div>

        {{-- Messages --}}
        <div id="messagesContainer"
             style="padding:20px; display:flex; flex-direction:column; gap:14px;
                    max-height:500px; overflow-y:auto; background:var(--surface2);">
            @forelse($messages as $msg)
            <div style="display:flex; {{ $msg->sender_type === 'service' ? '' : 'justify-content:flex-end;' }}">
                <div style="max-width:70%;">
                    {{-- Expéditeur --}}
                    <div style="font-size:11px; font-weight:700; margin-bottom:4px;
                                color:{{ $msg->sender_type === 'service' ? '#D97706' : 'var(--accent)' }};
                                display:flex; align-items:center; gap:4px;">
                        <i data-feather="{{ $msg->sender_type === 'service' ? 'briefcase' : 'user' }}"
                           style="width:11px;height:11px;"></i>
                        {{ ucfirst($msg->sender_type) }}
                    </div>

                    {{-- Bulle --}}
                    <div style="border-radius:12px;
                                {{ $msg->sender_type === 'service'
                                    ? 'border-top-left-radius:0; background:var(--surface); border:1px solid var(--border);'
                                    : 'border-top-right-radius:0; background:var(--accent); color:#fff;' }}
                                padding:12px 14px; box-shadow:0 2px 8px rgba(0,0,0,.06);">

                        @if($msg->message_type === 'texte')
                        <p style="font-size:13.5px; line-height:1.6; margin:0;
                                  color:{{ $msg->sender_type === 'service' ? 'var(--text)' : '#fff' }};">
                            {{ $msg->message }}
                        </p>
                        @else
                        <a href="{{ asset($msg->file_path) }}" target="_blank"
                           style="display:flex; align-items:center; gap:8px; padding:8px 12px;
                                  border-radius:8px; text-decoration:none;
                                  background:{{ $msg->sender_type === 'service' ? 'var(--surface2)' : 'rgba(255,255,255,.15)' }};
                                  color:{{ $msg->sender_type === 'service' ? 'var(--text)' : '#fff' }};
                                  font-size:13px; font-weight:600;">
                            <i data-feather="paperclip" style="width:14px;height:14px;"></i>
                            {{ $msg->file_name }}
                        </a>
                        @endif

                        {{-- Heure + suppression --}}
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;
                                    margin-top:6px; font-size:11px;
                                    color:{{ $msg->sender_type === 'service' ? 'var(--text-muted)' : 'rgba(255,255,255,.7)' }};">
                            <i data-feather="clock" style="width:11px;height:11px;"></i>
                            {{ $msg->created_at->format('H:i') }}
                            @if($msg->sender_type === 'service' && $msg->sender_id == session('service_id'))
                            <form action="{{ route('services.forum.message.delete', $msg->id) }}"
                                  method="POST" style="display:inline; margin-left:4px;">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="background:none; border:none; cursor:pointer; padding:0;
                                               color:{{ $msg->sender_type === 'service' ? '#EF4444' : 'rgba(255,255,255,.7)' }};"
                                        title="Supprimer">
                                    <i data-feather="trash-2" style="width:12px;height:12px;"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:48px 20px;">
                <div style="width:56px;height:56px;border-radius:50%;background:var(--border);
                             display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i data-feather="message-square" style="width:24px;height:24px;color:var(--border-mid);"></i>
                </div>
                <div style="font-size:14px; font-weight:600; color:var(--text); margin-bottom:4px;">Aucun message pour l'instant</div>
                <div style="font-size:12.5px; color:var(--text-muted);">Soyez le premier à écrire dans cet espace !</div>
            </div>
            @endforelse
        </div>

        {{-- Zone de saisie --}}
        <div style="padding:16px 20px; background:var(--bg); border-top:1px solid var(--border);">
            <form method="POST" action="{{ route('services.forum.group.send', $space->id) }}"
                  style="display:flex; gap:10px; align-items:flex-end;">
                @csrf
                <textarea name="message" rows="1"
                          class="form-input" style="flex:1; resize:none; max-height:120px;"
                          placeholder="Écrire un message…"
                          onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit();}"
                          oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
                <button type="submit" class="btn btn-accent">
                    <i data-feather="send"></i>
                    <span>Envoyer</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Stats rapides --}}
    <div class="grid-3 anim-fade">
        <div class="stat-card blue">
            <div class="sc-icon" style="background:#DBEAFE;">
                <i data-feather="message-circle" style="color:#3B82F6;"></i>
            </div>
            <div class="sc-value">{{ count($messages) }}</div>
            <div class="sc-label">Messages</div>
        </div>
        <div class="stat-card purple">
            <div class="sc-icon" style="background:#EDE9FE;">
                <i data-feather="users" style="color:#8B5CF6;"></i>
            </div>
            <div class="sc-value">{{ $messages->unique('sender_id')->count() }}</div>
            <div class="sc-label">Participants</div>
        </div>
        <div class="stat-card green">
            <div class="sc-icon" style="background:#D1FAE5;">
                <i data-feather="zap" style="color:#10B981;"></i>
            </div>
            <div class="sc-value" style="font-size:18px;">Actif</div>
            <div class="sc-label">Statut</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace({ width: 16, height: 16 });
        const c = document.getElementById('messagesContainer');
        if (c) c.scrollTop = c.scrollHeight;

        const ta = document.querySelector('textarea[name="message"]');
        if (ta && window.innerWidth > 768) ta.focus();
    });
</script>
@endpush
@endsection
