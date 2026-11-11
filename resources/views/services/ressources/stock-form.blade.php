@extends('layouts.portal')

@php $edition = $stock->exists; @endphp

@section('title', $edition ? 'Modifier un article' : 'Nouvel article de stock')
@section('page-title', $edition ? 'Modifier un article' : 'Nouvel article de stock')
@section('page-subtitle', $edition ? $stock->designation : 'Médicament, vaccin, matériel ou consommable')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.ressources.index') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ $edition ? route('services.ressources.stocks.update', $stock->id) : route('services.ressources.stocks.store') }}">
        @csrf
        @if($edition) @method('PUT') @endif

        <div class="ds-form-grid">
            <div class="ds-field">
                <label class="ds-label" for="categorie">Catégorie *</label>
                <select class="ds-select" id="categorie" name="categorie" required>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" @selected(old('categorie', $stock->categorie) === $c)>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="designation">Désignation *</label>
                <input class="ds-input" id="designation" name="designation" value="{{ old('designation', $stock->designation) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="lot">N° de lot</label>
                <input class="ds-input" id="lot" name="lot" value="{{ old('lot', $stock->lot) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="date_expiration">Date d'expiration</label>
                <input class="ds-input" type="date" id="date_expiration" name="date_expiration"
                       value="{{ old('date_expiration', $stock->date_expiration?->format('Y-m-d')) }}">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="quantite">Quantité *</label>
                <input class="ds-input" type="number" min="0" id="quantite" name="quantite" value="{{ old('quantite', $stock->quantite) }}" required>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="unite">Unité</label>
                <input class="ds-input" id="unite" name="unite" value="{{ old('unite', $stock->unite) }}" placeholder="boîtes, doses, pièces…">
            </div>
            <div class="ds-field">
                <label class="ds-label" for="seuil_alerte">Seuil d'alerte</label>
                <input class="ds-input" type="number" min="0" id="seuil_alerte" name="seuil_alerte" value="{{ old('seuil_alerte', $stock->seuil_alerte) }}">
            </div>
        </div>

        <button class="ds-btn ds-btn-primary" type="submit">{{ $edition ? 'Enregistrer' : 'Ajouter l\'article' }}</button>
    </form>
</x-card>
@endsection
