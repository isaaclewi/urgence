@props(['status' => null, 'type' => null])
@php
    // Correspondance automatique statut métier → couleur
    $map = [
        'terminee' => 'success', 'terminée' => 'success', 'actif' => 'success', 'résolue' => 'success',
        'pris en charge' => 'info', 'affectee' => 'info', 'affectée' => 'info', 'transmise' => 'info',
        'en route' => 'info', 'sur place' => 'info',
        'en attente' => 'warning', 'nouvelle' => 'warning',
        'ouvert' => 'danger', 'annulee' => 'danger', 'annulée' => 'danger',
        'inactif' => 'neutral', 'desactive' => 'neutral', 'désactivé' => 'neutral',
    ];
    $variant = $type ?? ($map[mb_strtolower((string) $status)] ?? 'neutral');
@endphp
<span {{ $attributes->class(['ds-badge', 'ds-badge-' . $variant]) }}>{{ $slot->isEmpty() ? $status : $slot }}</span>
