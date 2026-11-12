@extends('layouts.portal')

@section('title', 'Modifier le programme')
@section('page-title', 'Modifier le programme')
@section('page-subtitle', $programme->nom_programme)

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.vaccinationIndex') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ route('services.vaccinationUpdate', $programme->id) }}">
        @csrf @method('PUT')
        @include('services.partials.vaccination-programme-form')
    </form>
</x-card>
@endsection
