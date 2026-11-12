@extends('layouts.portal')

@section('title', 'Modifier le bilan de santé')
@section('page-title', 'Modifier le bilan')
@section('page-subtitle', ($citoyen?->nom ?? '') . ' ' . ($citoyen?->prenom ?? ''))

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.citoyensBilanShow', $bilan->id) }}">← Retour à la fiche</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ route('services.citoyensBilanUpdate', $bilan->id) }}">
        @csrf @method('PUT')
        @include('services.partials.bilan-form')
    </form>
</x-card>
@endsection
