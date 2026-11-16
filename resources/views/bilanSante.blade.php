@extends('citoyen')

@section('title', 'Mon bilan de santé — CongoAssist')

@push('styles')
<style>
    .bilan-wrap { max-width: 860px; width: 100%; }
    .bilan-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 24px; }
    .bilan-stats .stat-card { padding: 16px; gap: 12px; min-width: 0; }
    .bilan-stats .stat-card > div:last-child { min-width: 0; }
    .bilan-val { font-size: 16px; font-weight: 700; color: var(--text); overflow-wrap: anywhere; line-height: 1.25; }
    .bilan-field { background: var(--surface2); color: var(--text-sec); white-space: pre-wrap; overflow-wrap: anywhere; min-height: 40px; }
    .bilan-lock { max-width: 440px; margin: 8px auto 0; text-align: center; }
    .bilan-lock-icon { width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 14px; display: flex; align-items: center; justify-content: center; background: #d1fae5; color: #047857; }
    .bilan-pass { position: relative; }
    .bilan-pass .form-control { padding-right: 44px; }
    .bilan-pass button { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); background: none; border: 0; padding: 8px; cursor: pointer; color: var(--text-sec); }
    .bilan-bar { display: flex; justify-content: flex-end; margin-bottom: 14px; }
    @media (max-width: 899px) { .bilan-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 479px) {
        .bilan-stats { gap: 10px; }
        .bilan-stats .stat-card { padding: 12px; flex-direction: column; align-items: flex-start; gap: 8px; }
        .bilan-val { font-size: 14px; }
        .bilan-wrap .card-body { padding: 14px; }
        .bilan-bar .btn { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Mon bilan de santé</h1>
        <p>Vos informations médicales personnelles, tenues à jour par votre service de santé.</p>
    </div>
</div>

@if(!empty($verrouille))
<div class="bilan-wrap">
    <div class="card anim-fade bilan-lock" style="animation-delay:.06s;">
        <div class="card-body" style="padding:28px 22px;">
            <div class="bilan-lock-icon"><i data-feather="lock" style="width:24px;height:24px;"></i></div>
            <h2 style="font-size:17px;font-weight:700;color:var(--text);margin-bottom:6px;">Accès sécurisé</h2>
            <p style="font-size:13px;color:var(--text-sec);margin-bottom:20px;">
                Votre dossier médical est confidentiel. Saisissez le mot de passe de votre compte pour l'afficher.
            </p>

            @if($errors->any())
            <div class="alert alert-error" style="text-align:left;">
                <i data-feather="alert-circle" style="width:16px;height:16px;"></i>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif

            <form method="POST" action="{{ route('bilanController.deverrouiller') }}" style="text-align:left;">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="bilanPassword">Mot de passe</label>
                    <div class="bilan-pass">
                        <input type="password" id="bilanPassword" name="password" class="form-control"
                               autocomplete="current-password" required autofocus placeholder="Votre mot de passe">
                        <button type="button" aria-label="Afficher / masquer le mot de passe"
                                onclick="var i=document.getElementById('bilanPassword');i.type=i.type==='password'?'text':'password';">
                            <i data-feather="eye" style="width:16px;height:16px;"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;">
                    <i data-feather="unlock" style="width:15px;height:15px;"></i>
                    Afficher mon bilan
                </button>
            </form>
        </div>
    </div>
</div>
@else
<div class="bilan-bar">
    <form method="POST" action="{{ route('bilanController.verrouiller') }}">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm">
            <i data-feather="lock" style="width:13px;height:13px;"></i> Verrouiller
        </button>
    </form>
</div>
<div class="bilan-wrap">

    {{-- Indicateurs visuels --}}
    <div class="bilan-stats">
        @php
$indicators = [
    ['label'=>'Groupe sanguin', 'value'=>$bilan?->groupe_sanguin ?? 'N/R',  'icon'=>'droplet',     'color'=>'bg-red-soft'],
    ['label'=>'Taille',         'value'=>$bilan?->taille  ? $bilan->taille  . ' cm' : '—',          'icon'=>'maximize-2',  'color'=>'bg-blue-soft'],
    ['label'=>'Poids',          'value'=>$bilan?->poids   ? $bilan->poids   . ' kg' : '—',          'icon'=>'activity',    'color'=>'bg-green-soft'],
    ['label'=>'Allergies',      'value'=>$bilan?->allergies ?? 'Aucune',    'icon'=>'alert-circle', 'color'=>'bg-amber-soft'],
];
@endphp
        @foreach($indicators as $item)
        <div class="stat-card anim-fade">
            <div class="stat-icon {{ $item['color'] }}">
                <i data-feather="{{ $item['icon'] }}" style="width:18px;height:18px;"></i>
            </div>
            <div>
                <div class="bilan-val">{{ $item['value'] }}</div>
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
    ['label'=>'Maladies chroniques',           'value'=>$bilan?->maladies_chroniques            ?? 'Aucune',          'icon'=>'clipboard'],
    ['label'=>'Maladies passées importantes',  'value'=>$bilan?->maladies_passees_importantes   ?? 'Aucune',          'icon'=>'file-text'],
    ['label'=>'Interventions chirurgicales',   'value'=>$bilan?->interventions_chirurgicales    ?? 'Aucune',          'icon'=>'crosshair'],
    ['label'=>'Antécédents d\'hospitalisation','value'=>$bilan?->antecedents_hospitalisation    ?? 'Aucun',           'icon'=>'home'],
    ['label'=>'Antécédents familiaux',         'value'=>$bilan?->antecedents_familiaux          ?? 'Aucun',           'icon'=>'users'],
    ['label'=>'Mode de vie',                   'value'=>$bilan?->mode_de_vie                    ?? 'Non renseigné',   'icon'=>'smile'],
];
@endphp
                @foreach($fields as $f)
                <div>
                    <label class="form-label">
                        <i data-feather="{{ $f['icon'] }}" style="width:12px;height:12px;display:inline;margin-right:5px;"></i>
                        {{ $f['label'] }}
                    </label>
                    <div class="form-control bilan-field">{{ $f['value'] }}</div>
                </div>
                @endforeach
            </div>

            <div class="form-group" style="margin-top:4px;">
                <label class="form-label">
                    <i data-feather="package" style="width:12px;height:12px;display:inline;margin-right:5px;"></i>
                    Médicaments actuels
                </label>
                <div class="form-control bilan-field">{{ $bilan?->medicaments_pris_actuellement ?? 'Aucun' }}</div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i data-feather="shield" style="width:12px;height:12px;display:inline;margin-right:5px;"></i>
                    Vaccins reçus
                </label>
                <textarea class="form-control" rows="4" readonly
                          style="background:var(--surface2);color:var(--text-sec);resize:none;">{{ $bilan?->listez_vaccins_reçus ?? 'Aucun' }}</textarea>
            </div>
        </div>
    </div>

</div>

@endif

@endsection

