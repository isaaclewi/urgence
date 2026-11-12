@extends('layouts.portal')

@section('title', 'Nouveau vaccin')
@section('page-title', 'Nouveau vaccin')
@section('page-subtitle', 'Ajoutez un vaccin pour enfants')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.vaccinationEnfantsIndex') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ route('services.vaccinationEnfantsStore') }}">
        @csrf
        @include('services.partials.vaccination-vaccin-form')
    </form>
</x-card>
@endsection
