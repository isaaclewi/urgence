@props(['title' => null, 'flush' => false])
<div {{ $attributes->class(['ds-card', 'ds-card-flush' => $flush]) }}>
    @if($title || isset($actions))
        <div class="ds-card-header">
            <h3 class="ds-card-title">{{ $title }}</h3>
            @isset($actions)<div>{{ $actions }}</div>@endisset
        </div>
    @endif
    <div class="ds-card-body">{{ $slot }}</div>
</div>
