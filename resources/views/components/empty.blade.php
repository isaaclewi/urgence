@props(['title' => 'Aucune donnée'])
<div {{ $attributes->class(['ds-empty']) }}>
    <div class="ds-empty-title">{{ $title }}</div>
    <div>{{ $slot }}</div>
</div>
