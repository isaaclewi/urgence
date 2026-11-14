@extends('layouts.portal')

@section('title', 'Services proposés')
@section('page-title', 'Services proposés aux citoyens')
@section('page-subtitle', 'Les raccourcis affichés sur l\'espace citoyen (titre, description, lien, image)')

@section('page-actions')
    <a class="ds-btn ds-btn-secondary" href="{{ route('admin.services') }}">Comptes des services d'urgence</a>
@endsection

@section('content')
<x-card title="Ajouter un service proposé" class="ds-section">
    <form action="{{ route('admin.serviceStore') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="ds-form-grid">
            <div class="ds-field"><label class="ds-label" for="nom_service">Titre *</label>
                <input class="ds-input" id="nom_service" type="text" name="nom_service" value="{{ old('nom_service') }}" required></div>
            <div class="ds-field"><label class="ds-label" for="lien">Lien *</label>
                <input class="ds-input" id="lien" type="text" name="lien" value="{{ old('lien') }}" required></div>
            <div class="ds-field"><label class="ds-label" for="image">Image (jpg, png — 2 Mo max)</label>
                <input class="ds-input" id="image" type="file" name="image" accept=".jpg,.jpeg,.png"></div>
        </div>
        <div class="ds-field"><label class="ds-label" for="description">Description</label>
            <textarea class="ds-textarea" id="description" name="description" rows="3">{{ old('description') }}</textarea></div>
        <button class="ds-btn ds-btn-primary" type="submit">Créer le service</button>
    </form>
</x-card>

@if($servicesProposes->isEmpty())
    <x-card><x-empty title="Aucun service proposé">Le formulaire ci-dessus permet d'en créer un.</x-empty></x-card>
@else
    <div class="ds-grid" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr))">
        @foreach($servicesProposes as $service)
            <x-card :flush="true">
                <img src="{{ $service->image ?? asset('medias/default-service.jpg') }}" alt="{{ $service->nom_service }}"
                     loading="lazy" style="width:100%;height:150px;object-fit:cover;display:block">
                <div style="padding:16px">
                    <strong>{{ $service->nom_service }}</strong>
                    <p class="ds-muted" style="margin:6px 0 12px;font-size:13px">{{ \Illuminate\Support\Str::limit($service->description, 120) }}</p>
                    <form action="{{ route('admin.serviceDestroy', $service->id) }}" method="POST"
                          onsubmit="return confirm('Supprimer ce service proposé ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="ds-btn ds-btn-danger ds-btn-sm">Supprimer</button>
                    </form>
                </div>
            </x-card>
        @endforeach
    </div>
    @include('layouts.partials.pager', ['paginator' => $servicesProposes])
@endif
@endsection
