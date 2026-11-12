@extends('layouts.portal')

@section('title', 'Gérer les groupes')
@section('page-title', 'Messagerie — gestion des groupes')

@section('content')
@include('messagerie._styles')
<style>
.mg-form-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px; }
.mg-f label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:4px; }
.mg-f input[type=text], .mg-f select, .mg-f textarea { width:100%; box-sizing:border-box; border:1px solid #E2E8F0; border-radius:8px; padding:8px 10px; font:13.5px 'Inter',sans-serif; background:#fff; }
.mg-check { display:flex; gap:8px; align-items:center; font-size:13px; }
.mg-card { background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:16px; margin-bottom:18px; }
.mg-card > summary { cursor:pointer; font-weight:700; font-size:14px; }
.mg-table { width:100%; border-collapse:collapse; font-size:13px; }
.mg-table th { text-align:left; font-size:12px; color:#64748B; padding:8px 10px; border-bottom:1px solid #E2E8F0; }
.mg-table td { padding:10px; border-bottom:1px solid #F1F5F9; vertical-align:top; }
.mg-actions { display:flex; gap:6px; flex-wrap:wrap; }
.mg-scrollx { overflow-x:auto; }
.mg-edit-box { background:#F8FAFC; border-radius:10px; padding:12px; margin-top:8px; }
</style>

<div class="mg-page">
    <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:flex-start;">
        <div>
            <h1>Groupes de discussion</h1>
            <p class="mg-sub">Vous créez les groupes et vous décidez quels services y ont accès. Un service non ajouté ne voit pas le groupe.</p>
        </div>
        <a class="mg-btn" href="{{ route('admin.messagerie.index') }}">Ouvrir la messagerie</a>
    </div>

    <details class="mg-card" @if($spaces->isEmpty() || $errors->any()) open @endif>
        <summary>Créer un groupe</summary>
        <form method="POST" action="{{ route('admin.messagerie.gestion.store') }}" style="margin-top:14px;">
            @csrf
            @include('messagerie.admin._champs', ['space' => null, 'services' => $services])
            <div style="margin-top:14px;"><button class="mg-btn primary" type="submit">Créer le groupe</button></div>
        </form>
    </details>

    <div class="mg-card" style="padding:0;">
        <div class="mg-scrollx">
        <table class="mg-table">
            <thead><tr><th>Groupe</th><th>Visibilité</th><th>Services</th><th>Messages</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            @forelse($spaces as $s)
                <tr>
                    <td>
                        <b>{{ $s->title }}</b>
                        <div style="color:#64748B; font-size:12px;">{{ \Illuminate\Support\Str::limit($s->description, 80) }}</div>
                        @if($s->service)<div style="font-size:12px;">Responsable : {{ $s->service->nom }}</div>@endif
                    </td>
                    <td>
                        <span class="mg-tag">{{ $s->type === 'public' ? 'Public (citoyens)' : 'Privé' }}</span>
                        <div style="font-size:12px; color:#64748B; margin-top:4px;">
                            Accès : {{ $s->join_policy === 'request' ? 'sur demande' : 'sur invitation' }}
                        </div>
                    </td>
                    <td>
                        {{ $s->services_count }}
                        @if($s->demandes_count)<span class="mg-tag warn">{{ $s->demandes_count }} demande(s)</span>@endif
                    </td>
                    <td>{{ $s->messages_count }}</td>
                    <td>@if($s->is_active)<span class="mg-tag ok">Actif</span>@else<span class="mg-tag warn">Archivé</span>@endif</td>
                    <td>
                        <div class="mg-actions">
                            <a class="mg-btn" href="{{ route('admin.messagerie.show', $s->id) }}">Ouvrir</a>
                            <a class="mg-btn" href="{{ route('admin.messagerie.membres', $s->id) }}">Accès</a>
                            <form method="POST" action="{{ route('admin.messagerie.gestion.toggle', $s->id) }}">@csrf
                                <button class="mg-btn" type="submit">{{ $s->is_active ? 'Archiver' : 'Réactiver' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.messagerie.gestion.destroy', $s->id) }}"
                                  onsubmit="return confirm('Supprimer définitivement ce groupe et tous ses messages ?')">@csrf @method('DELETE')
                                <button class="mg-btn danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                        <details style="margin-top:6px;">
                            <summary style="cursor:pointer; font-size:12.5px; color:#475569;">Modifier les paramètres</summary>
                            <form method="POST" action="{{ route('admin.messagerie.gestion.update', $s->id) }}" class="mg-edit-box">
                                @csrf @method('PUT')
                                @include('messagerie.admin._champs', ['space' => $s, 'services' => $services, 'edition' => true])
                                <div style="margin-top:10px;"><button class="mg-btn primary" type="submit">Enregistrer</button></div>
                            </form>
                        </details>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="mg-empty">Aucun groupe pour le moment.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
