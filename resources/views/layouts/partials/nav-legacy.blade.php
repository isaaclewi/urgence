{{-- Menu du portail service pour l'ancien master (classes sidebar-link / sb-*).
     Même source que le layout portal : App\Support\Menu (liens filtrés par permissions). --}}
@php
    // Pastille « urgences en attente » : $stats (tableau de bord) ou $alertes (page Urgences)
    $urgencesEnAttente = $stats['urgences_en_cours']
        ?? (isset($alertes) && $alertes instanceof \Illuminate\Support\Collection
            ? $alertes->where('statut', 'en attente')->count()
            : 0);
@endphp

@foreach(\App\Support\Menu::sections('service') as $section => $items)
    <div class="sb-section-label">{{ $section }}</div>
    @foreach($items as $item)
        <a href="{{ route($item['route']) }}" class="sidebar-link {{ $item['active'] ? 'active' : '' }}">
            <i data-feather="{{ $item['icon'] }}"></i>
            <span class="sb-lbl">{{ $item['label'] }}</span>
            @if($item['route'] === 'services.urgenceSignalee' && $urgencesEnAttente > 0)
                <span class="sb-badge" id="badge-attente">{{ $urgencesEnAttente }}</span>
            @endif
        </a>
    @endforeach
@endforeach

<div class="sb-divider"></div>

<a href="{{ route('services.logout') }}" class="sidebar-link danger">
    <i data-feather="log-out"></i>
    <span class="sb-lbl">Déconnexion</span>
</a>
