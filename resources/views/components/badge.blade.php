@props(['status' => null, 'type' => null])
@php
    // Correspondance automatique statut métier → couleur
    $map = [
        'terminee' => 'success', 'terminée' => 'success', 'actif' => 'success', 'résolue' => 'success',
        'pris en charge' => 'info', 'affectee' => 'info', 'affectée' => 'info', 'transmise' => 'info',
        'en route' => 'info', 'sur place' => 'info',
        'en attente' => 'warning', 'nouvelle' => 'warning',
        'disponible' => 'success', 'en mission' => 'info', 'maintenance' => 'warning',
        'en congé' => 'warning', 'indisponible' => 'danger', 'hors service' => 'danger',
        'ouvert' => 'danger', 'annulee' => 'danger', 'annulée' => 'danger',
        'inactif' => 'neutral', 'desactive' => 'neutral', 'désactivé' => 'neutral',
    ];
    $variant = $type ?? ($map[mb_strtolower((string) $status)] ?? 'neutral');
@endphp
<span {{ $attributes->class(['ds-badge', 'ds-badge-' . $variant]) }}>{{ $slot->isEmpty() ? $status : $slot }}</span>
