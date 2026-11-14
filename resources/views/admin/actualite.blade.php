@extends('layouts.portal')

@section('title', 'Actualités')
@section('page-title', 'Actualités')
@section('page-subtitle', 'Publiez et gérez les informations visibles par les citoyens')

@section('content')
<x-card title="Nouvelle actualité" class="ds-section">
    <form action="{{ route('admin.actualiteStore') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="ds-field">
            <label class="ds-label" for="contenu">Contenu *</label>
            <textarea class="ds-textarea" id="contenu" name="contenu" rows="4" maxlength="5000" required>{{ old('contenu') }}</textarea>
        </div>
        <div class="ds-field">
            <label class="ds-label" for="url_media">Image ou vidéo (facultatif · jpg, png, gif, mp4 — 50 Mo max)</label>
            <input class="ds-input" type="file" id="url_media" name="url_media" accept=".jpg,.jpeg,.png,.gif,.mp4">
        </div>
        <button type="submit" class="ds-btn ds-btn-primary">Publier</button>
    </form>
</x-card>

@forelse($actualites as $actu)
    <x-card class="ds-section" :flush="true">
        @if($actu->url_media)
            <div style="background:var(--ds-surface-2);display:flex;justify-content:center;max-height:360px;overflow:hidden">
                @if($actu->type_media === 'mp4')
                    <video controls preload="metadata" style="width:100%;max-height:360px;object-fit:contain;background:#000">
                        <source src="{{ $actu->url_media }}" type="video/mp4">
                    </video>
                @else
                    <img src="{{ $actu->url_media }}" alt="Média de l'actualité" loading="lazy" style="max-width:100%;max-height:360px;object-fit:cover">
                @endif
            </div>
        @endif

        <div style="padding:20px">
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:8px">
                <strong>{{ $actu->auteur_nom }}</strong>
                <span class="ds-muted">{{ \Carbon\Carbon::parse($actu->date_publication)->format('d/m/Y') }}</span>
            </div>

            @if(mb_strlen($actu->contenu) > 280)
                <p style="margin:0;white-space:pre-line">{{ \Illuminate\Support\Str::limit($actu->contenu, 280) }}</p>
                <details style="margin-top:6px">
                    <summary style="cursor:pointer;color:var(--ds-accent-dark);font-weight:600;font-size:13px">Lire la suite</summary>
                    <p style="margin:8px 0 0;white-space:pre-line">{{ $actu->contenu }}</p>
                </details>
            @else
                <p style="margin:0;white-space:pre-line">{{ $actu->contenu }}</p>
            @endif
        </div>

        <div class="ds-row-actions" style="padding:10px 20px;border-top:1px solid var(--ds-border)">
            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('admin.actualiteEdit', $actu->id) }}">Modifier</a>
            <form action="{{ route('admin.actualiteDestroy', $actu->id) }}" method="POST"
                  onsubmit="return confirm('Supprimer cette actualité ? Cette action est irréversible.')">
                @csrf @method('DELETE')
                <button type="submit" class="ds-btn ds-btn-danger ds-btn-sm">Supprimer</button>
            </form>
        </div>
    </x-card>
@empty
    <x-card><x-empty title="Aucune actualité">Utilisez le formulaire ci-dessus pour publier la première.</x-empty></x-card>
@endforelse
@endsection
