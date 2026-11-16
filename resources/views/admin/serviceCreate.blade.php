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
            <div class="ds-field"><label class="ds-label" for="page">Service concerné *</label>
                <select class="ds-input" id="page" name="page" required>
                    <option value="">— Choisir —</option>
                    @foreach($pages as $cle => $p)
                        <option value="{{ $cle }}" data-desc="{{ $p['description'] }}" @selected(old('page') === $cle)>{{ $p['libelle'] }}</option>
                    @endforeach
                    <option value="autre" @selected(old('page') === 'autre')>Autre (lien externe)…</option>
                </select></div>
            <div class="ds-field"><label class="ds-label" for="nom_service">Titre <span class="ds-muted">(facultatif : rempli automatiquement)</span></label>
                <input class="ds-input" id="nom_service" type="text" name="nom_service" value="{{ old('nom_service') }}"></div>
            <div class="ds-field" id="bloc-site"><label class="ds-label" for="site">Adresse du site <span class="ds-muted">(facultatif)</span></label>
                <input class="ds-input" id="site" type="text" inputmode="url" name="site" value="{{ old('site') }}" placeholder="ex. {{ url('/') }}">
                <small class="ds-muted">Laissez vide : le lien fonctionnera sur n'importe quelle adresse. Sinon le reste du lien est complété automatiquement.</small></div>
            <div class="ds-field" id="bloc-externe" style="display:none"><label class="ds-label" for="lien_externe">Lien externe complet *</label>
                <input class="ds-input" id="lien_externe" type="text" inputmode="url" name="lien_externe" value="{{ old('lien_externe') }}" placeholder="https://..."></div>
            <div class="ds-field"><label class="ds-label" for="image">Image (jpg, png — 2 Mo max)</label>
                <input class="ds-input" id="image" type="file" name="image" accept=".jpg,.jpeg,.png"></div>
        </div>
        <div class="ds-field"><label class="ds-label" for="description">Description</label>
            <textarea class="ds-textarea" id="description" name="description" rows="3">{{ old('description') }}</textarea></div>
        <button class="ds-btn ds-btn-primary" type="submit">Créer le service</button>
    </form>
    <script>
    (function(){
        var sel=document.getElementById('page'), desc=document.getElementById('description'),
            site=document.getElementById('bloc-site'), ext=document.getElementById('bloc-externe');
        function maj(){
            var autre = sel.value==='autre';
            ext.style.display = autre ? '' : 'none';
            site.style.display = autre ? 'none' : '';
            var o = sel.options[sel.selectedIndex];
            if(!autre && o && o.dataset.desc && !desc.value.trim()){ desc.value=o.dataset.desc; }
        }
        sel.addEventListener('change', maj); maj();
    })();
    </script>
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
