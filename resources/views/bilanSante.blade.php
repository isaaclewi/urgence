@extends('citoyen')

@section('title', 'Mon bilan de santé — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Mon bilan de santé</h1>
        <p>Vos informations médicales personnelles, tenues à jour par votre service de santé.</p>
    </div>
</div>

<div style="max-width:860px;">

    {{-- Indicateurs visuels --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:24px;">
        @php
        $indicators = [
            ['label'=>'Groupe sanguin', 'value'=>$bilan->groupe_sanguin ?? 'N/R', 'icon'=>'droplet', 'color'=>'bg-red-soft'],
            ['label'=>'Taille', 'value'=>($bilan->taille ?? '—') . ($bilan->taille ? ' cm' : ''), 'icon'=>'maximize-2', 'color'=>'bg-blue-soft'],
            ['label'=>'Poids', 'value'=>($bilan->poids ?? '—') . ($bilan->poids ? ' kg' : ''), 'icon'=>'activity', 'color'=>'bg-green-soft'],
            ['label'=>'Allergies', 'value'=>$bilan->allergies ?? 'Aucune', 'icon'=>'alert-circle', 'color'=>'bg-amber-soft'],
        ];
        @endphp
        @foreach($indicators as $item)
        <div class="stat-card anim-fade">
            <div class="stat-icon {{ $item['color'] }}">
                <i data-feather="{{ $item['icon'] }}" style="width:18px;height:18px;"></i>
            </div>
            <div>
                <div style="font-size:16px;font-weight:700;color:var(--text);">{{ $item['value'] }}</div>
                <div class="stat-label">{{ $item['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Fiche complète --}}
    <div class="card anim-fade" style="animation-delay:.1s;">
        <div class="card-header" style="padding-bottom:14px;border-bottom:1px solid var(--border);">
            <span class="card-title" style="font-size:15px;">Dossier médical complet</span>
        </div>
        <div class="card-body">
            <div class="form-grid">
                @php
                $fields = [
                    ['label'=>'Maladies chroniques', 'value'=>$bilan->maladies_chroniques ?? 'Aucune', 'icon'=>'clipboard'],
                    ['label'=>'Maladies passées importantes', 'value'=>$bilan->maladies_passees_importantes ?? 'Aucune', 'icon'=>'file-text'],
                    ['label'=>'Interventions chirurgicales', 'value'=>$bilan->interventions_chirurgicales ?? 'Aucune', 'icon'=>'crosshair'],
                    ['label'=>'Antécédents d\'hospitalisation', 'value'=>$bilan->antecedents_hospitalisation ?? 'Aucun', 'icon'=>'home'],
                    ['label'=>'Antécédents familiaux', 'value'=>$bilan->antecedents_familiaux ?? 'Aucun', 'icon'=>'users'],
                    ['label'=>'Mode de vie', 'value'=>$bilan->mode_de_vie ?? 'Non renseigné', 'icon'=>'smile'],
                ];
                @endphp
                @foreach($fields as $f)
                <div>
                    <label class="form-label">
                        <i data-feather="{{ $f['icon'] }}" style="width:12px;height:12px;display:inline;margin-right:5px;"></i>
                        {{ $f['label'] }}
                    </label>
                    <div class="form-control" style="background:var(--surface2);color:var(--text-sec);">{{ $f['value'] }}</div>
                </div>
                @endforeach
            </div>

            <div class="form-group" style="margin-top:4px;">
                <label class="form-label">
                    <i data-feather="package" style="width:12px;height:12px;display:inline;margin-right:5px;"></i>
                    Médicaments actuels
                </label>
                <div class="form-control" style="background:var(--surface2);color:var(--text-sec);">
                    {{ $bilan->medicaments_pris_actuellement ?? 'Aucun' }}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i data-feather="shield" style="width:12px;height:12px;display:inline;margin-right:5px;"></i>
                    Vaccins reçus
                </label>
                <textarea class="form-control" rows="4" readonly
                          style="background:var(--surface2);color:var(--text-sec);resize:none;">{{ $bilan->listez_vaccins_reçus ?? 'Aucun' }}</textarea>
            </div>
        </div>
    </div>

</div>

@endsection
