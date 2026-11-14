@extends('layouts.portal')

@section('title', 'Actualités')
@section('page-title', 'Actualités')
@section('page-subtitle', 'Publiez les informations de votre service')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fil-actualites.css') }}">
@endpush

@section('content')
<div class="fa-page">

    <div class="fa-feed" id="fa-feed">

        @include('partials.fil-composer', [
            'action' => route('services.actualiteStore'),
            'auteur' => $service->nom,
            'photo'  => $service->photo_profil,
        ])

        @forelse($actualites as $actu)
            @include('partials.fil-post', [
                'actu' => $actu, 'gerer' => true,
                'routeEdit' => 'services.actualiteEdit', 'routeDestroy' => 'services.actualiteDestroy',
            ])
        @empty
            <div class="fa-empty">
                <div class="fa-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
                </div>
                <h3>Aucune actualité</h3>
                <p>Publiez votre première actualité avec le formulaire ci-dessus.</p>
            </div>
        @endforelse

        @if($actualites->hasMorePages())
            <div class="fa-more-wrap">
                <a class="fa-load-more" href="{{ $actualites->nextPageUrl() }}" data-fa-more>Voir plus de publications</a>
            </div>
        @endif
    </div>

    <aside class="fa-aside" aria-label="Conseils">
        <div class="fa-side-card">
            <h3>Vos publications</h3>
            <div class="fa-stat"><span>Total</span><strong>{{ $actualites->total() }}</strong></div>
        </div>
        <div class="fa-side-card">
            <h3>Conseils</h3>
            <ul>
                <li>Un texte court et clair est lu en entier.</li>
                <li>Une photo nette attire davantage l'attention.</li>
                <li>Vidéos MP4 : 50 Mo maximum.</li>
            </ul>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script src="{{ asset('javascript/fil-actualites.js') }}" defer></script>
@endpush
