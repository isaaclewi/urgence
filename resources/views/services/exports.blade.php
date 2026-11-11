@extends('layouts.portal')

@section('title', 'Rapports & exports')
@section('page-title', 'Rapports & exports')
@section('page-subtitle', 'Téléchargez vos données en PDF ou en Excel')

@section('content')

<div class="ds-grid ds-grid-2">

    <x-card title="Personnel">
        <p class="ds-muted" style="margin-top:0">Liste des agents : matricule, fonction, équipe, contact, statut.</p>
        <div class="ds-page-actions">
            <a class="ds-btn ds-btn-secondary" href="{{ route('services.exports.personnel', 'pdf') }}">PDF</a>
            <a class="ds-btn ds-btn-primary" href="{{ route('services.exports.personnel', 'xlsx') }}">Excel</a>
        </div>
    </x-card>

    @if($ressourcesDispo)
        <x-card title="Véhicules">
            <p class="ds-muted" style="margin-top:0">Parc de véhicules : statut, kilométrage, prochaine maintenance.</p>
            <div class="ds-page-actions">
                <a class="ds-btn ds-btn-secondary" href="{{ route('services.exports.vehicules', 'pdf') }}">PDF</a>
                <a class="ds-btn ds-btn-primary" href="{{ route('services.exports.vehicules', 'xlsx') }}">Excel</a>
            </div>
        </x-card>

        <x-card title="Stocks">
            <p class="ds-muted" style="margin-top:0">État des stocks avec alertes de seuil et de péremption.</p>
            <div class="ds-page-actions">
                <a class="ds-btn ds-btn-secondary" href="{{ route('services.exports.stocks', 'pdf') }}">PDF</a>
                <a class="ds-btn ds-btn-primary" href="{{ route('services.exports.stocks', 'xlsx') }}">Excel</a>
            </div>
        </x-card>
    @endif

    <x-card title="Interventions">
        <p class="ds-muted" style="margin-top:0">Interventions de vos équipes sur la période choisie.</p>
        <form method="GET">
            <div class="ds-form-grid">
                <div class="ds-field">
                    <label class="ds-label" for="du">Du</label>
                    <input class="ds-input" type="date" id="du" name="du" value="{{ old('du', $du) }}" required>
                </div>
                <div class="ds-field">
                    <label class="ds-label" for="au">Au</label>
                    <input class="ds-input" type="date" id="au" name="au" value="{{ old('au', $au) }}" required>
                </div>
            </div>
            <div class="ds-page-actions">
                <button class="ds-btn ds-btn-secondary" type="submit" formaction="{{ route('services.exports.interventions', 'pdf') }}">PDF</button>
                <button class="ds-btn ds-btn-primary" type="submit" formaction="{{ route('services.exports.interventions', 'xlsx') }}">Excel</button>
            </div>
        </form>
    </x-card>

    <x-card title="Synthèse des indicateurs">
        <p class="ds-muted" style="margin-top:0">Alertes, délais, charge des équipes et zones touchées (PDF).</p>
        <div class="ds-page-actions">
            @foreach([7, 30, 90] as $p)
                <a class="ds-btn ds-btn-secondary" href="{{ route('services.exports.kpi', ['jours' => $p]) }}">{{ $p }} jours</a>
            @endforeach
        </div>
    </x-card>

</div>

<p class="ds-muted" style="margin-top:16px">
    Le rapport détaillé d'une intervention se télécharge depuis la page
    <a href="{{ route('services.interventions.journalieres') }}">Interventions</a>.
</p>

@endsection
