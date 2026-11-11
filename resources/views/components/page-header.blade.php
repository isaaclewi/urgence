@props(['title', 'subtitle' => null])
<div class="ds-page-header">
    <div>
        <h1 class="ds-page-title">{{ $title }}</h1>
        @if($subtitle)<p class="ds-page-sub">{{ $subtitle }}</p>@endif
    </div>
    @if(! $slot->isEmpty())
        <div class="ds-page-actions">{{ $slot }}</div>
    @endif
</div>
