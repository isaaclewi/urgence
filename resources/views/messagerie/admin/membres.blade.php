@extends('layouts.portal')

@section('title', 'Accès — ' . $space->title)
@section('page-title', 'Messagerie — accès au groupe')

@section('content')
@include('messagerie._styles')
<style>
.mg-card { background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:16px; margin-bottom:18px; }
.mg-table { width:100%; border-collapse:collapse; font-size:13px; }
.mg-table th { text-align:left; font-size:12px; color:#64748B; padding:8px 10px; border-bottom:1px solid #E2E8F0; }
.mg-table td { padding:10px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
.mg-table select { border:1px solid #E2E8F0; border-radius:8px; padding:6px 8px; font:13px 'Inter',sans-serif; background:#fff; }
.mg-scrollx { overflow-x:auto; }
.mg-check { display:flex; gap:8px; align-items:center; font-size:13px; }
.mg-pending td { background:#FFFBEB; }
</style>

<div class="mg-page">
    <a class="mg-btn" href="{{ route('admin.messagerie.gestion') }}">← Tous les groupes</a>
    <h1 style="margin-top:14px;">{{ $space->title }}</h1>
    <p class="mg-sub">
        {{ $space->type === 'public' ? 'Groupe public' : 'Groupe privé' }}
        · accès {{ $space->join_policy === 'request' ? 'sur demande' : 'sur invitation' }}
        · {{ $space->is_active ? 'actif' : 'archivé' }}
        @if($space->type === 'public') · {{ $nbCitoyens }} citoyen(s) ont ouvert ce groupe @endif
    </p>

    <div class="mg-card">
        <b style="font-size:14px;">Ajouter des services</b>
        @if($candidats->isEmpty())
            <p class="mg-sub" style="margin:8px 0 0;">Tous les services sont déjà dans ce groupe.</p>
        @else
            <form method="POST" action="{{ route('admin.messagerie.membres.add', $space->id) }}" style="margin-top:10px;">
                @csrf
                <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:6px; max-height:200px; overflow:auto; border:1px solid #E2E8F0; border-radius:8px; padding:10px;">
                    @foreach($candidats as $sv)
                        <label class="mg-check"><input type="checkbox" name="service_ids[]" value="{{ $sv->id }}"> {{ $sv->nom }}
                            <span class="mg-tag">{{ \App\Support\Messagerie\Presenter::roleService($sv->role)[0] }}</span></label>
                    @endforeach
                </div>
                <div style="display:flex; gap:10px; align-items:center; margin-top:10px; flex-wrap:wrap;">
                    <select name="role" style="border:1px solid #E2E8F0; border-radius:8px; padding:8px 10px;">
                        <option value="member">En tant que membre</option>
                        <option value="moderator">En tant que modérateur</option>
                    </select>
                    <button class="mg-btn primary" type="submit">Ajouter</button>
                </div>
            </form>
        @endif
    </div>

    <div class="mg-card" style="padding:0;">
        <div class="mg-scrollx">
        <table class="mg-table">
            <thead><tr><th>Service</th><th>Rôle</th><th>Statut</th><th>Peut écrire</th><th></th></tr></thead>
            <tbody>
            @forelse($membres as $mb)
                @php $sv = $parId->get($mb->member_id); @endphp
                <tr class="{{ $mb->status === 'pending' ? 'mg-pending' : '' }}">
                    <td>
                        <b>{{ $sv->nom ?? 'Service supprimé' }}</b>
                        @if($sv)<span class="mg-tag">{{ \App\Support\Messagerie\Presenter::roleService($sv->role)[0] }}</span>@endif
                        @if($mb->status === 'pending')<span class="mg-tag warn">Demande d'accès</span>@endif
                    </td>
                    <td>
                        <select name="role" form="mf-{{ $mb->id }}">
                            <option value="member" @selected($mb->role === 'member')>Membre</option>
                            <option value="moderator" @selected($mb->role === 'moderator')>Modérateur</option>
                        </select>
                    </td>
                    <td>
                        <select name="status" id="mst-{{ $mb->id }}" form="mf-{{ $mb->id }}">
                            <option value="active"  @selected($mb->status === 'active')>Actif</option>
                            <option value="muted"   @selected($mb->status === 'muted')>Lecture seule</option>
                            <option value="pending" @selected($mb->status === 'pending')>En attente</option>
                        </select>
                    </td>
                    <td><label class="mg-check"><input type="checkbox" name="can_post" value="1" form="mf-{{ $mb->id }}" @checked($mb->can_post)> Oui</label></td>
                    <td style="white-space:nowrap;">
                        @if($mb->status === 'pending')
                            <button class="mg-btn primary" type="submit" form="mf-{{ $mb->id }}"
                                    onclick="document.getElementById('mst-{{ $mb->id }}').value='active'">Accepter</button>
                        @else
                            <button class="mg-btn" type="submit" form="mf-{{ $mb->id }}">Enregistrer</button>
                        @endif
                        <button class="mg-btn danger" type="submit" form="mr-{{ $mb->id }}">Retirer</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="mg-empty">Aucun service dans ce groupe. Ajoutez-en ci-dessus.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Formulaires liés aux lignes du tableau (attribut form="…") --}}
    @foreach($membres as $mb)
        <form id="mf-{{ $mb->id }}" method="POST" action="{{ route('admin.messagerie.membres.update', [$space->id, $mb->id]) }}">@csrf @method('PUT')</form>
        <form id="mr-{{ $mb->id }}" method="POST" action="{{ route('admin.messagerie.membres.remove', [$space->id, $mb->id]) }}"
              onsubmit="return confirm('Retirer ce service du groupe ?')">@csrf @method('DELETE')</form>
    @endforeach
</div>
@endsection
