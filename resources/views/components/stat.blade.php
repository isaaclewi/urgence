@props(['label', 'value', 'hint' => null, 'type' => null])
<div {{ $attributes->class(['ds-stat', $type ? 'ds-stat-' . $type : null]) }}>
    <div class="ds-stat-label">{{ $label }}</div>
    <div class="ds-stat-value">{{ $value }}</div>
    @if($hint)<div class="ds-stat-hint">{{ $hint }}</div>@endif
</div>
