@extends('layouts.portal')

@section('title', 'Modifier le vaccin')
@section('page-title', 'Modifier le vaccin')
@section('page-subtitle', $vaccin->nom_vaccin)

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.vaccinationEnfantsIndex') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ route('services.vaccinationEnfantsUpdate', $vaccin->id) }}">
        @csrf @method('PUT')
        @include('services.partials.vaccination-vaccin-form')
    </form>
</x-card>
@endsection
