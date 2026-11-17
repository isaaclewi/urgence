@extends('layouts.portal')

@section('title', 'Contrôle du dossier')
@section('page-title', 'Contrôle du dossier')
@section('page-subtitle', $citoyen->prenom . ' ' . $citoyen->nom . ' · ' . $citoyen->matricule)

@php
    $statut = $citoyen->statut_verification;
    $type   = ['verifie' => 'success', 'rejete' => 'danger'][$statut] ?? 'warning';
    $lib    = ['verifie' => 'Vérifié', 'rejete' => 'Rejeté'][$statut] ?? 'À vérifier';
    $expire = $citoyen->date_expiration_piece && $citoyen->date_expiration_piece->isPast();
    $actions = [
        'inscription' => 'Dossier déposé', 'email_verifie' => 'Email confirmé', 'valide' => 'Validé',
        'rejete' => 'Rejeté', 'renvoi_dossier' => 'Dossier renvoyé', 'consultation_dossier' => 'Dossier consulté',
        'consultation_piece' => 'Pièce consultée', 'consultation_selfie' => 'Selfie consulté',
        'reactive' => 'Compte réactivé', 'desactive' => 'Compte désactivé', 'purge_images' => 'Images supprimées',
    ];
@endphp

@section('content')
<p><a href="{{ route('admin.valider') }}" class="ds-btn ds-btn-ghost ds-btn-sm">← Retour à la liste</a></p>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;margin-top:12px">

    <x-card>
        <h3 style="margin-top:0">Informations saisies <x-badge :type="$type">{{ $lib }}</x-badge></h3>
        <table style="width:100%;font-size:14px">
            <tr><td class="ds-muted">Nom complet</td><td>{{ $citoyen->nom }} {{ $citoyen->prenom }}</td></tr>
            <tr><td class="ds-muted">Date de naissance</td><td>{{ $citoyen->date_naissance?->format('d/m/Y') ?? '—' }}@if($citoyen->age !== null) ({{ $citoyen->age }} ans)@endif</td></tr>
            <tr><td class="ds-muted">Sexe</td><td>{{ $citoyen->sexe === 'F' ? 'Femme' : 'Homme' }}</td></tr>
            <tr><td class="ds-muted">Adresse</td><td>{{ $citoyen->adresse }}</td></tr>
            <tr><td class="ds-muted">Téléphone</td><td>{{ $citoyen->telephone }}</td></tr>
            <tr><td class="ds-muted">Email</td><td>{{ $citoyen->email }}
                <x-badge :type="$citoyen->email_verifie_le ? 'success' : 'neutral'">{{ $citoyen->email_verifie_le ? 'confirmé' : 'non confirmé' }}</x-badge></td></tr>
            <tr><td class="ds-muted">Type de pièce</td><td>{{ \App\Support\Identite::TYPES[$citoyen->type_piece] ?? '—' }}</td></tr>
            <tr><td class="ds-muted">N° de pièce</td><td>{{ $citoyen->numeroPieceMasque() ?? '—' }}</td></tr>
            <tr><td class="ds-muted">Expiration</td><td>
                {{ $citoyen->date_expiration_piece?->format('d/m/Y') ?? '—' }}
                @if($expire)<x-badge type="danger">expirée</x-badge>@endif</td></tr>
        </table>
        @if($statut === 'rejete' && $citoyen->motif_rejet)
            <div class="ds-alert ds-alert-error" style="margin-top:12px"><strong>Motif du rejet :</strong> {{ $citoyen->motif_rejet }}</div>
        @endif
    </x-card>

    <x-card>
        <h3 style="margin-top:0">Pièce d’identité</h3>
        @if($citoyen->piece_identite)
            <a href="{{ route('citoyen.piece', $citoyen->id) }}" target="_blank" rel="noopener">
                <img src="{{ route('citoyen.piece', $citoyen->id) }}" alt="Pièce d’identité" style="max-width:100%;border-radius:8px;border:1px solid #ddd">
            </a>
        @else
            <p class="ds-muted">Aucune image (supprimée ou non fournie).</p>
        @endif
    </x-card>

    <x-card>
        <h3 style="margin-top:0">Selfie</h3>
        @if($citoyen->selfie_identite)
            <a href="{{ route('citoyen.selfie', $citoyen->id) }}" target="_blank" rel="noopener">
                <img src="{{ route('citoyen.selfie', $citoyen->id) }}" alt="Selfie" style="max-width:100%;border-radius:8px;border:1px solid #ddd">
            </a>
        @else
            <p class="ds-muted">Aucun selfie (ancien compte ou image supprimée).</p>
        @endif
    </x-card>
</div>

@if($statut !== 'verifie')
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;margin-top:16px">
    <x-card>
        <h3 style="margin-top:0">Valider le dossier</h3>
        <p class="ds-muted" style="font-size:13px">Cochez chaque point après l’avoir vérifié sur les images. Si un point n’est pas conforme, rejetez le dossier.</p>
        <form method="POST" action="{{ route('admin.valider.verifier', $citoyen->id) }}">
            @csrf
            @foreach($controles as $cle => $libelle)
                <label style="display:flex;gap:8px;align-items:flex-start;margin:8px 0">
                    <input type="checkbox" name="{{ $cle }}" value="1" @checked(old($cle))>
                    <span>{{ $libelle }}</span>
                </label>
            @endforeach
            <button class="ds-btn ds-btn-primary" type="submit" style="margin-top:12px">Valider et activer le compte</button>
        </form>
    </x-card>

    <x-card>
        <h3 style="margin-top:0">Rejeter le dossier</h3>
        <p class="ds-muted" style="font-size:13px">Le citoyen reçoit ce motif par email, avec un lien pour renvoyer ses documents.</p>
        <form method="POST" action="{{ route('admin.valider.rejeter', $citoyen->id) }}">
            @csrf
            <textarea class="ds-input" name="motif" rows="4" required minlength="10" maxlength="500"
                placeholder="Ex : la photo de la pièce est floue, renvoyez une image nette.">{{ old('motif') }}</textarea>
            <button class="ds-btn ds-btn-secondary" type="submit" style="margin-top:12px">Rejeter avec ce motif</button>
        </form>
    </x-card>
</div>
@endif

<x-card flush style="margin-top:16px">
    <h3 style="margin:16px 16px 0">Journal du dossier</h3>
    @if($journal->isEmpty())
        <x-empty title="Aucune entrée" />
    @else
        <x-table>
            <thead><tr><th>Date</th><th>Action</th><th>Admin</th><th>Détail</th><th>Adresse IP</th></tr></thead>
            <tbody>
                @foreach($journal as $l)
                    <tr>
                        <td style="white-space:nowrap">{{ \Illuminate\Support\Carbon::parse($l->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $actions[$l->action] ?? $l->action }}</td>
                        <td>{{ $l->admin_id ? '#' . $l->admin_id : '—' }}</td>
                        <td>{{ $l->detail }}</td>
                        <td class="ds-muted">{{ $l->ip }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif
</x-card>
@endsection
