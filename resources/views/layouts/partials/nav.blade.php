{{-- Menu du layout portal. La définition des liens est dans App\Support\Menu. --}}
@foreach(\App\Support\Menu::sections($portal) as $section => $items)
    <div class="ds-nav-label">{{ $section }}</div>
    @foreach($items as $item)
        <a href="{{ route($item['route']) }}" class="ds-nav-link {{ $item['active'] ? 'active' : '' }}">
            <i data-feather="{{ $item['icon'] }}" class="ds-nav-icon" style="width:16px;height:16px"></i> {{ $item['label'] }}
        </a>
    @endforeach
@endforeach
