@extends('layouts.portal')

@section('title', 'Nouveau programme de vaccination')
@section('page-title', 'Nouveau programme')
@section('page-subtitle', 'Ajoutez un programme de vaccination')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('services.vaccinationIndex') }}">← Retour</a>
@endsection

@section('content')
<x-card>
    <form method="POST" action="{{ route('services.vaccinationStore') }}">
        @csrf
        @include('services.partials.vaccination-programme-form')
    </form>
</x-card>
@endsection
