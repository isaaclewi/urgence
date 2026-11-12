@extends('layouts.portal')

@section('title', 'Actualités')
@section('page-title', 'Actualités')
@section('page-subtitle', 'Publiez les informations de votre service')

@section('content')
<div class="ds-grid ds-grid-aside">

    {{-- Publier --}}
    <x-card title="Nouvelle actualité">
        <form method="POST" action="{{ route('services.actualiteStore') }}" enctype="multipart/form-data">
            @csrf
            <div class="ds-field">
                <label class="ds-label">Auteur</label>
                <input class="ds-input" value="{{ $service->nom }}" readonly>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="contenu">Contenu *</label>
                <textarea class="ds-textarea" id="contenu" name="contenu" rows="6" maxlength="5000" required
                          placeholder="Écrivez votre actualité ici…">{{ old('contenu') }}</textarea>
            </div>
            <div class="ds-field">
                <label class="ds-label" for="url_media">Image ou vidéo (facultatif)</label>
                <input class="ds-input" type="file" id="url_media" name="url_media" accept=".jpg,.jpeg,.png,.gif,.mp4">
                <span class="ds-muted" style="font-size:12px">JPG, PNG, GIF ou MP4 — 50 Mo maximum.</span>
            </div>
            <button class="ds-btn ds-btn-primary" type="submit">Publier</button>
        </form>
    </x-card>

    {{-- Fil des publications --}}
    <div class="ds-stack">
        @forelse($actualites as $actu)
            <x-card flush>
                @if($actu->url_media)
                    @if($actu->type_media === 'mp4')
                        <video controls preload="metadata" style="width:100%;max-height:380px;display:block;background:#000">
                            <source src="{{ $actu->url_media }}" type="video/mp4">
                        </video>
                    @else
                        <img src="{{ $actu->url_media }}" alt="Illustration de l'actualité" loading="lazy"
                             style="width:100%;max-height:380px;object-fit:cover;display:block">
                    @endif
                @endif

                <div style="padding:18px 20px">
                    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:10px">
                        <div>
                            <strong>{{ $actu->auteur_nom }}</strong>
                            <div class="ds-muted" style="font-size:12px">
                                Publié le {{ $actu->created_at?->format('d/m/Y à H:i') }}
                                @if($actu->updated_at && $actu->updated_at->gt($actu->created_at->copy()->addMinute())) · modifié @endif
                            </div>
                        </div>
                        <div class="ds-row-actions">
                            <a class="ds-btn ds-btn-secondary ds-btn-sm" href="{{ route('services.actualiteEdit', $actu->id) }}">Modifier</a>
                            <form method="POST" action="{{ route('services.actualiteDestroy', $actu->id) }}"
                                  onsubmit="return confirm('Supprimer cette actualité ?')">
                                @csrf @method('DELETE')
                                <button class="ds-btn ds-btn-ghost ds-btn-sm ds-text-danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </div>

                    <p style="margin:0;white-space:pre-line;line-height:1.65">{{ $actu->contenu }}</p>
                </div>
            </x-card>
        @empty
            <x-card>
                <x-empty title="Aucune actualité">Publiez votre première actualité avec le formulaire.</x-empty>
            </x-card>
        @endforelse

        @include('layouts.partials.pager', ['paginator' => $actualites])
    </div>
</div>
@endsection
