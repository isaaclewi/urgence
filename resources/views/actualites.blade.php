@extends('citoyen')

@section('title', 'Actualités — CongoAssist')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fil-actualites.css') }}">
@endpush

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Actualités</h1>
        <p>{{ $actualites->total() }} publication{{ $actualites->total() > 1 ? 's' : '' }}</p>
    </div>
</div>

<div class="fa-page">

    <div class="fa-feed" id="fa-feed">
        @forelse($actualites as $actu)
            @include('partials.fil-post', ['actu' => $actu])
        @empty
            <div class="fa-empty">
                <div class="fa-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
                </div>
                <h3>Aucune actualité pour l'instant</h3>
                <p>Revenez plus tard pour découvrir les dernières informations.</p>
            </div>
        @endforelse

        @if($actualites->hasMorePages())
            <div class="fa-more-wrap">
                <a class="fa-load-more" href="{{ $actualites->nextPageUrl() }}" data-fa-more>Voir plus d'actualités</a>
            </div>
        @endif
    </div>

    <aside class="fa-aside" aria-label="À propos">
        <div class="fa-side-card">
            <h3>Informations officielles</h3>
            <p>Retrouvez ici les annonces publiées par les services d'urgence et l'administration de CongoAssist.</p>
        </div>
        <div class="fa-side-card">
            <h3>En bref</h3>
            <div class="fa-stat"><span>Publications</span><strong>{{ $actualites->total() }}</strong></div>
            @if($derniere ?? null)
            <div class="fa-stat"><span>Dernière</span><strong>{{ $derniere->copy()->locale('fr')->isoFormat('D MMM YYYY') }}</strong></div>
            @endif
        </div>
    </aside>
</div>

@push('scripts')
<script src="{{ asset('javascript/fil-actualites.js') }}" defer></script>
@endpush

@endsection
