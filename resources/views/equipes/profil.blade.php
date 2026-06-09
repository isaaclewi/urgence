@extends('equipes.master')

@section('title', 'Profil — ' . ($equipe->nom ?? 'Équipe'))
@section('page-icon', 'settings')
@section('page-title', 'Profil de l\'équipe')
@section('page-subtitle', 'Informations sur votre équipe')

@section('content')

<div style="max-width: 540px;">
    <div class="content-card anim-fade">
        <div class="cc-header">
            <div class="cc-title"><i data-feather="users"></i> Informations équipe</div>
        </div>
        <div class="cc-body">

            {{-- Avatar --}}
            <div style="text-align:center; margin-bottom:24px;">
                <div style="
                    width:72px; height:72px;
                    border-radius:50%;
                    background:var(--accent);
                    display:flex; align-items:center; justify-content:center;
                    font-family:'Sora',sans-serif;
                    font-size:28px; font-weight:700; color:#fff;
                    margin:0 auto 10px;
                ">
                    @if($equipe->photo_profil)
                        <img src="{{ asset($equipe->photo_profil) }}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                    @else
                        {{ strtoupper(substr($equipe->nom, 0, 1)) }}
                    @endif
                </div>
                <div style="font-family:'Sora',sans-serif; font-size:18px; font-weight:700; color:var(--text);">
                    {{ $equipe->nom }}
                </div>
                <span class="pill pill-orange" style="margin-top:6px;">Équipe de terrain</span>
            </div>

            {{-- Infos --}}
            @php
                $infos = [
                    ['icon' => 'mail',      'label' => 'Email',      'value' => $equipe->email,     'color' => '#DBEAFE', 'stroke' => '#3B82F6'],
                    ['icon' => 'phone',     'label' => 'Téléphone',  'value' => $equipe->telephone, 'color' => '#D1FAE5', 'stroke' => '#10B981'],
                    ['icon' => 'map-pin',   'label' => 'Adresse',    'value' => $equipe->adresse,   'color' => '#FEF3C7', 'stroke' => '#D97706'],
                    ['icon' => 'tag',       'label' => 'Rôle',       'value' => $equipe->role,      'color' => '#F3E8FF', 'stroke' => '#7C3AED'],
                    ['icon' => 'activity',  'label' => 'Disponible', 'value' => $equipe->disponible ? 'Oui' : 'Non', 'color' => '#D1FAE5', 'stroke' => '#10B981'],
                ];
                $parent = $equipe->parent_service_id ? \App\Models\Services::find($equipe->parent_service_id) : null;
            @endphp

            @foreach($infos as $info)
            <div style="display:flex; align-items:flex-start; gap:12px; padding:12px 0; border-bottom:1px solid var(--border);">
                <div style="
                    width:36px; height:36px; border-radius:8px; flex-shrink:0;
                    background:{{ $info['color'] }};
                    display:flex; align-items:center; justify-content:center;
                ">
                    <i data-feather="{{ $info['icon'] }}" style="stroke:{{ $info['stroke'] }};"></i>
                </div>
                <div>
                    <div style="font-size:10.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em;">
                        {{ $info['label'] }}
                    </div>
                    <div style="font-size:13.5px; font-weight:600; color:var(--text); margin-top:2px;">
                        {{ $info['value'] ?? '—' }}
                    </div>
                </div>
            </div>
            @endforeach

            @if($parent)
            <div style="display:flex; align-items:flex-start; gap:12px; padding:12px 0;">
                <div style="
                    width:36px; height:36px; border-radius:8px; flex-shrink:0;
                    background:#FFF7ED;
                    display:flex; align-items:center; justify-content:center;
                ">
                    <i data-feather="share-2" style="stroke:#F97316;"></i>
                </div>
                <div>
                    <div style="font-size:10.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.04em;">
                        Service parent
                    </div>
                    <div style="font-size:13.5px; font-weight:600; color:var(--text); margin-top:2px;">
                        {{ $parent->nom }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
