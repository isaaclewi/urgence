@if($errors->any())
    <div class="ds-alert ds-alert-error" style="margin-bottom:16px">
        <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="ds-form-grid">
    <div class="ds-field">
        <label class="ds-label" for="nom_vaccin">Nom du vaccin *</label>
        <input class="ds-input" id="nom_vaccin" name="nom_vaccin" maxlength="255" required
               value="{{ old('nom_vaccin', $vaccin->nom_vaccin) }}" placeholder="Ex : Pentavalent">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="fabricant">Fabricant</label>
        <input class="ds-input" id="fabricant" name="fabricant" maxlength="255"
               value="{{ old('fabricant', $vaccin->fabricant) }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="programme_id">Programme</label>
        <select class="ds-select" id="programme_id" name="programme_id">
            <option value="">Aucun programme</option>
            @foreach($programmes as $prog)
                <option value="{{ $prog->id }}" @selected((string) old('programme_id', $vaccin->programme_id) === (string) $prog->id)>{{ $prog->nom_programme }}</option>
            @endforeach
        </select>
    </div>
    <div class="ds-field">
        <label class="ds-label" for="voie_administration">Voie d'administration</label>
        <select class="ds-select" id="voie_administration" name="voie_administration">
            <option value="">Non précisée</option>
            @foreach($voies as $voie)
                <option value="{{ $voie }}" @selected(old('voie_administration', $vaccin->voie_administration) === $voie)>{{ $voie }}</option>
            @endforeach
        </select>
    </div>
    <div class="ds-field">
        <label class="ds-label" for="nombre_doses">Nombre de doses</label>
        <input class="ds-input" type="number" min="1" max="20" id="nombre_doses" name="nombre_doses"
               value="{{ old('nombre_doses', $vaccin->nombre_doses) }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="age_cible_min">Âge minimum (mois)</label>
        <input class="ds-input" type="number" min="0" id="age_cible_min" name="age_cible_min"
               value="{{ old('age_cible_min', $vaccin->age_cible_min) }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="age_cible_max">Âge maximum (mois)</label>
        <input class="ds-input" type="number" min="0" id="age_cible_max" name="age_cible_max"
               value="{{ old('age_cible_max', $vaccin->age_cible_max) }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="date_fabrication">Date de fabrication</label>
        <input class="ds-input" type="date" id="date_fabrication" name="date_fabrication"
               value="{{ old('date_fabrication', $vaccin->date_fabrication?->toDateString()) }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="date_expiration">Date d'expiration</label>
        <input class="ds-input" type="date" id="date_expiration" name="date_expiration"
               value="{{ old('date_expiration', $vaccin->date_expiration?->toDateString()) }}">
    </div>
</div>

<div class="ds-field">
    <input type="hidden" name="vaccination_obligatoire" value="0">
    <label style="display:flex;gap:8px;align-items:center">
        <input type="checkbox" name="vaccination_obligatoire" value="1"
               @checked(old('vaccination_obligatoire', $vaccin->vaccination_obligatoire))>
        Vaccination obligatoire
    </label>
</div>

<div class="ds-row-actions">
    <button class="ds-btn ds-btn-primary" type="submit">Enregistrer</button>
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.vaccinationEnfantsIndex') }}">Annuler</a>
</div>
