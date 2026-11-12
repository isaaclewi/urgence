<div class="ds-row-actions" style="margin-bottom:16px">
    <a class="ds-btn ds-btn-sm {{ $actif === 'programmes' ? 'ds-btn-primary' : 'ds-btn-secondary' }}"
       href="{{ route('services.vaccinationIndex') }}">Programmes</a>
    <a class="ds-btn ds-btn-sm {{ $actif === 'vaccins' ? 'ds-btn-primary' : 'ds-btn-secondary' }}"
       href="{{ route('services.vaccinationEnfantsIndex') }}">Vaccins enfants</a>
</div>
