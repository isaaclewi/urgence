@php
    $c = $alerte->citoyen;
    $detail = [
        'titre'       => $alerte->titre,
        'type'        => $alerte->type_alerte,
        'description' => $alerte->description,
        'lieu'        => $alerte->localisation,
        'lat'         => $alerte->latitude,
        'lng'         => $alerte->longitude,
        'date'        => $alerte->created_at?->format('d/m/Y à H:i'),
        'photo'       => $alerte->media_photo ? asset($alerte->media_photo) : null,
        'vocal'       => $alerte->media_vocal ? asset($alerte->media_vocal) : null,
        'equipe'      => $alerte->equipe?->nom,
        'citoyen'     => $c ? [
            'nom'       => trim($c->nom . ' ' . $c->prenom),
            'telephone' => $c->telephone,
            'email'     => $c->email,
            'adresse'   => $c->adresse ?? null,
        ] : null,
    ];
    $typeStatut = match ($alerte->statut) {
        'en attente'     => 'danger',
        'pris en charge' => 'info',
        'terminee'       => 'success',
        default          => 'neutral',
    };
    $libelle = ['en attente' => 'En attente', 'pris en charge' => 'Pris en charge', 'terminee' => 'Terminée'][$alerte->statut] ?? $alerte->statut;
@endphp
<tr id="alerte-row-{{ $alerte->id }}" data-id="{{ $alerte->id }}">
    <td>
        <strong>#{{ $alerte->id }}</strong>
        <div class="ds-muted" style="font-size:12px;white-space:nowrap">{{ $alerte->created_at?->format('d/m/Y H:i') }}</div>
    </td>
    <td>
        <strong>{{ $alerte->titre }}</strong>
        <div class="ds-muted" style="font-size:12px">{{ $alerte->localisation ?: 'Lieu non précisé' }}</div>
    </td>
    <td>{{ $alerte->type_alerte ?: '—' }}</td>
    <td>
        @if($c)
            {{ $c->nom }} {{ $c->prenom }}
        @else
            <span class="ds-muted">Anonyme</span>
        @endif
    </td>
    <td>
        <x-badge :type="$typeStatut">{{ $libelle }}</x-badge>
        @if($alerte->equipe)
            <div class="ds-muted" style="font-size:12px">→ {{ $alerte->equipe->nom }}</div>
        @endif
    </td>
    <td>
        <div class="ds-row-actions">
            <button type="button" class="ds-btn ds-btn-secondary ds-btn-sm" data-action="detail"
                    data-detail="{{ json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}">Détail</button>

            @if($alerte->statut !== 'terminee')
                <button type="button" class="ds-btn ds-btn-primary ds-btn-sm" data-action="affecter" data-id="{{ $alerte->id }}">
                    {{ $alerte->equipe ? 'Réaffecter' : 'Affecter' }}
                </button>
            @endif

            <form method="POST" action="{{ route('services.urgenceSignaleeUpdate', $alerte->id) }}" data-statut-form>
                @csrf
                <select class="ds-select" name="statut" aria-label="Changer le statut" style="min-width:140px"
                        @disabled($alerte->statut === 'terminee')>
                    @foreach(['en attente' => 'En attente', 'pris en charge' => 'Pris en charge', 'terminee' => 'Terminée'] as $val => $lab)
                        <option value="{{ $val }}" @selected($alerte->statut === $val)>{{ $lab }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </td>
</tr>
