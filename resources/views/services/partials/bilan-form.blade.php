@php $v = fn ($cle) => old($cle, $valeurs[$cle] ?? null); @endphp

@if($errors->any())
    <div class="ds-alert ds-alert-error" style="margin-bottom:16px">
        <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
        </ul>
    </div>
@endif

<h3 class="ds-card-title" style="margin:0 0 12px">Données biométriques</h3>
<div class="ds-form-grid">
    <div class="ds-field">
        <label class="ds-label" for="groupe_sanguin">Groupe sanguin</label>
        <select class="ds-select" id="groupe_sanguin" name="groupe_sanguin">
            <option value="">Non renseigné</option>
            @foreach($groupes as $g)
                <option value="{{ $g }}" @selected($v('groupe_sanguin') === $g)>{{ $g }}</option>
            @endforeach
        </select>
    </div>
    <div class="ds-field">
        <label class="ds-label" for="taille">Taille (cm)</label>
        <input class="ds-input" type="number" step="0.1" min="30" max="250" id="taille" name="taille" value="{{ $v('taille') }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="poids">Poids (kg)</label>
        <input class="ds-input" type="number" step="0.1" min="0.5" max="400" id="poids" name="poids" value="{{ $v('poids') }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="allergies">Allergies</label>
        <input class="ds-input" id="allergies" name="allergies" maxlength="1000" value="{{ $v('allergies') }}" placeholder="Ex : Arachides, pénicilline">
    </div>
</div>

<h3 class="ds-card-title" style="margin:8px 0 12px">Antécédents médicaux</h3>
@foreach([
    ['maladies_chroniques', 'Maladies chroniques', 'Ex : Diabète, hypertension'],
    ['maladies_passees_importantes', 'Maladies passées importantes', 'Ex : Tuberculose (2020)'],
    ['interventions_chirurgicales', 'Interventions chirurgicales', 'Opérations subies'],
    ['antecedents_hospitalisation', 'Hospitalisations', 'Motifs et dates'],
    ['antecedents_familiaux', 'Antécédents familiaux', 'Ex : Père diabétique'],
] as [$nom, $libelle, $aide])
    <div class="ds-field">
        <label class="ds-label" for="{{ $nom }}">{{ $libelle }}</label>
        <textarea class="ds-textarea" id="{{ $nom }}" name="{{ $nom }}" rows="2" maxlength="1000" placeholder="{{ $aide }}">{{ $v($nom) }}</textarea>
    </div>
@endforeach

<h3 class="ds-card-title" style="margin:8px 0 12px">Traitement, vaccins et mode de vie</h3>
@foreach([
    ['medicaments_pris_actuellement', 'Médicaments pris actuellement', 'Ex : Aspirine 100 mg/jour'],
    ['vaccins_recus', 'Vaccins reçus', 'Ex : BCG, DTC, hépatite B'],
    ['mode_de_vie', 'Mode de vie', 'Ex : Non-fumeur, sport régulier'],
] as [$nom, $libelle, $aide])
    <div class="ds-field">
        <label class="ds-label" for="{{ $nom }}">{{ $libelle }}</label>
        <textarea class="ds-textarea" id="{{ $nom }}" name="{{ $nom }}" rows="2" maxlength="1000" placeholder="{{ $aide }}">{{ $v($nom) }}</textarea>
    </div>
@endforeach

<div class="ds-row-actions">
    <button class="ds-btn ds-btn-primary" type="submit">Enregistrer le bilan</button>
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.citoyensBilan') }}">Annuler</a>
</div>
