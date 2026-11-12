@extends('layouts.portal')

@php
    $c = $bilan->citoyen;
    $lignes = [
        'Données biométriques' => [
            'groupe_sanguin' => 'Groupe sanguin', 'taille' => 'Taille', 'poids' => 'Poids', 'allergies' => 'Allergies',
        ],
        'Antécédents médicaux' => [
            'maladies_chroniques' => 'Maladies chroniques', 'maladies_passees_importantes' => 'Maladies passées importantes',
            'interventions_chirurgicales' => 'Interventions chirurgicales', 'antecedents_hospitalisation' => 'Hospitalisations',
            'antecedents_familiaux' => 'Antécédents familiaux',
        ],
        'Traitement, vaccins et mode de vie' => [
            'medicaments_pris_actuellement' => 'Médicaments actuels', 'vaccins_recus' => 'Vaccins reçus', 'mode_de_vie' => 'Mode de vie',
        ],
    ];
    $unites = ['taille' => ' cm', 'poids' => ' kg'];
    $actions = ['consultation' => 'a consulté', 'creation' => 'a créé', 'modification' => 'a modifié', 'suppression' => 'a supprimé'];
@endphp

@section('title', 'Bilan de santé')
@section('page-title', 'Bilan de santé')
@section('page-subtitle', ($c?->nom ?? 'Citoyen supprimé') . ' ' . ($c?->prenom ?? ''))

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.citoyensBilan') }}">← Liste</a>
    <a class="ds-btn ds-btn-primary" href="{{ route('services.citoyensBilanEdit', $bilan->id) }}">Modifier</a>
@endsection

@section('content')
<div class="ds-grid ds-grid-aside">
    <div class="ds-stack">
        @foreach($lignes as $titre => $champs)
            <x-card :title="$titre">
                <dl style="margin:0;display:grid;grid-template-columns:minmax(140px,200px) 1fr;gap:10px 16px">
                    @foreach($champs as $cle => $libelle)
                        <dt class="ds-muted">{{ $libelle }}</dt>
                        <dd style="margin:0;white-space:pre-line">{{ filled($valeurs[$cle] ?? null) ? $valeurs[$cle] . ($unites[$cle] ?? '') : '—' }}</dd>
                    @endforeach
                </dl>
            </x-card>
        @endforeach
    </div>

    <div class="ds-stack">
        <x-card title="Informations">
            <p style="margin:0 0 6px"><span class="ds-muted">Matricule :</span> {{ $c?->matricule ?? '—' }}</p>
            <p style="margin:0 0 6px"><span class="ds-muted">Créé par :</span> {{ $bilan->service?->nom ?? 'Non renseigné' }}</p>
            <p style="margin:0 0 6px"><span class="ds-muted">Dernière modification :</span> {{ $bilan->modifiePar?->nom ?? '—' }}</p>
            <p style="margin:0"><span class="ds-muted">Mis à jour le :</span> {{ $bilan->updated_at?->format('d/m/Y à H:i') }}</p>
        </x-card>

        <x-card title="Historique des accès">
            @forelse($journal as $j)
                <div style="padding:8px 0;border-bottom:1px solid var(--ds-border);font-size:13px">
                    <strong>{{ $j->service?->nom ?? 'Service supprimé' }}</strong>
                    {{ $actions[$j->action] ?? $j->action }} ce bilan
                    <div class="ds-muted" style="font-size:12px">{{ $j->created_at?->format('d/m/Y à H:i') }}</div>
                </div>
            @empty
                <span class="ds-muted">Aucun accès enregistré.</span>
            @endforelse
        </x-card>
    </div>
</div>
@endsection
