@extends('citoyen')

@section('title', 'Groupe — {{ $space->title }}')

@section('content')

{{-- Header groupe --}}
<div class="card anim-fade" style="margin-bottom:20px;overflow:hidden;">
    <div style="height:4px;background:linear-gradient(90deg,var(--green),#34d399);"></div>
    <div class="card-body" style="padding:18px 22px;">
        <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                <i data-feather="arrow-left" style="width:13px;height:13px;"></i>
            </a>
            <div style="width:44px;height:44px;border-radius:10px;background:var(--green-light);
                        display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                {{ $space->type === 'public' ? '🌐' : '🔒' }}
            </div>
            <div style="flex:1;min-width:0;">
                <h2 style="font-size:15px;font-weight:700;color:var(--text);margin:0 0 3px;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $space->title }}
                </h2>
                <p style="font-size:12px;color:var(--text-sec);margin:0;
                           white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $space->description ?? 'Discussion de groupe' }}
                </p>
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
                <span class="pill {{ $space->type === 'public' ? 'pill-green' : 'pill-blue' }}">
                    {{ strtoupper($space->type) }}
                </span>
                @if($space->service)
                <span class="pill pill-gray">{{ $space->service->nom }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Messages --}}
<div class="card anim-fade" style="margin-bottom:16px;">
    <div class="card-body" style="padding:20px;display:flex;flex-direction:column;gap:12px;min-height:400px;max-height:55vh;overflow-y:auto;" id="messagesContainer">
        @forelse($messages as $msg)
        <div style="display:flex;justify-content:{{ $msg->sender_type === 'citoyen' ? 'flex-end' : 'flex-start' }};">
            <div style="max-width:72%;padding:12px 16px;border-radius:{{ $msg->sender_type === 'citoyen' ? '14px 14px 4px 14px' : '14px 14px 14px 4px' }};
                        background:{{ $msg->sender_type === 'citoyen' ? 'var(--accent-light)' : 'var(--surface2)' }};
                        border:1px solid {{ $msg->sender_type === 'citoyen' ? '#bfdbfe' : 'var(--border)' }};">
                <div style="font-size:11px;font-weight:700;color:{{ $msg->sender_type === 'citoyen' ? 'var(--accent)' : 'var(--green)' }};margin-bottom:5px;text-transform:capitalize;">
                    {{ ucfirst($msg->sender_type) }}
                </div>
                @if($msg->message_type === 'texte')
                    <p style="font-size:13.5px;color:var(--text);margin:0;line-height:1.5;word-break:break-word;">
                        {{ $msg->message }}
                    </p>
                @else
                    <a href="{{ asset($msg->file_path) }}" target="_blank"
                       style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--surface);
                              border:1px solid var(--border);border-radius:var(--radius);text-decoration:none;
                              color:var(--text);font-size:13px;">
                        <i data-feather="paperclip" style="width:14px;height:14px;flex-shrink:0;"></i>
                        <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $msg->file_name }}</span>
                    </a>
                @endif
                <div style="font-size:11px;color:var(--text-muted);margin-top:6px;text-align:right;">
                    {{ $msg->created_at->format('H:i') }}
                </div>
            </div>
        </div>
        @empty
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
                    padding:40px;text-align:center;color:var(--text-sec);">
            <i data-feather="message-square" style="width:40px;height:40px;opacity:.25;margin-bottom:12px;"></i>
            <p style="font-size:14px;font-weight:600;margin:0 0 4px;">Aucun message</p>
            <p style="font-size:12.5px;margin:0;">Soyez le premier à écrire !</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Formulaire d'envoi --}}
<div class="card anim-fade" style="animation-delay:.1s;position:sticky;bottom:0;">
    <div class="card-body" style="padding:14px 18px;">
        <form method="POST" action="{{ route('creerGroupe', $space->id) }}"
              style="display:flex;gap:10px;align-items:flex-end;">
            @csrf
            <textarea name="message" rows="1" placeholder="Écrire un message…" class="form-control"
                      style="flex:1;border-radius:20px;padding:10px 16px;resize:none;max-height:120px;"
                      onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.form.submit();}"
                      oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,120)+'px'"></textarea>
            <button type="submit" class="btn btn-primary"
                    style="border-radius:50%;width:40px;height:40px;padding:0;justify-content:center;flex-shrink:0;">
                <i data-feather="send" style="width:15px;height:15px;"></i>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('messagesContainer');
        if (container) container.scrollTop = container.scrollHeight;
    });
</script>
@endpush

@endsection
