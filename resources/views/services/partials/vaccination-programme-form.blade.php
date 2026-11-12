@if($errors->any())
    <div class="ds-alert ds-alert-error" style="margin-bottom:16px">
        <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="ds-form-grid">
    <div class="ds-field">
        <label class="ds-label" for="nom_programme">Nom du programme *</label>
        <input class="ds-input" id="nom_programme" name="nom_programme" maxlength="255" required
               value="{{ old('nom_programme', $programme->nom_programme) }}" placeholder="Ex : Campagne contre la rougeole">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="categorie">Catégorie *</label>
        <select class="ds-select" id="categorie" name="categorie" required>
            <option value="">Sélectionner…</option>
            @foreach($categories as $cle => $label)
                <option value="{{ $cle }}" @selected(old('categorie', $programme->categorie) === $cle)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="ds-field">
        <label class="ds-label" for="age_cible">Âge cible</label>
        <input class="ds-input" id="age_cible" name="age_cible" maxlength="50"
               value="{{ old('age_cible', $programme->age_cible) }}" placeholder="Ex : 0 à 5 ans">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="nb_vaccins">Nombre de vaccins prévus</label>
        <input class="ds-input" type="number" min="0" max="100" id="nb_vaccins" name="nb_vaccins"
               value="{{ old('nb_vaccins', $programme->nb_vaccins) }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="date_debut">Date de début</label>
        <input class="ds-input" type="date" id="date_debut" name="date_debut"
               value="{{ old('date_debut', $programme->date_debut?->toDateString()) }}">
    </div>
    <div class="ds-field">
        <label class="ds-label" for="date_fin">Date de fin</label>
        <input class="ds-input" type="date" id="date_fin" name="date_fin"
               value="{{ old('date_fin', $programme->date_fin?->toDateString()) }}">
    </div>
</div>

<div class="ds-field">
    <label class="ds-label" for="organisme_resp">Organisme responsable</label>
    <input class="ds-input" id="organisme_resp" name="organisme_resp" maxlength="255"
           value="{{ old('organisme_resp', $programme->organisme_resp) }}" placeholder="Ex : Direction départementale de la santé">
</div>

<div class="ds-row-actions">
    <button class="ds-btn ds-btn-primary" type="submit">Enregistrer</button>
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.vaccinationIndex') }}">Annuler</a>
</div>
